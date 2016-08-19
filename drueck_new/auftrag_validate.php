<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$auftragsnr = $o->auftragsnr;

$sql = "SELECT * FROM daufkopf WHERE auftragsnr = '$auftragsnr' ";

$return = mysql_query($sql);
$output = "";



if(mysql_affected_rows()>0){
        while ($row = mysql_fetch_array($return)){
            $output = $row['auftragsnr'];
        }
}
else {
        $output = "ERROR-NOAUFTRAGSNR";
}

$retArr = array(
                'auftragsnr' => $output
            );

echo json_encode($retArr);