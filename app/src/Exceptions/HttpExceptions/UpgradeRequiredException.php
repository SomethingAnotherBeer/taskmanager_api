<?php
declare(strict_types = 1);
namespace App\Exceptions\HttpExceptions;

class UpgradeRequiredException extends HttpException
{
	public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->setHttpCode('HTTP/1.1 426 Upgrade Required');
	}
}