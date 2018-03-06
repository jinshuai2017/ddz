<?php
namespace app\index\controller;
use think\Controller;
use ddz\PlayGame;
class Index extends Controller
{
    public function index()
    {

        return view('index');
    }

    public function fapai(){
//        header("Content-type:text/html;charset=utf-8");
        setcookie('player1',null);
        setcookie('player2',null);
        setcookie('player3',null);
        setcookie('dp',null);
        $p1='1';//桌号
        $p2='2';
        $p3='3';
        $PlayGame=new PlayGame();
        $fapai=$PlayGame->fapai($p1,$p2,$p3);
//        $status="yes";
//        $nostatus="no";
//        $key=$fapai['jp'];
//        $dz=$PlayGame->qdz($key,$status);
//
//        $dz_brand= array_merge( $fapai['player'][$dz],$fapai['dp']);//合并地主底牌

        $zhuohao=array(
            $p1,$p2,$p3
        );
//        $key1 = array_search($dz,$zhuohao);
//        if ($key1 !== false)
//            array_splice($zhuohao, $key1, 1);

        foreach($fapai['dp'] as $key=>$val){
            $arr=explode('_',$val);
            $dp[$key]['pic']=$arr[1];
            $dp[$key]['pai']=$arr[0];
        }
        foreach($fapai['player'][$zhuohao[0]] as $key=>$val){
            $arr=explode('_',$val);
            $player1[$key]['pic']=$arr[1];
            $player1[$key]['pai']=$arr[0];
        }
        foreach($fapai['player'][$zhuohao[1]] as $key=>$val){
            $arr=explode('_',$val);
            $player2[$key]['pic']=$arr[1];
            $player2[$key]['pai']=$arr[0];
        }
        foreach($fapai['player'][$zhuohao[2]] as $key=>$val){
            $arr=explode('_',$val);
            $player3[$key]['pic']=$arr[1];
            $player3[$key]['pai']=$arr[0];
        }
        $data['dp']=$dp;
        $jiaopaikey=rand(0,2);//生成地主key
        $data['jiaopai']=$zhuohao[$jiaopaikey];//地主的桌号
        $data['player1']=$player1;
        $data['player2']=$player2;
        $data['player3']=$player3;

        setcookie('player1',serialize($player1));
        setcookie('player2',serialize($player2));
        setcookie('player3',serialize($player3));
        setcookie('dp',serialize($dp));
        setcookie('qdz_weight1',null);
        setcookie('qdz_weight2',null);
        setcookie('qdz_weight3',null);
        $res=json_encode($data);
        return $res;
    }


    public function jiaopai(){
        $status=$_GET['status'];//yes :叫地主，no：不叫地主
        $zhuohao=$_GET['zh'];//操作的桌号
        $qdz_weight=0;
        //桌号轮回
        if($zhuohao==3){
            $res['nextzh']=1;
        }else{
            $res['nextzh']=$zhuohao+1;
        }
        $ckey='qdz_weight'.$zhuohao;
        $all_zh=array('1','2','3');
        $key1 = array_search($zhuohao,$all_zh);
        if ($key1 !== false)
            array_splice($all_zh, $key1, 1);
        $lwp1_weight_key='qdz_weight'.$all_zh[0];
        $lwp2_weight_key='qdz_weight'.$all_zh[1];
        if($status=='yes'){
//            echo $_COOKIE[$ckey];
            if(!empty($_COOKIE[$ckey])){
                $qdz_weight=$_COOKIE[$ckey]+1;
                if($qdz_weight>=2){
                    $res['zhohao']=$zhuohao;
                    $res['nextzh']=0;//代表桌号轮回结束
                    $res['status']='success';//抢地主成功
                    $res['dz_brand']=self::hbdp_brand($zhuohao);
                    $res=json_encode($res);
                    return $res;
                }
            }else{
                $qdz_weight=1;
                $res['zhohao']=$zhuohao;
                $res['status']='undetermined';//抢地主待定
            }
            setcookie($ckey,$qdz_weight);
            $res=json_encode($res);
            return $res;
        }else{
            //这里默认前2个不抢地主就是最后一个人的地主
            $res['status']='fail';//抢地主失败
            setcookie($ckey,-1);//失败存-1
            if(!empty($_COOKIE[$lwp1_weight_key])){
                if($_COOKIE[$lwp1_weight_key]==-1){
                    $res['zhohao']=$all_zh[1];
                    $res['nextzh']=0;//代表桌号轮回结束
                    $res['status']='success';
                    $res['dz_brand']=self::hbdp_brand($zhuohao);
                }
            }
            if(!empty($_COOKIE[$lwp2_weight_key])){
                if($_COOKIE[$lwp2_weight_key]==-1){
                    $res['zhohao']=$all_zh[0];
                    $res['nextzh']=0;//代表桌号轮回结束
                    $res['status']='success';
                    $res['dz_brand']=self::hbdp_brand($zhuohao);
                }
            }
            $res=json_encode($res);
            return $res;
        }

    }

    public function hbdp_brand($zhuohao){//合并底牌
      $key='player'.$zhuohao;
      if(!empty($_COOKIE[$key]) && !empty($_COOKIE['dp'])){
          $dz_brand= array_merge( unserialize($_COOKIE[$key]),unserialize($_COOKIE['dp']));//合并地主底牌
      }
        setcookie('dz_brand',serialize($dz_brand));
        return $dz_brand;
    }
}
