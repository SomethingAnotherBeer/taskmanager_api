<?php
declare(strict_types = 1);
namespace App\Services\DomainServices;

use App\Mappers\TaskMapper;
use App\Mappers\UserMapper;
use App\Mappers\TaskStatusMapper;

use App\Models\Task;

use App\Exceptions\DBExceptions\DBException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskStatusNotFoundException;

abstract class TaskService
{
	protected TaskMapper $task_mapper;
	protected UserMapper $user_mapper;
	protected TaskStatusMapper $task_status_mapper;

	public function __construct(TaskMapper $task_mapper, UserMapper $user_mapper, TaskStatusMapper $task_status_mapper)
	{
		$this->task_mapper = $task_mapper;
		$this->user_mapper = $user_mapper;
		$this->task_status_mapper = $task_status_mapper;
	}

	
	protected function getTaskInfo(Task $task):array
	{
		$task_params_strings = [
			'task_create_date'=>'Дата постановки задачи',
			'task_start_date'=>'Дата начала выполнения задачи',
			'task_execution_time'=>'Время выполнения задачи',
			'task_end_date'=>'Дата завершения задачи',
			'task_stopped_time'=>'Время простоя задачи',
			'task_status'=>'Статус задачи'


		];

		$task_params = [];

		$prepared_task_params = [];
			
		$task_params['task_create_date'] = '';
		$task_params['task_start_date'] = '';
		$task_params['task_execution_time'] = '';
		$task_params['task_end_date'] = '';
		$task_params['task_status_name'] = '';
		


		try
		{
			
			$current_task_status = $this->task_status_mapper->getTaskStatusById($task->getTaskStatusId())->getTaskStatusName();
			$task_params['task_create_date'] = date("Y:m:d H:i:s", $task->getTaskCreateTimestamp());
			$task_params['task_start_date'] = ("not_started" !== $current_task_status) ? date("Y:m:d H:i:s",$task->getTaskFirstProcessingTimestamp()) : "";

			date_default_timezone_set('UTC');

			$task_params['task_execution_time'] = ("not_started" !== $current_task_status) ? (($task->getTaskLastProcessingTimestamp()) ?  date("H:i:s", $task->getTaskLastProcessingTimestamp() - $task->getTaskFirstProcessingTimestamp()) : date("H:i:s",$task->getTaskEndTimestamp() - $task->getTaskFirstProcessingTimestamp())) : "";
			date_default_timezone_set('Europe/Moscow');

			$task_params['task_end_date'] = ("complete" === $current_task_status) ? date("Y:m:d H:i:s", $task->getTaskEndTimestamp()) : "";

			date_default_timezone_set('UTC');

			$task_params['task_stopped_time'] = ("complete" === $current_task_status) ? ($task->getTaskLastProcessingTimestamp() ? date("H:i:s",$task->getTaskEndTimestamp() - $task->getTaskLastProcessingTimestamp()) : date("H:i:s",0)) : ""; 

			$task_params['task_status'] = $current_task_status;

			
			foreach($task_params as $task_param_key=>$task_param_value)
			{
				if($task_param_value)
				{
					$prepared_task_params[$task_param_key] = $task_params_strings[$task_param_key] . " " .$task_param_value;
				}
			}

			return $prepared_task_params;

  	
		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TaskStatusNotFoundException $e)
		{
			throw $e;
		}


	}







}