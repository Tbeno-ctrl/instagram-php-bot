<?php 

namespace InstagramBot\Service\Action;

use GuzzleHttp\Client;
use InstagramBot\Service\Auth\InstagramAuth;
use InstagramBot\Repo\Cookies\Cookies;

class InstagramAction implements ActionInterface {
	protected $client;
	protected $cookies;
	protected $auth;

	public function __construct(Client $client, Cookies $cookies, InstagramAuth $auth)
	{
		if(!$auth->check())
		{
			throw new \Exception("User is not authorized");
		}

		$this->auth = $auth;
		$this->cookies = $cookies;
	}

	public function setLike($mediaId)
	{
		$response = $this->_auth->getClient()->post('https://instagram.com/web/likes/' . $mediaId . '/like/', [
			'cookies' => $this->cookies->toObject(),
			'headers' => $this->headers->all()
		]);

		return $this->_checkResponse($response->json());
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
		if(!isset($response['status']))
		{
			throw new Exception("Error handling request :" . debug_backtrace()[1]['function']);
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