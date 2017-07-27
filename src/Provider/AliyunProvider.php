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
class AliyunProvider extends AbstractProvider implements ProviderInterface{
	protected static $client;

	protected function getClient(){
		if(empty(self::$client)){
			$iClientProfile = \Aliyun\Core\Profile\DefaultProfile::getProfile("cn-shanghai", $this->clientId, $this->clientSecret);
			self::$client = new \Aliyun\Core\DefaultAcsClient($iClientProfile);
		}
		return self::$client;
	}

	public function send(Request $request){
		$input = $request->all();

		$sign = empty($input['sign'])?'易之科技':$input['sign'];
		if (empty($input['mobile']) || empty($input['template']) || (!empty($input['params']) && !is_array($input['params']))) {
			return ['state'=>3001,'msg'=>'参数错误','data'=>$request->all()];
		}else{
			$input['params'] = json_encode($input['params']);
		}
		
		$request = new \Aliyun\Sms\Request\V20160927\SingleSendSmsRequest;
		$request->setSignName($sign);
		$request->setTemplateCode($input['template']);
		$request->setParamString($input['params']);
		$request->setRecNum($input['mobile']);

		try {
			$response = $this->getClient()->getAcsResponse($request);
		} catch (\Aliyun\Core\Exception\ServerException $e) {
			return ['state'=>$e->getCode(),'msg'=>$e->getMessage()];
		} catch (Exception $e){
			return ['state'=>$e->getCode(),'msg'=>$e->getMessage()];
		}

		return $this->parse_result($response);
	}

	public function balance(Request $request){
		$request = new \Aliyun\Sms\Request\V20160927\QuerySmsStatisticsRequest;
		$request->setStartTime(date('Ym01'));
		$request->setEndTime(date('Ymd'));
		$request->getSmsType($params);

		try {
			$response = $this->getClient()->getAcsResponse($request);
		} catch (\Aliyun\Core\Exception\ServerException $e) {
			return ['state'=>$e->getCode(),'msg'=>$e->getMessage()];
		} catch (Exception $e){
			return ['state'=>$e->getCode(),'msg'=>$e->getMessage()];
		}

		return $this->parse_result($response);
	}

	protected function parse_result($data){
		$res = get_object_vars($data);
		return empty($res)?$data:$res;
	}
}
