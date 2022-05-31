<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;

class GroupsMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('groups');
	}


	public function getAllGroups(int $limit, int $offset):array
	{
		$groups = [];
		$base_query_params = [];
		$base_query_string = '';

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		return $this->adapter->findAll($base_query_params,['group_name'],$base_query_string);

	}


	public function getGroupsByUserLogin(string $user_login, int $limit, int $offset):array
	{
		$groups = [];
		$base_query_string = "INNER JOIN users_per_groups ON groups.group_id = users_per_groups.group_id INNER JOIN users ON users_per_groups.user_id = users.user_id WHERE users.user_login = :user_login";
		$base_query_params =[':user_login'=>$user_login];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$groups = $this->adapter->findAll($base_query_params,['groups.group_name'],$base_query_string);

		return $groups;

	}

	
	public function getGroupsByUserId(int $user_id, int $limit, int $offset):array
	{
		$groups = [];

		$base_query_string = "INNER JOIN users_per_groups ON groups.group_id = users_per_groups.group_id WHERE users_per_groups.user_id = :user_id";
		$base_query_params =[':user_id'=>$user_id];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$groups = $this->adapter->findAll($base_query_params,['groups.group_name'],$base_query_string);

		return $groups;

	}


}