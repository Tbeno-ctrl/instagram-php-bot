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
	'cookies::',
	'ip::',
	'userAgent::'
]);
$dispatcher = new InstagramBot\Service\Dispatcher\Dispatcher($arguments);

$storageFile = __DIR__ . "/../storage/{$dispatcher->argument('username')}.txt";

//remove cookies file if update date > 1
if(file_exists($storageFile) && (time() - filectime($storageFile)) > 60 * 60) 
{
	unlink($storageFile);
}

$cookies = new GuzzleHttp\Cookie\FileCookieJar($storageFile);

$client = new GuzzleHttp\Client;
$cookies = new InstagramBot\Repo\Cookies\Cookies($cookies);
$headers = new InstagramBot\Repo\Headers\Headers($headers);

$responses = new InstagramBot\Repo\Responses\Responses;
$response = new InstagramBot\Service\Response\Response($dispatcher, $responses);

$responses->set([
	'username' => $dispatcher->argument('username')
]);

if($dispatcher->login())
{
	$auth = new InstagramBot\Service\Auth\InstagramAuth($client, $cookies, $headers);
	$auth->login($dispatcher->argument('username'), $dispatcher->argument('password'));
	$action = new InstagramBot\Service\Action\InstagramAction($client, $cookies, $headers, $auth);
}
else
{
	$responses->set([
		'status' => false,
		'action' => 'login'
	]);
	echo $response; die;
}

if($dispatcher->setComment())
{
	try {
		$status = $action->setComment($dispatcher->argument('mediaId'), $dispatcher->argument('commentText'));
	} catch (Exception $e) {
		$responses->set([
			'status' => false,
			'action' => 'setComment',
			'error' => $e->getMessage()
		]);
		echo $response;
	}
}

else if($dispatcher->setLike())
{
	try {
		$status = $action->setLike($dispatcher->argument('mediaId'));
	} catch (Exception $e) {
		$responses->set([
			'status' => false,
			'action' => 'setLike',
			'error' => $e->getMessage()
		]);
		echo $response;
	}
}

else if($dispatcher->setFollow())
{
	try {
		$status = $action->setFollow($dispatcher->argument('userId'));
	} catch (Exception $e) {
		$responses->set([
			'status' => false,
			'action' => 'setFollow',
			'error' => $e->getMessage()
		]);
		echo $response;
	}
}

else if($dispatcher->unsetFollow())
{
	try {
		$status = $action->unsetFollow($dispatcher->argument('userId'));
	} catch (Exception $e) {
		$responses->set([
			'status' => false,
			'action' => 'unsetFollow',
			'error' => $e->getMessage()
		]);
		echo $response;
	}
}

else
{
	$responses->set([
		'status' => true,
		'action' => 'login'
	]);
	echo $response;
}