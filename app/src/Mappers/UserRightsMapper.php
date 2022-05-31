<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;
use App\Models\UserRights;
use App\Exceptions\ModelExceptions\UserExceptions\UserRightsNotFoundException;

class UserRightsMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('user_rights');
	}

	public function getUserRightsByName(string $user_rights_name):UserRights
	{
		$query_string = "WHERE user_rights_name = :user_rights_name";
		$query_params = [':user_rights_name'=>$user_rights_name];

		$user_rights_params = $this->adapter->find($query_string, $query_params);

		if(!$user_rights_params) throw new UserRightsNotFoundException("Права пользователя не найдены");

		return new UserRights($user_rights_params);
	}

}