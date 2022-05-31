<?php
declare(strict_types = 1);
namespace App\Models;

class UsersAssociate
{
	private int $users_associate_id;
	private int $user_one_id;
	private int $user_two_id;
	private string $associate_status;


	public function __construct(array $users_associate_params)
	{
		$this->users_associate_id = $users_associate_params['users_associate_id'];
		$this->user_one_id = $users_associate_params['user_one_id'];
		$this->user_two_id = $users_associate_params['user_two_id'];
		$this->associate_status = $users_associate_params['associate_status'];
	}
	
	public function getUsersAssociateId():int
	{
		return $this->users_associate_id;
	}


	public function getUserOneId():int
	{
		return $this->user_one_id;
	}

	public function getUserTwoId():int
	{
		return $this->user_two_id;
	}

	public function getAssociateStatus():string
	{
		return $this->associate_status;
	}

	public function setUserOneId(int $user_one_id):void
	{
		$this->user_one_id = $user_one_id;
	}

	public function setUserTwoId(int $user_two_id):void
	{
		$this->user_two_id = $user_two_id;
	}

	public function setAssociateStatus(string $associate_status):void
	{
		$this->associate_status = $associate_status;
 	}


 	public function getAllProperties():array
 	{
 		$users_associate_properties = [];

 		$users_associate_properties['users_associate_id'] = $this->getUsersAssociateId();
 		$users_associate_properties['user_one_id'] = $this->getUserOneId();
 		$users_associate_properties['user_two_id'] = $this->getUserTwoId();
 		$users_associate_properties['associate_status'] = $this->getAssociateStatus();

 		return $users_associate_properties;
 	}

}