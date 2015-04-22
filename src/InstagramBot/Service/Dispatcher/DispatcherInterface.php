<?php 

namespace InstagramBot\Service\Dispatcher;

interface DispatcherInterface {
	
	/**
	*
	*/
	public function argument($name);

	/**
	*
	*/
	public function login();

	/**
	*
	*/
	public function setLike();

	/**
	*
	*/
	public function setComment();

	/**
	*
	*/
	public function setFollow();

	/**
	*
	*/
	public function unsetFollow();
}