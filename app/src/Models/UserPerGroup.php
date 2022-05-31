<?php
declare(strict_types = 1);
namespace App\Models;

class UserPerGroup
{
	private int $user_per_group_id;
	private int $user_id;
	private int $group_id;
	private bool $is_admin;
	private string $user_status;


	public function __construct(array $user_per_group_params)
	{
		$this->user_per_group_id = $user_per_group_params['user_per_group_id'];
		$this->user_id = $user_per_group_params['user_id'];
		$this->group_id = $user_per_group_params['group_id'];
		$this->is_admin = $user_per_group_params['is_admin'];
		$this->user_status = $user_per_group_params['user_status'];
	}


	public function getUserPerGroupId():int
	{
		return $this->user_per_group_id;

	}

	public function getUserId():int
	{
		return $this->user_id;
	}

	public function getGroupId():int
	{
		return $this->group_id;
	}

	public function getAdminStatus():bool
	{
		return $this->is_admin;
	}

	public function getUserStatus():string
	{
		return $this->user_status;
	}



	public function setUserId(int $user_id):void
	{
		$this->user_id = $user_id;
	}

	public function setGroupId(int $group_id):void
	{
		$this->group_id = $group_id;
	}

	public function setAdminStatus(bool $is_admin):void
	{
		$this->is_admin = $is_admin;
	}

	public function setUserStatus(string $user_status):void
	{
		$this->user_status = $user_status;
	}


	public function getAllProperties():array
	{
		$user_per_group_properties = [];

		$user_per_group_properties['user_per_group_id'] = $this->getUserPerGroupId();
		$user_per_group_properties['user_id'] = $this->getUserId();
		$user_per_group_properties['group_id'] = $this->getGroupId();
		$user_per_group_properties['is_admin'] = $this->getAdminStatus();
		$user_per_group_properties['user_status'] = $this->getUserStatus();

		return $user_per_group_properties;
	}


}