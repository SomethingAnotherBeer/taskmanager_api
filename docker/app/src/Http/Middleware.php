<?php
declare(strict_types = 1);
namespace App\Http;
use App\Helpers;

use App\Exceptions\HttpExceptions;
use App\Exceptions\ConfigurationExceptions\MiddlewareBadParamsException;
class Middleware
{
	private Request $request;
	private array $middleware_params;

	public function __construct(Request $request, array $middleware_params)
	{
		$this->checkMiddleWareParams($middleware_params);
		$this->request = $request;
		$this->middleware_params = $middleware_params;
	}

	public function checkRequest()
	{

		$this->checkRequestBody();
		$this->checkRequestParams();
	}


	private function checkRequestBody():void
	{
		$request_body_len = Helpers\getArrLengthRecursive($this->request->getRequestBody());
		
		if($request_body_len > $this->middleware_params['max_body_len'])
		{
			throw new HttpExceptions\PayloadTooLargeException("Ошибка: Тело запроса превышает максимально допустимую величину");
		}

	}

	private function checkRequestParams():void
	{
		$request_params_len = Helpers\getArrLengthRecursive($this->request->getRequestParams());
		if($request_params_len > $this->middleware_params['max_params_len'])
		{
			throw new HttpExceptions\PayloadTooLargeException("Ошибка: Величина параметров запроса превышает допустимую величину");
		}
	}

	private function checkMiddleWareParams(array $middleware_params):void
	{
		$accepted_middleware_params = ['max_body_len','max_params_len'];

		foreach (array_keys($middleware_params) as $middleware_param_key)
		{
			if(!in_array($middleware_param_key,$accepted_middleware_params))
			{
				throw new MiddlewareBadParamsException("Ошибка: параметр $middleware_param_key не является допустимым для middleware");
			}
		}

	}

}