<?php
namespace Utils;

class Clnsms{
	protected static $_instance = null;
	public static $openurl='http://sms.253.com/msg/send?needstatus=true';

	public static $config=[];
	public static $state = array(
					0    => array('state'=>2000, 'msg'=>'提交成功', ),
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

	protected function __construct($config){
		self::$config = $config;
	}

	public static function getInstance($config){
		if (!isset(self::$_instance)) {
			self::$_instance = new self($config);
		}
		return self::$_instance;
	}

	//发送消息
	public static function send($data){
		if(empty($data['to'])|| empty($data['content'])){
			return ['state'=>5001,'msg'=>'缺少参数'];
		}
		
		$post_data = array();
		$post_data['un'] = iconv('GB2312', 'GB2312', self::$config['account']);
		$post_data['pw'] = iconv('GB2312', 'GB2312', self::$config['password']);
		$post_data['phone'] = $data['to'];
		$post_data['msg'] = mb_convert_encoding($data['content'],'UTF-8', 'auto');

		$res = $this->curlPost( self::$openurl , $post_data);

		$r = explode(PHP_EOL,$res );
		if( isset($r[0]) ) {
			$tmp = explode(',',$r[0] );
			if( isset($tmp[1]) && isset(self::$state[$tmp[1]]) ){
				return self::$state[$tmp[1]];
			}
		}
		return self::$state[120];
	}

	private function curlPost($url,$postFields){
		$postFields = http_build_query($postFields); 
		if(function_exists('curl_init')){

			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
			$result = curl_exec ( $ch );
			if(curl_errno($ch))
			{
				return 'Curl error: ' . curl_error($ch);
			}
			curl_close ( $ch );
		}elseif(function_exists('file_get_contents')){
			
			$result=file_get_contents($url.$postFields);

		}
		return $result;
	}
}