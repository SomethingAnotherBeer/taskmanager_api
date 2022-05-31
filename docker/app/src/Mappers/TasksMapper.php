<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;

class TasksMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('tasks');
	}

	public function getTasksAsFormulator(int $formulator_id, int $limit = 0, int $offset = 0):array
	{
		$tasks_params = [];

		$base_query_string = "INNER JOIN users ON tasks.executor_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.formulator_id = :formulator_id";

		$base_query_params = [':formulator_id'=>$formulator_id];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$tasks_params = $this->adapter->findAll($base_query_params, ['tasks.task_key', 'tasks.task_name', 'users.user_login AS executor', 'task_statuses.task_status_name'],$base_query_string);

		return $tasks_params;

	}


	public function getTasksAsExecutor(int $executor_id, int $limit = 0, int $offset = 0):array
	{
		$tasks_params = [];


		$base_query_string = "INNER JOIN users ON tasks.formulator_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.executor_id = :executor_id";
		$base_query_params = [':executor_id'=>$executor_id];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$tasks_params = $this->adapter->findAll($base_query_params,['tasks.task_key', 'tasks.task_name','users.user_login AS formulator','task_statuses.task_status_name'],$base_query_string);


		return $tasks_params;
	}

	


	public function getTasksAsFormulatorWithStatus(int $formulator_id, int $task_status_id, int $limit = 0, int $offset = 0):array
	{
		$tasks_params = [];

		$base_query_string = "INNER JOIN users ON tasks.executor_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.formulator_id = :formulator_id AND tasks.task_status_id = :task_status_id";

		$base_query_params = [':formulator_id'=>$formulator_id,':task_status_id'=>$task_status_id];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}



		$tasks_params = $this->adapter->findAll($base_query_params, ['tasks.task_key', 'tasks.task_name', 'users.user_login AS executor', 'task_statuses.task_status_name'],$base_query_string);

		return $tasks_params;
	}


	public function getTasksAsExecutorWithStatus(int $executor_id, int $task_status_id, int $limit = 0, int $offset = 0):array
	{
		$tasks_params = [];

		$base_query_string = "INNER JOIN users ON tasks.formulator_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.executor_id = :executor_id AND tasks.task_status_id = :task_status_id";
		$base_query_params = [':executor_id'=>$executor_id,':task_status_id'=>$task_status_id];

		if($limit)
		{
			$base_query_string.= $this->getLimitString((bool)$offset);
			$base_query_params = array_merge($base_query_params, $this->getLimitParams($limit, $offset));
		}

		$tasks_params = $this->adapter->findAll($base_query_params,['tasks.task_key', 'tasks.task_name','users.user_login AS formulator','task_statuses.task_status_name'],$base_query_string);

		return $tasks_params;
		
	}

	




	public function getTasksCountAsExecutor(int $executor_id):int
	{

		$tasks_count = 0;

		$base_query_string = "INNER JOIN users ON tasks.formulator_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.executor_id = :executor_id";
		$base_query_params = [':executor_id'=>$executor_id];

		$tasks_count = $this->adapter->getCount($base_query_string, $base_query_params);

		return $tasks_count;
	}

	public function getTasksCountAsFormulator(int $formulator_id):int
	{
		$tasks_count = 0;	

		$base_query_string = "INNER JOIN users ON tasks.executor_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.formulator_id = :formulator_id";

		$base_query_params = [':formulator_id'=>$formulator_id];

		$tasks_count = $this->adapter->getCount($base_query_string, $base_query_params);

		return $tasks_count;
	}



	public function getTasksCountAsExecutorWithStatus(int $executor_id, int $task_status_id):int
	{
		$tasks_count = 0;

		$base_query_string = $base_query_string = "INNER JOIN users ON tasks.formulator_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.executor_id = :executor_id AND tasks.task_status_id = :task_status_id";

		$base_query_params = [':executor_id'=>$executor_id,':task_status_id'=>$task_status_id];

		$tasks_count = $this->adapter->getCount($base_query_string, $base_query_params);

		return $tasks_count;

	}

	public function getTasksCountAsFormulatorWithStatus(int $formulator_id, int $task_status_id):int
	{
		$tasks_count = 0;

		$base_query_string = "INNER JOIN users ON tasks.executor_id = users.user_id INNER JOIN task_statuses ON tasks.task_status_id = task_statuses.task_status_id WHERE tasks.formulator_id = :formulator_id AND tasks.task_status_id = :task_status_id";

		$base_query_params = [':formulator_id'=>$formulator_id,':task_status_id'=>$task_status_id];

		$tasks_count = $this->adapter->getCount($base_query_string, $base_query_params);

		return $tasks_count;

	}



}