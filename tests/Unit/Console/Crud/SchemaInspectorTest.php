<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests\Unit\Console\Crud;

use Kallbuloso\BreezeElementPlus\Console\Crud\SchemaInspector;
use Kallbuloso\BreezeElementPlus\Tests\TestCase;

class SchemaInspectorTest extends TestCase
{
    public function test_it_inspects_columns_and_foreign_keys(): void
    {
        $inspector = new SchemaInspector;
        $columns = $inspector->columns('products');
        $category = collect($columns)->firstWhere('name', 'category_id');

        $this->assertTrue($inspector->tableExists('products'));
        $this->assertSame('categories', $inspector->foreignKeyFor('products', 'category_id')['foreign_table']);
        $this->assertSame('foreign', $inspector->classify($category, $inspector->foreignKeyFor('products', 'category_id')));
        $this->assertSame('textarea', $inspector->classify(collect($columns)->firstWhere('name', 'description')));
    }
}
