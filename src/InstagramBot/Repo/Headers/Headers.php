<?php 

namespace InstagramBot\Repo\Headers;

class Headers implements HeadersInterface {
	protected $headers;

	public function __construct(Array $headers)
	{
		$this->headers = $headers;
	}

	public function get()
	{

	}

	public function set()
	{
		
	}

	public function all()
	{
		return $this->headers;
	}

	public function build()
	{
		
	}
}