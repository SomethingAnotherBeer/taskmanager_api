<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;

class UsersMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('users');
	}


	public function getAllUsers(int $limit = 0, int $offset = 0):array
	{
		$users = [];

		$base_query_string = "INNER JOIN user_rights ON users.user_rights_id = user_rights.user_rights_id";
		$base_query_params = [];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$users = $this->adapter->findAll($base_query_params, ['users.user_login','user_rights.user_rights_name'],$base_query_string);

		return $users;

	}



	public function getUsersByStatus(string $user_rights_name, int $limit = 0, int $offset = 0):array
	{	
		$base_query_string = "INNER JOIN user_rights ON users.user_rights_id = user_rights.user_rights_id WHERE user_rights.user_rights_name = :user_rights_name";
		$base_query_params =[':user_rights_name'=>$user_rights_name];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$users = $this->adapter->findAll($base_query_params, ['users.user_login','user_rights.user_rights_name'],$base_query_string);

		return $users;
	}

	public function getUsersByGroup(string $group_name, int $limit = 0, int $offset = 0):array
	{
		$base_query_string = "INNER JOIN user_rights ON users.user_rights_id = user_rights.user_rights_id INNER JOIN users_per_groups ON users.user_id = users_per_groups.user_id INNER JOIN groups ON users_per_groups.group_id = groups.group_id WHERE groups.group_name = :group_name";

		$base_query_params = [':group_name'=>$group_name];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$users = $this->adapter->findAll($base_query_params,['users.user_login, user_rights.user_rights_name, groups.group_name'],$base_query_string);

		return $users;
	}


	public function getOutRequestsOnAssociate(int $user_id):array
	{
		$base_query_string = "INNER JOIN users_associates ON users.user_id = users_associates.user_one_id WHERE users.user_id = :user_id AND users_associates.associate_status = 'request'";
		$base_query_params = [':user_id'=>$user_id];

		$users = $this->adapter->findAll($base_query_params,['users.user_login'],$base_query_string);

		return $users;

	}

	public function getInRequestsOnAssociate(int $user_id):array
	{
		$base_query_string = "INNER JOIN users_associates ON users.user_id = users_associates.user_two_id WHERE users.user_id = :user_id AND users_associates.associate_status = 'request'";
		$base_query_params = [':user_id'=>$user_id];

		$users = $this->adapter->findAll($base_query_params,['users.user_login'],$base_query_string);

		return $users;
	}

	public function getAssociatedUsers(int $user_id, int $limit = 0, int $offset = 0):array
	{
		$base_query_string = "INNER JOIN users_associates ON (users.user_id = users_associates.user_one_id OR users.user_id = users_associates.user_two_id) WHERE users.user_id = :user_id AND users_associates.associate_status = 'associate'";
		$base_query_params = [':user_id'=>$user_id];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}
		

		$users = $this->adapter->findAll($base_query_params, ['users.user_login'],$base_query_string);

		return $users;
		
	}


	public function getUsersCountFromGroup(string $group_name):int
	{
		$base_query_string = "INNER JOIN users_per_groups ON users.user_id = users_per_groups.user_id INNER JOIN groups ON users_per_groups.group_id = groups.group_id WHERE groups.group_name = :group_name";
		$base_query_params = [':group_name'=>$group_name];

		$users_count = $this->adapter->getCount($base_query_string, $base_query_params);

		return $users_count;


	}

}