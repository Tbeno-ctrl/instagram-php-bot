<?php 

namespace InstagramBot\Repo\Headers;

class Headers implements HeadersInterface {
	protected $headers;

	public function __construct(Array $headers)
	{
		$this->headers = $headers;
	}

	public function get($name)
	{
		if(isset($this->headers[$name]))
		{
			return ;$this->headers[$name];
		}
		else
		{
			return false;
		}
	}

	public function set(Array $headers)
	{
		$this->headers = array_merge($this->headers, $headers);
	}

	public function all()
	{
		return $this->headers;
	}
}