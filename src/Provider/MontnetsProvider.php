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
class MontnetsProvider extends AbstractProvider implements ProviderInterface{

	/**
	 * error_code
	 *
	 * @var string
	 */
	protected static $error_code = array(
		0		=> array('state'=>2000, 'msg'=>'提交成功', ),
		-100001  => array('state'=>1001, 'msg'=>'鉴权不通过,请检查账号,密码,时间戳,固定串', ),
		-100002  => array('state'=>1002, 'msg'=>'用户多次鉴权不通过', ),
		-100003  => array('state'=>1009, 'msg'=>'用户欠费', ),
		-100004  => array('state'=>1010, 'msg'=>'custid或者exdata字段填写不合法', ),
		-100011  => array('state'=>1011, 'msg'=>'短信内容超长', ),
		-100012  => array('state'=>1012, 'msg'=>'手机号码不合法', ),
		-100014  => array('state'=>1013, 'msg'=>'手机号码超过最大支持数量（1000）', ),
		-100029  => array('state'=>1015, 'msg'=>'端口绑定失败', ),
		-100056  => array('state'=>1016, 'msg'=>'用户账号登录的连接数超限', ),
		-100057  => array('state'=>1017, 'msg'=>'用户账号登录的IP错误', ),
		-100999  => array('state'=>1018, 'msg'=>'平台数据库内部错误', ),
		);

	public function send(Request $request,$type='single_send'){
		$mobile = $request->input('mobile');
		if(!empty($mobile) && strpos($mobile, ',')!==false){
			$type = 'batch_send';
		}
		if($type=='batch_send'){
			$result = $this->batch_send($request);
		}elseif ($type=='multi_send') {
			$result = $this->multi_send($request);
		}else{
			$result = $this->single_send($request);
		}
		return $result;
	}

	public function single_send(Request $request){
		$input = $request->only(['mobile', 'msg', 'svrtype', 'exno','custid','exdata']);
		foreach ($input as $key =>$value) {
			if(empty($value)){
				unset($input[$key]);
			}
		}
		if(empty($input['mobile']) || empty($input['msg'])){
			return ['state'=>3001,'msg'=>'参数错误','data'=>$request->all()];
		}
		$response = $this->getHttpClient()->post($this->baseUrl.'/sms/v2/std/single_send', [
			'form_params' => [
			'userid' => $this->clientId,
			'pwd'=>md5(strtoupper($this->clientId).'00000000'.$this->clientSecret.date('mdHis')),
			'mobile'=>$input['mobile'],
			'content'=>array_iconv($input['msg'],'utf8','gbk'),
			'timestamp'=>date('mdHis'),
			],
			]);
		$result = $response->getBody()->getContents();
		return $this->parse_result($result);
	}

	public function batch_send(Request $request){
		$input = $request->only(['mobile', 'msg', 'svrtype', 'exno','custid','exdata']);
		foreach ($input as $key =>$value) {
			if(empty($value)){
				unset($input[$key]);
			}
		}
		if(empty($input['mobile']) || empty($input['msg'])){
			return ['state'=>3001,'msg'=>'参数错误','data'=>$request->all()];
		}
		$response = $this->getHttpClient()->post($this->baseUrl.'/sms/v2/std/batch_send', [
			'form_params' => [
			'userid' => $this->clientId,
			'pwd'=>md5(strtoupper($this->clientId).'00000000'.$this->clientSecret.date('mdHis')),
			'mobile'=>$input['mobile'],
			'content'=>array_iconv($input['msg'],'utf8','gbk'),
			'timestamp'=>date('mdHis'),
			],
			]);
		$result = $response->getBody()->getContents();
		return $this->parse_result($result);

	}

	public function multi_send(Request $request){
		$multimt = $request->input('multimt');
		if(empty($multimt)){
			return ['state'=>3001,'msg'=>'参数错误','data'=>$request->all()];
		}

		foreach ($multimt as $key => $value) {
			$multimt[$key]['content'] = urlencode (array_iconv($value['content'],'utf8','gbk'));
		}
		$response = $this->getHttpClient()->post($this->baseUrl.'/sms/v2/std/multi_send', [
			'form_params' => [
			'userid' => $this->clientId,
			'pwd'=>md5(strtoupper($this->clientId).'00000000'.$this->clientSecret.date('mdHis')),
			'multimt'=>urldecode(json_encode($multimt)),
			'timestamp'=>date('mdHis'),
			],
			]);
		$result = $response->getBody()->getContents();
		return $this->parse_result($result);
	}

	public function balance(Request $request){
		$response = $this->getHttpClient()->post($this->baseUrl.'/sms/v2/std/get_balance', [
			'form_params' => [
			'userid' => $this->clientId,
			'pwd'=>md5(strtoupper($this->clientId).'00000000'.$this->clientSecret.date('mdHis')),
			'timestamp'=>date('mdHis'),
			],
			]);
		$result = $response->getBody()->getContents();
		return $this->parse_result($result);
	}

	public function result(Request $request){
		$retsize = $request->input('retsize');
		$response = $this->getHttpClient()->post($this->baseUrl.'/sms/v2/std/get_mo', [
			'form_params' => [
			'userid' => $this->clientId,
			'pwd'=>md5(strtoupper($this->clientId).'00000000'.$this->clientSecret.date('mdHis')),
			'timestamp'=>date('mdHis'),
			'retsize'=>empty($retsize)?200:$retsize,
			],
			]);
		$result = $response->getBody()->getContents();
		return $this->parse_result($result);
	}

	public function report(Request $request){
		$retsize = $request->input('retsize');
		$response = $this->getHttpClient()->post($this->baseUrl.'/sms/v2/std/get_rpt', [
			'form_params' => [
			'userid' => $this->clientId,
			'pwd'=>md5(strtoupper($this->clientId).'00000000'.$this->clientSecret.date('mdHis')),
			'timestamp'=>date('mdHis'),
			'retsize'=>empty($retsize)?200:$retsize,
			],
			]);
		$result = $response->getBody()->getContents();
		return $this->parse_result($result);
	}

	protected function getSign($clientId,$clientSecret){
		return md5($clientId.'00000000'.$clientSecret.date('mdHis'));
	}

	protected function parse_result($data){
		$tmp = json_decode($data,true);
		if(!empty($tmp)){
			$result = [
			'state'=>self::$error_code[$tmp['result']]['state'],
			'msg'=>self::$error_code[$tmp['result']]['msg'],
			'data'=>$tmp,
			];
			return $result;
		}else{
			return ['state'=>40001,'data'=>$data];
		}
	}
}
