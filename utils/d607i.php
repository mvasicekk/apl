<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../db.php';

$a = AplDB::getInstance();

$terminVon = 'P19500001';
$terminBis = 'P19509999';

$sql.=" select ";
$sql.="     dauftr.termin,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.`pos-pal-nr` as im_pal,";
$sql.="     dauftr.abgnr,";
$sql.="     sum(if(kzgut='G',dauftr.`stück`,0)) as sum_im_stk,";
$sql.="     sum(dauftr.VzKd*dauftr.`stück`) as sum_vzkd";
$sql.=" from dauftr";
$sql.=" where ";
$sql.="     dauftr.termin between '$terminVon' and '$terminBis'";
$sql.="     and dauftr.`auftragsnr-exp` is null";
$sql.=" group by";
$sql.="     dauftr.termin,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.`pos-pal-nr`,";
$sql.="     dauftr.abgnr";
$sql.=" HAVING ";
$sql.="     sum_vzkd>0";

$rows = $a->getQueryRows($sql);

foreach ($rows as $r){
    $termin = $r['termin'];
    $import = $r['auftragsnr'];
    $teil = $r['teil'];
    $pal = $r['im_pal'];
    $abgnr = $r['abgnr'];
    $abgnrArray[$abgnr] += 1;
    
    $zeilen[$termin][$import][$teil][$pal][$abgnr]['sum_vzkd'] = $r['sum_vzkd'];
    $zeilen[$termin][$import][$teil][$pal]['sum_im_stk'] += $r['sum_im_stk'];
}


$abgnrKeysArray = array_keys($abgnrArray);
sort($abgnrKeysArray);
//AplDB::varDump($abgnrKeysArray);

echo "<table border='1'>";
echo "<thead>";
    echo "<tr>";
	echo "<th>";
	echo "Termin";
	echo "</th>";
	echo "<th>";
	echo "Import";
	echo "</th>";
	echo "<th>";
	echo "Teil";
	echo "</th>";
	echo "<th>";
	echo "Pal";
	echo "</th>";
	echo "<th>";
	echo "ImStk";
	echo "</th>";
	//seznam vsech nalezenych operaci
	foreach ($abgnrKeysArray as $abgnr){
	    echo "<th>";
	    echo "$abgnr";
	    echo "</th>";
	}
    echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($zeilen as $termin=>$importe){
    foreach ($importe as $import=>$teile){
	foreach ($teile as $teil=>$paletten){
	    foreach ($paletten as $pal=>$palInfoArray){
		echo "<tr>";
		    echo "<td>";
		    echo $termin;
		    echo "</td>";
		    echo "<td>";
		    echo $import;
		    echo "</td>";
		    echo "<td>";
		    echo $teil;
		    echo "</td>";
		    echo "<td>";
		    echo $pal;
		    echo "</td>";
		    echo "<td>";
		    echo $palInfoArray['sum_im_stk'];
		    echo "</td>";
		    //projdu vsechny nelezene operaci a zobrazim 0, pokud ma paleta operaci, zobrazim mezeru pokud operaci nema
		    foreach ($abgnrKeysArray as $abgnr){
			if(array_key_exists($abgnr, $palInfoArray)){
			    // zjistit z drueck kolik uz mam na teto operaci kusu
			    $obsah = 0;
			}
			else{
			    $obsah = "";
			}
			echo "<td>";
			echo "$obsah";
			echo "</td>";
		    }
		echo "</tr>";
	    }
	}
    }
}
echo "</tbody>";
echo "</table>";


//AplDB::varDump($abgnrArray);
//
//AplDB::varDump($zeilen);