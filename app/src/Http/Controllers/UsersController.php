<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Http\Response;
use App\Output\Output;
use App\Services\AppServices\AuthenticationService;
use App\Services\DomainServices\UsersService;
use App\Exceptions\ValidationExceptions\InvalidParamException;

class UsersController extends Controller
{
	private UsersService $users_service;

	public function __construct(Request $request, AuthenticationService $authentication, UsersService $users_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->users_service = $users_service;

		$this->auth();
	}

	public function getUsers()
	{
		$request_params = [];
		$page = 0;

		$this->checkRequestParams(['page']);
		$request_params = $this->request->getRequestParams();

		if(isset($request_params['page']) && (!is_numeric($request_params['page']) || (int)$request_params['page'] <= 0)) throw new InvalidParamException("Номер страницы должен быть целым положительным числом");
		
		$page = (isset($request_params['page'])) ? (int)$request_params['page'] : 1;

		$result = $this->users_service->getUsers($page);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


	public function getUsersByStatus(string $status_name)
	{
		$request_params = [];
		$page = 0;

		$this->checkRequestParams(['page']);
		$request_params = $this->request->getRequestParams();

		if(isset($request_params['page']) && (!is_numeric($request_params['page']) || (int)$request_params['page'] <= 0)) throw new InvalidParamException("Номер страницы должен быть целым положительным числом");
		
		$page = (isset($request_params['page'])) ? (int)$request_params['page'] : 1;

		$result = $this->users_service->getUsersByStatus($page, $status_name);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
	}


	public function getUsersByGroup(string $group_name)
	{
		$request_params = [];
		$page = 0;

		$this->checkRequestParams(['page']);
		$request_params = $this->request->getRequestParams();

		if(isset($request_params['page']) && (!is_numeric($request_params['page']) || (int)$request_params['page'] <= 0)) throw new InvalidParamException("Номер страницы должен быть целым положительным числом");
		
		$page = (isset($request_params['page'])) ? (int)$request_params['page'] : 1;

		$result = $this->users_service->getUsersByGroup($page, $group_name);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(),JSON_UNESCAPED_UNICODE));
		
	}


	public function getInRequestsOnAssociate()
	{
		$result = $this->users_service->getInRequestsOnAssociate($this->current_user_id);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));

	}


	public function getOutRequestsOnAssociate()
	{
		$result = $this->users_service->getOutRequestsOnAssociate($this->current_user_id);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}

	public function getAssociatedUsers()
	{
		$result = $this->users_service->getAssociatedUsers($this->current_user_id);

		$http_code = 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
		
	}


}