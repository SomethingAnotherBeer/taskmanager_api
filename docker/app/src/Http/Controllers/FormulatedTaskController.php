<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Http\Response;
use App\Output\Output;
use App\Services\AppServices\AuthenticationService;
use App\Services\DomainServices\FormulatedTaskService;


class FormulatedTaskController extends Controller
{
	private FormulatedTaskService $task_service;

	public function __construct(Request $request, AuthenticationService $authentication, FormulatedTaskService $task_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->task_service = $task_service;
		$this->auth();
	}


	public function getFormulatedTask(string $task_key)
	{

		$result = $this->task_service->getFormulatedTask($this->current_user_id, $task_key);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));	
	}


	public function createTask()
	{
		$this->checkRequestBody(['executor_login','task_params'],true);
		$this->checkRequestBody(['task_name','task_description'],false,['task_params']);
		$this->checkNeedleRequestBodyParams(['task_name'],['task_params']);

		$result = $this->task_service->createTask($this->current_user_id, $this->request->getRequestBody()['executor_login'],$this->request->getRequestBody()['task_params']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}
	
	public function formulateTaskForAssociate()
	{
		$this->checkRequestBody(['executor_login','task_params'],true);
		$this->checkRequestBody(['task_name','task_description'],false,['task_params']);


		$result = $this->task_service->formulateTaskForAssociate($this->current_user_id, $this->request->getRequestBody()['executor_login'],$this->request->getRequestBody()['task_params']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));

	}

	public function formulateTaskByGroup()
	{

		$this->checkRequestBody(['executor_login','group_name','task_params'], true);
		$this->checkRequestBody(['task_name','task_description'],false,['task_params']);

		$executor_login = $this->request->getRequestBody()['executor_login'];
		$group_name = $this->request->getRequestBody()['group_name'];
		$task_params = $this->request->getRequestBody()['task_params'];

		$result = $this->task_service->formulateTaskByGroup($this->current_user_id, $executor_login, $group_name, $task_params);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));


	}
	

	public function dropFormulatedTask()
	{
		$this->checkRequestBody(['task_key'],true);

		$result = $this->task_service->dropFormulatedTask($this->current_user_id, $this->request->getRequestBody()['task_key']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));

	}



}