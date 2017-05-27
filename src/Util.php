<?php
namespace nbczw8750\sms;
/**
 * Created by PhpStorm.
 * User: 蔡志伟
 * Date: 2017/5/27
 * Time: 10:00
 */
class Util
{
    /**
     * 处理并过滤不需要的号码和不合法的号码
     * @param $phones
     * @param array $allow_type
     * @return array|bool
     */
    public static function dealPhone($phones,$allow_type = array("yd","lt","dx")){
        if(!$phones) return false;
        $phones_arr  = array();
        if(!is_array($phones)){
            $phones_arr = preg_split ( '/[,;\r\n]+/', trim ( $phones, ",;\r\n" ) );
        }
        foreach($phones_arr as $key => &$val){
            $val = preg_replace("/^\+86/",'',$val);
            $type = self::getMobileType($val);
            if(!in_array($type,$allow_type)){
                array_splice($phones_arr,$key,1);
            }
        }
        $phones_arr = array_unique($phones_arr);
        return $phones_arr;
    }

    /**
     * 获取手机号码归属运营商
     * @param $phoneNum
     * @return string 移动yd 联通lt 电信dx
     */
    public static function getMobileType($phoneNum){
        $rule = array();
        $rule['lt'] = '/^13[012][0-9]{8}|15[56][0-9]{8}|145[0-9]{8}|176[0-9]{8}|18[56][0-9]{8}$/';
        $rule['yd'] = '/^134[0-8][0-9]{7}|13[56789][0-9]{8}|147[0-9]{8}|15[012789][0-9]{8}|178[0-9]{8}|18[23478][0-9]{8}$/';
        $rule['dx'] = '/^133[0-9]{8}|153[0-9]{8}|177[0-9]{8}|18[019][0-9]{8}$/';
        if(preg_match ( $rule['yd'], $phoneNum ) === 1){
            $type = "yd";
        }elseif(preg_match ( $rule['lt'], $phoneNum ) === 1){
            $type = "lt";
        }elseif(preg_match ( $rule['dx'], $phoneNum ) === 1){
            $type = "dx";
        }
        return $type;
    }
}