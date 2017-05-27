<?php
/**
 * Created by PhpStorm.
 * User: 蔡志伟
 * Date: 2017/5/27
 * Time: 10:25
 */

namespace nbczw8750\sms;


class Sms
{
    protected $error = "";

    public function send($param){

    }
    public function getError(){
        return $this->error;
    }
}