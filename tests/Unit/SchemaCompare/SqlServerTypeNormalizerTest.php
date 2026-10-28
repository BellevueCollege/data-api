<?php

namespace Tests\Unit\SchemaCompare;

use App\Support\SchemaCompare\SqlServerTypeNormalizer;
use PHPUnit\Framework\TestCase;

class SqlServerTypeNormalizerTest extends TestCase
{
    private SqlServerTypeNormalizer $normalizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->normalizer = new SqlServerTypeNormalizer();
    }

    public function test_treats_nvarchar_byte_length_from_laravel_as_character_length(): void
    {
        $this->assertTrue($this->normalizer->equals('nvarchar(50)', 'nvarchar(100)'));
        $this->assertSame('nvarchar(50)', $this->normalizer->displayWarehouseType('nvarchar(100)'));
    }

    public function test_compares_nvarchar_max(): void
    {
        $this->assertTrue($this->normalizer->equals('nvarchar(max)', 'nvarchar(max)'));
    }

    public function test_compares_decimal_precision_and_scale(): void
    {
        $this->assertTrue($this->normalizer->equals('decimal(8,2)', 'decimal(8,2)'));
        $this->assertFalse($this->normalizer->equals('decimal(8,2)', 'decimal(10,2)'));
    }

    public function test_compares_types_case_insensitively(): void
    {
        $this->assertTrue($this->normalizer->equals('DATETIME', 'datetime'));
        $this->assertTrue($this->normalizer->equals('Bit', 'bit'));
    }

    public function test_detects_wrong_base_type(): void
    {
        $this->assertFalse($this->normalizer->equals('nvarchar(50)', 'varchar(50)'));
    }
}
