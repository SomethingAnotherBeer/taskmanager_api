<?php
declare(strict_types = 1);
namespace App\Services\DomainServices;
use App\Mappers\UsersMapper;
use App\Services\AppServices\PaginationService;
use App\Exceptions\DBExceptions\DBException;

class UsersService
{
	private UsersMapper $users_mapper;
	private PaginationService $paginator;

	public function __construct(UsersMapper $users_mapper, PaginationService $paginator)
	{
		$this->users_mapper = $users_mapper;
		$this->paginator = $paginator;
	}

	public function getUsers(int $page):array
	{
		try
		{	
			$this->paginator->setCurrentList($page);

			$result = $this->users_mapper->getAllUsers($this->paginator->getLimit(), $this->paginator->getOffset());
			return ($result) ? ['result'=>$result,'status'=>true] : ['result'=>['message'=>'Пользователи не найдены'],'status'=>false];

		}


		catch(DBException $e)
		{
			throw $e;
		}

	}


	public function getUsersByStatus(int $page, string $user_rights_name):array
	{
		try
		{
			$this->paginator->setCurrentList($page);

			$result = $this->users_mapper->getUsersByStatus($user_rights_name, $this->paginator->getLimit(), $this->paginator->getOffset());

			return ($result) ? ['result'=>$result,'status'=>true]:
				['result'=>['message'=>$this->getUserStatusNotFoundCondition($user_rights_name)],'status'=>false];
		}


		catch(DBException $e)
		{
			throw $e;
		}
	}


	public function getUsersByGroup(int $page, string $group_name):array
	{
		try
		{
			$this->paginator->setCurrentList($page);

			$result = $this->users_mapper->getUsersByGroup($group_name, $this->paginator->getLimit(), $this->paginator->getOffset());
			$users_in_group_count = $this->users_mapper->getUsersCountFromGroup($group_name);

			if($result)
			{
				return ['result'=>$result,'status'=>true];
			}

			else
			{	
				return ($users_in_group_count) ? ['result'=>['message'=>'В данной группе больше нет пользователей'],'status'=>false]:
					['result'=>['message'=>'В данной группе нет пользователей'], 'status'=>false];
			}

		}

		catch(DBException $e)
		{
			throw $e;
		}

	}


	public function getOutRequestsOnAssociate(int $user_id):array
	{
		try
		{
			$result = $this->users_mapper->getOutRequestsOnAssociate($user_id);

			return ($result) ? ['result'=>$result,'status'=>true] : 
				['result'=>['message'=>'У вас нет исходящих запросов на сотрудничество'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}
	}


	public function getInRequestsOnAssociate(int $user_id):array
	{
		try
		{
			$result = $this->users_mapper->getInRequestsOnAssociate($user_id);

			return ($result) ? ['result'=>$result,'status'=>true]:
				['result'=>['message'=>'У вас нет входящих запросов на сотрудничество'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}
	}


	public function getAssociatedUsers(int $user_id):array
	{
		try
		{	
			$result = $this->users_mapper->getAssociatedUsers($user_id);

			return ($result) ? ['result'=>$result, 'status'=>true]:
				['result'=>['message'=>'Коллеги не найдены'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}
	}


	private function getUserStatusNotFoundCondition(string $user_status):string
	{
		$users_statuses =[
			'admin'=>'Не найдены пользователи со статусом admin',
			'user'=>'Не найдены пользователи со статусом user'

		];

		return $users_statuses[$user_status];
	}
	





}