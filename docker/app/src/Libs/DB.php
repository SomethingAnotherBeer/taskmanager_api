<?php
declare(strict_types = 1);
namespace App\Libs;
use App\Exceptions\ConfigurationExceptions\DBBadParamsException;
class DB
{
	private static array $connection_params;

	public static function setConnectionParams(array $connection_params):void
	{

		self::checkConnectionParams($connection_params);
		self::$connection_params = $connection_params;

	}

	private static function checkConnectionParams(array $connection_params):void
	{
		$available_params = ['host','db','user','password'];
		
		foreach(array_keys($connection_params) as $connection_param_key)
		{
			if(!in_array($connection_param_key,$available_params))
			{
				throw new DBBadParamsException("Ошибка: Параметр $connection_param_key не является валидным для базы данных");
			}
		}


	}

	public function getConnection():\PDO
	{
		$host = self::$connection_params['host'];
		$db = self::$connection_params['db'];
		$user = self::$connection_params['user'];
		$password = self::$connection_params['password'];
		
		$connection = new \PDO("pgsql:host=$host;dbname=$db",$user,$password);
		$connection->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

		return $connection;
	}


}