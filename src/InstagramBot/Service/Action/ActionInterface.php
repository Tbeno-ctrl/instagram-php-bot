<?php 
namespace InstagramBot\Service\Action;

interface ActionInterface {
	/**
	*
	*/
	public function setLike($mediaId);

	/**
	*
	*/
	public function setComment($mediaId, $commentText);

	/**
	*
	*/
	public function setFollow($userId);

	/**
	*
	*/
	public function unsetFollow($userId);
}