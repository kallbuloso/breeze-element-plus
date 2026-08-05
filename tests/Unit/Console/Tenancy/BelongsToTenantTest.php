<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests\Unit\Console\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Kallbuloso\BreezeElementPlus\Tests\TestCase;

class BelongsToTenantTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('tenant_scoped_records');
        Schema::create('tenant_scoped_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
        });
    }

    public function test_it_assigns_and_scopes_records_to_the_authenticated_tenant(): void
    {
        require_once dirname(__DIR__, 4).'/stubs/tenancy/app/Scopes/TenantScope.php';
        require_once dirname(__DIR__, 4).'/stubs/tenancy/app/Traits/BelongsToTenant.php';

        $firstUser = new class extends \Illuminate\Foundation\Auth\User
        {
            public int $tenant_id = 1;
        };
        $secondUser = new class extends \Illuminate\Foundation\Auth\User
        {
            public int $tenant_id = 2;
        };
        $record = new class extends Model
        {
            use \App\Traits\BelongsToTenant;

            protected $table = 'tenant_scoped_records';
            public $timestamps = false;
            protected $guarded = [];
        };

        Auth::setUser($firstUser);
        $first = $record->newQuery()->create(['name' => 'first']);

        Auth::setUser($secondUser);
        $second = $record->newQuery()->create(['name' => 'second']);

        $this->assertSame(1, $first->tenant_id);
        $this->assertSame(2, $second->tenant_id);
        $this->assertSame(['second'], $record->newQuery()->pluck('name')->all());
        $this->assertSame(['first', 'second'], $record->newQueryWithoutScope(\App\Scopes\TenantScope::class)->orderBy('id')->pluck('name')->all());

        Auth::setUser($firstUser);
        $forced = $record->newQuery()->create(['name' => 'forced', 'tenant_id' => 2]);
        $this->assertSame(1, $forced->tenant_id);

        Auth::forgetUser();
        $this->assertSame([], $record->newQuery()->pluck('name')->all());
    }
}
