<?php 
declare(strict_types = 1);
namespace App\Services\DomainServices;

use App\Exceptions\DBExceptions\DBException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskNotFoundException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskStatusNotFoundException;

use App\Exceptions\ModelExceptions\TaskExceptions\TaskNotStartedException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskStoppedException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskAlreadyInProcessingException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskAlreadyCompleteException;

class EntrustedTaskService extends TaskService
{


	public function getEntrustedTask(int $executor_id, string $task_key):array
	{
		$entrusted_task_params = [];

		try
		{
			$task = $this->task_mapper->getTaskAsExecutorByKey($executor_id, $task_key);
			$entrusted_task_params['formulator_login'] = $this->user_mapper->getUserById($task->getFormulatorId())->getUserLogin();
			$entrusted_task_params = array_merge($entrusted_task_params, $this->getTaskInfo($task));

			return (!empty($entrusted_task_params)) ? ['result'=>$entrusted_task_params,'status'=>true] : ['result'=>[], 'status'=>false];
			

		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TaskNotFoundException | TaskStatusNotFoundException $e)
		{
			throw $e;
		}
	}

	

	public function initTask(int $executor_id, string $task_key):array
	{
		try
		{	
			$task = $this->task_mapper->getTaskAsExecutorByKey($executor_id, $task_key);
			$current_task_status = $this->task_status_mapper->getTaskStatusById($task->getTaskStatusId());
			$new_task_status_id = 0;


			$this->checkTaskStatus($current_task_status->getTaskStatusName(),
			['processing'=>'Задача уже инициализирована на выполнение','stopped'=>'Задача уже инициализирована на выполнение','complete'=>'Невозможно инициализировать завершенную задачу']);

			$new_task_status_id = $this->task_status_mapper->getTaskStatusByName('processing')->getTaskStatusId();
			$task->setTaskFirstProcessingTimestamp(time());
			$task->setTaskStatusId($new_task_status_id);

			$result = $this->task_mapper->updateTask($task);

			return ($result) ? ['result'=>['message'=>'Задача успешно поставлена на исполнение'],'status'=>true]:
				['result'=>['message'=>'Не удалось поставить задачу на исполнение'],'status'=>false];


		}

		catch(DBException $e)
		{
			throw $e;
		}
		catch(TaskNotFoundException | TaskStatusNotFoundException $e)
		{
			throw $e;
		}
	}


	public function startTask(int $executor_id, string $task_key):array
	{
		try
		{
			$task = $this->task_mapper->getTaskAsExecutorByKey($executor_id, $task_key);
			$current_task_status = $this->task_status_mapper->getTaskStatusById($task->getTaskStatusId());
			$new_task_status_id = 0;
			$current_processing_timestamp = 0;

			$this->checkTaskStatus($current_task_status->getTaskStatusName(),
				['not_started'=>'Невозможно начать неинициализированную задачу','processing'=>'Задача уже на исполнении','complete'=>'Невозможно возобновить завершенную задачу']);

		
			
			$new_task_status_id = $this->task_status_mapper->getTaskStatusByName('processing')->getTaskStatusId();
			$current_processing_timestamp = ($task->getTaskLastProcessingTimestamp()) ? $task->getTaskLastProcessingTimestamp() + ($task->getTaskLastProcessingTimestamp() - $task->getTaskFirstProcessingTimestamp()) : time();

			$task->setTaskStatusId($new_task_status_id);
			$task->setTaskLastProcessingTimestamp($current_processing_timestamp);
			$result = $this->task_mapper->updateTask($task);


			return ($result) ? ['result'=>['message'=>'Задача поставлена на исполнение'],'status'=>true] : 
				['result'=>['message'=>'Не удалось поставить задачу на исполнение'],'status'=>false];


		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TaskStatusNotFoundException)
		{
			throw $e;
		}


	}



	public function stopTask(int $executor_id, string $task_key):array
	{
		$current_task_status = 0;
		$new_task_status_id = 0;
	
		try
		{

			$task = $this->task_mapper->getTaskAsExecutorByKey($executor_id, $task_key);
			$current_task_status = $this->task_status_mapper->getTaskStatusById($task->getTaskStatusId());


			$this->checkTaskStatus($current_task_status->getTaskStatusName(),
				['not_started'=>'Невозможно поставить на паузу неинициализированную задачу','stopped'=>'Задача уже на паузе','complete'=>'Задача уже завершена','not_started'=>'Задача еще не начата']);

			$new_task_status_id = $this->task_status_mapper->getTaskStatusByName('stopped')->getTaskStatusId();

			$task->setTaskStatusId($new_task_status_id);


			$result = $this->task_mapper->updateTask($task);

			return ($result) ? ['result'=>['message'=>'Задача поставлена на паузу'],'status'=>true]:
				['result'=>['message'=>'Не удалось поставить задачу на паузу']];


		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TaskNotFoundException | TaskStatusNotFoundException $e)
		{
			throw $e;
		}

	}

	public function completeTask(int $executor_id, string $task_key):array
	{
		$new_task_status_id = 0;
		$current_task_status_name = '';

		try
		{
			$task = $this->task_mapper->getTaskAsExecutorByKey($executor_id, $task_key);
			$current_task_status_name = $this->task_status_mapper->getTaskStatusById($task->getTaskStatusId())->getTaskStatusName();

			$this->checkTaskStatus($current_task_status_name, 
				['not_started'=>'Невозможно завершить неинициализированную задачу','complete'=>'Задача уже завершена']);

			$task->setTaskEndTimestamp(time());

			$new_task_status_id = $this->task_status_mapper->getTaskStatusByName('complete')->getTaskStatusId();
			$task->setTaskStatusId($new_task_status_id);

			$result = $this->task_mapper->updateTask($task);

			return ($result) ? ['result'=>['message'=>'Задача успешно завершена'],'status'=>true]:
				['result'=>['message'=>'Не удалось заврешить задачу'],'status'=>false];


		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TaskNotFoundException | TaskStatusNotFoundException $e)
		{
			throw $e;
		}
	}


	public function dropTask(int $executor_id, string $task_key):array
	{
		$task_status_name = '';

		try
		{
			$task = $this->task_mapper->getTaskAsExecutorByKey($executor_id, $task_key);
			$result = $this->task_mapper->deleteTask($task);

			return ($result) ? ['result'=>['message'=>'Задача успешно удалена'],'status'=>true]:
				['result'=>['message'=>'Не удалось удалить задачу'],'status'=>false];
		}	

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TaskNotFoundException $e)
		{
			throw $e;
		}



	}



	private function checkTaskStatus(string $current_task_status, array $unexpected_task_statuses):void
	{
		$task_statuses_funcs = [
			'not_started'=>fn(string $message)=> throw new TaskNotStartedException($message),
			'stopped'=>fn(string $message)=> throw new TaskStoppedException($message),
			'processing'=>fn($message)=>throw new TaskAlreadyInProcessingException($message),
			'complete'=>fn($message)=>throw new TaskAlreadyCompleteException($message)
		];


		if(array_key_exists($current_task_status, $unexpected_task_statuses) && array_key_exists($current_task_status, $task_statuses_funcs))
		{
			$task_statuses_funcs[$current_task_status]($unexpected_task_statuses[$current_task_status]);
		}


	}



}