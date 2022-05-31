<?php
declare(strict_types = 1);
namespace App\Services\DomainServices;

use App\Mappers\TasksMapper;
use App\Mappers\TaskStatusMapper;
use App\Services\AppServices\PaginationService;

use App\Exceptions\DBExceptions\DBException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskNotFoundException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskStatusNotFoundException;

class TasksService
{
	private TasksMapper $tasks_mapper;
	private TaskStatusMapper $task_status_mapper;
	private PaginationService $paginator;

	public function __construct(TasksMapper $tasks_mapper, TaskStatusMapper $task_status_mapper, PaginationService $paginator)
	{
		$this->tasks_mapper = $tasks_mapper;
		$this->task_status_mapper = $task_status_mapper;
		$this->paginator = $paginator;
	}

	public function getFormulatedTasks(int $formulator_id, array $tasks_params):array
	{
		$this->paginator->setCurrentList((int)$tasks_params['page']);;

		$result = (isset($tasks_params['task_status_name'])) ? $this->getFormulatedTasksByStatus($formulator_id, $tasks_params['task_status_name']) : 
			$this->getAllFormulatedTasks($formulator_id);

		return $result;


	}

	public function getEntrustedTasks(int $executor_id, array $tasks_params):array
	{	
		$this->paginator->setCurrentList((int)$tasks_params['page']);

		$result = (isset($tasks_params['task_status_name'])) ? $this->getEntrustedTasksByStatus($executor_id,$tasks_params['task_status_name']):
			$this->getAllEntrustedTasks($executor_id);

		return $result;
	}

	
	


	private function getFormulatedTasksByStatus(int $formulator_id, string $task_status_name):array
	{
		$task_status_id = 0;

		try
		{
			$task_status_id = $this->task_status_mapper->getTaskStatusByName($task_status_name)->getTaskStatusId();

			$tasks = $this->tasks_mapper->getTasksAsFormulatorWithStatus($formulator_id, $task_status_id, $this->paginator->getLimit(), $this->paginator->getOffset());

			return ($tasks) ? ['result'=>$tasks,'status'=>true] : ['result'=>['message'=>$this->getTasksStatusNotFoundCondition($task_status_name)],'status'=>false];
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

	private function getEntrustedTasksByStatus(int $executor_id, string $task_status_name):array
	{
		$task_status_id = 0;

		try
		{
			$task_status_id = $this->task_status_mapper->getTaskStatusByName($task_status_name)->getTaskStatusId();

			$tasks = $this->tasks_mapper->getTasksAsExecutorWithStatus($executor_id, $task_status_id, $this->paginator->getLimit(), $this->paginator->getOffset());

			return ($tasks) ? ['result'=>$tasks,'status'=>true] : ['result'=>['message'=>$this->getTasksStatusNotFoundCondition($task_status_name)],'status'=>false];
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


	private function getAllFormulatedTasks(int $formulator_id):array
	{
		try
		{
			$tasks = $this->tasks_mapper->getTasksAsFormulator($formulator_id, $this->paginator->getLimit(), $this->paginator->getOffset());

			return ($tasks) ? ['result'=>$tasks,'status'=>true] : ['result'=>['message'=>'Задачи не найдены'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}
	}


	private function getAllEntrustedTasks(int $executor_id):array
	{
		try
		{

			$tasks = $this->tasks_mapper->getTasksAsExecutor($executor_id, $this->paginator->getLimit(), $this->paginator->getOffset());
			return ($tasks) ? ['result'=>$tasks,'status'=>true] : ['result'=>['message'=>'Задачи не найдены'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}
	}

	private function getTasksStatusNotFoundCondition(string $task_status_name):string
	{
		$task_statuses_conditions = [
			'not_started'=>'Не начатые задачи не найдены',
			'processing'=>'Активные задачи не найдены',
			'stopped'=>'Задачи, поставленные на паузу, не найдены',
			'complete'=>'Завершенные задачи не найдены'
		];

		return $task_statuses_conditions[$task_status_name];


	}



}