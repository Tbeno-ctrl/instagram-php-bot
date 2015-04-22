<?php 

namespace InstagramBot\Service\Auth;

interface AuthInterface {
	/**
	*
	*/
	public function login($username, $password);

	/**
	*
	*/
	public function logout();

	/**
	*
	*/
	public function check();

	/**
	*
	*/
	public function details();
}