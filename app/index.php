<?php 
require_once __DIR__ . '/../vendor/autoload.php';

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

$instagramAuth = new InstagramBot\InstagramAuth($arguments['username'], $arguments['password'], $ip, $userAgent);

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