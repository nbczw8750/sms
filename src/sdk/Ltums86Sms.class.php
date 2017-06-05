<?php
/**
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class Ltums86Sms extends Sms{
    protected $ApiBase = "http://gd.ums86.com:8899/";
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
        $data['UserNumber'] = implode(",",$param['mobile']);
        $data['MessageContent'] = iconv("UTF-8","GB2312//IGNORE",$param['content']);
        isset($param['extend']) && $data['ExtendAccessNum'] = $param['extend'];
        $data['f'] = 1;
        $data['SerialNumber'] = date("Ymdhis").mt_rand(1000,9999);
        $data['ScheduleTime'] = '';
        $result1 = $this->call('sms/Api/Send',$data,"POST",false);
        $result = iconv('GB2312', 'UTF-8//IGNORE',$result1);
       /* $result_arr = explode("&",$result);
        $result = array();
        foreach($result_arr as $val){
            $temp = explode("=",$val);
            $result[$temp[0]] = $temp[1];
        }*/
        if($result){
            $result_arr = array();
            parse_str($result, $result_arr);
            /*$result_arr = explode("&",$result);
            $result = array();
            foreach($result_arr as $val){
                $temp = explode("=",$val);
                $result[$temp[0]] = $temp[1];
            }*/
            $result = $result_arr;
            $return['result'] = $result['result'];
            $return['description'] = $result['description'];
            isset($result['faillist']) && $return['fail_list'] = explode(",",$result['faillist']);
            $return['serial_number'] = $data['SerialNumber'];
            if(isset($result['taskid']) || isset($result['task_id'])){
                $return['task_id'] = isset($result['taskid']) ? $result['taskid'] : ( isset($result['task_id']) ? $result['task_id'] : '');
            }
            isset($param['extend']) && $return['extend'] =  $param['extend'];
            $return['post_result'] = $result['description'];
            if($result['result'] == 0) return true;
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
        $result1 = $this->call("sms/Api/SearchNumber",array(),"POST",false);
        $result = iconv('GB2312', 'UTF-8//IGNORE',$result1);
        parse_str($result, $result_arr);
        if($result && $result_arr['result'] == 0){
            $return['result'] = $result_arr['result'];
            $return['description'] = $result_arr['description'];
            $return['number'] = $result_arr['number'];
        }
        if($result_arr['result'] == 0) return $result_arr['number'];
        return false;
    }
    /*
     * 获取状态
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function get($param = array() , &$return = ""){
        $result1 = $this->call("sms/Api/report");
        $result = iconv('GB2312', 'UTF-8//IGNORE',$result1);
        $return = $result;
        parse_str($result, $result_arr);
        if($result && $result_arr['result'] == 0) {
            $temp = isset($result_arr['out']) ? $result_arr['out'] : '' ;
            $temp = explode(";",$temp);
            $i = 0;
            $data = array();
            foreach($temp as $val){
                if(empty($val)) continue;
                $report = explode(",",$val);
                $data[$i]['serial_number'] = $report[0];
                $data[$i]['receive_phone'] = $report[1];
                $data[$i]['status'] = $report[2] == 0 ? 1 : 0 ;
                $data[$i]['report_result'] = $val;
                $data[$i]['report_status'] = $report[2];
                $i++;
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
    public function call($api, $param = '', $method = 'GET', $multi = false) {
        $params = array ('SpCode'=>$this->spcode,'LoginName' => $this->loginname,'Password' => $this->password  );
        //$data = file_get_contents( $this->url ( $api, '.aspx' ).'?'. http_build_query($this->param ( $params, $param )));
        //echo( $this->url ( $api, '.do' ).'?'. http_build_query($this->param ( $params, $param )));
        $vars = $this->param ( $params, $param );
        $data = $this->http ( $this->url ( $api, '.do' ), $vars, $method,1,$multi );
        //$data = $this->_httpClient($this->url ( $api, '.do' ),http_build_query($vars));
        //return json_decode ( $data, true );
        return $data;
    }
    /**
     * POST方式访问接口
     * @param string $data
     * @return mixed
     */
    private function _httpClient($url,$data) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $res = curl_exec($ch);
            curl_close($ch);
            return $res;
        } catch (Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }
    public function setLoginname($loginname){
        $this->loginname = $loginname;
    }
    public function setPassword($password){
        $this->password = $password;
    }

}