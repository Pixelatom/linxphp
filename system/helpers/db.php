<?php

class Database {

    protected $link;
    protected $prepared_queries = array();

    public function __construct($configuration = 'database'){
        $this->connect($configuration);
    }

    public function connect($configuration = 'database') {
        $c = Configuration::get($configuration);
        /* Connect to an ODBC database using driver invocation */
        $dsn = $c['dsn'];
        $user = $c['username'];
        $password = $c['password'];

        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //drops exception when error
        if ($dbh->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
            $dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE); //necesary for not hanging mysql
        }

        $this->link = $dbh;
        //$this->query("SET NAMES 'UTF-8'");
        $this->execute("SET NAMES utf8");
    }

    function __destructor(){
        unset($this->link);
    }

    public function get_pdolink() {
        if (!$this->link)
            $this->connect();
        return $this->link;
    }

    // private function close(){
    //    unset($this->link);
    //}


    public function query($sql, $params=array(), $bind_params=array(), $class_name = 'stdClass') {
        if (!$this->link)
            $this->connect();


        $return = array();

        if (empty($params)) {
            $stmt = $this->link->query($sql);
            $return = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class_name);
        } else {
            if (isset($this->prepared_queries[$sql]))
                $stmt = $this->prepared_queries[$sql];
            else {
                $stmt = $this->link->prepare($sql);
                $this->prepared_queries[$sql] = $stmt;
            }


            foreach ($params as $field_name => &$value) {
                if (isset($bind_params[$field_name]['data_type']) and isset($bind_params[$field_name]['length'])) {
                    $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type'], $bind_params[$field_name]['length']);
                } elseif (isset($bind_params[$field_name]['data_type'])) {
                    $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type']);
                }
                else
                    $stmt->bindParam($field_name, $value);
            }

            $stmt->execute();
            $return = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class_name);
        }

        $stmt->closeCursor();

        return $return;
    }

    public function execute($sql, $params=array(), $bind_params=array()) {
        if (!$this->link)
            $this->connect();
        //echo $sql;
        //var_dump($bind_params);
        //die();
        if (empty($params)) {
            try {
                $count = $this->link->exec($sql);
            } catch (Exception $e) {
                die($e->getMessage() . '<br>' . $sql);
            }
        } else {
            if (isset($this->prepared_queries[$sql]))
                $stmt = $this->prepared_queries[$sql];
            else {
                $stmt = $this->link->prepare($sql);
                $this->prepared_queries[$sql] = $stmt;
            }

            foreach ($params as $field_name => &$value) {

                if (isset($bind_params[$field_name]['data_type']) and isset($bind_params[$field_name]['length'])) {
                    $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type'], $bind_params[$field_name]['length']);
                } elseif (isset($bind_params[$field_name]['data_type'])) {
                    $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type']);
                } else {
                    $stmt->bindParam($field_name, $value);
                }
            }

            $stmt->execute();
            $count = $stmt->rowCount();
        }

        return $count;
    }

    public function query_scalar($sql, $params=array(), $bind_params=array()) {
        if (!$this->link)
            $this->connect();

        /* Execute a prepared statement by passing an array of values */
        $stmt = $this->link->prepare($sql);
        foreach ($params as $field_name => &$value) {

            if (isset($bind_params[$field_name]['data_type']) and isset($bind_params[$field_name]['length'])) {
                $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type'], $bind_params[$field_name]['length']);
            } elseif (isset($bind_params[$field_name]['data_type'])) {
                $stmt->bindParam($field_name, $value, $bind_params[$field_name]['data_type']);
            } else {
                $stmt->bindParam($field_name, $value);
            }
        }
        $stmt->execute();
        $r = $stmt->fetchAll(PDO::FETCH_BOTH);

        unset($stmt);

        return $r[0][0];
    }

    public function get_last_insert_id($name = NULL) {
        return $this->link->lastInsertId($name);
    }

}
/**
 * static wrapper for class Database
 */
class DB {
    static protected $database;
    static protected $connections;
    /**
     *
     * @param string $configuration
     * @return Database
     */
    static public function connect($configuration = 'database'){
        if ($configuration == 'database'){
            self::$database = new Database();
            return self::$database;
        }
        else{
            $database = new Database($configuration);
            self::$connections[] = $database;
            return $database;
        }
    }

    static public function query($sql, $params=array(), $bind_params=array(), $class_name = 'stdClass') {
        if (!self::$database)
            self::connect();

        return self::$database->query($sql, $params, $bind_params, $class_name);
    }

    static public function execute($sql, $params=array(), $bind_params=array()) {
        if (!self::$database)
            self::connect();
        return self::$database->execute($sql, $params, $bind_params);
    }

    static public function query_scalar($sql, $params=array(), $bind_params=array()) {
        if (!self::$database)
            self::connect();
        return self::$database->query_scalar($sql, $params, $bind_params);
    }

    static public function get_last_insert_id($name = NULL) {
        return self::$database->get_last_insert_id($name);
    }

}
