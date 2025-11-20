<?php

namespace App\Http\Controllers;

use App\Models\LinkFound;
use App\Http\Transformers\LinkFoundTransformer;
use App\Http\Controllers\Controller;
use App\Http\Serializers\CustomDataArraySerializer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use DB;

class LinkFoundController extends ApiController
{

    const WRAPPER = "Links";

    /**
    * Return all links
    *
    * @return \Illuminate\Http\JsonResponse
    **/
    public function index(Manager $fractal, Request $request)
    {
        $links  = LinkFound::all();
        $collection = new Collection($links, new LinkFoundTransformer);

        $fractal = new Manager;
        $data = $fractal->createData($collection)->toArray();

        return $this->respond($data);
    }

    /**
    * Return links with descriptions based on a provided SourceArea value
    *
    * @param string $sourceArea SourceArea value
    *
    * @return \Illuminate\Http\JsonResponse
    **/
    public function getLinksBySourceArea($sourceArea)
    {
        $links = DB::connection('copilot')
            ->table('vw_LinkFound')
            ->where('SourceArea', '=', str_replace("+", " ",$sourceArea))
            ->select('LinkText', 'LinkDescr')
            ->get();

        $data = $links;
        if (!is_null($links) && !$links->isEmpty()) {
            //When using the Eloquent query builder, we must "hydrate" the results back to collection of objects
            $links_hydrated = LinkFound::hydrate($links->toArray());
            $collection = new Collection($links_hydrated, new LinkFoundTransformer, self::WRAPPER);

            //set data serializer
            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($collection)->toArray();
        }

        return $this->respond($data);
    }

    /**
    * Return the count of links based on a provided SourceArea value
    *
    * @param string $sourceArea SourceArea value
    *
    * @return \Illuminate\Http\JsonResponse
    **/
    public function getLinkCountBySourceArea($sourceArea)
    {
        $count = DB::connection('copilot')
            ->table('vw_LinkFound')
            ->where('SourceArea', '=', str_replace("+", " ",$sourceArea))
            ->count();

        return $this->respond($count);
    }
}
