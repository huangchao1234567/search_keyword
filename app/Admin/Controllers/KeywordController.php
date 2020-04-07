<?php

namespace App\Admin\Controllers;

use App\Models\Website;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\ModelForm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KeywordController extends Controller
{
    use ModelForm;


    public function index(Request $request)
    {
        $param = array(
            'id'  => $request->get('id'),
            'num' => $request->get('id', 10)
        );
        return Admin::content(function (Content $content) use ($param) {
            $content->header('网址列表');
            $content->body($this->grid($param));
        });

    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('添加网址');
            $content->body($this->form());
        });
    }


    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {

        return Admin::content(function (Content $content) use ($id) {

            $content->header('网址列表');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Website::class, function (Form $form) {
            $form->text('website_string', '网址');
            $form->disableReset();
            $states = [
                'on'  => ['value' => 1, 'text' => '不需要', 'color' => 'danger'],
                'off' => ['value' => 2, 'text' => '需要', 'color' => 'success'],
            ];
            $form->switch('type', '状态')->states($states)->default(1);
            $form->saving(function ($form) {
               // $form->content_txt = strip_tags($form->content);
            });
        });

    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($param)
    {

        //修改数据来源
        $grid = new Grid(new Website());
        $grid->model()->orderBy('created_at', 'desc');

        $grid->website_string('网址');
        $grid->created_at('创建时间');

        // 设置text、color、和存储值
        $states = [
            'on'  => ['value' => 1, 'text' => '不需要', 'color' => 'danger'],
            'off' => ['value' => 2, 'text' => '需要', 'color' => 'success'],
        ];
        $grid->type('状态')->switch($states);

        $grid->file('file_column');

        //禁用导出数据按钮
        $grid->disableExport();


        //禁用行选择checkbox
        $grid->disableRowSelector();
      //  $grid->quickSearch('title');
        return $grid;

    }
}