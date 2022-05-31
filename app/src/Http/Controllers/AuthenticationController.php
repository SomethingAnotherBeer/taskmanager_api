<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Http\Response;
use App\Output\Output;
use App\Services\AppServices\AuthenticationService;

use App\Exceptions\HttpExceptions\BadRequestException;

class AuthenticationController extends Controller
{
	public function __construct(Request $request, AuthenticationService $authentication)
	{
		$this->request = $request;
		$this->authentication = $authentication;
	}

	public function login()
	{
		$this->checkRequestBody(['user_login','user_password'], true);

		$result = $this->authentication->login($this->request->getRequestBody()['user_login'], $this->request->getRequestBody()['user_password']);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));


	}

	public function logout()
	{
		$authentication_key = $this->request->getAuthenticationKey();

		if(!$authentication_key) throw new BadRequestException("Не указан ключ аутентификации");

		$result = $this->authentication->logout($authentication_key);

		$http_code = ($result['status']) ? 'HTTP/1.1 201 Created' : 'HTTP/1.1 200 OK';

		$response = new Response([$http_code],$result['result']);
		$output = new Output();
		$output->display(json_encode($response->createResponse(), JSON_UNESCAPED_UNICODE));
	}



}
