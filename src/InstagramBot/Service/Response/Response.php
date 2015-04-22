<?php 

namespace InstagramBot\Service\Response;

use InstagramBot\Service\Dispatcher\Dispatcher;
use InstagramBot\Repo\Cookies\Cookies;

class Response implements ResponseInterface {

	protected $dispatcher;
	protected $action;
	protected $response;

	public function __construct(Dispatcher $dispatcher, $action, $status)
	{
		$this->dispatcher = $dispatcher;
		$this->action = $action;
		$this->response = [
			'action' => $this->action,
			'username' => $this->dispatcher->argument('username'),
			'status' => (bool) $status,
			'action' => $action
		];

		$this->getResponse();
	}

	public function login()
	{
		return [
		];
	}

	public function setLike()
	{
		return [
			'mediaId' => $this->dispatcher->argument('mediaId')
		];		
	}

	public function setComment()
	{
		return [
			'mediaId' => $this->dispatcher->argument('mediaId'),
			'commentText' => $this->dispatcher->argument('commentText')
		];	
	}

	public function setFollow()
	{
		return [
			'userId' => $this->dispatcher->argument('userId'),
		];		
	}

	public function unsetFollow()
	{
		return [
			'userId' => $this->dispatcher->argument('userId'),
		];	
	}

	public function getResponse()
	{
		if(method_exists($this, $this->action))
		{
			return array_merge($this->response, $this->{$this->action}());
		}
		else
		{
			return [];
		}
	}

	public function __toString()
	{
		return json_encode($this->getResponse());
	}
}