<?php 
declare(strict_types = 1);
namespace App\Services\DomainServices;
use App\Mappers\GroupMapper;
use App\Mappers\UserPerGroupMapper;

use App\Exceptions\DBExceptions\DBException;
use App\Exceptions\ModelExceptions\GroupExceptions\GroupNotFoundException;
use App\Exceptions\ModelExceptions\GroupExceptions\GroupAlreadyExistsException;
use App\Exceptions\ModelExceptions\GroupExceptions\GroupnameEmptyException;


use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserPerGroupNotFoundException;
use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserNotGroupAdminException;
use App\Exceptions\ModelExceptions\UserPerGroupExceptions\UserNotGroupMemberException;

class GroupService
{
	private GroupMapper $group_mapper;
	private UserPerGroupMapper $user_per_group_mapper;


	public function __construct(GroupMapper $group_mapper, UserPerGroupMapper $user_per_group_mapper)
	{
		$this->group_mapper = $group_mapper;
		$this->user_per_group_mapper = $user_per_group_mapper;
	}



	public function createGroup(int $creator_id, string $group_name):array
	{
		$group_params = [];
		$user_per_group_params = [];

		if(mb_strlen($group_name) === 0)
		{
			throw new GroupnameEmptyException("Название группы не может быть пустым");
		}

		try
		{
			if($this->group_mapper->checkGroupByName($group_name))
			{
				throw new GroupAlreadyExistsException("Данная группа уже существует");
			}

			$group_params['group_name'] = $group_name;

			$group_result = $this->group_mapper->createGroup($group_params);


			$user_per_group_params['user_id'] = $creator_id;
			$user_per_group_params['group_id'] = $this->group_mapper->getGroupByName($group_name)->getGroupId();
			$user_per_group_params['is_admin'] = true;
			$user_per_group_params['user_status'] = 'member';

			$user_per_group_result = $this->user_per_group_mapper->addUserToGroup($user_per_group_params);

			if(!$group_result || !$user_per_group_result)
			{
				$this->user_per_group_mapper->removeUserFromGroup($creator_id);
				$this->group_mapper->deleteGroup($this->group_mapper->getGroupByName($group_name));
			}


			return ($group_result && $user_per_group_result) ? ['result'=>['message'=>'Группа успешно создана'],'status'=>true] : ['result'=>['message'=>'Не удалось создать группу'],'status'=>false];



		}	

		catch(DBException $e)
		{
			throw $e;
		}

		catch(GroupAlreadyExistsException | GroupnameEmptyException $e)
		{
			throw $e;
		}
	}


	public function renameGroup(int $admin_id, string $group_name, string $new_group_name):array
	{
		$group_id = 0;
		$admin_status = false;
		try
		{
			$group = $this->group_mapper->getGroupByName($group_name);

			$group_id = $group->getGroupId();

			$admin_status = $this->user_per_group_mapper->getUserPerGroup($admin_id, $group_id)->getAdminStatus();

			if(false === $admin_status)
			{
				throw new UserNotGroupAdminException("Вы не являетесь администратором группы");
			}

			$group->setGroupName($new_group_name);

			$result = $this->group_mapper->updateGroup($group);

			return ($result) ? ['result'=>['message'=>'Название группы успешно обновлено'],'status'=>true] : ['result'=>['message'=>'Название группы идентично текущему'],'status'=>false];
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

		catch(UserNotGroupAdminException $e)
		{
			throw $e;
		}

	}



	public function deleteGroup(int $admin_id, string $group_name):array
	{
		$group_id = 0;
		$admin_status = false;

		try
		{
			$group = $this->group_mapper->getGroupByName($group_name);
			$group_id = $group->getGroupId();

			$admin_status = $this->user_per_group_mapper->getUserPerGroup($admin_id, $group_id)->getAdminStatus();

			if(false === $admin_status)
			{
				throw new UserNotGroupAdminException("Вы не являетесь администратором группы");
			}

			$result = $this->group_mapper->deleteGroup($group);

			return ($result) ? ['result'=>['message'=>'Группа успешно удалена'],'status'=>true] : ['result'=>['message'=>'Не удалось удалить группу'],'status'=>false];

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

		catch(UserNotGroupAdminException $e)
		{
			throw $e;
		}

	}





}