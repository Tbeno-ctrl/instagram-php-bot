<?php 
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

$dispatcher = new InstagramBot\Service\Dispatcher\Dispatcher($_GET);

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
		$responses->set([
			'status' => true,
			'mediaId' => $dispatcher->argument('mediaId'),
			'action' => 'setComment'
		]);
	} catch (Exception $e) {
		$responses->set([
			'status' => false,
			'mediaId' => $dispatcher->argument('mediaId'),
			'action' => 'setComment',
			'error' => $e->getMessage()
		]);
	}
	echo $response;
}

else if($dispatcher->setLike())
{
	try {
		$status = $action->setLike($dispatcher->argument('mediaId'));
		$responses->set([
			'status' => true,
			'mediaId' => $dispatcher->argument('mediaId'),
			'action' => 'setLike'
		]);
	} catch (Exception $e) {
		$responses->set([
			'status' => false,
			'mediaId' => $dispatcher->argument('mediaId'),
			'action' => 'setLike',
			'error' => $e->getMessage()
		]);
	}
	echo $response;
}

else if($dispatcher->setFollow())
{
	try {
		$status = $action->setFollow($dispatcher->argument('userId'));
		$responses->set([
			'status' => true,
			'userId' => $dispatcher->argument('userId'),
			'action' => 'setFollow'
		]);
	} catch (Exception $e) {
		$responses->set([
			'status' => false,
			'userId' => $dispatcher->argument('userId'),
			'action' => 'setFollow',
			'error' => $e->getMessage()
		]);
	}
	echo $response;
}

else if($dispatcher->unsetFollow())
{
	try {
		$status = $action->unsetFollow($dispatcher->argument('userId'));
		$responses->set([
			'status' => true,
			'userId' => $dispatcher->argument('userId'),
			'action' => 'unsetFollow'
		]);
	} catch (Exception $e) {
		$responses->set([
			'status' => false,
			'userId' => $dispatcher->argument('userId'),
			'action' => 'unsetFollow',
			'error' => $e->getMessage()
		]);
	}

	echo $response;
}

else
{
	$responses->set([
		'status' => true,
		'action' => 'login'
	]);
	echo $response;
}