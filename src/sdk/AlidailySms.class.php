<?php
/**
 * 阿里大鱼
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class AlidailySms extends Sms{
    protected $ApiBase = "http://gw.api.taobao.com/router/rest";
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
            $data['sms_param'] = json_encode(contentParamFormat($param['content_param']));
        }
        $data['sms_free_sign_name'] = $param['sms_free_sign_name'];
        $data['sms_template_code'] = $param['sms_template_code'];
        $result = $this->sendsms($data);
        if($result){
            $return['result'] = $result;
            $return['post_result'] = json_encode($result);
            if(isset($result['result']['success']) && ( $result['result']['success'] == "true" || $result['result']['success'] == true)) return true;
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
    }
    public function setLoginname($loginname){
        $this->loginname = $loginname;
    }
    public function setPassword($password){
        $this->password = $password;
    }
    //发送短信
    public function sendsms($param){
        include "sdk/Alibaba/TopSdk.php";
        $c = new TopClient;
        $c->appkey = $this->loginname;
        $c->secretKey = $this->password ;
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