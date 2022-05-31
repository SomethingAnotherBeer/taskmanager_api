<?php
declare(strict_types = 1);
namespace App\Models;
use App\Contracts\Model;

class Token
{
	private int $token_id;
	private int $user_id;
	private string $token_key;
	private int $token_untill;

	public function __construct(array $token_params)
	{
		$this->token_id = $token_params['token_id'];
		$this->user_id = $token_params['user_id'];
		$this->token_key = $token_params['token_key'];
		$this->token_untill = $token_params['token_untill'];
	}

	public function getTokenId():int
	{
		return $this->token_id;
	}

	public function getUserId():int
	{
		return $this->user_id;
	}

	public function getTokenKey():string
	{
		return $this->token_key;
	}

	public function getTokenUntill():int
	{
		return $this->token_untill;
	}


	public function setTokenKey(string $token_key):void
	{
		$this->token_key = $token_key;
	}

	public function setTokenUntill(int $token_untill):void
	{
		$this->token_untill = $token_untill;
	}


	public function getAllProperties():array
	{
		$token_properties = [];

		$token_properties['token_id'] = $this->getTokenId();
		$token_properties['user_id'] = $this->getUserId();
		$token_properties['token_key'] = $this->getTokenKey();
		$token_properties['token_untill'] = $this->getTokenUntill();

		return $token_properties;
	}

	

}