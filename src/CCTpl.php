<?php
/**
 * Created by PhpStorm.
 * User: 蔡志伟
 * Date: 2017/5/27
 * Time: 10:25
 */

namespace nbczw8750\sms;


class CCTpl
{
    protected $error = "";
    protected $params = array();
    public function getError(){
        return $this->error;
    }

    public function getParams(){
        return $this->params;
    }
    /**
     * $param = "aa=1;" 数组 json
     * $tpl = "内容{变量}"
     * */
    public  function parseTpl($tpl,$param){
        if(!is_array($param)){
            $tmp = json_decode($param,true);
            if(!$tmp){
                $param = explode(";",$param);
                $tmp = array();
                foreach($param as $val){
                    $val = explode("=",$val);
                    $tmp[$val[0]] = $val[1];
                }
            }
            $param = $tmp;
            unset($tmp);
        }
        $tpl = preg_replace_callback ( '/\{([a-zA-Z_]+)\}/', function ($match) use($param) {
            return $param [$match [1]];
        }, $tpl );
        return trim($tpl);
    }

    /**
     * 匹配模板
     * @param $content 内容 abc1dbc2
     * @param $tpl {name}1{code}2
     * @return bool true => params => array( 'name'=>'abc','code'=>'dbc' )
     */
    public function matchTpl($content,$tpl){
        $key  = array();;
        $value  = array();
        $params = array();

        $tpl = preg_replace_callback ( '/\{([a-zA-Z_]+)\}/', function ($match) use(&$key) {
            $key[] = $match [1];
            return "(.*?)";
        }, $tpl );
        if(preg_match_all("/^$tpl$/",$content,$match)){
            $len = count($match);
            for($i = 1 ; $i < $len ; $i++){
                $value[] = $match[$i][0];
            }
            $len = count($key);
            for($i = 0 ; $i < $len ; $i++){
                $params[$key[$i]] = $value[$i];
            }
            $this->params = $params;
            return true;
        }else{
            return false;
        }

    }
}