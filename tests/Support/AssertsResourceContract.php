<?php

namespace Tests\Support;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Asserts resolved JsonResource payloads match expected arrays, including nested resources.
 */
trait AssertsResourceContract
{
    /**
     * @param  array<string, mixed>  $expected
     */
    protected function assertResourceContract(JsonResource $resource, array $expected): void
    {
        $this->assertSame($expected, $this->resolveResourcePayload($resource));
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveResourcePayload(JsonResource $resource): array
    {
        $payload = $resource->resolve(request());

        return $this->resolveNestedResourceValues($payload);
    }

    /**
     * @param  array<string, mixed>|list<mixed>  $payload
     * @return array<string, mixed>|list<mixed>
     */
    private function resolveNestedResourceValues(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if ($value instanceof JsonResource) {
                $payload[$key] = $this->resolveResourcePayload($value);

                continue;
            }

            if (is_array($value)) {
                $payload[$key] = $this->resolveNestedResourceValues($value);
            }
        }

        return $payload;
    }
}
