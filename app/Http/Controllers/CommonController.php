<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommonController extends Controller
{
    /*时间线API*/
    public function timeline()
    {
        list($limit,$skip) = paginate(rq('page'),rq('limit'));
        //dd($limit);  下面的get()方法取得的是一个collection

        /*获取问题数据*/
        $questions = ques_ins()->limit($limit)
                               ->with('user')
                               ->skip($skip)
                               ->orderBy('created_at','desc')
                               ->get();
        /*获取回答数据*/
        $answers = answer_ins()->limit($limit)
                               ->with('users')   //这个是谁投票的
                               ->with('user')   //这个是谁回答的
                               ->skip($skip)
                               ->orderBy('created_at','desc')
                               ->get();

        /*合并数据*/
        $data = $questions->merge($answers);
        /*将合并的数据按时间排序*/
        $data = $data->sortByDesc(function($item)
        {
            return $item->created_at;
        });

        $data = $data->values()->all();
        return ['status'=>1,'data'=>$data];
    }

}
