<?php
/**
 * 宁波旅游局短信
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class NbtravelSms extends Sms{
    protected $ApiBase = "http://dx.nbtravel.gov.cn/";
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
        $data['ls_jsrsjh'] = implode(",",$param['mobile']);
        $data['ls_fsrxx'] = implode(",",$param['mobile']);
        $data['ls_content'] = $param['content'];
        $result = $this->call('SendOneSms',$data);
        if($result){
            $return['result'] = $result;
            $return['post_result'] = json_encode($result);
            if($result['status'] == 1) return true;
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
        $params = array ('ls_userid' => $this->loginname,'ls_pwd' => $this->password  );
//        echo "params:";
//        print_r($params);
//        echo "param:";
//        print_r($param);
//        echo "合并";
//        print_r(array_merge(array(),$params,$param));
//        echo "url:".$this->url($api,".asmx?wsdl");
        //echo( $this->url ( $api, '.do' ).'?'. http_build_query($this->param ( $params, $param )));
        $client = new \SoapClient($this->url($api,".asmx?wsdl"));
        $client->soap_defencoding = "utf-8";
        $client->decode_utf8 = false;
        $client->xml_encoding = 'utf-8';
        $data = $client->SendSingleSms(array_merge($params,$param));
        //$data = $this->http ( $this->url ( $api, '.do' ), $this->param ( $params, $param ), $method,$multi );
        //require_once dirname ( dirname ( __FILE__ ) ) ."/util/XML2Array.php";
        //if($data){
        //    $data = XML2Array::createArray ( $data );
        //}
        //$data = XML2Array::createArray ( $data );
        $result = $this->object_array($data);
//        print_r($result);
        foreach ($result as $val){
            $return = $val;
        }
        if(is_string($return)){
            $return = json_decode($return,true);
        }
        return $return;
    }
    public function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        }
        if(is_array($array)) {
            foreach($array as $key=>$value) {

                $array[$key] = $this->object_array($value);
            }

        }
        return $array;
    }

    public function setLoginname($loginname){
        $this->loginname = $loginname;
    }
    public function setPassword($password){
        $this->password = $password;
    }

}