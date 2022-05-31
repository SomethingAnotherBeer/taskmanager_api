<?php
declare(strict_types = 1);
namespace App\Http;

class Request
{
	private array $request_body;
	private array $request_params;
	private string $authentication_key;
	private string $request_method;
	private string $request_uri;

	public function __construct()
	{
		$this->setRequestBody();
		$this->setRequestParams();
		$this->setRequestMethod();
		$this->setRequestUri();
		$this->setAuthenticationKey();
	}

	private function setRequestMethod():void
	{
		$this->request_method = $_SERVER['REQUEST_METHOD'];
	}

	private function setRequestBody():void
	{
		$request_body = json_decode(file_get_contents("php://input"), true);

		$this->request_body = (!empty($request_body)) ? $request_body : [];
	}

	private function setRequestParams():void
	{
		$this->request_params = $_GET;

	}
	
	private function setRequestUri():void
	{
		$this->request_uri = $_SERVER['REQUEST_URI'];
	}

	private function setAuthenticationKey():void
	{
		$this->authentication_key = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
	}


	public function getRequestMethod():string
	{
		return $this->request_method;
	}

	public function getRequestBody():array
	{
		return $this->request_body;
	}

	public function getRequestParams():array
	{
		return $this->request_params;
	}

	public function getRequestUri():string
	{
		return $this->request_uri;
	}

	public function getAuthenticationKey():string
	{
		return $this->authentication_key;
	}


}