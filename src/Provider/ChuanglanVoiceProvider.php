<?php

/*
 * This file is part of the overtrue/socialite.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Utils\Provider;

use Utils\Provider\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QQProvider.
 *
 * @link http://wiki.connect.qq.com/oauth2-0%E7%AE%80%E4%BB%8B [QQ - OAuth 2.0 登录QQ]
 */
class ChuanglanVoiceProvider extends AbstractProvider implements ProviderInterface
{
	/**
	 * The base url of 253SMS API.
	 *
	 * @var string
	 */
	protected $baseUrl = 'http://audio.253.com';


	protected $clientKey;

	/**
	 * error_code
	 *
	 * @var string
	 */
	protected static $error_code = array(
		0	=> array('state'=>2000, 'msg'=>'提交成功', ),
		-1  => array('state'=>1001, 'msg'=>'失败', ),
		-2  => array('state'=>1002, 'msg'=>'用户/项目不存在/项目无号码/无通话记录/录音记录', ),
		-3  => array('state'=>1003, 'msg'=>'参数错误', ),
		-4  => array('state'=>1004, 'msg'=>'Key错误', ),
		-5  => array('state'=>1005, 'msg'=>'用户密码不对', ),
		);

	/**
	 * Get the send sms URL for the provider.
	 *
	 * @param string $state
	 *
	 * @return string
	 */
	protected function getSendUrl($state=null){
		return $this->baseUrl.'/voice';
	}

	/**
	 * Get the token URL for the provider.
	 *
	 * @return string
	 */
	protected function getBalanceUrl($state=null){
		return $this->baseUrl.'/msg/QueryBalance';
	}

	public function send(Request $request){
		date_default_timezone_set("Asia/Shanghai");
		$vfcode = $this->request->get('code');
		$phone = $this->request->get('mobile');
		$timestamp = date("YmdHis");

		$data = [
			'voiceinfo'=>[
				'organization' => $this->clientId,
				'phonenum' => $phone,
				'timestamp' => $timestamp,
				'content' => md5($this->clientKey.$phone.$this->clientSecret.$timestamp),
				'vfcode' => $vfcode,
				'shownum' => '95213141',
				'uniqueid' => uniqid(),
			],
		];
		$post_data = 'method=vcfplay&voiceinfo='.urlencode(json_encode($data));
		$result = $this->curl_request('voice',$post_data,1);
		return $this->parse_send_result($result);
	}

	public function balance(Request $request){
		$response = $this->getHttpClient()->get($this->getBalanceUrl());
		$result = $response->getBody()->getContents();
		return $this->parse_balance_result($result);
	}


	protected function parse_send_result($data){
		$encode = mb_detect_encoding($data, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); 
		if ($encode == "EUC-CN"){ 
			$data = iconv("GBK","UTF-8",$data); 
		} 
		$tmp = json_decode($data,true);
		if(!empty($tmp)){
			$data = $tmp;
		}

		return [
		'state'=>self::$error_code[$data['code']]['state'],
		'msg'=>self::$error_code[$data['code']]['msg'],
		];
	}

	protected function parse_balance_result($data){
		$list=preg_split("/[\r\n]/",$data);
		foreach ($list as $key => $value) {
			if(strpos($value, ',')!==false){
				$list[$key] = explode(',', $value);
			}
		}
		$state = $list[0];
		unset($list[0]);
		$result = [
		'state'=>self::$error_code[$state[1]]['state'],
		'msg'=>self::$error_code[$state[1]]['msg'],
		'data'=>$list,
		'time'=>$state[0],
		];
		return $result;
	}

	 /**
 * post请求
 *
 * @param funAndOperate
 *            功能和操作
 * @param body
 *            要post的数据
 * @return
 * @throws IOException
 */
	 protected function curl_request($funAndOperate, $body,$is_post=false){
	    // 构造请求数据
	 	$url = $this->baseUrl.'/'.$funAndOperate;
	 	if(!$is_post){
	 		$url = $url.$body;
	 	}

	    // 提交请求
	 	$con = curl_init();
	 	curl_setopt($con, CURLOPT_URL, $url);
	 	curl_setopt($con, CURLOPT_SSL_VERIFYHOST, FALSE);
	 	curl_setopt($con, CURLOPT_SSL_VERIFYPEER, FALSE);
	 	curl_setopt ($con, CURLOPT_HEADER, 0);
	 	curl_setopt ($con, CURLOPT_RETURNTRANSFER, 1);
	 	curl_setopt($con, CURLOPT_POST, $is_post);
	 	if($is_post){
	 		curl_setopt($con, CURLOPT_POSTFIELDS, $body);
	 	}

	 	$result = curl_exec($con);
	 	curl_close($con);

	 	return $result;
	 }
	}
