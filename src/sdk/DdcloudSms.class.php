<?php
/**
 * 叮咚云
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class DdcloudSms extends Sms{
    protected $ApiBase = "https://api.dingdongcloud.com/v1/";
    /*
     * 发送短信
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function send($param = array() , &$return = array()){
        if(!$param && empty($param)){
            return false;
        }
        $data = array();
        $data['mobile'] = implode(",",$param['mobile']);
        $data['content'] = $param['content'];
        if($param['template_type'] == "notice"){
            $result = $this->call('sms/sendtz',$data,"POST");
        }else{
            $result = $this->call('sms/sendyzm',$data,"POST");
        }
        if($result){
            $return['result'] = $result;
            $return['post_result'] = json_encode($result);
            $return['task_id'] = $result['result'];
            $return['serial_number'] = $result['result'];
            if($result['code'] == 1) return true;
        }
        return false;
    }
    /*
     * 短信剩余条数查询
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function check($param = array() , &$return = ""){
        $result = $this->call("sms/querybalance");
        $return = $result;
        if($result){
            $return['result'] = $result;
            $return['post_result'] = json_encode($result);
            if($result['code'] == 1) return $result['result'];
        }
        return false;
    }
    /*
     * 获取状态
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function get($param = array() , &$return = ""){
        $result = $this->call("sms/reports");
        $return = $result;
        if($result['code'] == 1) {
            $data = array();
            foreach ($result['result'] as $key => $val){
                $data[$key]['receive_phone'] = $val['mobile'];
                $data[$key]['task_id'] = $val['serNo'];
                $data[$key]['serial_number'] = $val['serNo'];
                $data[$key]['report_status'] = $val['status'] == "DELIVRD" ? 1 : 0 ;
                $data[$key]['status'] = $val['status'] == "DELIVRD" ? 1 : 0 ;
                //$data[$key]['receive_phone'] = $val['sendTs'];
                //$data[$key]['receive_phone'] = $val['reportTs'];
                $data[$key]['report_result'] = $result['msg'];
            }
            return $data;
        }
        return false;
    }
    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api    开放平台API
     * @param  string $param  调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'POST', $multi = false) {
        $params = array ('apikey' => $this->password  );
        //echo( $this->url ( $api, '.do' ).'?'. http_build_query($this->param ( $params, $param )));
        $data = $this->http ( $this->url ( $api, '' ), $this->param ( $params, $param ), $method,$multi );
        return json_decode ( $data, true );
    }
    public function setLoginname($loginname){
        $this->loginname = $loginname;
    }
    public function setPassword($password){
        $this->password = $password;
    }
    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @return array  $data   响应数据
     */
    protected function http($url, $params, $method = 'GET', $header = array(), $multi = false) {
        /* 根据请求类型设置特定参数 */
        switch (strtoupper ( $method )) {
            case 'GET' :
                if(strpos($url,"?") === false){
                    $url = $url .'?'. http_build_query ( $params );
                }else{
                    $url = $url .'&'. http_build_query ( $params );
                }
                $ch = curl_init();
                /* 设置验证方式 */
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt ($ch, CURLOPT_URL, $url);
                $result = curl_exec($ch);
                curl_close($ch);
                break;
            case 'POST' :
                $ch = curl_init();
                /* 设置验证方式 */
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
                /* 设置返回结果为流 */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                /* 设置超时时间*/
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                /* 设置通信方式 */
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt ($ch, CURLOPT_URL, $url);
                $params = $multi ? $params : http_build_query ( $params );
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                $result = curl_exec($ch);
                curl_close($ch);
                break;
            default :
                throw new Exception ( '不支持的请求方式！' );
        }
        return $result;
    }

}