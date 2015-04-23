<?php 

namespace InstagramBot\Service\Response;

use InstagramBot\Service\Dispatcher\Dispatcher;
use InstagramBot\Repo\Responses\Responses;

class Response implements ResponseInterface {

	protected $dispatcher;
	protected $response;

	public function __construct(Dispatcher $dispatcher, Responses $response)
	{
		$this->dispatcher = $dispatcher;
		$this->response = $response;
	}

	public function login()
	{
	}

	public function setLike()
	{
		$this->response->set([
			'mediaId' => $this->dispatcher->argument('mediaId')
		]);
	}

	public function setComment()
	{
		$this->response->set([
			'mediaId' => $this->dispatcher->argument('mediaId'),
			'commentText' => $this->dispatcher->argument('commentText')
		]);
	}

	public function setFollow()
	{
		$this->response->set([
			'userId' => $this->dispatcher->argument('userId'),
		]);	
	}

	public function unsetFollow()
	{
		$this->response->set([
			'userId' => $this->dispatcher->argument('userId'),
		]);	
	}

	public function getResponse()
	{
		if(method_exists($this, $this->response->get('action')))
		{
			return $this->response->all();
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