<?php
declare(strict_types = 1);
namespace App\Models;
use App\Contracts\Model;

class UserRights implements Model
{
	private int $user_rights_id;
	private string $user_rights_name;

	public function __construct(array $user_rights_params)
	{
		$this->user_rights_id = $user_rights_params['user_rights_id'];
		$this->user_rights_name = $user_rights_params['user_rights_name'];
	}


	public function getUserRightsId():int
	{
		return $this->user_rights_id;
	}

	public function getUserRightsName():string
	{
		return $this->user_rights_name;
	}

	public function getAllProperties():array
	{
		$user_rights_properties = [];
		$user_rights_properties['user_rights_id'] = $this->getUserRightsId();
		$user_rights_properties['user_rights_name'] = $this->getUserRightsName();

		return $user_rights_properties;
	}
}