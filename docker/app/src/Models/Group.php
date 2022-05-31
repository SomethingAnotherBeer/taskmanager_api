<?php
declare(strict_types = 1);
namespace App\Models;

class Group
{
	private int $group_id;
	private string $group_name;

	public function __construct(array $group_params)
	{
		$this->group_id = $group_params['group_id'];
		$this->group_name = $group_params['group_name'];
	}

	public function getGroupId():int
	{
		return $this->group_id;
	}

	public function getGroupName():string
	{
		return $this->group_name;
	}


	public function setGroupName(string $group_name):void
	{		
		$this->group_name = $group_name;
	}	


	public function getAllProperties():array
	{
		$group_properties = [];

		$group_properties['group_id'] = $this->getGroupId();
		$group_properties['group_name'] = $this->getGroupName();

		return $group_properties;
	}

}