<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;
use App\Models\Group;
use App\Exceptions\ModelExceptions\GroupExceptions\GroupNotFoundException;

class GroupMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('groups');
	}


	public function getGroupById(int $group_id):Group
	{
		$group_params = [];

		$query_string = "WHERE group_id = :group_id";
		$query_params = [':group_id'=>$group_id];

		$group_params = $this->adapter->find($query_string, $query_params);

		if(!$group_params) throw new GroupNotFoundException("Группа не найдена");

		return new Group($group_params);

	}



	public function getGroupByName(string $group_name):Group
	{
		$group_params = [];

		$query_string = "WHERE group_name = :group_name";
		$query_params = [':group_name'=>$group_name];

		$group_params = $this->adapter->find($query_string, $query_params);

		if(!$group_params) throw new GroupNotFoundException("Группа не найдена");

		return new Group($group_params);

	}

	public function checkGroupByName(string $group_name):bool
	{
		$query_string = "WHERE group_name = :group_name";
		$query_params = [':group_name'=>$group_name];

		$group_id = $this->adapter->find($query_string, $query_params, ['group_id']);

		return ($group_id) ? true : false;
	}


	public function createGroup(array $group_params):bool
	{
		return $this->adapter->create($group_params);
	}


	public function updateGroup(Group $group):bool
	{
		$group_id = $group->getGroupId();
		$query_string = "WHERE group_id = :group_id";

		$current_params = $group->getAllProperties();
		$old_params = $this->getGroupById($group_id)->getAllProperties();

		$new_params = $this->getUpdatedParams($current_params, $old_params);

		if(!$new_params) return false;

		return $this->adapter->update($query_string, $new_params,[':group_id'=>$group_id]);

	}

	public function deleteGroup(Group $group):bool
	{
		$query_string = "WHERE group_name = :group_name";
		$query_params = [':group_name'=>$group->getGroupName()];

		return $this->adapter->delete($query_string, $query_params);
	}




}