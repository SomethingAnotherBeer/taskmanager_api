<?php
declare(strict_types = 1);
namespace App\Services\DomainServices;

use App\Mappers\GroupsMapper;
use App\Mappers\UserMapper;
use App\Services\AppServices\PaginationService;

use App\Exceptions\DBExceptions\DBException;
use App\Exceptions\ModelExceptions\UserExceptions\UserNotFoundException;

class GroupsService 
{
	private GroupsMapper $groups_mapper;
	private UserMapper $user_mapper;
	private PaginationService $paginator;

	public function __construct(GroupsMapper $groups_mapper, UserMapper $user_mapper, PaginationService $paginator)
	{
		$this->groups_mapper = $groups_mapper;
		$this->user_mapper = $user_mapper;
		$this->paginator = $paginator;
	}

	
	public function getAllGroups(int $page):array
	{
		try
		{
			$this->paginator->setCurrentList($page);
			$result = $this->groups_mapper->getAllGroups($this->paginator->getLimit(), $this->paginator->getOffset());

			return ($result) ? ['result'=>['message'=>$result],'status'=>true]:
				['result'=>['message'=>'Группы не найдены'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}
	}


	public function getMyGroups(int $user_id, int $page):array
	{
		try
		{
			$this->paginator->setCurrentList($page);

			$result = $this->groups_mapper->getGroupsByUserId($user_id, $this->paginator->getLimit(), $this->paginator->getOffset());

			return ($result) ? ['result'=>['message'=>$result],'status'=>true]:
				['result'=>['message'=>'Вы не состоите ни в одной из групп'],'status'=>false];
		}

		catch(DBException $e)
		{
			throw $e;
		}
	}


	public function getUserGroups(string $user_login, int $page):array
	{
		$result = [];
		$user_id = 0;

		try
		{
			$user_id = $this->user_mapper->getUserByLogin($user_login)->getUserId();
			$this->paginator->setCurrentList($page);

			$result = $this->groups_mapper->getGroupsByUserId($user_id, $this->paginator->getLimit(), $this->paginator->getOffset());

			return ($result) ? ['result'=>['message'=>$result],'status'=>true]:
				['result'=>['message'=>'Данный пользователь не состоит ни в одной из групп'],'status'=>false];


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