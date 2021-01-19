<?php

namespace SQLi;

use Revue\src\DataBase as DataBase;
use SQLi\SQLiException as SQLiException;
use SQLi\Result as Result;

class SQLi {

	private static $lasterror = false;

	/**
	 *  clonagem e construtor estão impossiblitados para uso
	 */
	private function __construct() {}
	private function __clone() {}


	public static function getLastError(){
		return self::$lasterror;
	}

	/**
	 *  Using selects in Data Base
	 *  @param $query string
	 *  @param $values array
	 *  @param $aliasDB string - Use this if you need select in other data base 
	 */	
	public static function query(string $query, array $values = [], string $aliasDB = "default"){
		
		if(($pdo = DataBase::get($aliasDB)) === null) return false;
	
		$st = $pdo->prepare($query);
		if(!$st) throw new SQLiException(0, $pdo->errorInfo()[2]);
			
		if(count($values) > 1)
			self::setBinds($st, $values);
		
		$result = new Result($st);
		if($result->hasError()){
			self::$lasterror = $result->getCode();
			return false;
		}

		return  $result;
		
	}

	/**
	 *  Using this function for simple insert new row
	 *	In case of success this function will return true. 
	 *	In each error, this function will return the error code 
	 *  @param $insert string
	 *  @param $values array - ['ssi', 'value1', 'value2']
	 *  @param $aliasDB string - Use this if you need insert in other data base 
	 *  @param $pdo - for inner context
	 */	
	public static function insert(string $insert, array $values, string $aliasDB = "default", $pdo = false){
		
		if(count($values) < 2) throw new SQLiException(5); 
		if(!$pdo && (DataBase::get($aliasDB)) !== false){
			$pdo = $database->get();
		}
		
		if($pdo === null) return false;
		
		$binds   = array_shift($values);
		$insert  = "INSERT INTO ".trim($insert)." VALUES (";
		$insert .= substr(str_repeat("?,", count($values)), 0, -1).")";
		
		$st = $pdo->prepare($insert);
		if(!$st) throw new SQLiException(0, $pdo->errorInfo()[2]);
		
		for($y = 1, $i = 0; $i < count($values); $i++, $y++){
			$var = &$values[$i];
			self::bind($st, $var, $y, $binds[$i]);
		}
		
		$res = new Result($st);
		return $res->hasError() ? $res->getCode() : true;
	
	}

	/**
	 *  Using this function for inset/updates/creates/etc - NOT SELECT
	 *  This function will not return rows
	 *	In case of success this function will return true. 
	 *	In each error, this function will return the error code 
	 *  @param $exec string
	 *  @param $values array - ['ssi', 'value1', 'value2']
	 *  @param $aliasDB string - Use this if you need insert in other data base 
	 */
	public static function exec(string $exec, $insert = false, string $aliasDB = "default", array $values = []){
		
		if(($pdo = DataBase::get($aliasDB)) === null) return false;
		$st = $pdo->prepare($exec);
		
		if(!$st) throw new SQLiException(0, $pdo->errorInfo()[2]);
			
		if(count($values) > 1)
			self::setBinds($st, $values);
			
		$res = new Result($st);
		if($insert) return $res->hasError() ? false : $pdo->lastInsertId();
		return $res->hasError() ? $res->getCode() : true;
		
	}
	
	/**
	 *  Using this function for simple insert new row and get your id
	 *	In case of success this function will return index of the inserted row. 
	 *	In each error, this function will return false 
	 *  @param $insert string
	 *  @param $values array - ['ssi', 'value1', 'value2']
	 *  @param $aliasDB string - Use this if you need insert in other data base 
	 */		
	public static function lastInsert(string $insert, array $values, string $aliasDB = "default"){
		
		if(count($values) < 2) throw new SQLiException(5); 
		
		if(($pdo = DataBase::get($aliasDB)) === null) return false;
		$status  = self::insert($insert, $values, $aliasDB, $pdo);
		
		if($status === true) return $pdo->lastInsertId();
		self::$lasterror = $status;

		return false;
		
	}

	/**
	 *  Using this function for inset many rows in same time
	 *	In case of success this function will return true. 
	 *	In each error, this function will return the error code 
	 *  @param $insert string - "table (value1, value2)"
	 *  @param $values array - ['ssi', ['value1A', 'value2A'],['value1B', 'value2B']]
	 *  @param $aliasDB string - Use this if you need insert in other data base 
	 */	
	public static function multiInsert(string $insert, array $values, string $aliasDB = "default"){
		
		if(count($values) < 2) throw new SQLiException(5); 
		if(($pdo = DataBase::get($aliasDB)) === null) return false;
		
		$binds  = array_shift($values);
		$insert = "INSERT INTO ".trim($insert)." VALUES ";	
		$insert .= substr(str_repeat("(".substr(str_repeat("?,", count($values[0])), 0, -1).")," , count($values)), 0, -1);
		
		$st = $pdo->prepare($insert);
		if(!$st) throw new SQLiException(0, $pdo->errorInfo()[2]);
		
		$y = 1;
		foreach($values as $k => $rows){
			$i = 0;
			foreach($rows as $key => $val){
				$var = &$values[$k][$key];
				self::bind($st, $var, $y, $binds[$i]);
				$i++; $y++;
			}
		}
		
		$res = new Result($st);
		return $res->hasError() ? $res->getCode() : true;

	}

	
	


	private static function setBinds($st, array $values){

		$binds = array_shift($values);
		
		for($i = 0, $y = 1; $i < strlen($binds); $i++, $y++){
			
			$var = &$values[$i];
			self::bind($st, $var, $y, $binds[$i]);
			
		}
		
	}

	private static function bind($st, &$var, $y, $bind){

		switch ($bind) {
			case 'i':
				$bind = \PDO::PARAM_INT;
				break;
			case 'b':
				$bind = \PDO::PARAM_BOOL;
				break;
			case 'd':
				$var = strval($var);
				$bind = \PDO::PARAM_STR;
				break;
			case 's':
				$bind = \PDO::PARAM_STR;
				break;
			default:
				$bind = false;
				break;
		}

		!$bind ? $st->bindParam($y, $var) : $st->bindParam($y, $var, $bind);
	}

}