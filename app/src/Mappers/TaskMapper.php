<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;
use App\Models\Task;

use App\Exceptions\ModelExceptions\TaskExceptions\TaskNotFoundException;

class TaskMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('tasks');
	}


	public function getTaskById(int $task_id):Task
	{
		$query_string = "WHERE task_id = :task_id";
		$query_params = ['task_id'=>$task_id];

		$task_params = $this->adapter->find($query_string, $query_params);

		if(!$task_params) throw new TaskNotFoundException("Задача не найдена");

		return new Task($task_params);
	}


	public function getTaskByKey(string $task_key):Task
	{
		$query_string = "WHERE task_key = :task_key";
		$query_params = [':task_key'=>$task_key];

		$task_params = $this->adapter->find($query_string, $query_params);

		if(!$task_params) throw new TaskNotFoundException("Задача не найдена");

		return new Task($task_params);
	}


	public function getTaskAsFormulatorByKey(int $formulator_id, string $task_key):Task
	{
		$query_string = "WHERE formulator_id = :formulator_id AND task_key = :task_key";
		$query_params = [':formulator_id'=>$formulator_id,':task_key'=>$task_key];

		$task_params = $this->adapter->find($query_string, $query_params);

		if(!$task_params) throw new TaskNotFoundException("Задача не найдена");

		return new Task($task_params);
	}


	public function getTaskAsExecutorByKey(int $executor_id, string $task_key):Task
	{
		$query_string = "WHERE executor_id = :executor_id AND task_key = :task_key";
		$query_params = [':executor_id'=>$executor_id,':task_key'=>$task_key];

		$task_params = $this->adapter->find($query_string, $query_params);

		if(!$task_params) throw new TaskNotFoundException("Задача не найдена");

		return new Task($task_params);
	}

	public function getTasksAsFormulator(int $formulator_id):array
	{
		$tasks_params = [];

		$base_query_string = "WHERE formulator_id = :formulator_id";
		$base_query_params = [':formulator_id'=>$formulator_id];

		$base_query_string = "INNER JOIN users ON tasks.executor_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.formulator_id = :formulator_id";

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$tasks_params = $this->adapter->findAll($base_query_string, $base_query_params, ['tasks.task_key', 'tasks.task_name', 'users.user_name AS executor', 'task_statuses.task_status_name']);

		return $tasks_params;

	}


	public function getTasksAsExecutor(int $executor_id, $expected_key, int $limit = 0, int $offset = 0):array
	{
		$tasks_params = [];


		$base_query_string = "INNER JOIN users ON tasks.formulator_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.formulator_id = :formulator_id";
		$base_query_params = [':executor_id'=>$executor_id];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$tasks_params = $this->adapter->findAll($base_query_string, $base_query_params,['tasks.task_key', 'tasks.task_name','users.user_name AS formulator','task_statuses.task_status_name']);

		return $tasks_params;
	}





	public function updateTask(Task $task):bool
	{
		$task_id = $task->getTaskId();
		$query_string = "WHERE task_id = :task_id";

		$current_params = $task->getAllProperties();

		$old_params = $this->getTaskById($task_id)->getAllProperties();

		$new_params = $this->getUpdatedParams($current_params, $old_params);

		if(!$new_params) return false;

		return $this->adapter->update($query_string, $new_params,[':task_id'=>$task_id]);

	}


	public function deleteTask(Task $task):bool
	{
		$task_id = $task->getTaskId();

		$query_string = "WHERE task_id = :task_id";
		$query_params = [':task_id'=>$task_id];

		return $this->adapter->delete($query_string, $query_params);
	}


	public function createTask(array $params):bool
	{
		return $this->adapter->create($params);
	}


}