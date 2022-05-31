<?php
declare(strict_types = 1);
namespace App\Models;
use App\Contracts\Model;

class TaskStatus implements Model
{
	private int $task_status_id;
	private string $task_status_name;

	public function __construct(array $task_status_params)
	{
		$this->task_status_id = $task_status_params['task_status_id'];
		$this->task_status_name = $task_status_params['task_status_name'];
	}



	public function getTaskStatusId():int
	{
		return $this->task_status_id;
	}

	public function getTaskStatusName():string
	{
		return $this->task_status_name;
	}


	public function getAllProperties():array
	{
		$task_status_properties = [];

		$task_status_properties['task_status_id'] = $this->getTaskStatusId();
		$task_status_properties['task_status_name'] = $this->getTaskStatusName();

		return $task_status_properties;
	}

}