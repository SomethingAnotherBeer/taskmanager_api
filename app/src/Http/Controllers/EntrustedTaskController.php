<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Response;
use App\Http\Request;
use App\Output\Output;
use App\Services\AppServices\AuthenticationService;
use App\Services\DomainServices\EntrustedTaskService;

class EntrustedTaskController extends Controller
{
	private EntrustedTaskService $task_service;

	public function __construct(Request $request, AuthenticationService $authentication, EntrustedTaskService $task_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->task_service = $task_service;
		$this->auth();
	}


	public function getEntrustedTask(string $task_key)
	{

		$result = $this->task_service->getEntrustedTask($this->current_user_id, $task_key);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}

	public function initTask()
	{
		$this->checkRequestBody(['task_key'],true);

		$result = $this->task_service->initTask($this->current_user_id, $this->request->getRequestBody()['task_key']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


	public function startTask()
	{
		$this->checkRequestBody(['task_key'],true);

		$result = $this->task_service->startTask($this->current_user_id, $this->request->getRequestBody()['task_key']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}
	
	public function stopTask()
	{
		$this->checkRequestBody(['task_key'],true);

		$result = $this->task_service->stopTask($this->current_user_id ,$this->request->getRequestBody()['task_key']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


	public function completeTask()
	{
		$this->checkRequestBody(['task_key'],true);

		$result = $this->task_service->completeTask($this->current_user_id, $this->request->getRequestBody()['task_key']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


	public function dropTask()
	{
		$this->checkRequestBody(['task_key'], true);

		$result = $this->task_service->dropTask($this->current_user_id, $this->request->getRequestBody()['task_key']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}

	


}