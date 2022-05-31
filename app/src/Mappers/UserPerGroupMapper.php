<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;
use App\Models\UserPerGroup;
use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserPerGroupNotFoundException;

class UserPerGroupMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('users_per_groups');
	}



	public function getUserPerGroup(int $user_id, int $group_id):UserPerGroup
	{
		$query_string = "WHERE user_id = :user_id AND group_id = :group_id";
		$query_params = [':user_id'=>$user_id,':group_id'=>$group_id];

		$user_per_group_params = $this->adapter->find($query_string, $query_params);

		if(!$user_per_group_params) throw new UserPerGroupNotFoundException("Пользователь не является членом группы");

		return new UserPerGroup($user_per_group_params);

	}


	public function checkUserPerGroup(int $user_id, int $group_id):bool
	{
		$query_string = "WHERE user_id = :user_id AND group_id = :group_id";
		$query_params = [':user_id'=>$user_id,':group_id'=>$group_id];

		$user_per_group = $this->adapter->find($query_string, $query_params, ['user_per_group_id']);

		return ($user_per_group) ? true : false;
	}



	public function addUserToGroup(array $params):bool
	{
		return $this->adapter->create($params);
	}

	public function removeUserFromGroup(int $user_id):bool
	{
		$query_string = "WHERE user_id = :user_id";
		$query_params = [':user_id'=>$user_id];

		return $this->adapter->delete($query_string, $query_params);
	}
	

	public function updateUserPerGroup(UserPerGroup $user_per_group):bool
	{
		$user_id = $user_per_group->getUserId();
		$group_id = $user_per_group->getGroupId();

		$query_string = "WHERE user_per_group_id = :user_per_group_id";

		$current_params = $user_per_group->getAllProperties();
		$old_params = $this->getUserPerGroup($user_id, $group_id)->getAllProperties();

		$new_params = $this->getUpdatedParams($current_params, $old_params);

		if(!$new_params) return false;

		return $this->adapter->update($query_string, $new_params, [':user_per_group_id'=>$user_per_group->getUserPerGroupId()]);

	}



}