<?php
declare(strict_types = 1);
namespace App\Mappers;
use App\Libs\Adapter;


abstract class Mapper
{
	protected Adapter $adapter;
	

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
	}



	protected function getUpdatedParams(array $current_params, array $old_params):array
	{
		$new_params = [];

		foreach($current_params as $current_param_key=>$current_param_value)
		{
			if($current_params[$current_param_key] !== $old_params[$current_param_key])
			{
				$new_params[$current_param_key] = $current_params[$current_param_key];
			}
		}
		

		return $new_params;
	}


	protected function getLimitString(bool $offset):string
	{
		$limit_string = " LIMIT :limit ";
		$limit_string.= ($offset) ? " OFFSET :offset" : "";

		return $limit_string;

	}

	protected function getLimitParams(int $limit, int $offset = 0):array
	{
		$limit_params = [];

		$limit_params[':limit'] = $limit;
		if($offset)  $limit_params[':offset'] = $offset;
		
		return $limit_params;
	}

}

