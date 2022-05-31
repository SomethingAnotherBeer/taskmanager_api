<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Http\Response;
use App\Services\AppServices\AuthenticationService;
use App\Output\Output;

use App\Services\DomainServices\GroupsService;

class GroupsController extends Controller
{
	private GroupsService $groups_service;

	public function __construct(Request $request, AuthenticationService $authentication, GroupsService $groups_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->groups_service = $groups_service;

		$this->auth();
	}


	public function getAllGroups()
	{
		$this->checkRequestParams(['page']);

		$page = (isset($this->request->getRequestParams()['page'])) ? $this->request->getRequestParams()['page'] : 1;

		$result = $this->groups_service->getAllGroups($page);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}

	public function getMyGroups()
	{
		$this->checkRequestParams(['page']);

		$page = (isset($this->request->getRequestParams()['page'])) ? $this->request->getRequestParams()['page'] : 1;

		$result = $this->groups_service->getMyGroups($this->current_user_id, $page);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}


	public function getUserGroups(string $user_login)
	{
		$this->checkRequestParams(['page']);

		$page = (isset($this->request->getRequestParams()['page'])) ? $this->request->getRequestParams()['page'] : 1;

		$result = $this->groups_service->getUserGroups($user_login, $page);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}

	

}