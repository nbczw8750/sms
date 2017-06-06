<?php
namespace nbczw8750\sms;
require dirname ( __FILE__ )."/sdk/Sms.class.php";
/**
 * Created by PhpStorm.
 * User: 志伟
 * Date: 15-5-27
 * Time: 下午2:50
 */
abstract class CCSms {
    public static function getInstance($type, $param = null ,&$return) {
        $name = ucfirst ( strtolower ( $type ) ) . 'Sms';
        $classPath = dirname ( __FILE__ )."/sdk/{$name}.class.php";
        if(!file_exists($classPath)){
            $return = $classPath."文件不存在";
            return false;
        }
        require_once $classPath;
        if (class_exists ( $name )) {
            return new $name ( $param );
        } else {
            $return = "{$name}类不存在";
            return false;
        }
    }
}