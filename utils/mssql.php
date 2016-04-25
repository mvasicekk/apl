<?php

foreach(PDO::getAvailableDrivers() as $driver)
    {
    echo $driver.'<br />';
    }
    
try
{
  //$db = new PDO('odbc:Driver=FreeTDS; Server=172.16.1.101; Port=1433; Database=apl; UID=root; PWD=nuredv;');
    $host = '172.16.1.101';
    $dbname = 'apl';
    $user = 'root';
    $pass = 'nuredv';
    $port = 1433;
    $db = new PDO("dblib:host=$host:$port;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<h1>otevrena DB !</h1>';
    //$STH = $db->prepare("INSERT INTO test1 ( cislo,popis ) values ( 1,'Cathy' )");
//$STH->execute();

//$query = "INSERT INTO text1 (cislo,popis) VALUES (:cislo,:popis)";
//$query = 'INSERT INTO text1(cislo,popis) VALUES(1,"4wdwfwe")';

//$stmt = $db->prepare($query,array(PDO::ATTR_EMULATE_PREPARES=>TRUE));
//$stmt = $db->prepare($query);

//$stmt->bindParam(':cislo', $cislo, PDO::PARAM_INT);
//$stmt->bindParam(':popis', $popis, PDO::PARAM_STR, 50);

// neco vlozim
for($i=0;$i<10;$i++){
    $cislo = $i;
    $popis = "$i eofie $i";
    $datum = date('Y-m-d');
    $db->exec("INSERT INTO test1 ( cislo,popis,datum ) values ( $i,'$popis','$datum' )");
//    $data = array('cislo'=>$cislo,'popis'=>$popis);
//    $res = $stmt->execute($data);
//    echo "res<pre>";
//    var_dump($res);
//    echo "</pre>";
//    echo "<hr>";
}

$query = 'SELECT * FROM test1 where cislo>?';
$statement = $db->prepare($query);
$statement->execute(array(3));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($result);
echo "</pre>";

/*
$query = "select * from sys.messages where message_id = 208";
$statement = $db->prepare($query);
$statement->execute(array(3));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($result);
echo "</pre>";
*/

// zase po sobe uklidit
$STH = $db->exec("delete from test1");
// uzavreni spojeni k DB, staci priradit null explicitne, nebo na konci skriptu php uzavre automaticky
$db = NULL;

}
catch(PDOException $exception)
{
    echo $exception->getMessage();
  //die("Unable to open database.<br />Error message:<br /><br />$exception.");
}




