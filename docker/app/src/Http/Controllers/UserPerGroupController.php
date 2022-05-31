<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Http\Response;
use App\Output\Output;
use App\Services\AppServices\AuthenticationService;
use App\Services\DomainServices\UserPerGroupService;

class UserPerGroupController extends Controller
{
	private UserPerGroupService $user_per_group_service;

	public function __construct(Request $request, AuthenticationService $authentication, UserPerGroupService $user_per_group_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->user_per_group_service = $user_per_group_service;
		$this->auth();
	}

	public function inviteUserToGroup()
	{
		$this->checkRequestBody(['user_login','group_name'], true);

		$user_login = $this->request->getRequestBody()['user_login'];
		$group_name = $this->request->getRequestBody()['group_name'];

		$result = $this->user_per_group_service->inviteUserToGroup($this->current_user_id, $user_login, $group_name);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


	public function acceptInviteToGroup()
	{
		$this->checkRequestBody(['group_name'],true);

		$group_name = $this->request->getRequestBody()['group_name'];

		$result = $this->user_per_group_service->acceptInviteToGroup($this->current_user_id, $group_name);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


	public function denyInviteToGroup()
	{
		$this->checkRequestBody(['group_name'], true);

		$group_name = $this->request->getRequestBody()['group_name'];

		$result = $this->user_per_group_service->denyInviteToGroup($this->current_user_id, $group_name);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}


	public function exitFromGroup()
	{
		$this->checkNeedleRequestBodyParams(['group_name']);

		$result = $this->user_per_group_service->exitFromGroup($this->current_user_id, $this->request->getRequestBody()['group_name']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}


}