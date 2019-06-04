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
    public function collection($resourceKey, array $data)
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
    public function item($resourceKey, array $data)
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
    public function null()
    {
        return null;
    }
}
?>