<?php
namespace App\Exceptions\HttpExceptions;

class NotFoundException extends HttpException
{
	public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->setHttpCode('HTTP/1.1 404 Not Found');
	}
}