<?php 
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

//autoloader
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'classes/Instagram.php';
require_once 'classes/InstagramAuth.php';
require_once 'classes/InstagramActions.php';

$arguments = getopt('', [
	'username:', 
	'password:', 
	'mediaId::', 
	'userId::', 
	'commentText::',
	'setLike::',
	'setFollow::',
	'unsetFollow::',
	'setComment::'
]);

/**
* Authorize first
*/
if(!isset($arguments['username']) || !isset($arguments['password']))
{
	throw new Exception('--username and --password parameters are obligatory');
}

$instagramAuth = new InstagramAuth($arguments['username'], $arguments['password']);

if(!$instagramAuth->check())
{
	throw new Exception('User not logged in');
}
$instagramActions = new InstagramActions($instagramAuth);

/**
* Set like
*/
if(isset($arguments['setLike']) && isset($arguments['mediaId']))
{
	var_dump($instagramActions->setLike($arguments['mediaId']));
}

/**
* Set comment
*/
if(isset($arguments['setComment']) && isset($arguments['mediaId']) && isset($arguments['commentText']))
{
	var_dump($instagramActions->setComment($arguments['mediaId'], $arguments['commentText']));
}

/**
* Set follow
*/
if(isset($arguments['setFollow']) && isset($arguments['userId']))
{
	var_dump($instagramActions->setFollow($arguments['userId']));
}

/**
* Unset follow
*/
if(isset($arguments['unsetFollow']) && isset($arguments['userId']))
{
	var_dump($instagramActions->unsetFollow($arguments['userId']));
}