<?php
/**
 * http请求处理类（基于CURL进行封装）
 *
 */
class HttpRequestCURL
{
	/**
	 * get方式请求（curl）
	 *
	 * @param string $url 请求的url
	 * @param integer $timeout 超时时间（s）
	 * @return string(请求成功) | false(请求失败)
	 */
	public static function curl_get($url, $timeout = 10)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$result = curl_exec($ch);
		curl_close($ch);
		if (is_string($result) && strlen($result))
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	/**
	 * post方式请求
	 *
	 * @param string $url 请求的url
	 * @param array $data 请求的参数数组（关联数组）
	 * @param integer $timeout 超时时间（s）
	 * @return string(请求成功) | false(请求失败)
	 */
	public static function curl_post($url, $data, $timeout = 2)
	{
        //判断是否传输文件
        if(is_array($data)){
            $data = http_build_query ( $data );
        }
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$result = curl_exec($ch);
		curl_close($ch);
		if (is_string($result) && strlen($result))
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	/**
	 * 多个url并行请求
	 *
	 * @param array $urls url数组
	 * @param integer $timeout 超时时间(s)
	 * @return array $res 返回结果
	 */
	public static function curl_get_urls($urls, $timeout = 1)
	{
		$mh=curl_multi_init();
		$chs=array();
		foreach($urls as $url)
		{
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_HEADER,false);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
			curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
			curl_multi_add_handle($mh,$ch);
			$chs[]=$ch;
		}
		$active=null;
		do {
			$mrc=curl_multi_exec($mh,$active);
		}while($mrc == CURLM_CALL_MULTI_PERFORM);
		while($active && $mrc == CURLM_OK)
		{
			if(curl_multi_select($mh) != -1)
			{
				do{
					$mrc=curl_multi_exec($mh,$active);
				}while($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		$res=array();
		foreach($chs as $ch)
		{
			$res[]=curl_multi_getcontent($ch);
			curl_multi_remove_handle($mh,$ch);
		}
		curl_multi_close($mh);
		return $res;
	}
}
?>