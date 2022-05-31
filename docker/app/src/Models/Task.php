<?php
declare(strict_types = 1);
namespace App\Models;
use App\Contracts\Model;

class Task implements Model
{
	private int $task_id;
	private int $formulator_id;
	private int $executor_id;
	private int $task_status_id;
	private string $task_key;
	private string $task_name;
	private string $task_description;
	private int $task_create_timestamp;
	private int $task_first_processing_timestamp;
	private int $task_last_processing_timestamp;
	private int $task_end_timestamp;

	public function __construct(array $task_params)
	{
		$this->task_id = $task_params['task_id'];
		$this->formulator_id = $task_params['formulator_id'];
		$this->executor_id = $task_params['executor_id'];
		$this->task_status_id = $task_params['task_status_id'];
		$this->task_key = $task_params['task_key'];
		$this->task_name = $task_params['task_name'];
		$this->task_description = $task_params['task_description'];
		$this->task_create_timestamp = $task_params['task_create_timestamp'];
		$this->task_first_processing_timestamp = $task_params['task_first_processing_timestamp'];
		$this->task_last_processing_timestamp = $task_params['task_last_processing_timestamp'];
		$this->task_end_timestamp = $task_params['task_end_timestamp'];
	}


	public function getTaskId():int
	{
		return $this->task_id;
	}

	public function getFormulatorId():int
	{
		return $this->formulator_id;
	}

	public function getExecutorId():int
	{
		return $this->executor_id;
	}

	public function getTaskStatusId():int
	{
		return $this->task_status_id;
	}

	public function getTaskKey():string
	{
		return $this->task_key;
	}

	public function getTaskName():string
	{
		return $this->task_name;
	}

	public function getTaskDescription():string
	{
		return $this->task_description;
	}

	public function getTaskCreateTimestamp():int
	{
		return $this->task_create_timestamp;
	}

	public function getTaskFirstProcessingTimestamp():int
	{
		return $this->task_first_processing_timestamp;
	}

	public function getTaskLastProcessingTimestamp():int
	{
		return $this->task_last_processing_timestamp;
	}


	public function getTaskEndTimestamp():int
	{
		return $this->task_end_timestamp;
	}


	public function setTaskStatusId(int $task_status_id):void
	{
		$this->task_status_id = $task_status_id;
	}

	public function setTaskKey(string $task_key):void
	{
		$this->task_key = $task_key;
	}

	public function setTaskName(string $task_name):void
	{
		$this->task_name = $task_name;
	}

	public function setTaskFirstProcessingTimestamp(int $task_first_processing_timestamp):void
	{
		$this->task_first_processing_timestamp = $task_first_processing_timestamp;
	}

	public function setTaskLastProcessingTimestamp(int $task_last_processing_timestamp):void
	{
		$this->task_last_processing_timestamp = $task_last_processing_timestamp;
	}

	public function setTaskEndTimestamp(int $task_end_timestamp):void
	{
		$this->task_end_timestamp = $task_end_timestamp;
	}


	public function getAllProperties():array
	{
		$task_properties = [];

		$task_properties['task_id'] = $this->getTaskId();
		$task_properties['formulator_id'] = $this->getFormulatorId();
		$task_properties['executor_id'] = $this->getExecutorId();
		$task_properties['task_status_id'] = $this->getTaskStatusId();
		$task_properties['task_key'] = $this->getTaskKey();
		$task_properties['task_name'] = $this->getTaskName();
		$task_properties['task_description'] = $this->getTaskDescription();
		$task_properties['task_create_timestamp'] = $this->getTaskCreateTimestamp();
		$task_properties['task_first_processing_timestamp'] = $this->getTaskFirstProcessingTimestamp();
		$task_properties['task_last_processing_timestamp'] = $this->getTaskLastProcessingTimestamp();
		$task_properties['task_end_timestamp'] = $this->getTaskEndTimestamp();

		return $task_properties;
	}

}
