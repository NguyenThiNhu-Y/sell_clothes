<?php

namespace App\Repositories;

use App\Models\category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectID;

class CategoryRepository implements CategoryRepositoryInterface
{

    /**
     * @return mixed
     */
    public function get_categories()
    {
        $collection = DB::connection('mongodb')->collection('categories');

        // Thực hiện truy vấn MongoDB
        // $category = $collection->where('visible', '=', 1)
        //     ->select('id', 'name', 'text', 'img')
        //     ->addSelect('category AS type')
        //     ->get();

        // // Chuyển kết quả về mảng
        // $categoryArray = $category->toArray();

        $categories = DB::collection('categories')
            ->select(['id', 'name', 'text', 'img'])
            ->where('visible', 1)
            ->where('parentsId', null)
            ->get(['id', 'name', 'text', 'img'])
            ->map(function ($item) {
                $item['type'] = 'category';
                return $item;
            });

        $newArray = [];

        foreach ($categories as $item) {
            $newArray[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'text' => $item['text'],
                'img' => $item['img'],
                'type' => $item['type'],
            ];
        }
        return $newArray;
    }

    public function get_categories_detail($cateId)
    {
        $collection = DB::connection('mongodb')->collection('categories');

        // Thực hiện truy vấn MongoDB
        $categories = DB::collection('categories')
            ->select(['id', 'name', 'text'])
            ->where('id', (int)$cateId)
            ->get(['id', 'name', 'text']);

        $result = [];
        foreach ($categories as $item) {
            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'text' => $item['text']
            ];
        }

        return $result[0];
    }

    public function get_subcate($parentsId)
    {
        // Thực hiện truy vấn MongoDB
        $categories = DB::collection('categories')
            ->select(['id', 'name', 'text'])
            ->where('parentsId', (int)$parentsId)
            ->get(['id', 'name', 'text']);

        $result = [];
        foreach ($categories as $item) {
            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'text' => $item['text']
            ];
        }
        return $result;
    }
}
