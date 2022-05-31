<?php
namespace App\Exceptions\HttpExceptions;

class BadRequestException extends HttpException
{
	public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->setHttpCode('HTTP/1.1 400 Bad Request');
	}
}