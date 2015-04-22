<?php 
namespace InstagramBot;

class Response {
	private $_action;
	private $_arguments;
	private $_status;
	private $_additional;

	public function __construct($action, $arguments, $status, $additional = [])
	{
		$this->_arguments = $arguments;
		$this->_status = $status;
		$this->_action = $action;
		$this->_additional = $additional;

		echo json_encode(array_merge($this->_parsestatus(), $this->_additional));
	}

	private function _parsestatus()
	{
		$methodName = '_' . $this->_action;

		if(method_exists($this, $methodName))
		{
			return $this->{$methodName}();
		}
		else
		{
			return ['status' => 'false'];
		}
	}

	private function _setComment()
	{
		return [
			'author' => $this->_arguments['username'], 
			'action' => $this->_action, 
			'argument' => $this->_arguments['mediaId'], 
			'status' => $this->_status
		];
	}

	private function _setLike()
	{
		return [
			'author' => $this->_arguments['username'], 
			'action' => $this->_action, 
			'argument' => $this->_arguments['mediaId'], 
			'status' => $this->_status
		];
	}

	private function _setFollow()
	{
		return [
			'author' => $this->_arguments['username'], 
			'action' => $this->_action, 
			'argument' => $this->_arguments['userId'], 
			'status' => $this->_status
		];
	}

	private function _unsetFollow()
	{
		return [
			'author' => $this->_arguments['username'], 
			'action' => $this->_action, 
			'argument' => $this->_arguments['userId'], 
			'status' => $this->_status
		];
	}

	private function _authorize()
	{
		return [
			'author' => $this->_arguments['username'], 
			'action' => $this->_action, 
			'status' => $this->_status
		];
	}
}