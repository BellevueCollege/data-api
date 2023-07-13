<?php
namespace App\Http\Serializers;

use League\Fractal\Serializer;

class CustomDataArraySerializer extends \League\Fractal\Serializer\ArraySerializer
{
    /**
    * Custom serializer for API data.
    * Allows passing of resource label which is used to wrap data.
    **/

    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection(?string $resourceKey, array $data):array
    {
        if ($resourceKey === false) {
            return $data;
        }
        return array($resourceKey ?: 'data' => $data);
    }

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item(?string $resourceKey, array $data):array
    {
        if ($resourceKey === false) {
            return $data;
        }
        return array($resourceKey ?: 'data' => $data);
    }

    /**
     * Serialize null resource.
     *
     * @return array
     */
    public function null():?array
    {
        return null;
    }
}
