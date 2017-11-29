<?php
require_once __DIR__.'/sdk/aliyun-dysms-php-sdk-lite/SmsApi.php';
use Aliyun\DySDKLite\Sms\SmsApi;
/**
 * 阿里云大于
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class AliyundySms extends Sms{
    protected $ApiBase = "";

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
            $return['post_result'] = isset($result['Message']) ? $result['Message'] : json_encode($result);
            if(isset($result['Code']) && $result['Code'] == "OK") {
                $return['serial_number'] = $result['BizId'];
                $return['task_id'] = $result['RequestId'];
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
        $sms = new SmsApi( $this->loginname, $this->password); // 请参阅 https://ak-console.aliyun.com/ 获取AK信息
        $response = $sms->queryDetails(
            $param['receive_phone'],  // 手机号码
            date("Ymd",$param["send_time"]), // 发送时间
            10, // 分页大小
            1, // 当前页码
            $param["serial_number"]// bizId 短信发送流水号，选填
        );
        $data = false;
        $result = json_decode(json_encode($response),true);
        if($result && !empty($result['SmsSendDetailDTOs'])){
            $data = true;
            foreach ($result['SmsSendDetailDTOs'] as $item){
                foreach ($item as $value){
                    $now = time();
                    if (isset($value['ReceiveDate'])){
                        $data = array();
                        $data['report_result'] = json_encode($value);
                        $data['report_status'] = $value['SendStatus'];
                        $status = 0;
                        if (1 == $value['SendStatus']){
                            $status = 0;
                        }elseif ( 2 == $value['SendStatus']){
                            $status = -2;
                        }elseif ( 3 == $value['SendStatus']){
                            $status = 1;
                        }
                        $data['status'] = $status;
                        $data['receive_phone'] = $value['PhoneNum'];;
                        $data['report_time'] = strtotime($value['ReceiveDate']);
                        $data['serial_number'] = $param["serial_number"];
                    }else if (isset($value['SendDate']) && ($now - strtotime($value['SendDate'])) > 172800 ){ //超过48小时还没收到 说明发送失败
                        $data = array();
                        $data['report_result'] = json_encode($value);
                        $data['report_status'] = $value['SendStatus'];
                        $status = -2; //定性为发送失败
                        $data['status'] = $status;
                        $data['receive_phone'] = $value['PhoneNum'];;
                        $data['report_time'] = $now;
                        $data['serial_number'] = $param["serial_number"];
                    }
                }
            }
        }
        $return = $result;
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
        $sms = new SmsApi( $this->loginname, $this->password); // 请参阅 https://ak-console.aliyun.com/ 获取AK信息
        $templateParam = null;
        if(isset($param['sms_param']) && $param['sms_param']){
            $templateParam = json_decode($param['sms_param'],true);
        }
        $response = $sms->sendSms(
            $param['sms_free_sign_name'], // 短信签名
            $param['sms_template_code'], // 短信模板编号
            $param['rec_num'], // 短信接收者
            $templateParam//,// 短信模板中字段的值
           // "123"   // 流水号,选填
        );
        if($response){
            $json = json_encode($response);
            $response = json_decode($json,true);
        }
        return $response;
    }

}