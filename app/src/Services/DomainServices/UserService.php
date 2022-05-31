<?php
declare(strict_types = 1);
namespace App\Services\DomainServices;
use App\Mappers\UserMapper;
use App\Mappers\UserRightsMapper;
use App\Exceptions\DBExceptions\DBException;
use App\Exceptions\ModelExceptions\UserExceptions\UserNotFoundException;
use App\Exceptions\ModelExceptions\UserExceptions\UserAlreadyExistsException;
use App\Exceptions\ValidationExceptions\InvalidParamException;

class UserService
{
	private UserMapper $user_mapper;
	private UserRightsMapper $user_rights_mapper;

	public function __construct(UserMapper $user_mapper, UserRightsMapper $user_rights_mapper)
	{
		$this->user_mapper = $user_mapper;
		$this->user_rights_mapper = $user_rights_mapper;
	}


	public function createUser(array $user_params):array
	{
		$prepared_user_params = [];
		$user_rights_name = isset($user_params['user_rights_name']) ? $user_params['user_rights_name'] : 'user';
		$user_rights_id = 0;


		try
		{

			if($this->user_mapper->checkUserByLogin($user_params['user_login']))
			{
				throw new UserAlreadyExistsException("Данный пользователь уже существует в системе");
			}

			$user_rights_id = $this->user_rights_mapper->getUserRightsByName($user_rights_name)->getUserRightsId();


			$prepared_user_params['user_login'] = $user_params['user_login'];
			$prepared_user_params['user_password'] = password_hash($user_params['user_password'], PASSWORD_DEFAULT);
			$prepared_user_params['user_email'] = (isset($user_params['user_email'])) ? $user_params['user_email'] : $user_params['user_login']."@taskmanager.com";
			$prepared_user_params['user_rights_id'] = $user_rights_id;
			list($prepared_user_params['user_name'], $prepared_user_params['user_surname']) = (isset($user_params['user_name']) && isset($user_params['user_surname'])) ? 
				[$user_params['user_name'], $user_params['user_surname']] : ['',''];



			$result = $this->user_mapper->createUser($prepared_user_params);

			return ($result) ? ['result'=>['message'=>'Пользователь успешно создан'],'status'=>true] : 
				['result'=>['message'=>'Не удалось создать пользователя'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserAlreadyExistsException $e)
		{
			throw $e;
		}
	}


	public function changeUserLogin(string $user_login, string $new_user_login):array
	{

		try
		{
			if(0 === mb_strlen($new_user_login)) throw new InvalidParamException("Логин пользователя не может быть пустым");


			if(($new_user_login !== $user_login) && $this->user_mapper->checkUserByLogin($new_user_login)) throw new UserAlreadyExistsException("Пользователь с данным логином уже определен в системе");

			$user = $this->user_mapper->getUserByLogin($user_login);
			$user->setUserLogin($new_user_login);

			$result = $this->user_mapper->updateUser($user);

			return ($result) ? ['result'=>['message'=>'Логин пользователя успешно обновлен'],'status'=>true]:
				['result'=>['message'=>'Логин пользователя идентичен текущему'],'status'=>false];

		}	

		catch(DBException $e)
		{
			throw $e;
		}

		catch(InvalidParamException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException | UserAlreadyExistsException $e)
		{
			throw $e;
		}
	}


	public function changeUserPassword(string $user_login, string $new_user_password):array
	{
		try
		{
			if(0  === mb_strlen($new_user_password)) throw new InvalidParamException("Пароль пользователя не может быть пустым");

			$user = $this->user_mapper->getUserByLogin($user_login);

			if(password_verify($new_user_password, $user->getUserPassword()))
			{
				return ['result'=>['message'=>'Новый пароль пользователя не может совпадать с текущим'],'status'=>false];
			}

			$user->setUserPassword(password_hash($new_user_password, PASSWORD_DEFAULT));

			$result = $this->user_mapper->updateUser($user);

			return ['result'=>['message'=>'Пароль пользователя успешно обновлен'],'status'=>true];
		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(InvalidParamException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}

	}


	public function changeUserFullName(string $user_login, array $user_params):array
	{

		try
		{	
			$user = $this->user_mapper->getUserByLogin($user_login);
			if(isset($user_params['new_user_name'])) $user->setUserName($user_params['new_user_name']);
			if(isset($user_params['new_user_surname'])) $user->setUserSurname($user_params['new_user_surname']);

			$result = $this->user_mapper->updateUser($user);

			return ($result) ? ['result'=>['message'=>'Пользователь успешно обновлен'],'status'=>true]:
				['result'=>['messsage'=>'Параметры обновления идентичны текущим параметрам пользователя'],'status'=>false];



		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}
	}


	public function deleteUser(string $user_login):array
	{
		try
		{
			$user = $this->user_mapper->getUserByLogin($user_login);
			$result = $this->user_mapper->deleteUser($user);

			return ($result) ? ['result'=>['message'=>'Пользователь успешно удален'],'status'=>true]:
				['result'=>['message'=>'Не удалось удалить пользователя'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}
	}


}