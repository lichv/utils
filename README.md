### 实用工具，整合创蓝短信和useragent判断
### 创蓝短信
```php
<?php
require_once  'vendor/autoload.php';

$result = \Utils\Sms::getInstance(['account'=>'myaccount','password'=>'mypasspord'])->send(['to'=>'15812345678','content'=>'亲爱的用户，您的活动验证码是123456，感谢您的参与此次活动。']);

```
### useragent判断
```php
<?php
require_once  'vendor/autoload.php';

$useragent = new \Utils\UserAgent();

$mobileflag = $useragent->isMobile();
var_dump($mobileflag);
$tabletflag = $useragent->isTablet();
var_dump($tabletflag);
$iphoneflag = $useragent->isIphone();
var_dump($iphoneflag);
$wechatflag = $useragent->isWechat();
var_dump($wechatflag);
$qqflag = $useragent->isQQ();
var_dump($qqflag);
```
