<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Response;
use App\Http\Request;
use App\Services\AppServices\AuthenticationService;
use App\Services\AppServices\AuthorizationService;
use App\Services\DomainServices\UserService;
use App\Output\Output;




class UserController extends Controller
{

	public function __construct(Request $request, AuthenticationService $authentication, AuthorizationService $authorization, UserService $user_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->user_service = $user_service;
		$this->auth();
		
		$authorization->checkRights($this->current_user_id, 'admin');
		
	}	

	
	public function createUser()
	{
		$this->checkRequestBody(['user_login','user_password','user_rights_name','user_email','user_name','user_surname']);
		$this->checkNeedleRequestBodyParams(['user_login','user_password']);

		$result = $this->user_service->createUser($this->request->getRequestBody());

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));


	}


	public function changeUserLogin()
	{
		$this->checkRequestBody(['user_login','updatable_params'],true);
		$this->checkRequestBody(['new_user_login'],true,['updatable_params']);

		$result = $this->user_service->changeUserLogin($this->request->getRequestBody()['user_login'],$this->request->getRequestBody()['updatable_params']['new_user_login']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}

	public function changeUserPassword()
	{
		$this->checkRequestBody(['user_login','updatable_params'],true);
		$this->checkRequestBody(['new_user_password'],true,['updatable_params']);

		$result = $this->user_service->changeUserPassword($this->request->getRequestBody()['user_login'], $this->request->getRequestBody()['updatable_params']['new_user_password']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));

	}


	public function changeUserFullName()
	{
		$this->checkRequestBody(['user_login','updatable_params'],true);
		$this->checkRequestBody(['new_user_name','new_user_surname'],false,['updatable_params']);

		$result = $this->user_service->changeUserFullName($this->request->getRequestBody()['user_login'], $this->request->getRequestBody()['updatable_params']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}



	public function deleteUser()
	{
		$this->checkRequestBody(['user_login'],true);

		$result = $this->user_service->deleteUser($this->request->getRequestBody()['user_login']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));

	}



}