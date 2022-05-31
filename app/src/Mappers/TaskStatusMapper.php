<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;
use App\Models\TaskStatus;

use App\Exceptions\ModelExceptions\TaskExceptions\TaskStatusNotFoundException;

class TaskStatusMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->adapter->setEntity('task_statuses');
	}

	public function getTaskStatusById(int $task_status_id):TaskStatus
	{
		$query_string = "WHERE task_status_id = :task_status_id";
		$query_params = [':task_status_id'=>$task_status_id];

		$task_status_params = $this->adapter->find($query_string, $query_params);

		if(!$task_status_params) throw new TaskStatusNotFoundException("Статус задачи не найден");

		return new TaskStatus($task_status_params);
	}


	public function getTaskStatusByName(string $task_status_name):TaskStatus
	{
		$query_string = "WHERE task_status_name = :task_status_name";
		$query_params = [':task_status_name'=>$task_status_name];

		$task_status_params = $this->adapter->find($query_string, $query_params);

		if(!$task_status_params) throw new TaskStatusNotFoundException("Статус задачи не найден");

		return new TaskStatus($task_status_params);
	}
}