<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests\Unit\Console\Crud;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kallbuloso\BreezeElementPlus\Console\Crud\SchemaInspector;
use Kallbuloso\BreezeElementPlus\Tests\TestCase;

class TenantMetadataTest extends TestCase
{
    public function test_it_recognizes_only_a_tenant_id_foreign_key_to_tenants(): void
    {
        Schema::create('tenants', fn (Blueprint $table) => $table->id());
        Schema::create('tenant_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
        });
        Schema::create('legacy_products', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
        });

        $inspector = new SchemaInspector;

        $this->assertTrue($inspector->hasTenantForeignKey('tenant_products'));
        $this->assertFalse($inspector->hasTenantForeignKey('legacy_products'));
        $this->assertFalse($inspector->hasTenantForeignKey('products'));
    }
}
