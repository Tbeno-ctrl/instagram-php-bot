<?php 
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