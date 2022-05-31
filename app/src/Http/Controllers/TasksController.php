<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Http\Response;
use App\Output\Output;
use App\Services\AppServices\AuthenticationService;
use App\Services\DomainServices\TasksService;

use App\Exceptions\ValidationExceptions\InvalidParamException;


class TasksController extends Controller
{
	private TasksService $tasks_service;

	public function __construct(Request $request, AuthenticationService $authentication, TasksService $tasks_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->tasks_service = $tasks_service;
		$this->auth();

	}


	public function getFormulatedTasks()
	{
		$this->checkRequestParams(['page','task_status_name']);
		$tasks_params = $this->request->getRequestParams();

		if(isset($tasks_params['page']) && (!is_numeric($tasks_params['page']) || (int)$tasks_params['page'] <= 0)) throw new InvalidParamException("Номер страницы должен быть целым положительным числом");

		if(!isset($tasks_params['page'])) $tasks_params['page'] = 1;


		$result = $this->tasks_service->getFormulatedTasks($this->current_user_id, $tasks_params);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}	



	public function getEntrustedTasks()
	{
		
		$this->checkRequestParams(['page','task_status_name']);
		$tasks_params = $this->request->getRequestParams();
		

		if(isset($tasks_params['page']) && (!is_numeric($tasks_params['page']) || (int)$tasks_params['page'] <= 0)) throw new InvalidParamException("Номер страницы должен быть целым положительным числом");

		if(!isset($tasks_params['page'])) $tasks_params['page'] = 1;

		$result = $this->tasks_service->getEntrustedTasks($this->current_user_id, $tasks_params);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));

	}



}
