<?php
declare(strict_types = 1);
namespace App\Libs;
use App\Libs\DB;
use App\Exceptions\DBExceptions\DBException;

class Adapter
{
	private \PDO $connection;
	private string $entity_name;

	public function __construct(DB $db)
	{
		$this->connection = $db->getConnection();
	}


	public function setEntity(string $entity_name):void
	{
		$this->entity_name = $entity_name;
	}


	public function find(string $query_string, array $params, array $needle = []):array
	{	

		$base_query = ($needle) ? "SELECT ".implode(',', $needle) : "SELECT *";
		$base_query.=" FROM $this->entity_name";

		$base_query.=' '.$query_string;
		
		return $this->executeQuery($base_query, $params, 'row');

	}


	public function findAll(array $params, array $needle = [], string $query_string = ""):array
	{
		
		$base_query = ($needle) ? "SELECT ".implode(',', $needle) : "SELECT *";
		$base_query.=" FROM $this->entity_name";

		$base_query.=' '.$query_string;
		
		return $this->executeQuery($base_query, $params, 'rows');
	}


	public function getCount(string $query_string, array $params):int
	{
		$base_query = "SELECT COUNT(*) FROM $this->entity_name ";
		$base_query.= $query_string;

		return $this->executeQuery($base_query, $params, 'column', 'int');

	}

	public function create(array $params):bool
	{
		$base_query = "INSERT INTO $this->entity_name" . " (". implode(',', array_keys($params)) . ") ";
		$prepared_params = [];


		foreach($params as $param_key=>$param_value)
		{
			$prepared_params[":$param_key"] = $param_value;
		}

		$base_query.= "VALUES ". "(".implode(",", array_keys($prepared_params)) . ")";

		return $this->executeQuery($base_query, $prepared_params,'execute');

	}


	public function update(string $query_string, array $params, array $target_params):bool
	{
		$base_query = "UPDATE $this->entity_name ";
		$prepared_params = [];
		$current_param = 0;


		foreach ($params as $param_key=>$param_value)
		{	
			$prepared_param_key = ":$param_key";
			$prepared_params[$prepared_param_key] = $param_value;

			if(0 === $current_param)
			{
				$base_query.= "SET $param_key = :$param_key";
			}
			else
			{
				$base_query.="$param_key = :$param_key";
			}

			$base_query.= ($current_param !== count($params) - 1) ? ', ' : ' '; 
			++$current_param;
		}

		$prepared_params = array_merge($prepared_params, $target_params);


		$base_query.=$query_string;

		return $this->executeQuery($base_query, $prepared_params, 'execute');

	}	


	public function delete(string $query_string, array $params):bool
	{
		$base_query = "DELETE FROM $this->entity_name ";
		$base_query.= $query_string;

		return $this->executeQuery($base_query, $params, 'execute');


	}



	private function executeQuery(string $query_string, array $prepared_params, string $execute_statement, string $r_type = 'string')
	{
		$r_types_returns = [
			'int'=>fn($column) => ($column) ? (int)$column : 0,
			'string'=>fn($column) => ($column) ? (string)$column : '',
			'bool'=>fn($column) => ($column) ? (bool)$column : false,
			'float'=>fn($column)=> ($column) ? (float)$column : 0.0
		];

		try
		{
			$sth = $this->connection->prepare($query_string);
			$sth->execute($prepared_params);

			switch($execute_statement)
			{
				case "row":
					return (0 !== $sth->rowCount()) ? $sth->fetch(\PDO::FETCH_ASSOC) : [];
				break;

				case "rows":
					return (0 !== $sth->rowCount()) ? $this->getValuesAsRows($sth) : [];
				break;

				case 'column':
					return $r_types_returns[$r_type]($sth->fetchColumn());

				break;

				case 'execute':
					return (0 !== $sth->rowCount()) ? true : false;
				break;


			}
		}

		catch(\PDOException $e)
		{
			throw new DBException($e->getMessage());
		}


	}



	private function getValuesAsRows(\PDOStatement $query):array
	{
		$arr = [];

		while($row = $query->fetch(\PDO::FETCH_ASSOC))
		{
			$arr[] = $row;
		}

		return $arr;
	}

	
	

}