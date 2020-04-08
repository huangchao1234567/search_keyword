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

    const list_length = 5;//列表根据关键字搜索出来取值的条数
    protected $excel_name = [
        'A' => 'keyword',//关键字
    ];

    public function keywordRun($bool = true, $min = 0, $max = 100000)
    {
        if ($bool) {
            $model = DB::table('keyword')->where('type', '<>', 1);
        } else {
            $model = DB::table('keyword')->where('type', '<>', 1)
                ->where('id', '>=', $min)->where('id', '<=', $max);
        }
        $number = 50;
        while ($model->first()) {
            $data   = $model->take($number)->get();
            $id_all = [];
            foreach ($data as $cal) {
                $id_all[] = $cal->id;
                $this->sogouGather($cal->keyword, $cal->id);
            }
            if ($id_all) {
                DB::table('keyword')->whereIn('id', $id_all)->update(['type' => 1]);
            }
        }
    }


    public function sogouGather($key, $id)
    {
        $url = 'https://www.sogou.com/sogou?query=' . urlencode($key) . '&ie=utf8&insite=wenwen.sogou.com';
        $this->listGather($url, $id);
    }

    /**列表采集
     * @return bool
     */
    public function listGather($url, $id)
    {
        if (empty($url)) {
            return false;
        }
        // 采集规则
        $rules = [
            'title' => ['.vrwrap>h3>a', 'text'],
            'url'   => ['.vrwrap>h3>a', 'href'],
        ];

        $data = QueryList::Query($url, $rules)->data;


        if ($data && count($data) >= self::list_length) {
            $arr = array_slice($data, 0, self::list_length);
            foreach ($arr as $val) {
                $details_data = $this->detailsGather('https://www.sogou.com' . $val['url']);
                if ($details_data === false) {
                    continue;
                }
                $add_array [] = [
                    'title_string' => $val['title'],
                    'url'          => $val['url'],
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                    'keyword_id'   => $id,
                    'details_data' => $details_data['details_data'],
                    'details_url'  => $details_data['details_url']
                ];
            }

            return Title::insert($add_array);
        }
        return true;
    }


    /**
     * 详情采集
     */
    public function detailsGather($url)
    {
        $client   = new Client(['base_uri' => $url, 'verify' => false]);
        $response = $client->request('GET');
        /*   var_export($response->getStatusCode());
           var_export($response->getBody()->getContents());*/
        $contents = $response->getBody()->getContents();
        preg_match_all('/window\.location\.replace\("([^"]*?)"\)/', $contents, $url_array);

        $url = count($url_array) == 2 ? $url_array[1][0] : '';
        if (empty($url)) {
            return false;
        }
        // 采集规则
        $rules = [
            'details' => ['pre', 'html'],
        ];
        $data  = QueryList::Query($url, $rules)->data;
        if (empty($data)) {
            return false;
        }
        return [
            'details_data' => $data[0]['details'],
            'details_url'  => $url
        ];
    }

    public function listData($where)
    {
        return DB::table('website')->paginate(array_get($where, 'num', 10))->toArray();
    }

    public function batchAdd($file)
    {
        $excel_array = $this->getExcelData($file);
        return DB::table('keyword')->insert($excel_array);

    }

    public function getExcelData($file, $webstr_id = 0)
    {
        $extension = pathinfo($file['name'])['extension'];
        $time      = Carbon::now();
        if ($extension == 'xlsx') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } else if ($extension == 'xls') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        } else {
            throw new \Exception("文件格式错误");
        }

        $spreadsheet = $reader->load($file['tmp_name']); // 载入excel文件
        $sheet       = $spreadsheet->getActiveSheet();

        $highestRow    = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        $excel_name    = $this->excel_name;
        $array         = [];
        for ($i = 1; $i <= $highestRow; $i++) {
            // 遍历每行的单元格
            $da_line = [];
            for ($k = 'A'; $k <= $highestColumn; $k++) {
                $key = array_get($excel_name, $k);
                if ($key) {
                    $val                   = $sheet->getCell("$k$i")->getValue();
                    $da_line[$key]         = empty($val) ? '' : $val;
                    $da_line['created_at'] = $time;
                    $da_line['updated_at'] = $time;
                    $da_line['webstr_id']  = $webstr_id;
                }
            }
            $array[] = $da_line;
        }
        return $array;
    }
}