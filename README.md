### 实用工具，整合useragent判断、短信、下载图片
### useragent判断
```php
<?php
require_once  'vendor/autoload.php';

$useragent = new \Utils\UserAgent();


if($useragent->is('iOS')){
	echo 'this os is ios';
}elseif ($useragent->is('AndroidOS')) {
	echo 'this os is ios';
}

if($useragent->isMobile()){
	echo 'this device is mobile';
}elseif ($useragent->isTablet()) {
	echo 'this os is tablet';
}

$result = \Utils\Sms::getInstance(['account'=>'myaccount','password'=>'mypasspord'])->send(['to'=>'15812345678','content'=>'亲爱的用户，您的活动验证码是123456，感谢您的参与此次活动。']);

```
### 短信
需要先配置好config/service.php
```php
<?php
require_once  'vendor/autoload.php';

//梦网批量发送不同手机号，不同内容
$list = [['mobile'=>15000000000,'msg'=>'这是第一条测试短信'],['mobile'=>15712345678,'msg'=>'这是第二条测试短信']];
$request = new Request(['multimt'=>$list]);
$result = Sms::driver('montnets')->send($request,'multi_send');

//梦网，mobile可以单个手机号，或多个手机号英文逗号隔开
$request = new Request(['mobile'=>"15000000000,15100000000",'msg'=>'这是第一条测试短信']);
$result = Sms::driver('montnets')->send($request);

//创蓝旧版，mobile可以单个手机号，或多个手机号英文逗号隔开
$request = new Request(['mobile'=>"15000000000,15100000000",'msg'=>'这是第一条测试短信']);
//发送短信
$result = Sms::driver('chuanglanold')->send($request);
//查询余额
$result = Sms::driver('chuanglanold')->balance($request);

//创蓝新版，mobile可以单个手机号，或多个手机号英文逗号隔开
$request = new Request(['mobile'=>"15000000000,15100000000",'msg'=>'这是第一条测试短信']);
//发送短信
$result = Sms::driver('chuanglan')->send($request);
//查询余额
$result = Sms::driver('chuanglan')->balance($request);


```
### 短信
需要先配置好config/service.php
```php

<?php
require_once  'vendor/autoload.php';

try {
	$downloadImg = new \Utils\DownloadImages();
	$downloadImg->setRemoteUrl("https://www.baidu.com/img/bd_logo1.png", true)
	->setLocalPath("/var/data/")
	->setLocalName("bd_logo1.png");
	$copySize = $downloadImg->download();
} catch (Exception $e) {
	return ['state'=>$e->getCode(),'msg'=>$e->getMessage()];
}

var_dump($copySize);

```