<?php
declare(strict_types = 1);
namespace App\Services\DomainServices;
use App\Mappers\UsersAssociateMapper;
use App\Mappers\UserMapper;

use App\Exceptions\DBExceptions\DBException;

use App\Exceptions\ModelExceptions\UserExceptions\UserNotFoundException;

use App\Exceptions\ModelExceptions\UsersAssociateExceptions\UsersAlreadyAssociateException;
use App\Exceptions\ModelExceptions\UsersAssociateExceptions\UsersAssociateNotFoundException;
use App\Exceptions\ModelExceptions\UsersAssociateExceptions\RequestOnAssociateNotFoundException;
use App\Exceptions\ModelExceptions\UsersAssociateExceptions\UsersNotAssociatedException;
use App\Exceptions\ModelExceptions\UsersAssociateExceptions\RecursiveAssociateException;
use App\Exceptions\ModelExceptions\UsersAssociateExceptions\RequestOnAssociateNotFound;



class UsersAssociateService
{
	private UsersAssociateMapper $users_associate_mapper;
	private UserMapper $user_mapper;

	public function __construct(UsersAssociateMapper $users_associate_mapper, UserMapper $user_mapper)
	{
		$this->users_associate_mapper = $users_associate_mapper;
		$this->user_mapper = $user_mapper;
	}


	public function sendRequestOnAssociate(int $requesting_id, string $receiving_login):array
	{
		$receiving_id = 0;
		$users_associate_params = [];

		try
		{
			$receiving_id = $this->user_mapper->getUserByLogin($receiving_login)->getUserId();

			if($requesting_id === $receiving_id)
			{
				throw new RecursiveAssociateException("Невозможно послать запрос на сотрудничество самому себе");
			}

			if($this->users_associate_mapper->checkUsersAssociate($requesting_id, $receiving_id))
			{
				throw new UsersAlreadyAssociateException("Вы уже посылали запрос данному пользователю или являетесь коллегами");
			}	

			$users_associate_params['user_one_id'] = $requesting_id;
			$users_associate_params['user_two_id'] = $receiving_id;
			$users_associate_params['associate_status'] ='request';

			$result = $this->users_associate_mapper->createUsersAssociate($users_associate_params);

			return ($result) ? ['result'=>['message'=>'Запрос на сотрудничество отправлен пользователю'],'status'=>true]:
				['result'=>['message'=>'Не удалось отправить запрос на сотрудничество'],'status'=>false];



		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}

		catch(UsersAlreadyAssociateException $e)
		{
			throw $e;
		}

	}


	public function acceptRequestOnAssociate(int $receiving_id, string $requesting_login):array
	{
		$requesting_id = 0;

		try
		{	
			$requesting_id = $this->user_mapper->getUserByLogin($requesting_login)->getUserId();
			$users_associate = $this->users_associate_mapper->getUsersAssociate($requesting_id, $receiving_id);

			if($users_associate->getAssociateStatus() === 'associate')
			{
				throw new UsersAlreadyAssociateException("Вы уже являетесь коллегами");
			}

			$users_associate->setAssociateStatus('associate');

			$result = $this->users_associate_mapper->updateUsersAssociate($users_associate);

			return ($result) ? ['result'=>['message'=>'Вы приняли запрос на сотрудничество'],'status'=>true] : ['result'=>['message'=>'Не удалось принять запрос на сотрудничество'],'status'=>false];




		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}

		catch(UsersAssociateNotFoundException $e)
		{
			throw new RequestOnAssociateNotFound("Данный пользователь не отправлял вам запрос на сотрудничество");
		}
	}


	public function removeAssociate(int $user_one_id, string $user_two_login):array
	{
		$user_two_id = 0;
		$message = '';

		try
		{	
			$user_two_id = $this->user_mapper->getUserByLogin($user_two_login)->getUserId();
			$users_associate = $this->users_associate_mapper->getUsersExtendedAssociate($user_one_id,$user_two_id);

			if($users_associate->getAssociateStatus() !== 'associate')
			{
				throw new UsersNotAssociatedException("Вы не являетесь коллегами");
			}

			$result = $this->users_associate_mapper->removeUsersAssociate($users_associate);

			return ($result) ? ['result'=>['message'=>'Пользователь удален из списка ваших коллег'],'status'=>true]:
				['result'=>['message'=>'Не удалось удалить пользователя из списка ваших коллег'],'status'=>false];

		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}

		catch(UsersAssociateNotFoundException $e)
		{
			throw new UsersNotAssociatedException("Вы не являетесь коллегами");
		}
	}



	public function denyRequestOnAssociate(int $receiving_id, string $requesting_login):array
	{
		$requesting_id = 0;

		try
		{
			$requesting_id = $this->user_mapper->getUserByLogin($requesting_login)->getUserId();
			$users_associate = $this->users_associate_mapper->getUsersAssociate($requesting_id, $receiving_id);

			if($users_associate->getAssociateStatus() === 'associate')
			{
				throw new UsersAlreadyAssociateException('Невозможно отклонить ранее принятый запрос на сотрудничество');
			}

			$result = $this->users_associate_mapper->removeUsersAssociate($users_associate);

			return ($result) ? ['result'=>['message'=>'Запрос на сотрудничество отклонен'],'status'=>true]:
				['result'=>['message'=>'Не удалось отклонить запрос на сотрудничество'],'status'=>false];


		}	

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException $e)
		{
			throw $e;
		}

		catch (UsersAssociateNotFoundException $e)
		{
			throw new RequestOnAssociateNotFoundException("Данный пользователь не посылал вам запрос на сотрудничество");
		}
	}


}