<?php
declare(strict_types = 1);
namespace App\Http;

class Response
{
	private static array $included_headers = [];
	private static array $additionals = [];
	private static int $is_included = 0;
	private array $headers;
	private array $response;

	public function __construct(array $headers, array $response)
	{
		$this->headers = array_merge(self::$included_headers,$headers);
		$this->response = $response;
		self::$is_included = 1;
	}


	public function createResponse()
	{
		foreach($this->headers as $header)
		{
			header($header);
		}

		return  (self::$additionals) ?  array_merge($this->response, self::$additionals) : $this->response;
	}


	public static function setHeader(string $header)
	{
		if(self::$is_included) throw new ResponseException("Ошибка: Объект ответа уже создан, невозможно инициализировать включаемые заголовки");

		self::$included_headers[] = $header;
	}


	public static function setAdditionals(array $additionals)
	{
		self::$additionals[] = $additionals;
	}


}