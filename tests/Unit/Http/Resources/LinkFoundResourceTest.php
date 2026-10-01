<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\LinkFoundResource;
use App\Models\LinkFound;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of LinkFoundResource matches the links-found API contract.
 */
class LinkFoundResourceTest extends ResourceTestCase
{
    public function test_serializes_link_found_contract(): void
    {
        $link = new LinkFound();
        $link->forceFill([
            'LinkText' => 'Apply for aid',
            'LinkDescr' => 'Aid application',
        ]);

        $this->assertResourceContract(new LinkFoundResource($link), [
            'Link' => 'Apply for aid',
            'Description' => 'Aid application',
        ]);
    }
}
