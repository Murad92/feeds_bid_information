<?php

namespace App\Http\Controllers;

use ClickHouseDB;

class GetInfoFromCHController extends Controller
{
    public function __construct()
    {
        $this->config = [
            'host'     => env('CLICK_HOUSE_HOST'),
            'port'     => env('CLICK_HOUSE_PORT'),
            'username' => env('CLICK_HOUSE_USERNAME'),
            'password' => env('CLICK_HOUSE_PASSWORD'),
        ];
        $this->connection = new ClickHouseDB\Client($this->config);
        $this->connection->database('visitstats');
    }



    public  function getData($field = '*', $date, $country)
    {
        $sql = "
        SELECT
           {$field}
        FROM
          visitstats.push_notifications
        WHERE
          stats_day = '{$date}' AND country = '{$country}'
        LIMIT 1000";

        return $this->connection->select($sql)->rows();
    }

    public  function getDataForeCPM($options)
    {

        $sql = "SELECT ";
        foreach ($options['feeds'] as $campagneId => $feedName) {
            $sql .= " sumIf(price, (event_type = 'click') AND (campaign_id = {$campagneId}) AND (price < 2)) AS revenue_{$feedName},
                         countIf(event_type, (event_type= 'show') AND (campaign_id = {$campagneId}) AND (price < 2)) AS show_{$feedName},
                         countIf(event_type, (event_type= 'click') AND (campaign_id = {$campagneId}) AND (price < 2)) AS click_{$feedName},
                         ";
        }
        $sql .= " substring(toString(event_time), 1, 10) AS x
          FROM push_notifications
          WHERE toDate(event_time) >= '{$options['startDate']}' AND toDate(event_time) < '{$options['endDate']}'";

        $targeting = [];

        if($options['osType']) {
            $osType = join("','",$options['osType']);
            $osType = str_replace('undefined','',$osType);
            $targeting[] = " os_type IN ('$osType') ";
        }
        if($options['country']) {
                $country = join("','",$options['country']);
                $targeting[] = " country IN ('$country') ";
            }
            if(!empty($targeting)) {
                $sql .= ' AND '.join('AND',$targeting);
            }
          $sql.= "GROUP BY x
          ORDER BY x DESC";

        return $this->connection->select($sql)->rows();
    }
}
