<?php 

namespace InstagramBot\Service\Dispatcher;

class Dispatcher implements DispatcherInterface {

	protected $arguments;

	public function __construct(Array $arguments)
	{
		$this->arguments = $arguments;

		var_dump($arguments);
	}

	public function argument($name)
	{
		if(isset($this->arguments[$name]))
		{
			return $this->arguments[$name];
		}
		else
		{
			return false;
		}
	}

	public function login()
	{
		if(empty($this->arguments['username']) || empty($this->arguments['password']))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function setLike()
	{
		if(!$this->login)
		{
			return false;
		}

		if(!isset($this->arguments['setLike']) || empty($this->arguments['mediaId']))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function setComment()
	{
		if(!$this->login)
		{
			return false;
		}

		if(!isset($this->arguments['setComment']) || empty($this->arguments['mediaId']) || empty($this->arguments['commentText']))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function setFollow()
	{
		if(!$this->login)
		{
			return false;
		}

		if(!isset($this->arguments['setFollow']) || empty($this->arguments['userId']))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function unsetFollow()
	{
		if(!$this->login)
		{
			return false;
		}

		if(!isset($this->arguments['unsetFollow']) || empty($this->arguments['userId']))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}