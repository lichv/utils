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
class ChuanglanProvider extends AbstractProvider implements ProviderInterface{
	/**
	 * error_code
	 *
	 * @var string
	 */
	protected static $error_code = array(
		0	=> array('state'=>2000, 'msg'=>'提交成功', ),
		101  => array('state'=>1001, 'msg'=>'无此用户', ),
		102  => array('state'=>1002, 'msg'=>'密码错', ),
		103  => array('state'=>1003, 'msg'=>'提交过快（提交速度超过流速限制）', ),
		104  => array('state'=>1004, 'msg'=>'系统忙（因平台侧原因，暂时无法处理提交的短信）', ),
		105  => array('state'=>1005, 'msg'=>'敏感短信（短信内容包含敏感词）', ),
		106  => array('state'=>1006, 'msg'=>'消息长度错（>536或<=0）', ),
		107  => array('state'=>1007, 'msg'=>'包含错误的手机号码', ),
		108  => array('state'=>1008, 'msg'=>'手机号码个数错（群发>50000或<=0;单发>200或<=0）', ),
		109  => array('state'=>1009, 'msg'=>'无发送额度（该用户可用短信数已使用完）', ),
		110  => array('state'=>1010, 'msg'=>'不在发送时间内', ),
		111  => array('state'=>1011, 'msg'=>'超出该账户当月发送额度限制', ),
		112  => array('state'=>1012, 'msg'=>'无此产品，用户没有订购该产品', ),
		113  => array('state'=>1013, 'msg'=>'extno格式错（非数字或者长度不对）', ),
		115  => array('state'=>1015, 'msg'=>'自动审核驳回', ),
		116  => array('state'=>1016, 'msg'=>'签名不合法，未带签名（用户必须带签名的前提下）', ),
		117  => array('state'=>1017, 'msg'=>'IP地址认证错,请求调用的IP地址不是系统登记的IP地址', ),
		118  => array('state'=>1018, 'msg'=>'用户没有相应的发送权限', ),
		119  => array('state'=>1019, 'msg'=>'用户已过期', ),
		120  => array('state'=>1020, 'msg'=>'内容不是白名单', ),
		121  => array('state'=>1021, 'msg'=>'必填参数。是否需要状态报告，取值true或false', ),
		122  => array('state'=>1022, 'msg'=>'5分钟内相同账号提交相同消息内容过多', ),
		123  => array('state'=>1023, 'msg'=>'发送类型错误(账号发送短信接口权限)', ),
		124  => array('state'=>1024, 'msg'=>'白模板匹配错误', ),
		125  => array('state'=>1025, 'msg'=>'驳回模板匹配错误', ),
		128  => array('state'=>1028, 'msg'=>'内容解码失败', ),
		);

	/**
	 * Get the send sms URL for the provider.
	 *
	 * @param string $state
	 *
	 * @return string
	 */
	protected function getSendUrl($state=null){
		return $this->buildAuthUrlFromBase($this->baseUrl.'/msg/send', $state);
	}

	/**
	 * Get the token URL for the provider.
	 *
	 * @return string
	 */
	protected function getBalanceUrl($state=null){
		return $this->buildAuthUrlFromBase($this->baseUrl.'/msg/balance', $state);
	}

	public function send(Request $request){
		$input = $request->all();
		if(empty($input['msg']) || empty($input['mobile'])){
			return ['state'=>3001,'msg'=>'参数错误','data'=>$request->all()];
		}
		$response = $this->getHttpClient()->get($this->getSendUrl().'&'.http_build_query([
			'msg' => '【'.$this->clientSign.'】'.$input['msg'],
			'phone' => empty($input['mobile'])?'':$input['mobile'],
			'rd' => empty($input['rd'])?'1':$input['rd'],
			]));
		$result = $response->getBody()->getContents();
		return $this->parse_result($result);
	}

	public function balance(Request $request){
		$response = $this->getHttpClient()->get($this->getBalanceUrl());
		$result = $response->getBody()->getContents();
		return $this->parse_result($result);
	}

	protected function getCodeFields($state = null)
	{
		$fields = array_merge([
			'un' => $this->clientId,
			'pw' => $this->clientSecret,
			], $this->parameters);

		if ($this->usesState()) {
			$fields['state'] = $state;
		}

		return $fields;
	}

	protected function parse_result($data){
		if(is_string($data) &&strpos($data, ',')){
			$list=preg_split("/[,\r\n]/",$data);
		}else{
			$list = $data;
		}
		$result = [
		'state'=>self::$error_code[$list[1]]['state'],
		'msg'=>self::$error_code[$list[1]]['msg'],
		'data'=>isset($list[2])?$list[2]:'',
		'time'=>$list[0],
		];
		return $result;
	}
}
