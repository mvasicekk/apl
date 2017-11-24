<?php
require '../db.php';
$apl = AplDB::getInstance();

$code = $_GET['p'];
$u = $_GET['u'];
$tag = $_GET['tag'];
$dir = $_GET['dir'];
$stk = intval($_GET['stk']);
$skrinka = intval($_GET['skrinka']);

//$post = $_POST;
$errors = array(
    "ok"=>array(
	"code"=>0,
	"description"=>"vse ok, vlozeno do DB"
	),
    "nodbinsert"=>array(
	"code"=>1,
	"description"=>"nepodarilo se zapsat do DB"
	),
    "noskrinka"=>array(
	"code"=>2,
	"description"=>"nebyla zadana skrinka"
	),
    "codelengtherror"=>array(
	"code"=>3,
	"description"=>"chyba delka kodu, neni 8 znaku"
	),
    "nocoderead"=>array(
	"code"=>4,
	"description"=>"nenacten zadny kod"
	),
);

$returnArray = NULL;

if(strlen($code)>0){
    //testy na validni data
    // delka kodu musi byt 8
    if(strlen($code)==8){
	if(strlen($skrinka)>0){
	    // skrinka musi byt zadana
	    $sql = "insert into barcode_scanner (ean,tag,user,direction,stk,skrinka) values('$code','$tag','$u','$dir','$stk','$skrinka')";
	    $insertId = $apl->insert($sql);
	    //echo "kod:$code,$stk ks,skrinka:$skrinka";
	    if($insertId>0){
		// 0 je ok, vlozeno do DB
		//echo $errors["ok"]["code"];
		$returnArray = $errors["ok"];
	    }
	    else{
		// nevlozeno do DB
		//echo $errors["nodbinsert"]["code"];
		$returnArray = $errors["nodbinsert"];
	    }
	}
	else{
	    //neni zadana skrinka
	    //echo $errors["noskrinka"]["code"];
	    $returnArray = $errors["noskrinka"];
	}
    }
    else{
	//spatna delka kodu
	//echo $errors["codelengtherror"]["code"];
	$returnArray = $errors["codelengtherror"];
    }
}
else{
    //nenacten zadny kod
    //echo $errors["nocoderead"]["code"];
    $returnArray = $errors["nocoderead"];
}

echo json_encode($returnArray);