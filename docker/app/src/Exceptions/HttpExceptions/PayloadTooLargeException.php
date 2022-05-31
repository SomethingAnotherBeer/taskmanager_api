<?php
namespace App\Exceptions\HttpExceptions;

class PayloadTooLargeException extends HttpException
{
	public function __construct(string $message = '', int $code = 0, \Throwable $previous)
	{

	}

	public function setHttpCode():void
	{
		$this->http_code = 'HTTP/1.1 413 Payload Too Large';
	}
}