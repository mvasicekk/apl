<?php

class sqldb {
    /*     * * Declare instance ** */

    private static $host = '172.16.1.101';
    private static $dbname = 'FA1';
    private static $user = 'apl';
    private static $pass = 'nuredv';
    private static $port = 1433;
    private static $con;
    private static $instance = NULL;

    public static function getInstance($dbname='FA1') {
	if (!self::$instance instanceof self) {
	    self::$instance = new self($dbname);
	}
	return self::$instance;
    }

    public function __clone() {
	trigger_error('Clone not allowed', E_USER_ERROR);
    }

    public function __wakeup() {
	trigger_error('Deserialisation is not allowed', E_USER_ERROR);
    }

    public function __construct($dbname='FA1') {
	self::$dbname = $dbname;
	$this->con = new PDO("dblib:host=" . self::$host . ":" . self::$port . ";dbname=" . self::$dbname . "", self::$user, self::$pass);
	$this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

// public interface
    /**
     * 
     * @param type $query
     * @param type $params
     * @return type
     */
    public function getResult($query, $params = NULL) {
	//$query = 'SELECT * FROM test1 where cislo>?';
	$statement = $this->con->prepare($query);
	$statement->execute($params);
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	return $result;
    }

    /**
     * 
     * @param type $query
     * @return type
     */
    public function exec($query) {
	$count = $this->con->exec($query);
	return $count;
    }

    /**
     * 
     */
    public function getLastInsertId(){
	$query = "SELECT scope_identity() AS id";
	$res = $this->con->query($query);
	if($res){
	    $r = $res->fetch();
	    return $r['id'];
	}
	else{
	    return -1;
	}
    }
    /**
     * 
     * @param type $artikel
     * @param type $ks
     * @param type $bemerk
     * @param type $abdatum
     * @param type $user
     * @param type $anftyp
     * @param type $prio
     * @return type
     */
    public function insertEinkaufAnforderung($artikel, $ks, $bemerk, $abdatum, $user, $anftyp, $prio) {
	$sql = "insert into eink_anforderungen";
	$sql.=" (artikel,anzahl,[user],bemerkung,abdatum,anftyp,prio)";
	$sql.=" values('$artikel',$ks,'$user','$bemerk','$abdatum','$anftyp','$prio')";
	$this->con->exec($sql);
	return $this->getLastInsertId();
    }

}

/*** end of class ***/
