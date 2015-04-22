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

		//adjusting for ajax requests
		$this->headers->set([
			'X-Instagram-AJAX' => '1',
			'X-Requested-With' => 'XMLHttpRequest'
		]);
	}

	public function setLike($mediaId)
	{
		$response = $this->client->post("https://instagram.com/web/likes/{$mediaId}/like/", [
			'cookies' => $this->cookies->toObject(),
			'headers' => $this->headers->all()
		]);

		return $this->checkResponse($response);
	}

	public function setComment($mediaId, $commentText)
	{
		$response = $this->client->post("https://instagram.com/web/comments/{$mediaId}/add/", [
			'cookies' => $this->cookies->toObject(),
			'headers' => $this->headers->all(),
			'body' => [
				'comment_text' => $commentText
			]
		]);

		return $this->checkResponse($response);
	}

	public function setFollow($userId)
	{
		$response = $this->client->post("https://instagram.com/web/friendships/{$userId}/follow/", [
			'cookies' => $this->cookies->toObject(),
			'headers' => $this->headers->all()
		]);

		return $this->checkResponse($response);
	}

	public function unsetFollow($userId)
	{
		$response = $this->client->post("https://instagram.com/web/friendships/{$userId}/unfollow/", [
			'cookies' => $this->cookies->toObject(),
			'headers' => $this->headers->all()
		]);

		return $this->checkResponse($response);
	}

	private function checkResponse($response)
	{
		if($response->getStatusCode() != 200)
		{
			throw new \Exception("Error handling request : " . debug_backtrace()[1]['function']);
		}

		$response = $response->json();

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