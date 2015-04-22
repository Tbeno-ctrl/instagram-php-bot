<?php 

namespace InstagramBot\Repo\Cookies;

interface CookiesInterface {

	/**
	*
	*/
	public function get($name);

	/**
	*
	*/
	public function set();

	/**
	*
	*/
	public function toObject();

	/**
	*
	*/
	public function all();
}