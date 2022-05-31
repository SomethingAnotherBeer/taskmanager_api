<?php
namespace App\Exceptions\HttpExceptions;

abstract class HttpException extends \Exception
{
	protected $http_code;
	
	 protected function setHttpCode(string $http_code):void
	 {
	 	$this->http_code = $http_code;
	 }

	public function getHttpCode():string
	{
		return $this->http_code;
	}
}