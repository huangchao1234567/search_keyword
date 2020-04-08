<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Server\Keyword;
use Illuminate\Http\Request;

class BatchAddController extends Controller
{
    public $keyword;

    public function __construct()
    {
        $this->keyword = new Keyword();
    }

    public function index()
    {
        return 'guaosi';
    }

    /**导入excel批量添加搜索关键字
     * @param Request $request
     * @return array
     */
    public function batchAdd(Request $request)
    {
        $param     = array(
            'id' => $request->get('id'),
        );
        $file      = $_FILES;
        try {
            $add = $this->keyword->batchAdd($file['file']);
           // $this->keyword->keywordRun();
        } catch (\Exception $e) {
            return [
                'code' => 101,
                'msg'  => $e->getMessage()
            ];
        }

        return ['code' => $add ? 100 : 101,];
    }

    public function listData(Request $request)
    {
        $param          = array(
            'id'  => $request->get('id'),
            'num' => $request->get('id', 10)
        );
        $list           = $this->keyword->listData($param);
        $param['total'] = $list['total'];
        return [
            'param' => $param,
            'data'  => $list['data']
        ];

    }

}