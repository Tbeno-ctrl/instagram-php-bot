<?php 
require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$cookies = new GuzzleHttp\Cookie\CookieJar;
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

$client = new GuzzleHttp\Client;
$cookies = new InstagramBot\Repo\Cookies\Cookies($cookies);
$headers = new InstagramBot\Repo\Headers\Headers($headers);

$auth = new InstagramBot\Service\Auth\InstagramAuth($client, $cookies, $headers);
// $auth->login('evointeractive', 'success2017');

// $action = new InstagramBot\Service\Action\InstagramAction($client, $cookies, $headers, $auth);

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

$dispatcher = new InstagramBot\Service\Dispatcher\Dispatcher($arguments);

if($dispatcher->setComment())
{
	var_dump($action->setComment($dispatcher->argument('mediaId'), $dispatcher->argument('commentText')));
}

if($dispatcher->setLike())
{
	var_dump($action->setLike($dispatcher->argument('mediaId')));
}

if($dispatcher->setFollow())
{
	var_dump($action->setFollow($dispatcher->argument('userId')));
}

if($dispatcher->unsetFollow())
{
	var_dump($action->unsetFollow($dispatcher->argument('userId')));
}