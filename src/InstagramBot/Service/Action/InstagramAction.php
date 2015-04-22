<?php 

namespace InstagramBot\Service\Action;

use GuzzleHttp\Client;
use InstagramBot\Service\Auth\InstagramAuth;
use InstagramBot\Repo\Cookies\Cookies;
use InstagramBot\Repo\Headers\Headers;

class InstagramAction implements ActionInterface {
	protected $client;
	protected $cookies;
	protected $auth;

	public function __construct(Client $client, Cookies $cookies, Headers $headers, InstagramAuth $auth)
	{
		if(!$auth->check())
		{
			throw new \Exception("User is not authorized");
		}

		$this->client = $client;
		$this->cookies = $cookies;
		$this->headers = $headers;
		$this->auth = $auth;
	}

	public function setLike($mediaId)
	{
		var_dump($this->cookies->all());
		die();

		$response = $this->client->post("https://instagram.com/web/likes/{$mediaId}/like/", [
			'cookies' => $this->cookies->toObject(),
			'headers' => $this->headers->all()
		]);

		$this->checkResponse($response);
	}

	public function setComment($mediaId, $commentText)
	{

	}

	public function setFollow($userId)
	{

	}

	public function unsetFollow($userId)
	{

	}

	private function checkResponse($response)
	{
		$response = $response->getStatusCode();

		if(!isset($response['status']))
		{
			throw new \Exception("Error handling request : " . debug_backtrace()[1]['function']);
		}

		if($response['status'] == 'ok')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}