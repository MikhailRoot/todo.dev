<?php


namespace App;


use App\Http\Exceptions\DbException;

class Db
{
    private static $_instance=null;

    private $_connection;

    /**
     * @var \PDOStatement
     */
    private $_lastPDOStatement;

    private $_host = DB_HOST;
    private $_username = DB_USER;
    private $_password = DB_PASSWORD;
    private $_database = DB_NAME;

    private function __construct()
    {
        try {

            $this->_connection  = new \PDO(
                "mysql:host={$this->_host};dbname={$this->_database}",
                $this->_username,
                $this->_password,
                [\PDO::ATTR_PERSISTENT => true]
            );

            //echo 'Connected to database';

        } catch (\PDOException $e) {

            throw new DbException($e->getMessage(),$e->getCode(), $e);

        }
    }

    public function __destruct()
    {
        $this->_lastPDOStatement = null;
        $this->_connection = null;
    }


    public function Query($sql,$inputs=null)
    {
        $this->_lastPDOStatement=$this->_connection->prepare($sql);
        $this->_lastPDOStatement->execute($inputs);

        return $this->_lastPDOStatement;
    }

    public function getLastInsertId()
    {
       return intval( $this->_connection->lastInsertId() );
    }

    private function __clone()
    {
    }
    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }




}