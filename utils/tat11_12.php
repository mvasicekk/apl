 <meta charset="UTF-8"> 
<?php

require_once '../db.php';

$convertTat = array(
    59=>array(71,72),
    89=>array(81,82),
    2061=>array(2061,2062),
    2089=>array(2081,2082),
    2189=>array(2181,2182),
    4059=>array(4071,4072),
    4061=>array(4061,4062),
    4161=>array(4161,4162),
    5061=>array(5061,5062),
    6459=>array(6471,6472),
    6489=>array(6481,6482),
    6559=>array(6571,6572),
    6589=>array(6581,6582),
    6759=>array(6771,6772),
);

$a = AplDB::getInstance();

AplDB::varDump($convertTat);

$sql =" select";
$sql.="     dkopf.Kunde,";
$sql.="     dkopf.Teil,";
$sql.="     SUBSTRING(CAST(dpos.`TaetNr-Aby` as CHAR),1,2) as abgnr";
$sql.=" from";
$sql.="     dkopf";
$sql.=" join dpos on dpos.Teil=dkopf.Teil";
$sql.=" where";
$sql.="     dpos.`TaetNr-Aby` between 1100 and 1299";
$sql.=" group by";
$sql.="     dkopf.Kunde,";
$sql.="     dkopf.teil,";
$sql.="     SUBSTRING(dpos.`TaetNr-Aby`,1,2)";


$rows = $a->getQueryRows($sql);

$teileA = array();
if($rows!==NULL){
    foreach ($rows as $r){
	$kunde = $r['Kunde'];
	$teil = $r['Teil'];
	$abgnr = $r['abgnr'];
	$teileA[$kunde][$teil][$abgnr] += 1;
    }
}

echo "<br>'Vynechane' dily (pracuji si nimi jako by mely 11xx)<hr>";
//vypisu dily s vice operacema 11 a 12
foreach ($teileA as $kunde=>$t){
    foreach ($t as $teilenr=>$tatA){
	$tatKeys = array_keys($tatA);
	if(count($tatA)>1){
	    echo $kunde.' - '.$teilenr.'('.$tatKeys[0].') - '."' -> 11xx' : ( ma 11xx i 12xx )<br>";
	}
    }
}
echo "<hr>uprava DPOS<br>";
//vypisu dily s vice operacema 11 a 12
foreach ($teileA as $kunde=>$t){
    foreach ($t as $teilenr=>$tatA){
	$tatKeys = array_keys($tatA);
	if(count($tatA)>1){
	    echo $kunde.' - '.$teilenr.'('.$tatKeys[0].') - '." -> 11xx: ( ma 11xx i 12xx )<br>";
	}
	//else{
	    //zmenit dpos
	    foreach ($convertTat as $originalAbgNr=>$newAbgnrArray){
		//najit original
		$sql = "select dpos.`TaetNr-Aby` from dpos where Teil='$teilenr' and `TaetNr-Aby`='$originalAbgNr'";
		$rows = $a->getQueryRows($sql);
		if($rows!==NULL){
		    // nasel jsem original
		    $convertIndex = $tatKeys[0]==11?0:1;
		    if(count($tatA)>1){
			$convertIndex = 0;
		    }
		    $newAbgnr = $newAbgnrArray[$convertIndex];
		    $sql = "update dpos set `TaetNr-Aby`='$newAbgnr' where Teil='$teilenr' and `TaetNr-Aby`='$originalAbgNr' limit 1";
		    //$ar = $a->query($sql);
		    echo $kunde.' - '.$teilenr.'('.$tatKeys[0].') - '.$originalAbgNr." -> ".$newAbgnr." ($ar)<br>";
		}
	    }
	//}
    }
}

//ostatni tabulky dauftr,drech,drueck,dlagerbew
foreach ($teileA as $kunde=>$t){
    foreach ($t as $teilenr=>$tatA){
	$tatKeys = array_keys($tatA);
	if(count($tatA)>1){
	    echo $kunde.' - '.$teilenr.'('.$tatKeys[0].') - '."-> 11xx : ( ma 11xx i 12xx )<br>";
	}
	//else{
	    //zmenit tabulky
	    foreach ($convertTat as $originalAbgNr=>$newAbgnrArray){
		//dauftr
		$sql = "select dauftr.auftragsnr,dauftr.`pos-pal-nr` as pal,dauftr.abgnr from dauftr where Teil='$teilenr' and abgnr='$originalAbgNr'";
		$rows = $a->getQueryRows($sql);
		if($rows!==NULL){
		    // nasel jsem original
		    $rowsCount = count($rows);
		    $convertIndex = $tatKeys[0]==11?0:1;
		    if(count($tatA)>1){
			$convertIndex = 0;
		    }
		    $newAbgnr = $newAbgnrArray[$convertIndex];
		    $sql = "update dauftr set abgnr='$newAbgnr' where Teil='$teilenr' and abgnr='$originalAbgNr'";
		    //$ar = $a->query($sql);
		    echo "dauftr - (poc. radku:$rowsCount)".' - '.$teilenr.'('.$tatKeys[0].') - '.$originalAbgNr." -> ".$newAbgnr." ($ar)<br>";
		}
		
		//drech
		$sql = "select drech.abgnr from drech where Teil='$teilenr' and abgnr='$originalAbgNr'";
		$rows = $a->getQueryRows($sql);
		if($rows!==NULL){
		    // nasel jsem original
		    $rowsCount = count($rows);
		    $convertIndex = $tatKeys[0]==11?0:1;
		    if(count($tatA)>1){
			$convertIndex = 0;
		    }
		    $newAbgnr = $newAbgnrArray[$convertIndex];
		    $sql = "update drech set abgnr='$newAbgnr' where Teil='$teilenr' and abgnr='$originalAbgNr'";
		    //$ar = $a->query($sql);
		    echo "drech - (poc. radku:$rowsCount)".' - '.$teilenr.'('.$tatKeys[0].') - '.$originalAbgNr." -> ".$newAbgnr." ($ar)<br>";
		}
		
		//dlagerbew
		$sql = "select dlagerbew.abgnr from dlagerbew where teil='$teilenr' and abgnr='$originalAbgNr'";
		$rows = $a->getQueryRows($sql);
		if($rows!==NULL){
		    // nasel jsem original
		    $rowsCount = count($rows);
		    $convertIndex = $tatKeys[0]==11?0:1;
		    if(count($tatA)>1){
			$convertIndex = 0;
		    }
		    $newAbgnr = $newAbgnrArray[$convertIndex];
		    $sql = "update dlagerbew set abgnr='$newAbgnr' where teil='$teilenr' and abgnr='$originalAbgNr'";
		    //$ar = $a->query($sql);
		    echo "dlagerbew - (poc. radku:$rowsCount)".' - '.$teilenr.'('.$tatKeys[0].') - '.$originalAbgNr." -> ".$newAbgnr." ($ar)<br>";
		}
		
		//drueck
		$sql = "select drueck.TaetNr from drueck where teil='$teilenr' and TaetNr='$originalAbgNr'";
		$rows = $a->getQueryRows($sql);
		if($rows!==NULL){
		    // nasel jsem original
		    $rowsCount = count($rows);
		    $convertIndex = $tatKeys[0]==11?0:1;
		    if(count($tatA)>1){
			$convertIndex = 0;
		    }
		    $newAbgnr = $newAbgnrArray[$convertIndex];
		    $sql = "update drueck set TaetNr='$newAbgnr' where teil='$teilenr' and TaetNr='$originalAbgNr'";
		    //$ar = $a->query($sql);
		    echo "drueck - (poc. radku:$rowsCount)".' - '.$teilenr.'('.$tatKeys[0].') - '.$originalAbgNr." -> ".$newAbgnr." ($ar)<br>";
		}
	    }
	//}
    }
}

//AplDB::varDump($convertTat);