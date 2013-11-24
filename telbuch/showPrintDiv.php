<?
session_start();
require_once '../db.php';

    $id = $_GET['id'];

    $apl = AplDB::getInstance();

    $adressId = 0;
    
    $user = $_SESSION['user'];
    $report = 'S103';
    
    $ar=0;
    $katArray = $apl->getAdresyKategorien();
    
    $formDiv.="<div id='printdiv'>";
        $formDiv.="<div style='margin-top:5px;margin-bottom:10px;border-bottom:1px solid black;'>";
        $formDiv.="<strong>Druck / Tisk</strong>";
        $formDiv.="<a target='_blank' href='../Reports/S103_pdf.php' id='printgo'>&nbsp;Go !&nbsp;</a>";
        $formDiv.="</div>";
    
    // table with kategorien
    $formDiv.="<table style='float:left;background-color:#ffd'>";
    $formDiv.="<tr><th colspan='2'>Kategorien</th></tr>";
    foreach ($katArray as $kat){
	
	// get param value then set checked attribute
	$param = "printkat_".$kat['id'];
	$value = $apl->getReportPrintParam($report,$user,$param);
	$checked =  $value==1?"checked='checked'":"";
	
	$formDiv.="<tr>";
//	$formDiv.="<td>kat[id]=".$kat['id'].",aInKatArray=".  implode(',', $aInKatA).",as=$as</td>";
	$formDiv.="<td>";
	$formDiv.="<input type='checkbox' $checked acturl='./updatePrintAdresyKategorie.php?report=".$report."&user=".$user."' id='printkat_".$kat['id']."'/>";
	$formDiv.="</td>";
	$formDiv.="<td>";
	$formDiv.= $kat['kategorie'].'&nbsp;';
	$formDiv.="</td>";
	$formDiv.="</tr>";
    }
    $formDiv.="</table>";

    // table with columns
    $formDiv.="<table style='background-color:#dff'>";
    $formDiv.="<tr><th colspan='2'>Spalten / Sloupce</th></tr>";
    $columnsArray = array(
	'firma'=>"Firma",
	'ansprechpartner'=>"Ansprechpartner",
	'fullname'=>"Kontaktname",
	'funktion'=>"Funktion",
	'geboren'=>"geboren",
	'telefon'=>"Tel",
	'telefonprivat'=>"priv. Tel",
	'fax'=>"Fax",
	'handy'=>"Handy",
	'adr'=>"Adresse",
	'email'=>"Email",
	'sonstiges'=>"Sonst",
    );
    foreach ($columnsArray as $col=>$colName){
	
	// get param value then set checked attribute
	$param = "column_".$col;
	$value = $apl->getReportPrintParam($report,$user,$param);
	$checked =  $value==1?"checked='checked'":"";
	
	$formDiv.="<tr>";
	$formDiv.="<td>";
	$formDiv.="<input type='checkbox' $checked acturl='./updatePrintAdresyKategorie.php?report=".$report."&user=".$user."' id='column_".$col."'/>";
	$formDiv.="</td>";
	$formDiv.="<td>";
	$formDiv.= $colName.'&nbsp;';
	$formDiv.="</td>";
	$formDiv.="</tr>";
    }
    $formDiv.="</table>";
    $formDiv.="</div>";
    echo json_encode(array(
                            'id'=>$id,
			    'adressId'=>$adressId,
			    'div'=>$formDiv,
			    'ar'=>$ar,
			    'aik'=>$aInKatA,
    ));
?>
