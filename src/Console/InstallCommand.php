<?php

namespace Kallbuloso\BreezeElementPlus\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Pest\TestSuite;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\select;

#[AsCommand(name: 'breeze-element-plus:install')]
class InstallCommand extends Command implements PromptsForMissingInput
{
    use InstallsApiStack, InstallsInertiaStacks;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'breeze-element-plus:install {stack : The development stack that should be installed (vue,api)}
                            {--pest : Indicate that Pest should be installed}
                            {--ssr : Indicates if Inertia SSR support should be installed}
                            {--eslint : Indicates if ESLint with Prettier should be installed}
                            {--lang= : Application language (en, es, pt, pt_BR)}
                            {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Breeze Element Plus controllers and resources';

    protected string $language = 'pt_BR';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        if (! $this->resolveLanguage()) {
            return 1;
        }

        if ($this->argument('stack') === 'vue') {
            return $this->installInertiaVueStack();
        } elseif ($this->argument('stack') === 'api') {
            return $this->installApiStack();
        }

        $this->components->error('Invalid stack. Supported stacks are [vue] and [api].');

        return 1;
    }

    protected function resolveLanguage(): bool
    {
        $supported = [
            'en' => 'English',
            'es' => 'Español',
            'pt' => 'Português',
            'pt_BR' => 'Português (Brasil)',
        ];
        $language = trim((string) $this->option('lang'));

        if ($language === '' && $this->input->isInteractive()) {
            $language = select(
                label: 'Which language would you like to install?',
                options: $supported,
                default: 'pt_BR',
            );
        }

        $language = $language === '' ? 'pt_BR' : $language;

        if (! array_key_exists($language, $supported)) {
            $this->components->error('Invalid language: '.$language.'. Supported languages are: '.implode(', ', array_keys($supported)).'.');

            return false;
        }

        $this->language = $language;

        return true;
    }

    /**
     * Install Breeze's tests.
     *
     * @return bool
     */
    protected function installTests()
    {
        (new Filesystem)->ensureDirectoryExists(base_path('tests/Feature'));

        $stubStack = match ($this->argument('stack')) {
            'api' => 'api',
            default => 'inertia-common',
        };

        if ($this->option('pest') || $this->isUsingPest()) {
            if ($this->hasComposerPackage('phpunit/phpunit')) {
                $this->removeComposerPackages(['phpunit/phpunit'], true);
            }

            if (! $this->requireComposerPackages(['pestphp/pest', 'pestphp/pest-plugin-laravel'], true)) {
                return false;
            }

            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/'.$stubStack.'/pest-tests/Feature', base_path('tests/Feature'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/'.$stubStack.'/pest-tests/Unit', base_path('tests/Unit'));
            (new Filesystem)->copy(__DIR__.'/../../stubs/'.$stubStack.'/pest-tests/Pest.php', base_path('tests/Pest.php'));
        } else {
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/'.$stubStack.'/tests/Feature', base_path('tests/Feature'));
        }

        return true;
    }

    /**
     * Install the given middleware names into the application.
     *
     * @param  array|string  $name
     * @param  string  $group
     * @param  string  $modifier
     * @return void
     */
    protected function installMiddleware($names, $group = 'web', $modifier = 'append')
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        $names = collect((array) $names)
            ->filter(fn ($name) => ! str_contains($bootstrapApp, $name))
            ->whenNotEmpty(function ($names) use ($bootstrapApp, $group, $modifier) {
                $names = $names->map(fn ($name) => "$name")->implode(','.PHP_EOL.'            ');

                $stubs = [
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware): void {',
                ];

                $bootstrapApp = str_replace(
                    $stubs,
                    collect($stubs)->transform(fn ($stub) => $stub
                        .PHP_EOL."        \$middleware->$group($modifier: ["
                        .PHP_EOL."            $names,"
                        .PHP_EOL.'        ]);'
                        .PHP_EOL
                    )->all(),
                    $bootstrapApp,
                );

                file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
            });
    }

    /**
     * Install the given middleware aliases into the application.
     *
     * @param  array  $aliases
     * @return void
     */
    protected function installMiddlewareAliases($aliases)
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        $aliases = collect($aliases)
            ->filter(fn ($alias) => ! str_contains($bootstrapApp, $alias))
            ->whenNotEmpty(function ($aliases) use ($bootstrapApp) {
                $aliases = $aliases->map(fn ($name, $alias) => "'$alias' => $name")->implode(','.PHP_EOL.'            ');

                $stubs = [
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware): void {',
                ];

                $bootstrapApp = str_replace(
                    $stubs,
                    collect($stubs)->transform(fn ($stub) => $stub
                        .PHP_EOL.'        $middleware->alias(['
                        .PHP_EOL."            $aliases,"
                        .PHP_EOL.'        ]);'
                        .PHP_EOL
                    )->all(),
                    $bootstrapApp,
                );

                file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
            });
    }

    /**
     * Determine if the given Composer package is installed.
     *
     * @param  string  $package
     * @return bool
     */
    protected function hasComposerPackage($package)
    {
        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        return array_key_exists($package, $packages['require'] ?? [])
            || array_key_exists($package, $packages['require-dev'] ?? []);
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  bool  $asDev
     * @return bool
     */
    protected function requireComposerPackages(array $packages, $asDev = false)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Removes the given Composer Packages from the application.
     *
     * @param  bool  $asDev
     * @return bool
     */
    protected function removeComposerPackages(array $packages, $asDev = false)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'remove'];
        }

        $command = array_merge(
            $command ?? ['composer', 'remove'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Update the dependencies in the "package.json" file.
     *
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Update the scripts in the "package.json" file.
     *
     * @return void
     */
    protected static function updateNodeScripts(callable $callback)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $content = json_decode(file_get_contents(base_path('package.json')), true);

        $content['scripts'] = $callback(
            array_key_exists('scripts', $content) ? $content['scripts'] : []
        );

        file_put_contents(
            base_path('package.json'),
            json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Delete the "node_modules" directory and remove the associated lock files.
     *
     * @return void
     */
    protected static function flushNodeModules()
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('pnpm-lock.yaml'));
            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('bun.lock'));
            $files->delete(base_path('bun.lockb'));
            $files->delete(base_path('deno.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Install the authentication model, migration, factory and seeder.
     *
     * @return void
     */
    protected function installAuthScaffolding()
    {
        $files = new Filesystem;

        $files->copyDirectory(__DIR__.'/../../stubs/auth/app/Models', app_path('Models'));
        $files->copyDirectory(__DIR__.'/../../stubs/auth/database/factories', database_path('factories'));
        $files->copyDirectory(__DIR__.'/../../stubs/auth/database/migrations', database_path('migrations'));
        $files->copyDirectory(__DIR__.'/../../stubs/auth/database/seeders', database_path('seeders'));
    }

    protected function installLocalizationScaffolding(bool $withFrontend = false): void
    {
        $files = new Filesystem;
        $fakerLocales = [
            'en' => 'en_US',
            'es' => 'es_ES',
            'pt' => 'pt_PT',
            'pt_BR' => 'pt_BR',
        ];
        $files->copyDirectory(
            __DIR__.'/../../stubs/localization/lang/'.$this->language,
            lang_path($this->language),
        );

        $files->copy(
            __DIR__.'/../../stubs/localization/LICENSE-LARAVEL-LANG',
            lang_path('LICENSE-LARAVEL-LANG'),
        );

        foreach (['.env', '.env.example'] as $filename) {
            $path = base_path($filename);

            if (! $files->exists($path)) {
                continue;
            }

            $content = $files->get($path);
            $content = $this->setEnvironmentValue($content, 'APP_LOCALE', $this->language);
            $content = $this->setEnvironmentValue($content, 'APP_FALLBACK_LOCALE', $this->language);
            $content = $this->setEnvironmentValue($content, 'APP_FAKER_LOCALE', $fakerLocales[$this->language]);
            $files->put($path, $content);
        }

        if (! $withFrontend) {
            return;
        }

        $files->ensureDirectoryExists(resource_path('js/locales'));
        $files->ensureDirectoryExists(resource_path('js/locales/'.$this->language));
        $files->copy(
            __DIR__.'/../../stubs/localization/resources/js/locales/'.$this->language.'.js',
            resource_path('js/locales/'.$this->language.'.js'),
        );
        $files->copy(
            __DIR__.'/../../stubs/localization/resources/js/locales/'.$this->language.'/message.js',
            resource_path('js/locales/'.$this->language.'/message.js'),
        );

        $files->put(
            resource_path('js/locales/index.js'),
            "export { default } from './{$this->language}'".PHP_EOL
            ."export { default as messages } from './{$this->language}/message'".PHP_EOL
            ."export const locale = '{$this->language}'".PHP_EOL,
        );
    }

    /**
     * Install password hashing and HTTP security defaults.
     */
    protected function installSecurityScaffolding(): void
    {
        $files = new Filesystem;

        $files->copyDirectory(__DIR__.'/../../stubs/security/app/Hashing', app_path('Hashing'));
        $files->copyDirectory(__DIR__.'/../../stubs/security/app/Providers', app_path('Providers'));
        $files->copyDirectory(__DIR__.'/../../stubs/security/config', config_path());

        ServiceProvider::addProviderToBootstrapFile('App\\Providers\\HashServiceProvider');

        $this->configureSecurityEnvironment();
        $this->installSecurityMiddleware();
        $this->runCommands([$this->artisanCommand('config:clear')]);
    }

    /**
     * Generate and configure secrets without exposing them in .env.example.
     */
    protected function configureSecurityEnvironment(): void
    {
        $files = new Filesystem;

        if (! $files->exists(base_path('.env')) && $files->exists(base_path('.env.example'))) {
            $files->copy(base_path('.env.example'), base_path('.env'));
        }

        foreach (['.env', '.env.example'] as $filename) {
            $path = base_path($filename);

            if (! $files->exists($path)) {
                continue;
            }

            $content = $files->get($path);
            $isExample = $filename === '.env.example';
            $appUrl = trim((string) $this->environmentValue($content, 'APP_URL'), " \t\n\r\0\x0B\"'");
            $secureCookie = str_starts_with($appUrl, 'https://') ? 'true' : 'false';
            $pepper = $isExample ? '' : $this->environmentValue($content, 'HASH_PEPPER');

            if (! $isExample && blank($pepper)) {
                $pepper = bin2hex(random_bytes(32));
            }

            $values = [
                'HASH_DRIVER' => 'argon2id_pepper',
                'HASH_PEPPER_ID' => 'v1',
                'HASH_PEPPER' => (string) $pepper,
                'HASH_PREVIOUS_PEPPERS' => '',
                'HASH_ALLOW_LEGACY' => 'true',
                'HASH_VERIFY' => 'true',
                'ARGON_MEMORY' => '32768',
                'ARGON_TIME' => '3',
                'ARGON_THREADS' => '1',
                'SESSION_ENCRYPT' => 'true',
                'SESSION_SECURE_COOKIE' => $secureCookie,
                'SESSION_HTTP_ONLY' => 'true',
                'SESSION_SAME_SITE' => 'lax',
                'TRUSTED_PROXIES' => '',
            ];

            foreach ($values as $key => $value) {
                $preserve = ! $isExample && in_array($key, [
                    'HASH_PEPPER_ID',
                    'HASH_PREVIOUS_PEPPERS',
                    'HASH_ALLOW_LEGACY',
                    'SESSION_SECURE_COOKIE',
                    'SESSION_HTTP_ONLY',
                    'SESSION_SAME_SITE',
                    'TRUSTED_PROXIES',
                ], true);

                $content = $this->setEnvironmentValue($content, $key, $value, ! $preserve);
            }

            $files->put($path, $content);
        }
    }

    protected function environmentValue(string $content, string $key): ?string
    {
        if (! preg_match('/^'.preg_quote($key, '/').'=(.*)$/m', $content, $matches)) {
            return null;
        }

        return trim($matches[1]);
    }

    protected function setEnvironmentValue(string $content, string $key, string $value, bool $overwrite = true): string
    {
        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

        if (preg_match($pattern, $content)) {
            return $overwrite
                ? preg_replace($pattern, $key.'='.$value, $content, 1) ?? $content
                : $content;
        }

        return rtrim($content).PHP_EOL.$key.'='.$value.PHP_EOL;
    }

    protected function installSecurityMiddleware(): void
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        if (! str_contains($bootstrapApp, 'use Illuminate\Http\Request;')) {
            $bootstrapApp = preg_replace(
                '/use Illuminate\\\\Foundation\\\\Configuration\\\\Middleware;\R/',
                'use Illuminate\Foundation\Configuration\Middleware;'.PHP_EOL.'use Illuminate\Http\Request;'.PHP_EOL,
                $bootstrapApp,
                1,
            ) ?? $bootstrapApp;
        }

        $configuration = '';

        if (! str_contains($bootstrapApp, '$middleware->trustHosts(')) {
            $configuration .= PHP_EOL.'        $middleware->trustHosts(at: null, subdomains: false);'.PHP_EOL;
        }

        if (! str_contains($bootstrapApp, "env('TRUSTED_PROXIES')")) {
            $configuration .= PHP_EOL
                ."        \$trustedProxies = env('TRUSTED_PROXIES');".PHP_EOL
                .PHP_EOL
                ."        if (filled(\$trustedProxies)) {".PHP_EOL
                ."            \$middleware->trustProxies(".PHP_EOL
                ."                at: \$trustedProxies === '*'".PHP_EOL
                ."                    ? '*'".PHP_EOL
                ."                    : array_map('trim', explode(',', \$trustedProxies)),".PHP_EOL
                ."                headers: Request::HEADER_X_FORWARDED_FOR".PHP_EOL
                ."                    | Request::HEADER_X_FORWARDED_HOST".PHP_EOL
                ."                    | Request::HEADER_X_FORWARDED_PORT".PHP_EOL
                ."                    | Request::HEADER_X_FORWARDED_PROTO,".PHP_EOL
                ."            );".PHP_EOL
                ."        }".PHP_EOL;
        }

        if ($configuration === '') {
            return;
        }

        $stubs = [
            '->withMiddleware(function (Middleware $middleware) {',
            '->withMiddleware(function (Middleware $middleware): void {',
        ];

        $bootstrapApp = str_replace(
            $stubs,
            collect($stubs)->map(fn ($stub) => $stub.$configuration)->all(),
            $bootstrapApp,
        );

        file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
    }

    /**
     * Get an Artisan command that uses the current PHP binary.
     *
     * @param  string  $command
     * @return string
     */
    protected function artisanCommand($command)
    {
        $phpBinary = $this->phpBinary();

        if (str_contains($phpBinary, ' ')) {
            $phpBinary = '"'.$phpBinary.'"';
        }

        return $phpBinary.' artisan '.$command;
    }

    /**
     * Get the path to the appropriate PHP binary.
     *
     * @return string
     */
    protected function phpBinary()
    {
        if (function_exists('Illuminate\Support\php_binary')) {
            return \Illuminate\Support\php_binary();
        }

        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }

    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'stack' => fn () => select(
                label: 'Which Breeze stack would you like to install?',
                options: [
                    'vue' => 'Vue with Inertia and Element Plus',
                    'api' => 'API only',
                ],
                scroll: 2,
            ),
        ];
    }

    /**
     * Interact further with the user if they were prompted for missing arguments.
     *
     * @return void
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        $stack = $input->getArgument('stack');

        if ($stack === 'vue') {
            $input->setOption('ssr', select(
                label: 'Would you like Inertia SSR support?',
                options: ['No', 'Yes'],
                default: 'No',
            ) === 'Yes');

            $input->setOption('eslint', select(
                label: 'Would you like ESLint with Prettier?',
                options: ['Yes', 'No'],
                default: 'Yes',
            ) === 'Yes');
        }

        $input->setOption('pest', select(
            label: 'Which testing framework do you prefer?',
            options: ['Pest', 'PHPUnit'],
            default: 'Pest',
        ) === 'Pest');
    }

    /**
     * Determine whether the project is already using Pest.
     *
     * @return bool
     */
    protected function isUsingPest()
    {
        return class_exists(TestSuite::class);
    }
}
