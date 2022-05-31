<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;
use App\Models\User;
use App\Exceptions\ModelExceptions\UserExceptions\UserNotFoundException;

class UserMapper extends Mapper
{

	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('users');
	}


	public function getUserById(int $user_id):User
	{
		$query_string = "WHERE user_id = :user_id";
		$query_params = [':user_id'=>$user_id];

		$user_params = $this->adapter->find($query_string, $query_params);

		if(!$user_params) throw new UserNotFoundException("Пользователь не найден");

		return new User($user_params);


	}

	public function getUserByLogin(string $user_login):User
	{

		$query_string = "WHERE user_login = :user_login";
		$query_params = [':user_login'=>$user_login];
		$user_params = $this->adapter->find($query_string, $query_params);

		if(!$user_params) throw new UserNotFoundException("Пользователь не найден");

		return new User($user_params);

	}

	public function checkUserByLogin(string $user_login):bool
	{
		$query_string = "WHERE user_login = :user_login";
		$query_params = [':user_login'=>$user_login];
		$user_id = $this->adapter->find($query_string, $query_params, ['user_id']);

		return ($user_id) ? true : false;
	}


	public function createUser(array $params):bool
	{
		return $this->adapter->create($params);
	}


	public function updateUser(User $user):bool
	{
		$user_id = $user->getUserId();
		$query_string = "WHERE user_id = :user_id";
        

		$current_params = $user->getAllProperties();

		$old_params = $this->getUserById($user->getUserId())->getAllProperties();

		$new_params = $this->getUpdatedParams($current_params, $old_params);

		if(!$new_params) return false;

		
		return $this->adapter->update($query_string, $new_params,[':user_id'=>$user->getUserId()]);

	}


	public function deleteUser(User $user):bool
	{
		$user_id = $user->getUserId();
		$query_string = "WHERE user_id = :user_id";
		$query_params = [':user_id'=>$user_id];

		return $this->adapter->delete($query_string, $query_params);
	}

	public function associateWithById(int $user_id, int $associate_user_id):bool
	{
		$query_string = "INNER JOIN users_associates ON (users.user_id = users_associates.user_one_id OR users.user_id = users_associates.user_two_id) WHERE users.user_id = :user_id AND (users_associates.user_one_id = :associate_user_id OR users_associates.user_two_id = :associate_user_id)";

		$query_params = [':user_id'=>$user_id,':associate_user_id'=>$associate_user_id];

		return ($this->adapter->find($query_string,$query_params)) ? true : false;


	}

	public function inGroupByGroupName(int $user_id, string $group_name):bool
	{
		$query_string = "INNER JOIN users_per_groups ON users.user_id = users_per_groups.user_id INNER JOIN groups ON users_per_groups.group_id = groups.group_id WHERE users.user_id = :user_id AND groups.group_name = :group_name";
		$query_params = [':user_id'=>$user_id,':group_name'=>$group_name];

		return ($this->adapter->find($query_string, $query_params)) ? true : false;
	}


}