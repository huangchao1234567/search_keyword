<?php

namespace App\Server;

use App\Models\Title;
use Illuminate\Filesystem\Cache;
use QL\QueryList;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class Keyword
{

    public function sogouGather($key)
    {
        $url = 'https://www.sogou.com/sogou?query=' . $key . '&&insite=wenwen.sogou.com';;
        $this->listGather($url);

        $this->getDetails();

    }

    /**列表采集
     * @return bool
     */
    public function listGather($url)
    {
        if (empty($url)) {
            return false;
        }
        // 采集规则
        $rules = [
            'title' => ['.vrTitle>a', 'text'],
            'url'   => ['.vrTitle>a', 'href'],
        ];

        $data = QueryList::Query($url, $rules)->data;

        if ($data) {
            foreach ($data as $val) {
                $add_array [] = [
                    'title_string' => $val['title'],
                    'url'          => $val['url'],
                    'created_at'   => Carbon::now()
                ];
            }

            return Title::insert($add_array);
        }
        return true;
    }

    public function getDetails()
    {
        $url   = 'https://www.sogou.com';
        $title = Title::take(2)->get()->toArray();
        foreach ($title as $value) {
            $add = $this->detailsGather($url . $value['url'], $value['id']);
            Title::where('id', $value['id'])->update([
                'type'       => $add === false ? 2 : 1,
                'updated_at' => Carbon::now()
            ]);
        }

    }

    /**
     * 详情采集
     */
    public function detailsGather($url, $id)
    {

        $client   = new Client(['base_uri' => $url, 'verify' => false]);
        $response = $client->request('GET');
        /*   var_export($response->getStatusCode());
           var_export($response->getBody()->getContents());*/
        $contents = $response->getBody()->getContents();
        preg_match_all('/window\.location\.replace\("([^"]*?)"\)/', $contents, $url_array);
        $url = '';
        if (count($url_array) == 2) {
            $url = $url_array[1][0];
        }
        if (empty($url)) {
            return false;
        }


        // 采集规则
        $rules = [
            'details' => ['pre', 'html'],
        ];

        $data = QueryList::Query($url, $rules)->data;
        if ($data) {
            foreach ($data as $val) {
                $add[] = [
                    'title_id' => $id,
                    'details'  => $val['details'],
                    'url'      => $url
                ];
            }
            return DB::table('details')->insert($add);
        }
        return false;
    }
}