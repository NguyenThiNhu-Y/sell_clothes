<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CollectionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class CollectionRepository implements CollectionRepositoryInterface
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function get_collection($collectionId, $start)
    {
        $query = "select id, name, mobileBanner, pcBanner"
            . " FROM collection"
            . " WHERE visible = 1 and id = " . (string)$collectionId;

        $result = DB::select(DB::raw($query));

        $collection = array();

        if (isset($result)) {
            $collection = (array)$result[0];
        }

        $collection["products"] = $this->productRepository->get_productsCollection($collectionId, $start);
        $collection["total"] = count($this->productRepository->get_productsCollection($collectionId, -1));

        return $collection;
    }

    public function get_collections()
    {
        // $query = "select id, name FROM collection WHERE visible = 1";
        // $result = DB::select(DB::raw($query));
        // $resultArray = [];
        // if (!empty($result)) {
        //     $resultArray = (array) $result;
        // }

        // return (array) DB::select(DB::raw($query));
        // return $resultArray;

        $collection = DB::connection('mongodb')->collection('collections');
        $result = $collection->where('visible', 1)->get(['_id', 'name']);
        $resultArray = $result->toArray();

        return $resultArray;
    }

    public function get_collection_banner()
    {
        // $query = "select id, name, mobileBanner, pcBanner, 'collection' AS type FROM collection"
        //     . " WHERE visible = 1";

        // return (array) DB::select(DB::raw($query));
        // Kết nối tới MongoDB
        $collection = DB::connection('mongodb')->collection('collections');

        // Thực hiện truy vấn MongoDB
        $collections = $collection->where('visible', '=', 1)
            ->get(['_id', 'name', 'mobileBanner', 'pcBanner', 'type' => 'collection']);

        // Chuyển kết quả về mảng
        $collectionsArray = $collections->toArray();

        return $collectionsArray;
    }
}
