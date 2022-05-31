<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Http\Response;
use App\Output\Output;
use App\Services\AppServices\AuthenticationService;
use App\Services\DomainServices\GroupService;


class GroupController extends Controller
{
	private GroupService $group_service;

	public function __construct(Request $request, AuthenticationService $authentication, GroupService $group_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->group_service = $group_service;
		$this->auth();
	}


	public function createGroup()
	{
		$this->checkRequestBody(['group_name'],true);

		$result = $this->group_service->createGroup($this->current_user_id, $this->request->getRequestBody()['group_name']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));

	}

	public function renameGroup()
	{
		$this->checkRequestBody(['group_name','new_group_name'],true);

		$result = $this->group_service->renameGroup($this->current_user_id, $this->request->getRequestBody()['group_name'], $this->request->getRequestBody()['new_group_name']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


	public function deleteGroup()
	{
		$this->checkRequestBody(['group_name'], true);

		$result = $this->group_service->deleteGroup($this->current_user_id, $this->request->getRequestBody()['group_name']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


}