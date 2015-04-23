<?php 

namespace InstagramBot\Repo\Responses;

interface ResponsesInterface {

	/**
	*
	*/
	public function get($name);

	/**
	*
	*/
	public function set(Array $responses);

	/**
	*
	*/
	public function all();
}