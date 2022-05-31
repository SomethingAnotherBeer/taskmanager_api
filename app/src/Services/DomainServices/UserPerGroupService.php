<?php
declare(strict_types = 1);
namespace App\Services\DomainServices;

use App\Mappers\UserPerGroupMapper;
use App\Mappers\UserMapper;
use App\Mappers\GroupMapper;

use App\Exceptions\DBExceptions\DBException;

use App\Exceptions\ModelExceptions\UserExceptions\UserNotFoundException;
use App\Exceptions\ModelExceptions\GroupExceptions\GroupNotFoundException;

use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserPerGroupNotFoundException;
use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserNotInvitedToGroupException;
use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserNotGroupMemberException;
use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserNotGroupAdminException;
use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserAlreadyGroupMemberException;

class UserPerGroupService 
{
	private UserPerGroupMapper $user_per_group_mapper;
	private UserMapper $user_mapper;
	private GroupMapper $group_mapper;

	public function __construct(UserPerGroupMapper $user_per_group_mapper, UserMapper $user_mapper, GroupMapper $group_mapper)
	{
		$this->user_per_group_mapper = $user_per_group_mapper;
		$this->user_mapper = $user_mapper;
		$this->group_mapper = $group_mapper;
	}


	public function inviteUserToGroup(int $inviter_id, string $invited_login, string $group_name):array
	{
		$invited_id = 0;
		$group_id = 0;
		$invited_params = [];

		try
		{
			
			$group_id = $this->group_mapper->getGroupByName($group_name)->getGroupId();
			
			if(!$this->user_per_group_mapper->checkUserPerGroup($inviter_id, $group_id))
			{
				throw new UserNotGroupMemberException("Вы не являетесь членом данной группы");
			}

			$invited_id = $this->user_mapper->getUserByLogin($invited_login)->getUserId();

			if($this->user_per_group_mapper->checkUserPerGroup($invited_id, $group_id))
			{
				throw new UserAlreadyGroupMemberException("Данный пользователь либо уже приглашен, либо является членом группы");
			}


			$invited_params['user_id'] = $invited_id;
			$invited_params['group_id'] = $group_id;
			$invited_params['user_status'] = 'invited';

			$result = $this->user_per_group_mapper->addUserToGroup($invited_params);

			return ($result) ? ['result'=>['message'=>'Пользователь приглашен'],'status'=>true] : ['result'=>['message'=>'Не удалось пригласить пользователя'],'status'=>false];

		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException | GroupNotFoundException $e)
		{
			throw $e;
		}

		catch(UserNotGroupMemberException | UserAlreadyGroupMemberException $e)
		{
			throw $e;
		}


	}


	public function acceptInviteToGroup(int $invited_id, string $group_name):array
	{
		$group_id = 0;

		try
		{
			$group_id = $this->group_mapper->getGroupByName($group_name)->getGroupId();

			$invite = $this->user_per_group_mapper->getUserPerGroup($invited_id, $group_id);

			if($invite->getUserStatus() === 'member')
			{
				throw new UserAlreadyGroupMemberException('Приглашение в группу недействительно: Вы являетесь членом группы');
			}

			$invite->setUserStatus('member');

			$result = $this->user_per_group_mapper->updateUserPerGroup($invite);

			return ($result) ? ['result'=>['message'=>"Теперь вы член группы $group_name"],'status'=>true] : ['result'=>['message'=>'Не удалось принять приглашение'],'status'=>false];

		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(UserNotFoundException | GroupNotFoundException $e)
		{
			throw $e;
		}

		catch(UserAlreadyGroupMemberException $e)
		{
			throw $e;
		}

		catch(UserPerGroupNotFoundException $e)
		{
			throw new UserNotInvitedToGroupException("Вы не были приглашены в группу");
		}

	}


	public function denyInviteToGroup(int $invited_id, string $group_name):array
	{
		$group_id = 0;

		try
		{
			$group_id = $this->group_mapper->getGroupByName($group_name)->getGroupId();

			$invite = $this->user_per_group_mapper->getUserPerGroup($invited_id, $group_id);

			if($invite->getUserStatus() === 'member')
			{
				throw new UserAlreadyGroupMemberException('Приглашение в группу недействительно: Вы являетесь членом группы');
			}

			$result = $this->user_per_group_mapper->removeUserFromGroup($invited_id, $group_id);

			return ($result) ? ['result'=>['message'=>'Приглашение в группу отклонено'],'status'=>true] : 
				['result'=>['message'=>'Не удалось отклонить приглашение в группу'], 'status'=>false];


		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(GroupNotFoundException $e)
		{
			throw $e;
		}

		catch(UserAlreadyGroupMemberException $e)
		{
			throw $e;
		}

		catch(UserPerGroupNotFoundException $e)
		{
			throw new UserNotInvitedToGroupException("Вы не были приглашены в группу");
		}
	}



	public function exitFromGroup(int $user_id, string $group_name):array
	{
		$group_id = 0;

		try
		{	
			$group_id = $this->group_mapper->getGroupByName($group_name)->getGroupId();

			$group_member = $this->user_per_group_mapper->getUserPerGroup($user_id, $group_id);

			if($group_member->getUserStatus() === 'invited')
			{
				throw new UserNotGroupMemberException("Вы не являетесь членом группы");
			}

			$result = $this->user_per_group_mapper->removeUserFromGroup($user_id, $group_id);

			return ($result) ? ['result'=>['message'=>'Выход из группы осуществлен'],'status'=>true]:
				['result'=>['message'=>'Не удалось выйти из группы'],'status'=>false];

		}

		catch(DBException $e)
		{
			throw $e;
		}

		catch(GroupNotFoundException $e)
		{
			throw $e;
		}

		catch(UserPerGroupNotFoundException $e)
		{
			throw new UserNotGroupMemberException("Вы не являетесь членом группы");
		}

		catch(UserNotGroupMemberException $e)
		{
			throw $e;
		}


	}





}