<?php
declare(strict_types = 1);
namespace App\Services\AppServices;
use App\Http\Response;
use App\Mappers\UserMapper;
use App\Mappers\TokenMapper;

use App\Exceptions\DBExceptions\DBException;

use App\Exceptions\HttpExceptions\BadRequestException;
use App\Exceptions\HttpExceptions\UnauthorizedException;
use App\Exceptions\HttpExceptions\UpgradeRequiredException;

use App\Exceptions\ModelExceptions\UserExceptions\UserNotFoundException;
use App\Exceptions\ModelExceptions\UserExceptions\UserInvalidPasswordException;
use App\Exceptions\ModelExceptions\TokenExceptions\TokenNotFoundException;
use App\Exceptions\ModelExceptions\TokenExceptions\TokenOverdueException;

class AuthenticationService
{
	private UserMapper $user_mapper;
	private TokenMapper $token_mapper;
	private int $token_untill_from_now;
	private bool $needle_reauth;

	public function __construct(UserMapper $user_mapper, TokenMapper $token_mapper)
	{
		$this->user_mapper = $user_mapper;
		$this->token_mapper = $token_mapper;
		$this->token_untill_from_now = 86400;
		$this->needle_reauth = false;

	}

	public function login(string $user_login, string $user_password)
	{
		$token_key = '';
		$prepared_token_params = [];

		try
		{	
			$user = $this->user_mapper->getUserByLogin($user_login);
			
			if(!password_verify($user_password, $user->getUserPassword()))
			{
				throw new UserInvalidPasswordException();
			}

			if($this->token_mapper->checkTokenByUserId($user->getUserId()))
			{
				$this->removeOldToken($user->getUserId());
			}

			$token_key = $this->generateAndGetTokenKey();

			$prepared_token_params['user_id'] = $user->getUserId();
			$prepared_token_params['token_key'] = md5($token_key);
			$prepared_token_params['token_untill'] = time() + $this->token_untill_from_now;

			$result = $this->token_mapper->createToken($prepared_token_params);
 
			return ($result) ? ['result'=>['message'=>'Аутентификация прошла успешно','token_key'=>$token_key],'status'=>true] :
				['result'=>['message'=>'Не удалось осуществить аутентификацию'], 'status'=>false];




		}

		catch(DBException | ConditionQueryException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException | UserInvalidPasswordException $e)
		{
			return ['result'=>['message'=>'Неправильный логин или пароль'],'status'=>false];
		}

	}

	public function logout(string $authentication_key):array
	{

		try
		{
			$token = $this->token_mapper->getTokenByKey(md5($authentication_key));
			$result = $this->token_mapper->deleteToken($token);

			return ($result) ? ['result'=>['message'=>'Выход произведен успешно'],'status'=>true]:
				['result'=>['message'=>'Не удалось осуществить выход'],'status'=>false];

		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TokenNotFoundException $e)
		{
			throw new UnauthorizedException("Вы не аутентифицированы");
		}



	}


	public function getAuthenticatedUserId(string $authentication_key):int
	{
		try
		{
			$token = $this->token_mapper->getTokenByKey(md5($authentication_key));

			if($token->getTokenUntill() - time() <= 0) throw new TokenOverdueException("Токен устарел, необходима повторная аутентификация");

			if(($token->getTokenUntill() - time()) < $this->token_untill_from_now / 3)
			{
				$new_token_key = $this->generateAndGetTokenKey();
				$token->setTokenKey(md5($new_token_key));
				$token->setTokenUntill(time() + $this->token_untill_from_now);
				$this->token_mapper->updateToken($token);

				Response::setAdditionals(['new_token_key'=>$new_token_key]);


			}

			return $token->getUserId();

			



		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(TokenNotFoundException $e)
		{
			throw new UnauthorizedException("Вы не аутентифицированы");
		}

		catch(TokenOverdueException $e)
		{
			throw new UpgradeRequiredException($e->getMessage());
		}


	}


	private function generateAndGetTokenKey():string
	{
		$token_key = '';
		$token_len = 25;

		$current_ch_group = 0;

		for($i = 0; $i < $token_len; $i++)
		{
			if(0 !== $i && 0 === ($i % 5))
			{
				$token_key[$i] = '-';
			}

			$current_ch_group = rand(0,2);	

			switch($current_ch_group)
			{
				case 0:
					$token_key[$i] = chr(rand(97, 122));
				break;

				case 1:
					$token_key[$i] = rand(0, 9);
				break;

				case 2:
					$token_key[$i] = chr(rand(65, 90));
				break;
			}

		}

		return $token_key;

	}


	private function removeOldToken(int $user_id):void
	{
		$token = $this->token_mapper->getTokenByUserId($user_id);
		$this->token_mapper->deleteToken($token);

	}	


}