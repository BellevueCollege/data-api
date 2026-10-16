<?php

namespace Tests\Feature;

use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Copilot link-found lookup by source area on the public API.
 */
class LinkFoundApiTest extends ApiFeatureTestCase
{
    public function test_returns_links_for_source_area(): void
    {
        WarehouseFixtures::seedLinks();

        $this->get('/api/v1/linksfound/Financial%20Aid')
            ->assertOk()
            ->assertJsonFragment([
                'Link' => 'Apply for aid',
            ]);
    }

    public function test_replaces_plus_with_space_in_source_area(): void
    {
        WarehouseFixtures::seedLinks();

        $this->get('/api/v1/linksfound/Financial+Aid')
            ->assertOk()
            ->assertJsonFragment([
                'Link' => 'Apply for aid',
            ]);
    }

    public function test_returns_zero_count_for_unknown_source_area(): void
    {
        $this->get('/api/v1/linkscount/UnknownArea')
            ->assertOk()
            ->assertContent('0');
    }

    public function test_returns_link_count_for_source_area(): void
    {
        WarehouseFixtures::seedLinks();

        $this->get('/api/v1/linkscount/Financial%20Aid')
            ->assertOk()
            ->assertContent('1');
    }
}
