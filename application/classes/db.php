<?php
class DB {
  static protected $link;
  static protected $prepared_queries = array();

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


  static public function query($sql,$params=array(),$bind_params=array(),$class_name = 'stdClass'){
    if(!self::$link)
    self::connect();
    

    $return = array();
    
    if (empty($params)){
      $stmt = self::$link->query($sql);
      $return = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class_name );

    }
    else{
      if (isset(self::$prepared_queries[$sql]))
      $stmt = self::$prepared_queries[$sql];
      else{
        $stmt = self::$link->prepare($sql);
        self::$prepared_queries[$sql] = $stmt;
      }
      

      foreach($params as $field_name=>&$value){
        if (isset($bind_params[$field_name]['data_type']) and isset($bind_params[$field_name]['length'])){
          $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type'], $bind_params[$field_name]['length']);
        }
        elseif (isset($bind_params[$field_name]['data_type'])){
          $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type']);
        }
        else
        $stmt->bindParam($field_name, $value);
      }

      $stmt->execute();
      $return = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class_name);
    }

    $stmt->closeCursor();

    return  $return;
  }
  static public function execute($sql,$params=array(),$bind_params=array()){
    if(!self::$link)
    self::connect();
    //echo $sql;
    //var_dump($bind_params);
    //die();
    if (empty($params)){
      $count = self::$link->exec($sql);
    }
    else{
      if (isset(self::$prepared_queries[$sql]))
      $stmt = self::$prepared_queries[$sql];
      else{
        $stmt = self::$link->prepare($sql);
        self::$prepared_queries[$sql] = $stmt;
      }

      foreach($params as $field_name=>&$value){
        
        if (isset($bind_params[$field_name]['data_type']) and isset($bind_params[$field_name]['length'])){
          $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type'], $bind_params[$field_name]['length']);
        }
        elseif (isset($bind_params[$field_name]['data_type'])){          
          $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type']);
        }
        else{
          $stmt->bindParam($field_name, $value);
        }
        
      }      

      $stmt->execute();
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
        // Other RDBMS.  Graceful degradation
        $exists = true;
        $cmdOthers = "select 1 from `" . $tableName . "` where 1 = 0";
        self::query($cmdOthers);
      }
      catch(Exception $e1)
      {
        $exists = false;
      }
    

    return $exists;
  }

  static public function get_last_insert_id(){
    return PDO::lastInsertId();
  }


}
?>