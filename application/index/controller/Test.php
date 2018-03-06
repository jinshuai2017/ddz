<?php
namespace app\index\controller;
use http\Client;


/**
 * Created by PhpStorm.
 * User: js
 * Date: 2018/2/8
 * Time: 11:46
 */
class Test{
    public function test(){
        $url="https://www.ithome.io/";
        $c=new Client();
        $content =$c->get($url);
        echo $content;
//        return view();
    }
}
