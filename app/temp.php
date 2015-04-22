$arguments = getopt('', [
	'username:', 
	'password:', 
	'mediaId::', 
	'userId::', 
	'commentText::',
	'setLike::',
	'setFollow::',
	'unsetFollow::',
	'setComment::',

	'cookies::',
	'ip::',
	'userAgent::'
]);

/**
* Authorize first
*/
if(!isset($arguments['username']) || !isset($arguments['password']))
{
	throw new Exception('--username and --password parameters are obligatory');
}

$ip = (isset($arguments['ip'])) ? $arguments['ip'] : null;
$userAgent = (isset($arguments['userAgent'])) ? $arguments['userAgent'] : null;

if(isset($arguments['cookies']))
{
	$cookies = json_decode('[{"Name":"mid","Value":"VTdpHwAEAAFLUebXw5zqGYhIxVev","Domain":"instagram.com","Path":"\/","Max-Age":"630720000","Expires":2060414751,"Secure":false,"Discard":false,"HttpOnly":false},{"Name":"csrftoken","Value":"723eb1595c155ab0a80b507b907e2f2b","Domain":"instagram.com","Path":"\/","Max-Age":"31449600","Expires":1461144351,"Secure":false,"Discard":false,"HttpOnly":false},{"Name":"sessionid","Value":"IGSC15da5287028299dbcbfb52c936d8f0bb3dab5f0cb8ed610370cc465c70827def%3AkEP1Hs8ENryre3qZnwJ3FD2qzySReiMC%3A%7B%22_auth_user_id%22%3A1164467438%2C%22_token%22%3A%221164467438%3AwPP0vLZ3ZVTbmbkbLPlwtnzxCi511OXR%3Aaed4131c8f33b4e3e7480ae72c8689fdd8a4fccb072cc409a0c085b84cd70a91%22%2C%22_auth_user_backend%22%3A%22accounts.backends.CaseInsensitiveModelBackend%22%2C%22last_refreshed%22%3A1429694751.554879%2C%22_tl%22%3A1%2C%22_platform%22%3A4%7D","Domain":"instagram.com","Path":"\/","Max-Age":"7776000","Expires":1437470751,"Secure":false,"Discard":false,"HttpOnly":true},{"Name":"ds_user_id","Value":"1164467438","Domain":"instagram.com","Path":"\/","Max-Age":"7776000","Expires":1437470751,"Secure":false,"Discard":false,"HttpOnly":false}]', true);
	$cookies = new \GuzzleHttp\Cookie\CookieJar(false, $cookies);
}
else
{
	$cookies = [];
}

$instagramAuth = new InstagramBot\InstagramAuth($arguments['username'], $arguments['password'], $cookies, $ip, $userAgent);

if(!$instagramAuth->check())
{
	die(json_encode([
		'author' => $arguments['username'], 'action' => 'login', 'status' => false
	]));
}
$instagramActions = new InstagramBot\InstagramActions($instagramAuth);

/**
* Set like
*/
if(isset($arguments['setLike']) && isset($arguments['mediaId']))
{
	$action = $instagramActions->setLike($arguments['mediaId']);
	return (new InstagramBot\Response('setLike', $arguments, $action));
}

/**
* Set comment
*/
if(isset($arguments['setComment']) && isset($arguments['mediaId']) && isset($arguments['commentText']))
{
	$action = $instagramActions->setComment($arguments['mediaId'], $arguments['commentText']);
	return (new InstagramBot\Response('setComment', $arguments, $action));
}

/**
* Set follow
*/
else if(isset($arguments['setFollow']) && isset($arguments['userId']))
{
	$action = $instagramActions->setFollow($arguments['userId']);
	return (new InstagramBot\Response('setFollow', $arguments, $action));
}

/**
* Unset follow
*/
else if(isset($arguments['unsetFollow']) && isset($arguments['userId']))
{
	$action = $instagramActions->unsetFollow($arguments['userId']);
	return (new InstagramBot\Response('unsetFollow', $arguments, $action));
}

else 
{
	return (new InstagramBot\Response('authorize', $arguments, true, [
		'cookies' => $instagramAuth->getCookies()->toArray()
	]));
}