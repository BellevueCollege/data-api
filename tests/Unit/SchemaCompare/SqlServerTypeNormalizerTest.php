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
        $this->assertSame('nvarchar(max)', $this->normalizer->canonicalize('nvarchar(max)'));
        $this->assertSame('nvarchar(max)', $this->normalizer->displayWarehouseType('nvarchar(max)'));
    }

    public function test_compares_decimal_precision_and_scale(): void
    {
        $this->assertTrue($this->normalizer->equals('decimal(8,2)', 'decimal(8,2)'));
        $this->assertFalse($this->normalizer->equals('decimal(8,2)', 'decimal(10,2)'));
        $this->assertFalse($this->normalizer->equals('decimal(8,2)', 'decimal(8,4)'));
        $this->assertSame('decimal(8,2)', $this->normalizer->canonicalize('decimal(8,2)'));
        $this->assertSame('numeric(10,2)', $this->normalizer->canonicalize('numeric(10,2)'));
    }

    public function test_defaults_missing_decimal_scale_to_zero(): void
    {
        $this->assertSame('decimal(8,0)', $this->normalizer->canonicalize('decimal(8)'));
    }

    public function test_compares_types_case_insensitively(): void
    {
        $this->assertTrue($this->normalizer->equals('DATETIME', 'datetime'));
        $this->assertTrue($this->normalizer->equals('Bit', 'bit'));
        $this->assertSame('bit', $this->normalizer->canonicalize('BIT'));
    }

    public function test_detects_wrong_base_type(): void
    {
        $this->assertFalse($this->normalizer->equals('nvarchar(50)', 'varchar(50)'));
    }

    public function test_canonicalizes_varchar_and_binary_lengths(): void
    {
        $this->assertSame('varchar(50)', $this->normalizer->canonicalize('varchar(50)'));
        $this->assertSame('char(10)', $this->normalizer->canonicalize('char(10)'));
        $this->assertSame('varbinary(16)', $this->normalizer->canonicalize('varbinary(16)'));
        $this->assertFalse($this->normalizer->equals('varchar(50)', 'varchar(60)'));
    }

    public function test_halves_nchar_byte_length_from_laravel(): void
    {
        $this->assertSame(
            'nchar(10)',
            $this->normalizer->canonicalize('nchar(20)', lengthIsBytesForNationalStrings: true)
        );
        $this->assertSame(
            'nchar(20)',
            $this->normalizer->canonicalize('nchar(20)', lengthIsBytesForNationalStrings: false)
        );
    }

    public function test_canonicalizes_datetime2_and_float_precision(): void
    {
        $this->assertSame('datetime2(7)', $this->normalizer->canonicalize('datetime2(7)'));
        $this->assertSame('float(53)', $this->normalizer->canonicalize('float(53)'));
        $this->assertSame('time(3)', $this->normalizer->canonicalize('time(3)'));
    }

    public function test_strips_whitespace_before_parsing(): void
    {
        $this->assertSame('nvarchar(50)', $this->normalizer->canonicalize('nvarchar ( 50 )'));
        $this->assertSame('decimal(8,2)', $this->normalizer->canonicalize('decimal ( 8 , 2 )'));
    }

    public function test_parses_max_decimal_length_and_bare_shapes(): void
    {
        $this->assertSame(
            [
                'base' => 'nvarchar',
                'length' => null,
                'precision' => null,
                'scale' => null,
                'max' => true,
            ],
            $this->normalizer->parse('nvarchar(max)')
        );

        $this->assertSame(
            [
                'base' => 'decimal',
                'length' => null,
                'precision' => 8,
                'scale' => 2,
                'max' => false,
            ],
            $this->normalizer->parse('decimal(8,2)')
        );

        $this->assertSame(
            [
                'base' => 'varchar',
                'length' => 50,
                'precision' => 50,
                'scale' => null,
                'max' => false,
            ],
            $this->normalizer->parse('varchar(50)')
        );

        $this->assertSame(
            [
                'base' => 'bit',
                'length' => null,
                'precision' => null,
                'scale' => null,
                'max' => false,
            ],
            $this->normalizer->parse('bit')
        );
    }

    public function test_keeps_unrecognized_type_strings_as_the_base(): void
    {
        $this->assertSame(
            [
                'base' => 'weird[]',
                'length' => null,
                'precision' => null,
                'scale' => null,
                'max' => false,
            ],
            $this->normalizer->parse('weird[]')
        );
        $this->assertSame('weird[]', $this->normalizer->canonicalize('weird[]'));
    }

    public function test_does_not_treat_unknown_parameterized_types_as_length_or_precision(): void
    {
        $this->assertSame('foo', $this->normalizer->canonicalize('foo(10)'));
    }

    public function test_requires_full_string_match_for_typed_shapes(): void
    {
        $this->assertSame('nvarchar(max)#', $this->normalizer->canonicalize('nvarchar(max)#'));
        $this->assertSame('#nvarchar(max)', $this->normalizer->canonicalize('#nvarchar(max)'));
        $this->assertSame('decimal(8,2)#', $this->normalizer->canonicalize('decimal(8,2)#'));
        $this->assertSame('#decimal(8,2)', $this->normalizer->canonicalize('#decimal(8,2)'));
        $this->assertSame('varchar(50)#', $this->normalizer->canonicalize('varchar(50)#'));
        $this->assertSame('#varchar(50)', $this->normalizer->canonicalize('#varchar(50)'));
        $this->assertSame('!bit', $this->normalizer->canonicalize('!bit'));
        $this->assertSame('bit!', $this->normalizer->canonicalize('bit!'));
    }
}
