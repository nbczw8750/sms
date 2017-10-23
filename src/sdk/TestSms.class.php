<?php
/**
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class TestSms extends Sms{
    protected $ApiBase = "";
    /*
     * 发送短信
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function send($param = array() , &$return = array()){
        $return['task_id'] = 11111;
        return true;
    }
    /*
     * 短信剩余条数查询
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function check($param = array() , &$return = ""){
        return 9999;
    }
    /*
     * 获取状态
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function get($param = array() , &$return = ""){
        $result = "13056941231#成功#1213#11111#33#1";
        $return = $result;
        if($result) {
            $temp = explode("||",$result);
            $data = array();
            $i = 0;
            foreach($temp as $val){
                if(empty($val)) continue;
                $report = explode("#",$val);
                $data[$i]['receive_phone'] = $report[0];
                $data[$i]['content'] = $report[1];
                $data[$i]['send_time'] = $report[2];
                $data[$i]['task_id'] = $report[3];
                $data[$i]['report_result'] = $val;
                $data[$i]['report_status'] = 1;
                $data[$i]['status'] = 1;
                $i++;
            }
            return $data;
        }
        return null;
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

}