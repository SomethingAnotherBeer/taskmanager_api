<?php
declare(strict_types = 1);
namespace App\Services\AppServices;

class PaginationService
{
	private int $elements_in_list;
	private int $current_list;

	public function __construct()
	{
		$this->elements_in_list = 5;
	}


	public function setCurrentList(int $current_list):void
	{
		$this->current_list = $current_list;
	}

	public function getCurrentList():int
	{
		return $this->current_list;
	}

	public function getOffset():int
	{
		return ($this->current_list - 1) * $this->elements_in_list;
	}

	public function getLimit():int
	{
		return $this->elements_in_list;
	}

	public function getListsCount(int $rows_count):int
	{
		return $rows_count / $this->elements_in_list;
	}

}