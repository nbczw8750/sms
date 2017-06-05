<?php
/**
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:50
 */
abstract class Sms {
    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = '';
    /**
     * 取得Oauth实例
     * @static
     * @return mixed 返回Oauth
     */

    protected $loginname;
    protected $password;
    protected $spcode;
    /**
     * 构造方法，配置应用信息
     * @param array $token
     */
    public function __construct($param = array()) {

        $this->loginname = $param['loginname'];
        $this->password = $param['password'];
        $this->spcode = $param['spcode'];
    }
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
    /**
     * 合并默认参数和额外参数
     * @param array $params  默认参数
     * @param array/string $param 额外参数
     * @return array:
     */
    protected function param($params, $param) {
        if (is_string ( $param ))
            parse_str ( $param, $param );
        return array_merge ( $params, $param );
    }
    /**
     * 获取指定API请求的URL
     * @param  string $api API名称
     * @param  string $fix api后缀
     * @return string      请求的完整URL
     */
    protected function url($api, $fix = '') {
        return $this->ApiBase . $api . $fix;
    }
    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @return array  $data   响应数据
     */
    protected function http($url, $params, $method = 'GET', $header = array(), $multi = false) {
        require_once dirname ( dirname ( __FILE__ ) ) ."/util/HttpRequestCURL.php";
        $opts = array (CURLOPT_HEADER => 0);
        /* 根据请求类型设置特定参数 */
        switch (strtoupper ( $method )) {
            case 'GET' :
                if(strpos($url,"?") === false){
                    $url = $url .'?'. http_build_query ( $params );
                }else{
                    $url = $url .'&'. http_build_query ( $params );
                }
                $result = HttpRequestCURL::curl_get($url);
                break;
            case 'POST' :
                //判断是否传输文件
                $params = $multi ? $params : http_build_query ( $params );
                $result = HttpRequestCURL::curl_post($url,$params);
                break;
            default :
                throw new Exception ( '不支持的请求方式！' );
        }
        return $result;
    }
    /**
     * 抽象方法，在SNSSDK中实现
     * 组装接口调用参数 并调用接口
     */
    abstract protected function call($api, $param = '', $method = 'GET', $multi = false);
}