<?php 

namespace InstagramBot\Repo\Cookies;

use GuzzleHttp\Cookie\CookieJar;

class Cookies Implements CookiesInterface {

	protected $cookies;

	public function __construct(CookieJar $cookies)
	{
		$this->cookies = $cookies;
	}

	public function toObject()
	{
		return $this->cookies;
	}

	public function get($name)
	{
		$filtered = array_filter($this->all(), function($cookie) use ($name) {
        	return ($cookie['Name'] == $name);
    	});
    	$filtered = array_values($filtered);

    	return ($filtered) ? $filtered[0]['Value'] : false;
	}

	public function set()
	{
		
	}

	public function all()
	{
		return $this->cookies->toArray();
	}
}