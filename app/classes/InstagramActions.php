<?php 

class InstagramActions {
	private $_auth;
	private $_headers;

	public function __construct(InstagramAuth $auth)
	{
		$this->_auth = $auth;

		if(!$this->_auth->check())
		{
			throw new Exception("User is not authorized");
		}

		$this->_headers = array_merge($this->_auth->getHeaders(), [
			'X-Instagram-AJAX' => '1',
			'X-CSRFToken' => $this->_auth->getCsrftoken(),
			'X-Requested-With' => 'XMLHttpRequest'
		]);
	}

	public function setLike($mediaId)
	{
		try 
		{
			$response = $this->_auth->getClient()->post('https://instagram.com/web/likes/' . $mediaId . '/like/', [
			    'cookies' => $this->_auth->getCookies(),
			    'headers' => $this->_headers
			]);

			return $this->_checkResponse($response->json());
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function setComment($mediaId, $commentText)
	{
		try 
		{
			$response = $this->_auth->getClient()->post('https://instagram.com/web/comments/' . $mediaId . '/add/', [
			    'cookies' => $this->_auth->getCookies(),
			    'headers' => $this->_headers,
			    'body' => [
			    	'comment_text' => $commentText
			    ]
			]);

			return $this->_checkResponse($response->json());
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function setFollow($userId)
	{
		try 
		{
			$response = $this->_auth->getClient()->post('https://instagram.com/web/friendships/' . $userId . '/follow/', [
			    'cookies' => $this->_auth->getCookies(),
			    'headers' => $this->_headers
			]);

			return $this->_checkResponse($response->json());
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function unsetFollow($userId)
	{
		try 
		{
			$response = $this->_auth->getClient()->post('https://instagram.com/web/friendships/' . $userId . '/unfollow/', [
			    'cookies' => $this->_auth->getCookies(),
			    'headers' => $this->_headers
			]);

			return $this->_checkResponse($response->json());
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	private function _checkResponse($response)
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