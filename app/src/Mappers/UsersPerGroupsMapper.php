<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;

class UsersPerGroupsMapper extends Mapper
{
	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter);
		$this->adapter->setEntity('')
	}
}