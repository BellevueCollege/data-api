<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** @var string Access token */
            'access_token' => $this->resource['access_token'],
            'token_type' => 'bearer',
            /** @var int Time in seconds until token expires */
            'expires_in' => $this->resource['expires_in']
        ];
    }
}
