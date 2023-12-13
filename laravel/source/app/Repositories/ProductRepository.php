<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations\Response;
use stdClass;

class ProductRepository implements ProductRepositoryInterface
{
	public function all()
	{
		return Product::all();
	}

	public function getProductByCategoryId($cateId)
	{
		$query = "Select * from product where categoryId = " . $cateId;
		$list_product = DB::select($query);
		return $list_product;
	}

	public function get_max_price()
	{
		// $query = "select max(price) as maxPrice from product p where p.deleted != 1";

		// $result = array();
		// $result = DB::select(DB::raw($query));

		// return $result[0]->maxPrice;

		$max_price = DB::collection('products')
			->select(['id', 'name', 'price', 'img'])
			->max('price');
		return $max_price;
	}

	/**
	 * @param mixed $cateId
	 * @param mixed $start
	 * @param mixed $colors
	 * @param mixed $sizes
	 * @param mixed $sort
	 * @param mixed $price
	 * @param mixed $limit
	 * @return mixed
	 */
	public function product_filter($cateId, $start, $colors = array(), $sizes = array(), $sort, $price = array(), $limit)
	{
		if ($price[1] == -1) {
			$max_price = DB::collection('products')
				->select(['id', 'name', 'price', 'img'])
				->max('price');
			$price = [0, $max_price];
		}


		// select categories
		$categories = DB::collection('categories')
			->select(['id', 'name', 'price', 'img'])
			->where('parentsId', (int)$cateId)
			->get();

		$category_Ids = [(int)$cateId];
		foreach ($categories as $item) {
			$category_Ids[] =
				$item['id'];
		}



		#select variations khi lọc theo size
		$variation_Ids_by_size = [];
		if (count((array)$sizes) > 0) {
			$sizes = DB::collection('sizes')
				->whereIn('size', $sizes)
				->get(['variantId']);
			foreach ($sizes as $i) {
				$variation_Ids_by_size[] =
					$i['variantId'];
			}
		} else {
			$sizes = DB::collection('sizes')
				->get(['variantId']);
			foreach ($sizes as $i) {
				$variation_Ids_by_size[] =
					$i['variantId'];
			}
		}

		#select variations khi lọc theo màu
		$variation_Ids_by_color = [];
		if (count((array)$colors) > 0) {
			$variations = DB::collection('variations')
				->whereIn('colorId', $colors)
				->whereIn('id', $variation_Ids_by_size)
				->get(['productId']);
			foreach ($variations as $i) {
				$variation_Ids_by_color[] =
					$i['productId'];
			}
		} else {
			$variations = DB::collection('variations')
				->whereIn('id', $variation_Ids_by_size)
				->get();
			foreach ($variations as $i) {
				$variation_Ids_by_color[] =
					$i['productId'];
			}
		}

		$pds = DB::collection('products')
			->select(['id', 'name', 'price', 'img'])
			->whereIn('categoryId', $category_Ids)
			->where('deleted', 0)
			->whereBetween('price', $price)
			->whereIn('id', $variation_Ids_by_color)
			->get();

		$total = $pds->count();



		$products =
			DB::collection('products')
			->select(['id', 'name', 'price', 'img'])
			->whereIn('categoryId', $category_Ids)
			->whereIn('id', $variation_Ids_by_color)
			->where('deleted', 0)
			->whereBetween('price', $price)
			->orderByDesc('price')
			// ->limit($limit)
			->get();

		if ($sort == "asc") {
			$products =
				DB::collection('products')
				->select(['id', 'name', 'price', 'img'])
				->whereIn('categoryId', $category_Ids)
				->whereIn('id', $variation_Ids_by_color)
				->where('deleted', 0)
				->whereBetween('price', $price)
				->orderBy('price')
				// ->limit($limit)
				->get();
		}

		$result = [];
		foreach ($products as $item) {

			#select total color
			$distinctColorCount = DB::collection('variations')
				->where('productId', (int)$item['id'])
				->distinct('colorId')
				->count();

			#select quantity
			#select variations
			$variations = DB::collection('variations')
				->where('productId', (int)$item['id'])
				->get(['id']);
			$variation_Ids = [];
			foreach ($variations as $i) {
				$variation_Ids[] =
					$i['id'];
			}

			#select sizes and count quantity
			$qty = DB::collection('sizes')
				->whereIn('variantId', $variation_Ids)
				->sum('quantity');



			$result[] = [
				'id' => $item['id'],
				'name' => $item['name'],
				'price' => $item['price'],
				'img_url' => $item['img'],
				'discount' => 0,
				'salePrice' => 0,
				'color' => $distinctColorCount,
				'qty' => $qty,


			];
		}


		return array(
			'total' => $total,
			'products' => $result
		);
	}

	public function get_product($productId)
	{

		$products =
			DB::collection('products')
			->select(['id', 'name', 'price', 'img', 'categoryId'])
			->where('deleted', 0)
			->where('id', (int)$productId)
			->get();

		$result = [];
		foreach ($products as $item) {
			# select variation$products =
			$variations = DB::collection('variations')
				->select(['id', 'thumbnail', 'colorId'])
				->where('productId', (int)$item['id'])
				->get();

			$variation_Ids = [];
			foreach ($variations as $i) {
				$variation_Ids[] =
					$i['id'];
			}
			$qty = DB::collection('sizes')
				->whereIn('variantId', $variation_Ids)
				->sum('quantity');

			$variants = [];
			foreach ($variations as $v) {
				# get color name
				$color = DB::collection('colors')
					->select(['id', 'name'])
					->where('id', (int)$v['colorId'])
					->get();

				# select sizes
				$sizes = DB::collection('sizes')
					->select(['id', 'size', 'quantity'])
					->where('variantId', (int)$v['id'])
					->get();

				$sizes_arr = [];
				foreach ($sizes as $i) {
					$sizes_arr[] = [
						'size' => $i['size'],
						'quantity' => $i['quantity']

					];
				}

				# select images
				$images = DB::collection('images')
					->select(['id', 'url'])
					->where('variantId', (int)$v['id'])
					->get();

				$images_arr = [];
				foreach ($images as $i) {
					$images_arr[] = [
						'id' => $i['id'],
						'url' => $i['url']

					];
				}

				$variants[] = [
					'id' => $v['id'],
					'thumbnail' => $v['thumbnail'],
					'name' => $color[0]['name'],
					'qty' => $qty,
					'sizes' => $sizes_arr,
					'images' => $images_arr
				];
			}


			$result[] = [
				'id' => $item['id'],
				'name' => $item['name'],
				'price' => $item['price'],
				'img_url' => $item['img'],
				'categoryId' => $item['categoryId'],
				'discount' => 0,
				'salePrice' => 0,
				'description' => null,
				'variants' => $variants,


			];
		}
		return $result[0];
	}

	public function get_weekly_best_product($limit, $cateId)
	{
		// select categories
		$categories = DB::collection('categories')
			->select(['id', 'name', 'price', 'img'])
			// ->where('id', (int)$cateId)
			->where('parentsId', (int)$cateId)
			->get();

		$category_Ids = [(int)$cateId];
		foreach ($categories as $item) {
			$category_Ids[] =
				$item['id'];
		}

		$products = DB::collection('products')
			->select(['id', 'name', 'price', 'img'])
			->whereIn('categoryId', $category_Ids)
			->where('products.deleted', '!=', 1)
			->limit($limit)
			->get();

		$result = [];
		foreach ($products as $item) {

			#select total color
			$distinctColorCount = DB::collection('variations')
				->where('productId', (int)$item['id'])
				->distinct('colorId')
				->count();

			#select quantity
			#select variations
			$variations = DB::collection('variations')
				->where('productId', (int)$item['id'])
				->get(['id']);
			$variation_Ids = [];
			foreach ($variations as $i) {
				$variation_Ids[] =
					$i['id'];
			}

			#select sizes and count quantity
			$qty = DB::collection('sizes')
				->whereIn('variantId', $variation_Ids)
				->sum('quantity');



			$result[] = [
				'id' => $item['id'],
				'name' => $item['name'],
				'price' => $item['price'],
				'img_url' => $item['img'],
				'discount' => 0,
				'salePrice' => 0,
				'color' => $distinctColorCount,
				'qty' => $qty

			];
		}

		return $result;
	}

	public function get_new_product($limit, $cateId)
	{
		// select categories
		$categories = DB::collection('categories')
			->select(['id', 'name', 'price', 'img'])
			// ->where('id', (int)$cateId)
			->where('parentsId', (int)$cateId)
			->get();

		$category_Ids = [(int)$cateId];
		foreach ($categories as $item) {
			$category_Ids[] =
				$item['id'];
		}

		$products = DB::collection('products')
			->select(['id', 'name', 'price', 'img'])
			->whereIn('categoryId', $category_Ids)
			->where('products.deleted', '!=', 1)
			->orderByDesc('id')
			->limit($limit)
			->get();

		$result = [];
		foreach ($products as $item) {

			#select total color
			$distinctColorCount = DB::collection('variations')
				->where('productId', (int)$item['id'])
				->distinct('colorId')
				->count();

			#select quantity
			#select variations
			$variations = DB::collection('variations')
				->where('productId', (int)$item['id'])
				->get(['id']);
			$variation_Ids = [];
			foreach ($variations as $i) {
				$variation_Ids[] =
					$i['id'];
			}

			#select sizes and count quantity
			$qty = DB::collection('sizes')
				->whereIn('variantId', $variation_Ids)
				->sum('quantity');



			$result[] = [
				'id' => $item['id'],
				'name' => $item['name'],
				'price' => $item['price'],
				'img_url' => $item['img'],
				'discount' => 0,
				'salePrice' => 0,
				'color' => $distinctColorCount,
				'qty' => $qty

			];
		}

		return $result;
	}
	/**
	 * @param mixed $searchStr
	 * @param mixed $limit
	 * @return mixed
	 */
	public function search_products($searchStr, $limit)
	{
		$query = "select p.id, p.name, price, sum(s.quantity) as qty, p.img as img_url, vc.color, IFNULL(discount, 0) AS discount, ROUND(IFNULL((100 - discount) * (price / 100), 0),0) AS salePrice"
			. " FROM product p JOIN variation v ON p.id = v.productId JOIN size s on v.id = s.variantId LEFT JOIN"
			. " (select vr.productId, COUNT(vr.id) as color from variation vr group by vr.productId) as vc ON vc.productId = p.id LEFT JOIN"
			. " productsales ps ON p.id = ps.productid LEFT JOIN"
			. " salespromotion sp ON ps.salesid = sp.id"
			. " and CURRENT_TIMESTAMP() BETWEEN sp.timeStart AND sp.timeEnd"
			. " WHERE p.deleted != 1 AND p.name like '%" . $searchStr . "%'"
			. " GROUP BY p.id , name , price , discount , salePrice"
			. " ORDER BY -p.id";

		if ((string)$searchStr == "") {
			$query .= " LIMIT "  . (string)$limit;
		}

		return DB::select(DB::raw($query));
	}



	public function get_productsCollection($collectionId, $start)
	{
		if ($start != -1) {
			$strLimit = "LIMIT " . $start . ", 8";
		} else {
			$strLimit = "";
		}

		$query = "select  p.id, p.name, price, sum(s.quantity) as qty, p.img as img_url, vc.color, IFNULL(discount, 0) AS discount,ROUND(IFNULL((100 - discount) * (price / 100), 0),0) AS salePrice"
			. " FROM productcollection pc JOIN product p on pc.productId = p.id"
			. " JOIN variation v ON p.id = v.productId"
			. " JOIN size s on v.id = s.variantId"
			. " LEFT JOIN"
			. " (select vr.productId, COUNT(vr.id) as color from variation vr group by vr.productId) as vc"
			. " ON vc.productId = p.id"
			. " LEFT JOIN productsales ps ON p.id = ps.productid"
			. " LEFT JOIN salespromotion sp ON ps.salesid = sp.id"
			. " and CURRENT_TIMESTAMP() BETWEEN sp.timeStart AND sp.timeEnd"
			. " WHERE p.deleted != 1 AND pc.collectionId = " . $collectionId
			. " GROUP BY p.id , name , price , discount , salePrice"
			. " ORDER BY -p.id " . $strLimit;

		return (array)DB::select(DB::raw($query));
	}

	public function productsSale($salesId, $size, $cateId, $start)
	{
		if ($start != -1) {
			$strLimit = " LIMIT " . $start . ", 8";
		} else {
			$strLimit = "";
		}

		if ($size != -1) {
			$strSize = " and size = " . $size;
		} else {
			$strSize = "";
		}

		if ($cateId != -1) {
			$strCate = "JOIN category cate ON p.categoryId = cate.id
			AND (cate.parentsId = " . $cateId . " OR p.categoryId = " . $cateId . ")";
		} else {
			$strCate = "";
		}

		$query = "select p.id, p.name, price, sum(s.quantity) as qty, p.img as img_url, vc.color,"
			. " IFNULL(discount, 0) AS discount,"
			. " IFNULL((100 - discount) * (price / 100), 0) AS salePrice"
			. " FROM productsales ps JOIN"
			. " product p on ps.productId = p.id"
			. " JOIN variation v ON p.id = v.productId"
			. " JOIN size s on v.id = s.variantId " . $strSize
			. " LEFT JOIN (select vr.productId, COUNT(vr.id) as color from variation vr group by vr.productId) as vc"
			. " ON vc.productId = p.id"
			. " LEFT JOIN salespromotion sp ON ps.salesid = sp.id"
			. " and CURRENT_TIMESTAMP() BETWEEN sp.timeStart AND sp.timeEnd " . $strCate . " WHERE p.deleted != 1 AND ps.salesid = " . $salesId
			. " GROUP BY p.id , name , price , discount , salePrice"
			. " ORDER BY -p.id " . $strLimit;

		return (array) DB::select(DB::raw($query));
	}

	public function get_productsSale($salesId, $size, $cateId, $start)
	{
		$result = array();

		$result["products"] = $this->productsSale($salesId, $size, $cateId, $start);
		$result["total"] = count($this->productsSale($salesId, $size, $cateId, -1));

		return $result;
	}
}
