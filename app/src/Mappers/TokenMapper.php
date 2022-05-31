<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;
use App\Models\Token;
use App\Exceptions\ModelExceptions\TokenExceptions\TokenNotFoundException;


class TokenMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('tokens');
	}


	public function getTokenById(int $token_id):Token
	{
		$query_string = "WHERE token_id = :token_id";
		$query_params = [':token_id'=>$token_id];

		$token_params = $this->adapter->find($query_string, $query_params);

		if(!$token_params) throw new TokenNotFoundException("Токен не найден");

		return new Token($token_params);
	}

	public function getTokenByKey(string $token_key):Token
	{
		$query_string = "WHERE token_key = :token_key";
		$query_params = [':token_key'=>$token_key];

		$token_params = $this->adapter->find($query_string, $query_params);
		if(!$token_params) throw new TokenNotFoundException("Токен не найден");
		return new Token($token_params);

	
	}

	public function getTokenByUserId(int $user_id):Token
	{
		$query_string = "WHERE user_id = :user_id";
		$query_params = [':user_id'=>$user_id];

		$token_params = $this->adapter->find($query_string, $query_params);

		if(!$token_params) throw new TokenNotFoundException("Токен не найден");

		return new Token($token_params);
	}


	public function checkTokenByUserId(int $user_id):bool
	{
		$query_string = "WHERE user_id = :user_id";
		$query_params = [':user_id'=>$user_id];

		return ($this->adapter->find($query_string, $query_params, ['token_id'])) ? true : false;
	}


	public function createToken(array $params):bool
	{
		return $this->adapter->create($params);
	}

	public function updateToken(Token $token):bool
	{
		$token_id = $token->getTokenId();
		$query_string = "WHERE token_id = :token_id";

		$current_params = $token->getAllProperties();
		$old_params = $this->getTokenById($token_id)->getAllProperties();

		$new_params = $this->getUpdatedParams($current_params, $old_params);
		
		if(!$new_params) return false;

		return $this->adapter->update($query_string, $new_params,[':token_id'=>$token_id]);

	}	

	public function deleteToken(Token $token):bool
	{
		$token_id = $token->getTokenId();
		$query_string = "WHERE token_id = :token_id";
		$query_params = [':token_id'=>$token_id];

		return $this->adapter->delete($query_string, $query_params);
	}


}