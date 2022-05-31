<?php
declare(strict_types = 1);
namespace App\Http\Controllers;
use App\Http\Request;
use App\Services\AppServices\AuthenticationService;
use App\Exceptions\HttpExceptions\BadRequestException;
use App\Exceptions\AppExceptions\AuthenticationRequiredException;

abstract class Controller
{
	protected Request $request;
	protected AuthenticationService $authentication;
	protected int $current_user_id;

	public function checkRequestBody(array $available_params_keys, bool $strict = false, array $subparams = []):void
	{	
		$request_body = $this->request->getRequestBody();

		if([] === $request_body)
		{
			throw new BadRequestException("Ошибка: Пустое или некорректное тело запроса");
		}


		if($subparams)
		{	
			foreach($subparams as $subparam)
			{
				if(in_array($subparam, array_keys($request_body)) && is_array($request_body[$subparam]))
				{
					$request_body = $request_body[$subparam];
				}
			}
		}


		foreach(array_keys($request_body) as $request_body_key)
		{
			if(!in_array($request_body_key, $available_params_keys)) throw new BadRequestException("Ошибка: Параметр $request_body_key не является валидным для данного запроса");
		}

		if($strict)
		{
			foreach($available_params_keys as $available_param_key)
			{
				if(!in_array($available_param_key,array_keys($request_body))) throw new BadRequestException("Ошибка: Ожидаемый параметр $available_param_key не найден в теле запроса");
			}
		}


	}


	public function checkNeedleRequestBodyParams(array $available_params_keys, array $subparams = []):void
	{
		$request_body = $this->request->getRequestBody();
		$request_body_keys = [];

		if([] === $request_body) throw new BadRequestException("Ошибка: Пустое или некорректное тело запроса");


		if($subparams)
		{
			foreach($subparams as $subparam)
			{
				if(in_array($subparam, array_keys($request_body)) && is_array($request_body[$subparam]))
				{
					$request_body = $request_body[$subparam];
				}
			}
		}

		$request_body_keys = array_keys($request_body);

		foreach($available_params_keys as $available_param_key)
		{
			if(!in_array($available_param_key, $request_body_keys)) throw new BadRequestException("Ошибка: Ожидаемый параметр $available_param_key не найден в теле запроса");
		}



	}


	public function checkRequestParams(array $available_params_keys):void
	{
		$request_params_keys = array_keys($this->request->getRequestParams());

		foreach($request_params_keys as $request_param_key)
		{
			if(!in_array($request_param_key,$available_params_keys)) throw new BadRequestException("Ошибка: Параметр запроса $request_param_key не является валидным для данного ресурса");
		}
	}


	protected function auth():void
	{
		if(!$this->request->getAuthenticationKey()) throw new BadRequestException("Ошибка: Ключ аутентификации не указан");

		if (!($this->authentication instanceof AuthenticationService)) throw new AuthenticationRequiredException("Запрос должен включать в себя аутентификацию");
		
		$this->current_user_id = $this->authentication->getAuthenticatedUserId($this->request->getAuthenticationKey());

		unset($this->authentication);
		

	}






}