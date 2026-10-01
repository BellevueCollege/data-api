<?php

namespace Tests\Support;

use Tests\TestCase;

/**
 * Unit tests for JsonResource output shape without warehouse database setup.
 */
abstract class ResourceTestCase extends TestCase
{
    use AssertsResourceContract;
}
