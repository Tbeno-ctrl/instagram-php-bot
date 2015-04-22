<?php 

namespace InstagramBot\Service\Auth;

use GuzzleHttp\Client;
use InstagramBot\Repo\Cookies\Cookies;
use InstagramBot\Repo\Headers\Headers;

class InstagramAuth implements AuthInterface {

	public static $loginPage = 'https://instagram.com/accounts/login/';
	public static $loginAjax = 'https://instagram.com/accounts/login/ajax/';
	
	protected $client;
	protected $cookies;
	protected $headers;

	public function __construct(Client $client, Cookies $cookies, Headers $headers)
	{
		$this->client = $client;
		$this->cookies = $cookies;
		$this->headers = $headers;
	}

	public function login($username, $password)
	{
		if($this->check())
		{
			return true;
		}

		$response = $this->client->get(self::$loginPage, [
			'cookies' => $this->cookies->toObject(),
			'headers' => $this->headers->all()
		]);

		if($response->getStatusCode() != 200)
		{
			throw new \Exception("Error sending initial request to instagram");
		}

		if(!$this->cookies->get('csrftoken'))
		{
			throw new \Exception("Csrf token has not been found");
		}

		$headers = array_merge($this->headers->all(), [
			'X-CSRFToken' => $this->cookies->get('csrftoken'),
			'X-Instagram-AJAX' => '1',
			'X-Requested-With' => 'XMLHttpRequest'
		]);

		$response = $this->client->post(self::$loginAjax, [
		    'cookies' => $this->cookies->toObject(),
		    'headers' => $headers,
			'body' => [
				'username' => $username,
				'password' => $password
			]
		]);

		if($response->getStatusCode() != 200)
		{
			throw new \Exception("Error sending authentication request to instagram");
		}

		if(isset($response->json()['authenticated']) && $response->json()['authenticated'])
		{
			$this->headers->set([
				'X-CSRFToken' => $this->cookies->get('csrftoken')
			]);

			return true;
		}
		else 
		{
			return false;
		}
	}

	public function logout()
	{

	}

	public function check()
	{
		return ($this->cookies->get('sessionid') && $this->cookies->get('ds_user_id'));
	}

	public function details()
	{

	}
}