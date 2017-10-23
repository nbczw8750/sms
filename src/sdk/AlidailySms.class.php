<?php
include_once "sdk/Alibaba/TopSdk.php";
/**
 * 阿里大鱼
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class AlidailySms extends Sms{
    protected $ApiBase = "http://gw.api.taobao.com/router/rest";

    static $REPORT_PARAM = ["serial_number","receive_phone","send_time"];
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
        $data['rec_num'] = implode(",",$param['mobile']);
        if($param['content_param']){
            $data['sms_param'] = $param['content_param'];
        }
        !is_array($param['other_param']) && $param['other_param'] = json_decode($param['other_param'],true);
        $data['sms_free_sign_name'] = isset($param['other_param']['sms_free_sign_name']) ? $param['other_param']['sms_free_sign_name'] : '' ;
        $data['sms_template_code'] = isset($param['other_param']['sms_template_code'])  ? $param['other_param']['sms_template_code'] : '';
        $result = $this->sendsms($data);
        if($result){
            $return['result'] = $result;
            $return['post_result'] = isset($result['sub_msg']) ? $result['sub_msg'] : json_encode($result);
            if(isset($result['result']['success']) && ( $result['result']['success'] == "true" || $result['result']['success'] == true)) {
                $return['serial_number'] = $result['result']['model'];
                $return['task_id'] = $result['request_id'];
                return true;
            }
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
        return false;
    }
    /*
     * 获取状态
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function get($param = array() , &$return = ""){
        $c = new TopClient;
        $c->appkey = $this->loginname;
        $c->secretKey = $this->password ;
        $c->format = "json";
        $req = new AlibabaAliqinFcSmsNumQueryRequest();
        $req->setBizId($param["serial_number"]);
        $req->setRecNum($param['receive_phone']);
        $req->setQueryDate(date("Ymd",$param["send_time"]));
        $req->setCurrentPage("1");
        $req->setPageSize("1");
        $response = $c->execute($req);
        $result = json_decode(json_encode($response),true);
        $data = false;
        if($result && !empty($result['values'])){
            foreach ($result['values'] as $item){
                foreach ($item as $value){
                    $data = array();
                    $data['report_result'] = json_encode($value);
                    $data['report_status'] = $value['sms_status'];
                    $status = 0;
                    if (1 == $value['sms_status']){
                        $status = 0;
                    }elseif ( 2 == $value['sms_status']){
                        $status = -2;
                    }elseif ( 3 == $value['sms_status']){
                        $status = 1;
                    }
                    $data['status'] = $status;
                    $data['receive_phone'] = $value['rec_num'];;
                    $data['report_time'] = strtotime($value['sms_receiver_time']);
                    $data['serial_number'] = $param["serial_number"];
                }
            }
        }else{
            $return = $response;
        }
        return $data;
    }
    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api    开放平台API
     * @param  string $param  调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'POST', $multi = false) {
    }
    public function setLoginname($loginname){
        $this->loginname = $loginname;
    }
    public function setPassword($password){
        $this->password = $password;
    }
    //发送短信
    public function sendsms($param){
        $c = new TopClient;
        $c->appkey = $this->loginname;
        $c->secretKey = $this->password ;
        $c->format = "json";
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($param['sms_free_sign_name']);
        if(isset($param['sms_param']) && $param['sms_param']){
            $req->setSmsParam($param['sms_param']);
        }
        $req->setRecNum($param['rec_num']);
        $req->setSmsTemplateCode($param['sms_template_code']);
        $data = $c->execute($req);
        if($data){
            $json = json_encode($data);
            $data = json_decode($json,true);
        }
        return $data;
    }

}