<?php

namespace App;

use App\Http\Controllers\GetInfoFromCHController;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{

    private $responce = [];

    public function insertData($name, $date)
    {
        $allCountryes = config('app.country', []);
        foreach ($allCountryes as $code => $value) {
            echo $name . ' ' . date('Y-m-d H:i:s') . PHP_EOL;
            $response = 0;
            $chModel = new GetInfoFromCHController();
            $model = Feed::where('name', $name)->get()->toArray()[0];
            $dataFromCH = $chModel->getData($model['fields'], $date, $code);
            if (empty($dataFromCH)) {
                echo 'NOT FOUND Data IN '.$value.' ('.$code.')'.PHP_EOL;
                continue;
            }

            $request = count($dataFromCH);
            echo 'Get Data FROM '.$value.' ('.$code.') COUNT: '.$request.PHP_EOL;
            FeedStat::where('date', $date)->where('feed_name', $name)->where('country',$code)->delete();
            $params = unserialize($model['const_params']);
            $field = unserialize($model['params']);
            $puID = 1;
            $bidFields = explode(',', $model['bid_fields']);
            foreach ($dataFromCH as $data) {
                $data['ua'] = config('app.ua', [])[rand(0, 1623)];
                foreach ($field as $paramsKey => $dataKey) {
                    $params[$paramsKey] = $puID++;
                    if ($dataKey !== '') {
                        $params[$paramsKey] = $data[$dataKey];
                    }
                }

                if ($source = @file_get_contents($model['url_path'] . http_build_query($params))) {
                    if ($name === 'AdsCompass') {
                        $source = str_replace("<![CDATA[", "", $source);
                        $source = str_replace("]]>", "", $source);
                    }

                    if ($model['type'] === 'json') {
                        $bid = json_decode($source, true);
                    } else {
                        $bid = $this->xml2array(@simplexml_load_string($source));
                        if (is_array($bid) && isset($bid[$bidFields[0]][0]) && is_object($bid[$bidFields[0]][0])) {
                            $bid[$bidFields[0]] = $this->xml2array($bid[$bidFields[0]][0]);
                        }
                    }

                    $bid = $this->check($bid, $bidFields);
                    if (!is_array($bid)) {
                        $response++;
                        if (is_null($bid)) {
                            $bid = 0;
                        }
                        $this->responce[] = [
                            'bid' => round($bid, 8),
                            'country' => $data['country'],
                            'feed_name' => $model['name'],
                            'date' => $date,
                        ];
                    }

                    if (count($this->responce) >= 100) {
                        FeedStat::insert($this->responce);
                        $this->responce = [];
                    }
                }
            }

            Coverage::insert([
                'name' => $name,
                'date' => $date,
                'response' => $response,
                'request' => $request,
                'country' => $code,
            ]);

            if (count($this->responce) > 0) {
                FeedStat::insert($this->responce);
                $this->responce = [];
            }
            echo $name . ' ' . date('Y-m-d H:i:s') . PHP_EOL;
        }
    }

    private function xml2array($xmlObject, $out = [])
    {
        if(!is_object($xmlObject)) {
            return false;
        }
        foreach ((array)$xmlObject as $index => $node) {
            $out[$index] = (is_object($node)) ? $this->xml2array($node) : $node;
        }

        return $out;
    }

    private function check($bid,$bidFields)
    {
        $newFields = $bidFields;
        foreach ($bidFields as $bidField) {
            if(isset($bid[$bidField])) {
                $bid = $bid[$bidField];
                array_shift($newFields);
                if(count($newFields) > 0) {
                    $this->check($bid,$newFields);
                }
            }
        }
        return $bid;
    }
}
