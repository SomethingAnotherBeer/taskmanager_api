<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;
use App\Models\UsersAssociate;

use App\Exceptions\ModelExceptions\UsersAssociateExceptions\UsersAssociateNotFoundException;

class UsersAssociateMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('users_associates');
	}



	public function getUsersAssociate(int $user_one_id, int $user_two_id):UsersAssociate
	{
		$users_associate_params = [];

		$query_string = "WHERE user_one_id = :user_one_id AND user_two_id = :user_two_id";
		$query_params = [':user_one_id'=>$user_one_id,':user_two_id'=>$user_two_id];

		$users_associate_params = $this->adapter->find($query_string, $query_params);

		if(!$users_associate_params) throw new UsersAssociateNotFoundException("Ассоциация пользователей не найдена");

		return new UsersAssociate($users_associate_params);
	}

	public function getUsersExtendedAssociate(int $user_one_id, $user_two_id):UsersAssociate
	{
		$users_associate_params = [];

		$query_string = "WHERE (user_one_id = :user_one_id AND user_two_id = :user_two_id) OR (user_one_id = :user_two_id AND user_two_id = :user_one_id)";
		$query_params = [':user_one_id'=>$user_one_id,':user_two_id'=>$user_two_id];

		$users_associate_params = $this->adapter->find($query_string, $query_params);

		if(!$users_associate_params) throw new UsersAssociateNotFoundException("Ассоциация пользователей не найдена");

		return new UsersAssociate($users_associate_params);
	}


	public function checkUsersAssociate(int $user_one_id, int $user_two_id):bool
	{
		$query_string = "WHERE user_one_id = :user_one_id AND user_two_id = :user_two_id";
		$query_params = [':user_one_id'=>$user_one_id,':user_two_id'=>$user_two_id];

		$users_associate = $this->adapter->find($query_string, $query_params,['users_associate_id']);

		return ($users_associate) ? true : false;
	}


	public function createUsersAssociate(array $users_associate_params):bool
	{
		return $this->adapter->create($users_associate_params);
	}

	public function removeUsersAssociate(UsersAssociate $users_associate):bool
	{
		$query_string = "WHERE users_associate_id = :users_associate_id";
		$query_params = [':users_associate_id'=>$users_associate->getUsersAssociateId()];

		return $this->adapter->delete($query_string, $query_params);
	}


	public function updateUsersAssociate(UsersAssociate $users_associate):bool
	{
		$users_associate_id = $users_associate->getUsersAssociateId();


		$query_string = "WHERE users_associate_id = :users_associate_id";
		$current_params = $users_associate->getAllProperties();
		$old_params = $this->getUsersAssociate($users_associate->getUserOneId(), $users_associate->getUserTwoId())->getAllProperties();

		$new_params = $this->getUpdatedParams($current_params, $old_params);

		if(!$new_params) return false;

		return $this->adapter->update($query_string, $new_params,[':users_associate_id'=>$users_associate_id]);

	}


}