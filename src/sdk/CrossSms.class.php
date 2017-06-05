<?php
/**
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class CrossSms extends Sms{
    protected $ApiBase = "http://211.140.53.167/";
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
        $result = $this->call('sms/smsInterface',$data);
        if($result){
            $return['result'] = $result;
            $return['post_result'] = json_encode($result);
            if($result['result']['resultcode'] == 0) return true;
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
        $result = $this->call("sms/smsBalance");
        $return = $result;
        if($result){
            $return['result'] = $result;
            $return['post_result'] = $result;
            if($result['result']['resultcode'] == 0) return $result['result']['smsbalancenum'];
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
        $result = $this->call("ws/Get");
        $return = $result;
        if($result >= 0) {
            $temp = explode("||",$result);
            $data = array();
            foreach($temp as $val){
                if(empty($val)) continue;
                $report = explode("#",$val);
                $data[]['receive_phone'] = $report[0];
                $data[]['content'] = $report[1];
                $data[]['send_time'] = $report[2];
                $data[]['task_id'] = $report[3];
                $data[]['report_result'] = $val;
                $data[]['report_status'] = $result;
                $data[]['status'] = 1;
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
        $params = array ('username' => $this->loginname,'password' => $this->password  );
        //echo( $this->url ( $api, '.do' ).'?'. http_build_query($this->param ( $params, $param )));
        $data = $this->http ( $this->url ( $api, '.do' ), $this->param ( $params, $param ), $method,$multi );
        require_once dirname ( dirname ( __FILE__ ) ) ."/util/XML2Array.php";
        if($data){
            $data = XML2Array::createArray ( $data );
        }
        //$data = XML2Array::createArray ( $data );
        //return json_decode ( $data, true );
        return $data;
    }
    public function setLoginname($loginname){
        $this->loginname = $loginname;
    }
    public function setPassword($password){
        $this->password = $password;
    }

}