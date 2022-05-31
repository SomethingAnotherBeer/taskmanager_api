<?php
declare(strict_types = 1);
namespace App;

use DI\Container;
use App\Http\Request;
use App\Http\Response;
use App\Http\Middleware;
use App\Output\Output;
use App\Libs\DB;
use App\Core\Router;

use App\Exceptions\HttpExceptions\HttpException;
use App\Exceptions\AppExceptions\AppException;
use App\Exceptions\ConfigurationExceptions\ConfigurationException;
use App\Exceptions\HttpExceptions\NotFoundException;
use App\Exceptions\ModelExceptions\ModelException;
use App\Exceptions\ValidationExceptions\ValidationException;

class App
{
	private array $configuration;
	private Container $container;

	public function __construct(array $configuration, Container $container)
	{
		$this->configuration = $configuration;
		$this->container = $container;
	}


	public function run():void
	{
		try
		{	

			$request = new Request();
			$middleware = new Middleware($request, $this->configuration['middleware']);
			$middleware->checkRequest();
				
			unset($middleware);

			Response::setHeader('Content-Type: application/json');
			DB::setConnectionParams($this->configuration['db']);
			$router = new Router($request,$this->container);
			$router->route();
			
		}

		catch(HttpException $e)
		{	

			$log_date = date("Y:m:d H:i:s");
			file_put_contents("../src/Logs/error_logs/http_errors.txt",$log_date . ":". $e->getMessage()."\n", FILE_APPEND);
			$http_code = $e->getHttpCode();	
			$response = new Response([$http_code],['error'=>$e->getMessage()]); 
			$this->createExceptionMessage($response->createResponse());

		}

		catch(ModelException $e)
		{	
			$log_date = date("Y:m:d H:i:s");
			file_put_contents("../src/Logs/error_logs/model_errors.txt",$log_date . ":" . $e->getMessage(). "\n", FILE_APPEND);
			$http_code = 'HTTP/1.1 200 OK';
			$response = new Response([$http_code],['error'=>$e->getMessage()]);
			$this->createExceptionMessage($response->createResponse());

		}

		catch(AppException $e)
		{	
			$log_date = date("Y:m:d H:i:s");
			file_put_contents("../src/Logs/error_logs/app_errors.txt",$log_date .":". $e->getMessage().'\n', FILE_APPEND);
			$this->createExceptionMessage(['app_error'=>$e->getMessage()]);

		}
		catch(ConfigurationException $e)
		{
			$log_date = date("Y:m:d H:i:s");
			file_put_contents("../src/Logs/error_logs/configuration_errors.txt", $log_date .":". $e->getMessage()."\n",FILE_APPEND);
			$this->createExceptionMessage(['configuration_error'=>$e->getMessage()]);
		}
		
		catch(DBException $e)
		{
			$log_date = date("Y:m:d H:i:s");
			file_put_contents("../src/Logs/error_logs/db_errors.txt", $log_date . ":" . $e->getMessage()."\n", FILE_APPEND);
			$this->createExceptionMessage(['db_error'=>$e->getMessage()]);
		}

		catch(ValidationException $e)
		{
			$log_date = date("Y:m:d H:i:s");
			file_put_contents("../src/Logs/error_logs/validation_errors.txt", $log_date . ":" . $e->getMessage()."\n", FILE_APPEND);
			$this->createExceptionMessage(['validation_error'=>$e->getMessage()]);
		}

		

		catch(\Exception $e)
		{
			
			die($e->getMessage());
		}



	}

	private function createExceptionMessage(array $message):void
	{
		$output = new Output();

		$message = json_encode($message,JSON_UNESCAPED_UNICODE);
		$output->display($message);

	}


}