<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/12/16
 * Time: 11:53 AM
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GlobalClass extends Controller
{
    const API = 'http://openapi.zjs.jili.com/crm/get';

    static public function dataMerge($mainResult = array(),$sideResult = array()){
        $callback = function($mainResult_e,$sideResult_e){
            if(is_array($mainResult_e) && is_array($sideResult_e)){
                return array_merge($mainResult_e,$sideResult_e);
            }else{
                die('elements of either mainResult or sideResult is not an array');
            }
        };
        return array_map($callback,$mainResult,$sideResult);
    }
    
    static public function curl($url,$post_data){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    static public function curlGet($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST,false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    static public function arrayToString($uidsArr = array()){
        $callback = function(&$result,$uid){
            return $result .= $uid['uid'].",";
        };
        $result = array_reduce($uidsArr,$callback);
        $uids_string = substr($result,0,strlen($result)-1);
        return $uids_string;
    }
    
    static public function getManagersAndCategoriesByUidsArray($uidsArray = array(),$em){
        //a callback function to push every category & manager of clients into array
        $categories = array();
        $managers = array();
        $callback = function($item) use(&$categories,&$managers,$em){
            $uid = $item['uid'];
            $categories[] = array('category' => $em->getRepository('CRMBundle:ClientCategory')->getCategoryByUid($uid));
            $managers[] = array('manager' => $em->getRepository('UserBundle:UserInfo')->getManagerByUid($uid));
        };
        array_walk($uidsArray,$callback);
        return array('managers'=>$managers,'categories'=>$categories);
    }

    static public function response($urlName,$message){
        $url = self::generateUrl($urlName);
        return new Response("<script>alert('$message');window.location.href='$url';</script>");
    }

    /**
     * @param $param
     * @param $k
     * @param null $v
     * @return mixed
     */
    static public function apiGetData($param, $k=null, $v=null,$page=null,$t=null,$sorting=null,$pagination=true,$sendToken=true){
        if($sendToken){
            $key = "f3696f51132c85c605d4260c417da697";
            $access_token = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key,time()."51jili",MCRYPT_MODE_ECB);
        }else{
            $access_token = null;
        }
        $url = GlobalClass::API.$param;
        if(is_array($v)){
            $v = json_encode($v);
            $postData = array('k'=>$k,'v'=>$v,'page'=>$page,'t'=>$t,'sorting'=>$sorting,'pagination'=>$pagination,'access_token'=>$access_token);
        }else{
            if($v!=null){
                $postData = array('k'=>$k,'v'=>$v,'page'=>$page,'t'=>$t,'sorting'=>$sorting,'pagination'=>$pagination,'access_token'=>$access_token);
            }else{
                $postData = array('k'=>$k,'page'=>$page,'t'=>$t,'sorting'=>$sorting,'pagination'=>$pagination,'access_token'=>$access_token);
            }

        }
        $res = self::curl($url,$postData);
        return json_decode($res,true);

    }

    
}