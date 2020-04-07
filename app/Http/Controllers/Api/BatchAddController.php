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

    public function batchAdd(Request $request)
    {
        $param     = array(
            'id' => $request->get('id'),
        );
        $file      = $_FILES;
        $file_data = $file['file'];
        dd($param, $file_data);
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
            'data'   => $list['data']
        ];

    }

}