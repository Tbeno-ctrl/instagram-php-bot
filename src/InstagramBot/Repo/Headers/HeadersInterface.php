<?php 

namespace InstagramBot\Repo\Headers;

interface HeadersInterface {

	/**
	*
	*/
	public function get($name);

	/**
	*
	*/
	public function set(Array $headers);

	/**
	*
	*/
	public function all();
}