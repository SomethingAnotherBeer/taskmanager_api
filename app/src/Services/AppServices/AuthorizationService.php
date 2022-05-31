<?php
declare(strict_types = 1);
namespace App\Services\AppServices;
use App\Mappers\UserMapper;
use App\Mappers\UserRightsMapper;

use App\Exceptions\DBExceptions\DBException;
use App\Exceptions\ModelExceptions\UserExceptions\UserNotFoundException;

use App\Exceptions\HttpExceptions\ForbiddenException;

class AuthorizationService
{
	private UserMapper $user_mapper;
	private UserRightsMapper $user_rights_mapper;

	public function __construct(UserMapper $user_mapper, UserRightsMapper $user_rights_mapper)
	{
		$this->user_mapper = $user_mapper;
		$this->user_rights_mapper = $user_rights_mapper;
	}


	public function checkRights(int $user_id, string $rights):void
	{
		$user = $this->user_mapper->getUserById($user_id);
		$user_rights = $this->user_rights_mapper->getUserRightsByName($rights);

		if($user->getUserRightsId() !== $user_rights->getUserRightsId()) throw new ForbiddenException("Отсутствуют права доступа к ресурсу");
	}

}