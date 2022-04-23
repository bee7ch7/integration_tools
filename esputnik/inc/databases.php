<?php

//include('config_mysql_db.php');



class ConnectionFront {
  private $pdo = null;
  private $stmt = null;

	private $username = "xxx";
	private $password = "xxx";
	private $dbname = "postgres";
	private $pg_ip = "xx.xx.xx.xx";

  function __construct($ip = null){

	   if ($ip !== null){
            $this->pg_ip = $ip;
        }

    try {
      $this->pdo = new PDO(
        "pgsql:host=". $this->pg_ip .";dbname=".$this->dbname, $this->username, $this->password,
         [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]
      );
    } catch (Exception $ex) { die($ex->getMessage()); }
  }

  function __destruct(){
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  function select($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }
}


class ConnectionPyxis {
  private $pdo = null;
  private $stmt = null;

	private $username = "xxx";
	private $password = "xxx";
	private $dbname = "xxx";
	private $pg_ip = "xx.xx.xx.xx";

  function __construct($ip = null){

	   if ($ip !== null){
            $this->pg_ip = $ip;
        }

    try {
      $this->pdo = new PDO(
        "pgsql:host=". $this->pg_ip .";dbname=".$this->dbname, $this->username, $this->password,
         [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]
      );
    } catch (Exception $ex) { die($ex->getMessage()); }
  }

  function __destruct(){
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  function select($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }
}



class ConnectionMordor {
  private $pdo = null;
  private $stmt = null;

	private $username = "xxx";
	private $password = "xxx";
	private $dbname = "xxx";
	private $pg_ip = "localhost";

  function __construct($ip = null){

	   if ($ip !== null){
            $this->pg_ip = $ip;
        }

    try {
      $this->pdo = new PDO(
        "mysql:host=". $this->pg_ip .";dbname=".$this->dbname, $this->username, $this->password,
         [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]
      );
    } catch (Exception $ex) { die($ex->getMessage()); }
  }

  function __destruct(){
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  function select($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

  function insert($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

  function update($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $res2 = $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $res2;
  }

  function delete($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

}


class ConnectionMordor_slave {
  private $pdo = null;
  private $stmt = null;

	private $username = "xxx";
	private $password = "xxx";
	private $dbname = "xxx";
	private $pg_ip = "xx.xx.xx.xx";

  function __construct($ip = null){

	   if ($ip !== null){
            $this->pg_ip = $ip;
        }

    try {
      $this->pdo = new PDO(
        "mysql:host=". $this->pg_ip .";dbname=".$this->dbname, $this->username, $this->password,
         [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]
      );
    } catch (Exception $ex) { die($ex->getMessage()); }
  }

  function __destruct(){
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  function select($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

  function insert($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

  function update($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $res2 = $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $res2;
  }

  function delete($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

}


class ConnectionLMBP {
  private $pdo = null;
  private $stmt = null;

	private $username = "xxx";
	private $password = "xxx";
	private $dbname = "xxx";
	private $pg_ip = "xx.xx.xx.xx";

  function __construct($ip = null){

	   if ($ip !== null){
            $this->pg_ip = $ip;
        }

    try {
      $this->pdo = new PDO(
        "mysql:host=". $this->pg_ip .";charset=utf8mb4;dbname=".$this->dbname, $this->username, $this->password,
         [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]
      );
    } catch (Exception $ex) { die($ex->getMessage()); }
  }

  function __destruct(){
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  function select($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

  function insert($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

  function update($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $res2 = $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $res2;
  }

  function delete($sql, $cond=null){
    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      $result = $this->stmt->fetchAll();
    } catch (Exception $ex) { die($ex->getMessage()); }
    $this->stmt = null;
    return $result;
  }

}


?>
