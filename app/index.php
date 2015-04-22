<?php 
require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$headers = [
	'Origin' => 'https://instagram.com',
	'Accept-Encoding' => 'gzip, deflate',
	'Accept-Language' => 'en-US,en;q=0.8,ru;q=0.6,pl;q=0.4',
	'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36',
	'Content-Type' => 'application/x-www-form-urlencoded',
	'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
	'Cache-Control' => 'max-age=0',
	'Referer' => 'https://instagram.com/accounts/login/',
	'Connection' => 'keep-alive'
];

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

$client = new GuzzleHttp\Client;
$cookies = new GuzzleHttp\Cookie\CookieJar;
$cookies = new InstagramBot\Repo\Cookies\Cookies($cookies);
$headers = new InstagramBot\Repo\Headers\Headers($headers);
$dispatcher = new InstagramBot\Service\Dispatcher\Dispatcher($arguments);

if($dispatcher->login())
{
	$auth = new InstagramBot\Service\Auth\InstagramAuth($client, $cookies, $headers);
	$auth->login($dispatcher->argument('username'), $dispatcher->argument('password'));
	$action = new InstagramBot\Service\Action\InstagramAction($client, $cookies, $headers, $auth);
}
else
{
	throw new Exception("username / password is not provided");
}

if($dispatcher->setComment())
{
	$status = $action->setComment($dispatcher->argument('mediaId'), $dispatcher->argument('commentText'));
	$action = 'setComment';
}

else if($dispatcher->setLike())
{
	$status = $action->setLike($dispatcher->argument('mediaId'));
	$action = 'setLike';
}

else if($dispatcher->setFollow())
{
	$status = $action->setFollow($dispatcher->argument('userId'));
	$action = 'setFollow';
}

else if($dispatcher->unsetFollow())
{
	$status = $action->unsetFollow($dispatcher->argument('userId'));
	$action = 'unsetFollow';
}

else
{
	$status = true;
	$action = 'login';
}

if(isset($status) && isset($action))
{
	echo new InstagramBot\Service\Response\Response($dispatcher, $cookies, $action, $status);
}