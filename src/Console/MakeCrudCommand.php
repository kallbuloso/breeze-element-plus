<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Kallbuloso\BreezeElementPlus\Console\Crud\FileWriter;
use Kallbuloso\BreezeElementPlus\Console\Crud\IconSuggester;
use Kallbuloso\BreezeElementPlus\Console\Crud\SchemaInspector;
use Kallbuloso\BreezeElementPlus\Console\Crud\StubProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'breeze-element-plus:crud')]
class MakeCrudCommand extends Command
{
    protected $signature = 'breeze-element-plus:crud
        {table : Existing database table to inspect}
        {--route= : Resource URI and route-name base}
        {--group= : Navigation group key}
        {--group-label= : Navigation group label}
        {--icon= : Iconify icon}
        {--singular= : Human singular label}
        {--plural= : Human plural label}
        {--force : Overwrite generated files without prompts}
        {--dry-run : Report planned changes without writing}
        {--skip-format : Do not run formatters after generation}';

    protected $description = 'Generate an Inertia, Vue, and Element Plus CRUD from an existing table.';

    private SchemaInspector $schema;

    private StubProcessor $stubs;

    private FileWriter $writer;

    /** @var array<string, mixed> */
    private array $context;

    public function handle(): int
    {
        $this->schema = new SchemaInspector;

        if (! $this->schema->tableExists((string) $this->argument('table'))) {
            $this->components->error("The table [{$this->argument('table')}] does not exist. Run its migration first.");

            return self::FAILURE;
        }

        if (! $this->hasVueStackPrerequisites()) {
            return self::FAILURE;
        }

        if (! $this->validateGeneratorOptions()) {
            return self::FAILURE;
        }

        $this->context = $this->buildContext((string) $this->argument('table'));
        $this->stubs = new StubProcessor(dirname(__DIR__, 2).'/stubs/crud');
        $this->writer = new FileWriter(new Filesystem, $this, (bool) $this->option('force'), (bool) $this->option('dry-run'));

        if (! $this->generateArtifacts()) {
            $this->components->error('CRUD generation was cancelled; routes, navigation, and locale were not changed.');

            return self::FAILURE;
        }
        $this->integrateApplication();
        $this->printReport();

        if ($this->option('dry-run')) {
            $this->components->warn('Dry run complete; no files were written.');

            return self::SUCCESS;
        }

        if (! $this->option('skip-format')) {
            $this->runFormatters();
        }

        $this->components->info("CRUD for {$this->context['model']} generated successfully.");

        return self::SUCCESS;
    }

    private function hasVueStackPrerequisites(): bool
    {
        $required = [
            base_path('routes/web.php'),
            resource_path('js/Configs/nav.js'),
            resource_path('js/Layouts/AuthenticatedLayout.vue'),
            resource_path('js/locales/index.js'),
        ];

        foreach ($required as $path) {
            if (! is_file($path)) {
                $this->components->error("This command requires the Breeze Element Plus Vue stack. Missing: {$path}");

                return false;
            }
        }

        return true;
    }

    private function validateGeneratorOptions(): bool
    {
        $table = (string) $this->argument('table');
        $route = trim((string) $this->option('route')) ?: $table;
        $labels = [
            trim((string) $this->option('singular')),
            trim((string) $this->option('plural')),
            trim((string) $this->option('group-label')),
        ];

        if (preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $table) !== 1) {
            $this->components->error('The table name must contain only letters, numbers, and underscores.');

            return false;
        }

        if (preg_match('/^[A-Za-z0-9_-]+(?:\/[A-Za-z0-9_-]+)*$/', $route) !== 1 || str_contains($route, '..')) {
            $this->components->error('The resource URI must contain safe path segments only.');

            return false;
        }

        foreach ($labels as $label) {
            if ($label !== '' && preg_match('/^[\pL\pN][\pL\pN .,&()-]*$/u', $label) !== 1) {
                $this->components->error('Labels may contain only letters, numbers, spaces, and basic punctuation.');

                return false;
            }
        }

        $icon = trim((string) $this->option('icon'));
        if ($icon !== '' && preg_match('/^[a-z0-9-]+:[a-z0-9-]+$/', $icon) !== 1) {
            $this->components->error('The icon must be a safe Iconify name, for example ri:box-3-line.');

            return false;
        }

        return true;
    }

    /** @return array<string, mixed> */
    private function buildContext(string $table): array
    {
        $model = Str::studly(Str::singular($table));
        $pluralModel = Str::studly(Str::plural($table));
        $route = trim((string) $this->option('route')) ?: Str::kebab(Str::plural($table));
        $route = trim($route, '/');
        $routeName = str_replace('/', '.', $route);
        $singular = trim((string) $this->option('singular')) ?: $model;
        $plural = trim((string) $this->option('plural')) ?: $pluralModel;
        $group = trim((string) $this->option('group')) ?: $routeName;
        $group = Str::camel($group);
        $groupLabel = trim((string) $this->option('group-label')) ?: $plural;

        return [
            'table' => $table,
            'model' => $model,
            'modelVariable' => Str::camel($model),
            'pluralModel' => $pluralModel,
            'route' => $route,
            'routeName' => $routeName,
            'view' => $pluralModel,
            'singular' => $singular,
            'plural' => $plural,
            'group' => $group,
            'groupLabel' => $groupLabel,
            'icon' => trim((string) $this->option('icon')) ?: (new IconSuggester)->suggest($model),
            'columns' => $this->schema->columns($table),
        ];
    }

    private function generateArtifacts(): bool
    {
        $replacements = $this->replacements();
        $model = $this->context['model'];
        $viewPath = resource_path('js/Pages/'.$this->context['view']);

        $written = $this->writeStub('Model', 'Model', app_path("Models/{$model}.php"), $replacements);
        $written = $this->writeStub('Controller', 'Controller', app_path("Http/Controllers/{$model}Controller.php"), $replacements) && $written;
        $written = $this->writeStub('Request', 'Request', app_path("Http/Requests/{$model}Request.php"), $replacements) && $written;
        $written = $this->writeStub('Factory', 'Factory', database_path("factories/{$model}Factory.php"), $replacements) && $written;
        $written = $this->writeStub('Seeder', 'Seeder', database_path("seeders/{$model}Seeder.php"), $replacements) && $written;

        foreach (['Index', 'Create', 'Edit', 'Show', '_Form'] as $view) {
            $written = $this->writeStub("Vue {$view}", "views/{$view}", "{$viewPath}/{$view}.vue", $replacements) && $written;
        }

        return $written;
    }

    /** @param array<string, string> $replacements */
    private function writeStub(string $label, string $stub, string $path, array $replacements): bool
    {
        return $this->writer->write($label, $path, $this->stubs->render($stub, $replacements));
    }

    /** @return array<string, string> */
    private function replacements(): array
    {
        return [
            '{{Model}}' => $this->context['model'],
            '{{model}}' => $this->context['modelVariable'],
            '{{Table}}' => $this->context['table'],
            '{{View}}' => $this->context['view'],
            '{{Route}}' => $this->context['route'],
            '{{RouteName}}' => $this->context['routeName'],
            '{{Singular}}' => $this->context['singular'],
            '{{Plural}}' => $this->context['plural'],
            '{{Fillable}}' => $this->fillable(),
            '{{Casts}}' => $this->casts(),
            '{{Relationships}}' => $this->relationships(),
            '{{Rules}}' => $this->rules(),
            '{{Searchable}}' => $this->searchable(),
            '{{FactoryFields}}' => $this->factoryFields(),
            '{{FormData}}' => $this->formData(),
            '{{FormFields}}' => $this->formFields(),
            '{{TableColumns}}' => $this->tableColumns(),
            '{{ShowFields}}' => $this->showFields(),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function editableColumns(): array
    {
        return array_values(array_filter($this->context['columns'], static fn (array $column): bool => ! in_array($column['name'], ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'], true)));
    }

    private function fillable(): string
    {
        return implode(PHP_EOL, array_map(static fn (array $column): string => "        '{$column['name']}',", $this->editableColumns()));
    }

    private function casts(): string
    {
        $casts = [];
        foreach ($this->editableColumns() as $column) {
            $kind = $this->schema->classify($column, $this->schema->foreignKeyFor($this->context['table'], $column['name']));
            $cast = match ($kind) {
                'boolean' => 'boolean', 'decimal' => 'decimal:2', 'date' => 'date', 'datetime' => 'datetime', 'json' => 'array', default => null,
            };
            if ($cast !== null) {
                $casts[] = "        '{$column['name']}' => '{$cast}',";
            }
        }

        return implode(PHP_EOL, $casts);
    }

    private function relationships(): string
    {
        $relationships = [];
        foreach ($this->editableColumns() as $column) {
            $foreignKey = $this->schema->foreignKeyFor($this->context['table'], $column['name']);
            if ($foreignKey === null) {
                continue;
            }
            $related = Str::studly(Str::singular((string) $foreignKey['foreign_table']));
            $name = Str::camel(Str::beforeLast($column['name'], '_id'));
            $relationships[] = "\n    public function {$name}()\n    {\n        return \$this->belongsTo(\\App\\Models\\{$related}::class, '{$column['name']}');\n    }";
        }

        return implode(PHP_EOL, $relationships);
    }

    private function rules(): string
    {
        $rules = [];
        foreach ($this->editableColumns() as $column) {
            $foreignKey = $this->schema->foreignKeyFor($this->context['table'], $column['name']);
            $kind = $this->schema->classify($column, $foreignKey);
            $parts = [$column['nullable'] ? 'nullable' : 'required'];
            $parts[] = match ($kind) {
                'boolean' => 'boolean', 'integer' => 'integer', 'decimal' => 'numeric', 'date', 'datetime' => 'date', 'json' => 'array',
                'foreign' => 'integer', 'textarea' => 'string', default => 'string',
            };
            if ($kind === 'foreign') {
                $parts[] = 'exists:'.$foreignKey['foreign_table'].',id';
            }
            if ($kind === 'string' && $column['length'] !== null) {
                $parts[] = 'max:'.$column['length'];
            }
            $rules[] = "            '{$column['name']}' => ['".implode("', '", $parts)."'],";
        }

        return implode(PHP_EOL, $rules);
    }

    private function searchable(): string
    {
        $columns = [];
        foreach ($this->editableColumns() as $column) {
            $kind = $this->schema->classify($column, $this->schema->foreignKeyFor($this->context['table'], $column['name']));
            if (in_array($kind, ['string', 'textarea'], true)) {
                $columns[] = "'{$column['name']}'";
            }
        }

        return '['.implode(', ', $columns).']';
    }

    private function factoryFields(): string
    {
        $fields = [];
        foreach ($this->editableColumns() as $column) {
            $kind = $this->schema->classify($column, $this->schema->foreignKeyFor($this->context['table'], $column['name']));
            $value = match ($kind) {
                'boolean' => 'fake()->boolean()', 'integer', 'foreign' => 'fake()->numberBetween(1, 100)', 'decimal' => 'fake()->randomFloat(2, 0, 1000)',
                'date' => 'fake()->date()', 'datetime' => 'fake()->dateTime()', 'textarea' => 'fake()->paragraph()', 'json' => '[]', default => 'fake()->word()',
            };
            $fields[] = "            '{$column['name']}' => {$value},";
        }

        return implode(PHP_EOL, $fields);
    }

    private function formData(): string
    {
        $fields = [];
        foreach ($this->editableColumns() as $column) {
            $kind = $this->schema->classify($column, $this->schema->foreignKeyFor($this->context['table'], $column['name']));
            $default = match ($kind) { 'boolean' => 'false', 'integer', 'decimal', 'foreign' => 'null', 'json' => '{}', default => "''" };
            $fields[] = "  {$column['name']}: {$default},";
        }

        return implode(PHP_EOL, $fields);
    }

    private function formFields(): string
    {
        $fields = [];
        foreach ($this->editableColumns() as $column) {
            $name = $column['name'];
            $label = Str::headline($name);
            $kind = $this->schema->classify($column, $this->schema->foreignKeyFor($this->context['table'], $name));
            $control = match ($kind) {
                'boolean' => "          <ElSwitch v-model=\"form.{$name}\" />",
                'integer' => "          <ElInputNumber v-model=\"form.{$name}\" style=\"width: 100%\" />",
                'decimal' => "          <ElInputNumber v-model=\"form.{$name}\" :precision=\"2\" :step=\"0.01\" style=\"width: 100%\" />",
                'date' => "          <ElDatePicker v-model=\"form.{$name}\" type=\"date\" style=\"width: 100%\" />",
                'datetime' => "          <ElDatePicker v-model=\"form.{$name}\" type=\"datetime\" style=\"width: 100%\" />",
                'textarea', 'json' => "          <ElInput v-model=\"form.{$name}\" type=\"textarea\" :rows=\"4\" />",
                'foreign' => "          <!-- TODO: provide options for {$name} -->\n          <ElInputNumber v-model=\"form.{$name}\" style=\"width: 100%\" />",
                default => "          <ElInput v-model=\"form.{$name}\" />",
            };
            $fields[] = "        <ElFormItem label=\"{$label}\" :error=\"form.errors.{$name}\">\n{$control}\n        </ElFormItem>";
        }

        return implode(PHP_EOL.PHP_EOL, $fields);
    }

    private function tableColumns(): string
    {
        return implode(PHP_EOL, array_map(static fn (array $column): string => '      <ElTableColumn prop="'.$column['name'].'" label="'.Str::headline($column['name']).'" min-width="140" />', $this->editableColumns()));
    }

    private function showFields(): string
    {
        $model = $this->context['modelVariable'];

        return implode(PHP_EOL, array_map(static fn (array $column): string => '      <ElDescriptionsItem label="'.Str::headline($column['name']).'">{{ '.$model.'.'.$column['name']." ?? '—' }}</ElDescriptionsItem>", $this->editableColumns()));
    }

    private function integrateApplication(): void
    {
        $this->integrateRoutes();
        $this->integrateNavigation();
        $this->integrateLocale();
    }

    private function integrateRoutes(): void
    {
        $model = $this->context['model'];
        $import = "use App\\Http\\Controllers\\{$model}Controller;";
        $block = "    // breeze-element-plus:crud {$model}\n    Route::resource('{$this->context['route']}', {$model}Controller::class);\n    // /breeze-element-plus:crud {$model}";

        $this->writer->update('Routes', base_path('routes/web.php'), function (string $content) use ($import, $block, $model): string {
            if (str_contains($content, "// breeze-element-plus:crud {$model}")) {
                return $content;
            }
            if (! str_contains($content, "Route::middleware(['auth', 'verified'])->group(function () {")) {
                $this->components->error('Could not find the authenticated route group. Routes were not changed.');

                return $content;
            }
            if (! str_contains($content, $import)) {
                $content = preg_replace('/<\?php\R/', "<?php\n\n{$import}\n", $content, 1) ?? $content;
            }

            return preg_replace("/(Route::middleware\(\['auth', 'verified'\]\)->group\(function \(\) \{)(.*?)(\n\}\);)/s", "$1$2\n\n{$block}$3", $content, 1) ?? $content;
        });
    }

    private function integrateNavigation(): void
    {
        $group = $this->context['group'];
        $feature = "      {\n        key: '{$this->context['routeName']}.index',\n        labelKey: 'crud.{$this->context['routeName']}.plural',\n        icon: '{$this->context['icon']}',\n        route: '{$this->context['routeName']}.index'\n      }";
        $groupBlock = "  {\n    key: '{$group}',\n    labelKey: 'crud.{$this->context['routeName']}.plural',\n    icon: '{$this->context['icon']}',\n    features: [\n{$feature}\n    ]\n  }";

        $this->writer->update('Navigation', resource_path('js/Configs/nav.js'), function (string $content) use ($group, $feature, $groupBlock): string {
            if (str_contains($content, "route: '{$this->context['routeName']}.index'")) {
                return $content;
            }
            $pattern = "/(key:\s*'".preg_quote($group, '/')."'[\\s\\S]*?features:\s*\[)/";
            if (preg_match($pattern, $content) === 1) {
                return preg_replace($pattern, "$1\n{$feature},", $content, 1) ?? $content;
            }

            return preg_replace('/\n\]\s*$/', ",\n{$groupBlock}\n]\n", rtrim($content), 1) ?? $content;
        });
    }

    private function integrateLocale(): void
    {
        $index = (string) file_get_contents(resource_path('js/locales/index.js'));
        if (! preg_match("/locale\s*=\s*'([^']+)'/", $index, $matches)) {
            $this->components->warn('Could not detect the active frontend locale. Locale was not changed.');

            return;
        }
        $path = resource_path("js/locales/{$matches[1]}/message.js");
        $entry = "  crud: {\n    {$this->context['routeName']}: {\n      singular: '".addslashes($this->context['singular'])."',\n      plural: '".addslashes($this->context['plural'])."'\n    }\n  }";
        $this->writer->update('Locale', $path, function (string $content) use ($entry): string {
            if (str_contains($content, "{$this->context['routeName']}: {")) {
                return $content;
            }

            return preg_replace('/\n\}\s*$/', ",\n{$entry}\n}\n", rtrim($content), 1) ?? $content;
        });
    }

    private function printReport(): void
    {
        $this->table(['Artifact', 'Path', 'Status'], $this->writer->report);
    }

    private function runFormatters(): void
    {
        $phpFiles = [
            app_path("Models/{$this->context['model']}.php"),
            app_path("Http/Controllers/{$this->context['model']}Controller.php"),
            app_path("Http/Requests/{$this->context['model']}Request.php"),
            database_path("factories/{$this->context['model']}Factory.php"),
            database_path("seeders/{$this->context['model']}Seeder.php"),
        ];

        if (is_file(base_path('vendor/bin/pint'))) {
            $this->runFormatter(array_merge(['vendor/bin/pint'], $phpFiles), 'Pint');
        }

        $packageJson = base_path('package.json');
        if (! is_file($packageJson)) {
            return;
        }

        $scripts = json_decode((string) file_get_contents($packageJson), true)['scripts'] ?? [];
        foreach (['lint', 'format:check', 'build'] as $script) {
            if (array_key_exists($script, $scripts)) {
                $this->runFormatter(['npm', 'run', $script], "npm run {$script}");
            }
        }
    }

    /** @param array<int, string> $command */
    private function runFormatter(array $command, string $label): void
    {
        $this->line("Formatting: {$label}");
        $process = new Process($command, base_path());
        $process->setTimeout(null);
        $process->run(function (string $type, string $output): void {
            $this->output->write($output);
        });

        if (! $process->isSuccessful()) {
            $this->components->warn("{$label} exited with code {$process->getExitCode()}.");
        }
    }
}
