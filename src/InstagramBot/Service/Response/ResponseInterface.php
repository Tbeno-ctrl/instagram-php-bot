<?php 

namespace InstagramBot\Service\Response;

interface ResponseInterface {

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

	/**
	*
	*/
	public function getResponse();
}