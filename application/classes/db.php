<?php
class DB {
  static private $link;

  static private function connect(){
    $c = Configuration::get('database');
        /* Connect to an ODBC database using driver invocation */
    $dsn = $c['dsn'];
    $user = $c['username'];
    $password = $c['password'];

    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);//drops exception when error
    $dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,TRUE);//necesary for not hanging mysql
    self::$link = $dbh;
  }

  //static private function close(){
  //    unset(self::$link);
  //}


  static public function query($sql,$params=array()){
    if(!self::$link)
    self::connect();

    $return = array();
    
    if (empty($params)){
      $stmt = self::$link->query($sql);
      $return = $stmt->fetchAll(PDO::FETCH_CLASS, 'stdClass');

    }
    else{
      $stmt = self::$link->prepare($sql);      
      $stmt->execute($params);
      $return = $stmt->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    $stmt->closeCursor();

    return  $return;
  }
  static public function execute($sql,$params=array()){
    if(!self::$link)
    self::connect();
    if (empty($params)){
      $count = self::$link->exec($sql);
    }
    else{
      $stmt = self::$link->prepare($sql);      
      $stmt->execute($params);
      $count = $stmt->rowCount();      
    }

    return  $count;
  }



  static public function query_scalar($sql){
    if(!self::$link)
    self::connect();

    /* Execute a prepared statement by passing an array of values */
    $sth = self::$link->prepare($sql);
    $params = func_get_args();
    unset($params[0]);
    $sth->execute(array_values($params));
    $r = $sth->fetchAll(PDO::FETCH_BOTH);

    unset($sth);

    return $r[0][0];
  }

  static public function table_exists($tableName){
    try
    {
      // ANSI SQL way.  Works in PostgreSQL, MSSQL, MySQL.
      $cmd = "select case when exists((select * from information_schema.tables where table_name = '" .$tableName. "')) then 1 else 0 end";

      $exists = ((int) self::query_scalar($cmd)) == 1;
    }
    catch (Exception $e)
    {
      try
      {
        // Other RDBMS.  Graceful degradation
        $exists = true;
        $cmdOthers = "select 1 from " . $tableName . " where 1 = 0";
        self::query($cmdOthers);
      }
      catch(Exception $e1)
      {
        $exists = false;
      }
    }

    return $exists;
  }

  static public function get_last_insert_id(){
    return PDO::lastInsertId();
  }


}
?>