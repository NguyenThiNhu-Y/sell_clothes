<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SalesPromotionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class SalesPromotionRepository implements SalesPromotionRepositoryInterface
{

	/**
	 * @param mixed $salesId
	 * @param mixed $start
	 * @return mixed
	 */
	public function get_sales($salesId, $start) {
        $query = "select id, name, mobileBanner, pcBanner"
        ." FROM salespromotion"
        ." WHERE visible = 1 AND CURRENT_TIMESTAMP() BETWEEN timeStart AND timeEnd"
        ." and id = ".$salesId;

        $result = DB::select(DB::raw($query));

        return $result;
	}

    public function get_sale(){
        // $query = "select id, name"
        // ." FROM salespromotion"
        // ." WHERE visible = 1 AND CURRENT_TIMESTAMP() BETWEEN timeStart AND timeEnd";

        // return (array) DB::select(DB::raw($query));

        // Kết nối tới MongoDB
        $collection = DB::connection('mongodb')->collection('salespromotions');

        // Lấy ngày giờ hiện tại
        $currentTimestamp = Carbon::now();

        // Thực hiện truy vấn MongoDB
        $salesPromotions = $collection->where('visible', '=', 1)
            ->where('timeStart', '<=', $currentTimestamp)
            ->where('timeEnd', '>=', $currentTimestamp)
            ->get(['_id', 'name']);

        // Chuyển kết quả về mảng
        $salesPromotionsArray = $salesPromotions->toArray();

        return $salesPromotionsArray;
    }

    public function get_sale_banner(){
        // $query = "select id, name, mobileBanner, pcBanner, 'sale' AS type"
        // ." FROM salespromotion"
        // ." WHERE visible = 1 AND CURRENT_TIMESTAMP() BETWEEN timeStart AND timeEnd";

        // return (array) DB::select(DB::raw($query));
        // Kết nối tới MongoDB
        $collection = DB::connection('mongodb')->collection('salespromotions');

        // Lấy ngày giờ hiện tại
        $currentTimestamp = Carbon::now();

        // Thực hiện truy vấn MongoDB
        $salesPromotions = $collection->where('visible', '=', 1)
            ->where('timeStart', '<=', $currentTimestamp)
            ->where('timeEnd', '>=', $currentTimestamp)
            ->get(['_id', 'name', 'mobileBanner', 'pcBanner', 'type' => 'sale']);

        // Chuyển kết quả về mảng
        $salesPromotionsArray = $salesPromotions->toArray();

        return $salesPromotionsArray;
    }
}