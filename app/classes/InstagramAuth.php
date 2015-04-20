<?php 

class InstagramAuth extends Instagram {
	private $_username;
	private $_password;
	private $_client;

	public function __construct($username, $password)
	{
		parent::__construct();
		$this->_username = $username;
		$this->_password = $password;
		$this->_client = new GuzzleHttp\Client();

		$this->run();
	}

	public function getUsername()
	{
		return $this->_username;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function getClient()
	{
		return $this->_client;
	}

	public function check()
	{
		return ($this->findCookie('sessionid') && $this->findCookie('ds_user_id'));
	}

	public function getCsrftoken()
	{
		return $this->findCookie('csrftoken');
	}

	public function run()
	{
		$cookies = $this->getCookies();

		$response = $this->_client->get(self::loginPage, [
		    'cookies' => $cookies,
		    'headers' => $this->getHeaders()
		]);

		if($response->getStatusCode() != 200)
		{
			throw new Exception("Error Processing Request");
		}

		$csrftoken = $this->findCookie('csrftoken');
		if(!$csrftoken)
		{
			throw new Exception("Error Fetching CSRFToken");
		}

		$response = $this->_client->post(self::loginAjax, [
		    'cookies' => $cookies,
		    'headers' => array_merge($this->getHeaders(), [
				'X-Instagram-AJAX' => '1',
				'X-CSRFToken' => $csrftoken,
				'X-Requested-With' => 'XMLHttpRequest'
			]),
			'body' => [
				'username' => $this->getUsername(),
				'password' => $this->getPassword()
			]
		]);

		if($response->getStatusCode() != 200 || !$response->json())
		{
			throw new Exception("Error Processing Request");
		}

		if(isset($response->json()['authenticated']) && $response->json()['authenticated'])
		{
			$this->setCookies($cookies);
			return true;
		}
		else 
		{
			return false;
		}
	}
}