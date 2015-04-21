<?php
namespace InstagramBot;

class Instagram {
	const loginPage = 'https://instagram.com/accounts/login/';
	const loginAjax = 'https://instagram.com/accounts/login/ajax/';
	private $_defaultHeaders = [
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
	private $_headers;
	private $_cookies;

	public function __construct()
	{
		$this->_cookies = new \GuzzleHttp\Cookie\CookieJar();
		$this->resetHeaders();
	}

	public function addHeader($key, $value)
	{
		$this->_headers[$key] = $value;
	}

	public function removeHeader($key)
	{
		unset($this->_headers[$key]);
	}

	public function resetHeaders()
	{
		$this->_headers = $this->_defaultHeaders;
	}

	public function getHeaders()
	{
		return $this->_headers;
	}

	public function setIp($ip)
	{
		if($ip)
		{
			$this->_headers['X-Forwarded-For'] = $ip;
		}
	}

	public function setUserAgent($userAgent)
	{
		if($userAgent)
		{
			$this->_headers['User-Agent'] = $userAgent;
		}
	}

	public function setCookies(GuzzleHttp\Cookie\CookieJar $jar)
	{
		$this->_cookies = $jar;
	}

	public function getCookies()
	{
		return $this->_cookies;
	}

	public function findCookie($name)
	{
    	$filtered = array_filter($this->getCookies()->toArray(), function($cookie) use ($name) {
            	return ($cookie['Name'] == $name);
    	});
    	$filtered = array_values($filtered);

    	return ($filtered) ? $filtered[0]['Value'] : false;
	}

}
