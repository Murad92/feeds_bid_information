<?php

namespace App\Http\Controllers;

use App\Feed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class StatisticController extends Controller
{
    const DATA_TYPE = ['total' => 'average_bid','max' => 'max_bid', 'min' => 'min_bid'];
    const IMPRESSION_LIMIT = 10000;

    public function data(Request $request)
    {
        $countries = $request->input('countries');
        $startDate = date('Y-m-d',strtotime("-1 weeks"));
        $endDate = date('Y-m-d');

        if($request->input('start_date')) {
            $startDate = $request->input('start_date');
        };

        if($request->input('end_date')) {
            $endDate = $request->input('end_date');
        };

        $data = [];
        $orders = DB::table('feeds_statistic')
            ->select('feed_name','country',
                DB::raw('AVG(bid) as total'),
                DB::raw('MAX(bid) as max'),
                DB::raw('MIN(bid) as min')
            );
        $coverage = DB::table('coverages')
            ->select('name','country',
                DB::raw('SUM(response) as resp'),
                DB::raw('SUM(request) as req')
            );

        if($countries) {
            $orders = $orders->whereIn('country',$countries);
            $coverage = $coverage->whereIn('country',$countries);
        }

        $orders = $orders->whereBetween('date', [$startDate, $endDate])
            ->groupBy('feed_name')
            ->groupBy('country')
            ->orderBy('country')
            ->get()
            ->toArray();

        foreach ($orders as $order) {
            $data[$order->country][$order->feed_name] =
                [
                    'total' => $order->total,
                    'max'   => $order->max,
                    'min'   => $order->min,
                ];
        }

        $coverage = $coverage->whereBetween('date',[$startDate, $endDate])
            ->groupBy('name')
            ->groupBy('country')
            ->orderBy('country')
            ->get()
            ->toArray();

        $groupByCountry = [];
        foreach($coverage as $item) {
            $groupByCountry[$item->country][$item->name] = [
                'response' => $item->resp,
                'request' => $item->req,
            ];
        }

        $feeds = Feed::pluck('name')->toArray();
        $country = config('app.country', []);

        return View::make("stat/statistics",[
            'country' => $country,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'countries' => $countries,
            'data' => $data,
            'dataType' => self::DATA_TYPE,
            'coverage' => $groupByCountry,
            'feeds' => $feeds,
        ]);
    }

    public function ecpm(Request $request)
    {
        $startDate = date('Y-m-d',strtotime("-2 weeks"));
        $endDate = date('Y-m-d');
        $limit = self::IMPRESSION_LIMIT;
        $countries = $request->input('countries');
        $osType = $request->input('os_types');

        if($request->input('start_date')) {
            $startDate = $request->input('start_date');
        };

        if($request->input('start_date')) {
            $startDate = $request->input('start_date');
        };

        if($request->input('impression_limit')) {
            $limit = $request->input('impression_limit');
        };

        $feeds = config('app.feeds', []);
        $options = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'feeds' => $feeds,
            'osType' => $osType,
            'country' => $countries,
        ];
        $chModel = new GetInfoFromCHController();
        $data = $chModel->getDataForeCPM($options);
        $country = config('app.country', []);
        $osTypes = config('app.osType', []);

        return View::make("stat/ecpm",[
            'feeds' => array_values($feeds),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'country' => $country,
            'osTypes' => $osTypes,
            'osType' => $osType,
            'countries' => $countries,
            'impressionLimit' => config('app.impressionLimits',[self::IMPRESSION_LIMIT]),
            'limit' => $limit,
            'data' => $data,
        ]);
    }
}
