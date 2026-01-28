<?php

namespace App\Http\Controllers;

use App\Models\LinkFound;
use App\Http\Resources\LinkFoundCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LinkFoundController extends ApiController
{
    /**
    * Return all links
    *
    * @return LinkFoundCollection
    **/
    public function index(Request $request)
    {
        $links  = LinkFound::all();
        return new LinkFoundCollection($links); 
    }

    /**
    * Return links with descriptions based on a provided SourceArea value
    *
    * @param string $sourceArea SourceArea value
    *
    * @return LinkFoundCollection | JsonResponse
    **/
    public function getLinksBySourceArea($sourceArea)
    {
        try {
            $links = LinkFound::where('SourceArea', '=', str_replace("+", " ",$sourceArea))
                ->get();
            return new LinkFoundCollection($links);
        } catch (\Exception $e) {
            return response()->json(['Links' => []]);
        }
    }

    /**
    * Return the count of links based on a provided SourceArea value
    *
    * @param string $sourceArea SourceArea value
    *
    * @return int
    **/
    public function getLinkCountBySourceArea($sourceArea)
    {
        $count = LinkFound::where('SourceArea', '=', str_replace("+", " ",$sourceArea))
            ->count();

        return $this->respond($count);
    }
}
