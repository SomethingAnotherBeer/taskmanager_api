<?php
declare(strict_types = 1);
namespace App\Services\DomainServices;
use App\Exceptions\DBExceptions\DBException;

use App\Exceptions\ModelExceptions\UserExceptions\UserNotFoundException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskStatusNotFoundException;
use App\Exceptions\ModelExceptions\TaskExceptions\TaskAlreadyInProcessingException;

use App\Exceptions\ModelExceptions\UsersAssociateExceptions\UsersNotAssociatedException;

use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserPerGroupNotFoundException;

class FormulatedTaskService extends TaskService
{

	public function getFormulatedTask(int $formulator_id, string $task_key):array
	{
		$formulated_task_params = [];

		try
		{
			$task = $this->task_mapper->getTaskAsFormulatorByKey($formulator_id, $task_key);
			$formulated_task_params['executor_login'] = $this->user_mapper->getUserById($task->getExecutorId())->getUserLogin();
			$formulated_task_params =  array_merge($formulated_task_params, $this->getTaskInfo($task));

			return (!empty($formulated_task_params)) ? ['result'=>$formulated_task_params,'status'=>true] : ['result'=>[], 'status'=>false];
			
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




	public function FormulateTaskForAssociate(int $formulator_id, string $executor_login, array $task_params):array
	{
		$prepared_task_params = [];
		$task_status_id = 0;
		$executor_id = 0;

		try
		{
			$executor_id = $this->user_mapper->getUserByLogin($executor_login)->getUserId();


			if(!$this->user_mapper->associateWithById($formulator_id, $executor_id))
			{
				throw new UsersNotAssociatedException("Вы не можете назначить задачу пользователю, не являясь коллегами");
			}

			$task_status_id = $this->task_status_mapper->getTaskStatusByName('not_started')->getTaskStatusId();


			$prepared_task_params['formulator_id'] = $formulator_id;
			$prepared_task_params['executor_id'] = $executor_id;
			$prepared_task_params['task_status_id'] = $task_status_id;
			$prepared_task_params['task_key'] = $this->generateTaskKey();
			$prepared_task_params['task_name'] = $task_params['task_name'];
			$prepared_task_params['task_description'] = (isset($task_params['task_description'])) ? $task_params['task_description'] : '';



			$result = $this->task_mapper->createTask($prepared_task_params);

			return ($result) ? ['result'=>['message'=>'Задача успешно создана','task_key'=>$prepared_task_params['task_key']],'status'=>true]:
				['result'=>['message'=>'Не удалось создать задачу'],'status'=>false];


		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}

		catch(UsersNotAssociatedException $e)
		{
			throw $e;
		}

		catch(TaskStatusNotFoundException $e)
		{
			throw $e;
		}

	}



	public function formulateTaskByGroup(int $formulator_id, string $executor_login, string $group_name, array $task_params):array
	{
		$prepared_task_params = [];
		$task_status_id = 0;
		$executor_id = 0;

		try
		{
			$executor_id = $this->user_mapper->getUserByLogin($executor_login)->getUserId();

			if(!$this->user_mapper->inGroupByGroupName($formulator_id, $group_name) && !$this->user_mapper->inGroupByGroupName($executor_id, $group_name))
			{
				throw new UserPerGroupNotFoundException("Вы не состоите с пользователем в одной группе или группы не существует");
			}

			$task_status_id = $this->task_status_mapper->getTaskStatusByName('not_started')->getTaskStatusId();

			$prepared_task_params['formulator_id'] = $formulator_id;
			$prepared_task_params['executor_id'] = $executor_id;
			$prepared_task_params['task_status_id'] = $task_status_id;
			$prepared_task_params['task_key'] = $this->generateTaskKey();
			$prepared_task_params['task_name'] = $task_params['task_name'];
			$prepared_task_params['task_description'] = (isset($task_params['task_description'])) ? $task_params['task_description'] : '';



			$result = $this->task_mapper->createTask($prepared_task_params);

			return ($result) ? ['result'=>['message'=>'Задача успешно создана','task_key'=>$prepared_task_params['task_key']],'status'=>true]:
				['result'=>['message'=>'Не удалось создать задачу'],'status'=>false];

		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}

		catch(UserPerGroupNotFoundException $e)
		{
			throw $e;
		}

		catch(TaskStatusNotFoundException)
		{
			throw $e;
		}

	}



	public function createTask(int $formulator_id, string $executor_login, array $task_params):array
	{
		$prepared_task_params = [];
		$task_status_id = 0;
		$executor_id = 0;

		try
		{

			$task_status_id = $this->task_status_mapper->getTaskStatusByName('not_started')->getTaskStatusId();
			$executor_id = $this->user_mapper->getUserByLogin($executor_login)->getUserId();

			$prepared_task_params['formulator_id'] = $formulator_id;
			$prepared_task_params['executor_id'] = $executor_id;
			$prepared_task_params['task_status_id'] = $task_status_id;
			$prepared_task_params['task_key'] = $this->generateTaskKey();
			$prepared_task_params['task_name'] = $task_params['task_name'];
			$prepared_task_params['task_description'] = (isset($task_params['task_description'])) ? $task_params['task_description'] : '';



			$result = $this->task_mapper->createTask($prepared_task_params);

			return ($result) ? ['result'=>['message'=>'Задача успешно создана','task_key'=>$prepared_task_params['task_key']],'status'=>true]:
				['result'=>['message'=>'Не удалось создать задачу'],'status'=>false];


		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}

		catch(TaskStatusNotFoundException $e)
		{
			throw $e;
		}
	}

	
	public function dropFormulatedTask(int $formulator_id, string $task_key):array
	{
		$task_status_name = 0;
		$task = null;

		try
		{
			$task = $this->task_mapper->getTaskAsFormulatorByKey($formulator_id,$task_key);
			$task_status_name = $this->task_status_mapper->getTaskStatusById($task->getTaskStatusId())->getTaskStatusName();

			if('not_started' !== $task_status_name)
			{
				throw new TaskAlreadyInProcessingException("Невозможно отозвать поставленную на выполнение задачу");

			}

			$result = $this->task_mapper->deleteTask($task);

			return ($result) ? ['result'=>['message'=>'Задача успешно удалена'],'status'=>true]:
			['result'=>['message'=>'Не удалось удалить задачу'],'status'=>false];


		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TaskAlreadyInProcessingException | TaskNotFoundException $e)
		{
			throw $e;
		}

	}



	private function generateTaskKey():string
	{
		$task_key = '';
		$task_key_len = 25;
		$current_ch_group = 0;

		for($i = 0; $i < $task_key_len; $i++)
		{
			$current_ch_group = rand(0,2);

			switch($current_ch_group)
			{
				case 0:
					$task_key[$i] = chr(rand(97, 122));
				break;

				case 1:
					$task_key[$i] = chr(rand(65,90));
				break;

				case 2:
					$task_key[$i] = rand(0,9);
				break;
			}
		}


		return $task_key;

	}


}