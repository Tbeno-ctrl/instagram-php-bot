<?php 

namespace InstagramBot\Repo\Responses;

class Responses implements ResponsesInterface {
	protected $responses;

	public function __construct()
	{
		$this->responses = [];
	}

	public function get($name)
	{
		if(isset($this->responses[$name])) 
		{
			return $this->responses[$name];
		}
		else
		{
			return false;
		}
	}

	public function set(Array $responses)
	{
		$this->responses = array_merge($this->responses, $responses);
	}

	public function all()
	{
		return $this->responses;
	}
}