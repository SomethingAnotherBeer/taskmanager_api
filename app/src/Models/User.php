<?php
declare(strict_types = 1);
namespace App\Models;
use App\Contracts\Model;


class User implements Model
{
	private int $user_id;
	private int $user_rights_id;
	private string $user_login;
	private string $user_password;
	private string $user_email;
	private string $user_name;
	private string $user_surname;


	public function __construct(array $user_params)
	{
		$this->user_id = $user_params['user_id'];
		$this->user_rights_id = $user_params['user_rights_id'];
		$this->user_login = $user_params['user_login'];
		$this->user_password = $user_params['user_password'];
		$this->user_email = isset($user_params['user_email']) ? $user_params['user_email'] : '';
		$this->user_name = isset($user_params['user_name']) ? $user_params['user_name'] : '';
		$this->user_surname = isset($user_params['user_surname']) ? $user_params['user_surname'] : '';

	}


	public function getUserId():int
	{
		return $this->user_id;
	}

	public function getUserRightsId():int
	{
		return $this->user_rights_id;
	}

	public function getUserLogin():string
	{
		return $this->user_login;
	}

	public function getUserPassword():string
	{
		return $this->user_password;
	}


	public function getUserEmail():string
	{
		return $this->user_email;
	}

	public function getUserName():string
	{
		return $this->user_name;
	}

	public function getUserSurname():string
	{
		return $this->user_surname;
	}


	public function getUserFullName():string
	{
		return ($this->user_name && $this->user_surname) ? $this->user_name . " " . $this->user_surname : 'Анонимный пользователь';
	}


	public function setUserRightsId(int $user_rights_id):void
	{
		$this->user_rights_id = $user_rights_id;
	}

	public function setUserLogin(string $user_login):void
	{
		$this->user_login = $user_login;
	}

	public function setUserPassword(string $user_password):void
	{
		$this->user_password = $user_password;
	}

	public function setUserEmail(string $user_email):void
	{
		$this->user_email = $user_email;
	}

	public function setUserName(string $user_name):void
	{
		$this->user_name = $user_name;
	}

	public function setUserSurname(string $user_surname):void
	{
		$this->user_surname = $user_surname;
	}

	public function getAllProperties():array
	{
		$user_properties = [];

		$user_properties['user_id'] = $this->getUserId();
		$user_properties['user_rights_id'] = $this->getUserRightsId();
		$user_properties['user_login'] = $this->getUserLogin();
		$user_properties['user_password'] = $this->getUserPassword();
		$user_properties['user_name'] = $this->getUserName();
		$user_properties['user_surname'] = $this->getUserSurname();

		return $user_properties;
	}



}