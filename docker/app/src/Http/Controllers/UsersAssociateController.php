<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Http\Response;
use App\Output\Output;
use App\Services\AppServices\AuthenticationService;
use App\Services\DomainServices\UsersAssociateService;


class UsersAssociateController extends Controller
{
	private UsersAssociateService $users_associate_service;

	public function __construct(Request $request, AuthenticationService $authentication, UsersAssociateService $users_associate_service)
	{
		$this->request = $request;
		$this->authentication = $authentication;
		$this->auth();

		$this->users_associate_service = $users_associate_service;
	}


	public function sendRequestOnAssociate()
	{
		$this->checkRequestBody(['receiving_login'],true);

		$result = $this->users_associate_service->sendRequestOnAssociate($this->current_user_id, $this->request->getRequestBody()['receiving_login']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}


	public function acceptRequestOnAssociate()
	{
		$this->checkRequestBody(['requesting_login'], true);

		$result = $this->users_associate_service->acceptRequestOnAssociate($this->current_user_id, $this->request->getRequestBody()['requesting_login']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}

	public function denyRequestOnAssociate()
	{
		$this->checkRequestBody(['requesting_login'], true);

		$result = $this->users_associate_service->denyRequestOnAssociate($this->current_user_id, $this->request->getRequestBody()['requesting_login']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));

	}

	public function removeAssociate()
	{
		$this->checkRequestBody(['target_user_login'],true);

		$result = $this->users_associate_service->removeAssociate($this->current_user_id, $this->request->getRequestBody()['target_user_login']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code], $result['result']);
		$output = new Output();

		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));

	}




}