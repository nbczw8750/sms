<?php
/**
 * User: nbczw8750
 * Date: 15-5-27
 * Time: 下午2:56
 */
class LinkSms extends Sms{
    protected $ApiBase = "http://sdk.zhongguowuxian.com:98/";
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
        $data['Mobile'] = implode(",",$param['mobile']);
        $data['Content'] = iconv("UTF-8","GB2312//IGNORE",$param['content']);
        isset($param['extend']) && $data['Cell'] = $param['extend'];
        $result = $this->call('ws/batchSend',$data);
        $return['result'] = $result;
        $return['post_result'] = $result;
        isset($param['extend']) && $return['task_id'] =  $param['extend'];
        isset($param['extend']) && $return['extend'] =  $param['extend'];
        if($result >= 0) return true;
        return false;
    }
    /*
     * 短信剩余条数查询
     * @param array $param 数据包
     * @param string $return 返回信息
     * @return bool
     * */
    public function check($param = array() , &$return = ""){
        $result = $this->call("ws/SelSum");
        $return = $result;
        if($result >= 0) return $result;
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
    public function call($api, $param = '', $method = 'GET', $multi = false) {
        $params = array ('CorpID' => $this->loginname,'Pwd' => $this->password  );
        $data = file_get_contents( $this->url ( $api, '.aspx' ).'?'. http_build_query($this->param ( $params, $param )));
        //echo( $this->url ( $api, '.aspx' ).'?'. http_build_query($this->param ( $params, $param )));
        //$data = $this->http ( $this->url ( $api, '.aspx' ), $this->param ( $params, $param ), $method,$multi );
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