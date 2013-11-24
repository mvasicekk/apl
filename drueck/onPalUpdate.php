<?
session_start();
require_once '../db.php';
require_once '../fns_dotazy.php';

    $apl = AplDB::getInstance();

    $id = $_POST['id'];
    $auftragsnr = $_POST['auftragsnr'];
    $pal = $_POST['pal'];
    $user = get_user_pc();
    $ar=0;
    // zjistim pocet importnich kusu na palete
    $dauftrId = $apl->getDauftrIdGPal1($auftragsnr, $pal);
    $dauftrRow = $apl->getDauftrRow($dauftrId);
    $abgnrG = "";
    
    if($dauftrRow!==NULL){
	$importStk = $dauftrRow['stk'];
	$abgnrG=$dauftrRow['abgnr'];
    }
    else
	$importStk = 0;
    
    $obsahDiv = "";
    
    $drueckTatArray = $apl->getDrueckTatArray($auftragsnr, $pal);
    
    if ($drueckTatArray !== NULL) {
	$obsahDiv.="<table id='druecktattable'>";
	$obsahDiv.="<tr><th>Tat</th><th>IMStk</th><th>StkGut</th><th>Auss_2</th><th>Auss_4</th><th>Auss_6</th></tr>";
	foreach ($drueckTatArray as $dt) {
	    $abgnr = $dt['abgnr'];
	    $rowStyle='';
	    if($abgnr==$abgnrG) $rowStyle='gtat';
	    $stk = number_format($dt['drueck_stk'],0,',',' ');
	    $imstk = number_format($importStk,0,',',' ');
	    $auss2 = number_format($dt['drueck_auss_2'],0,',',' ');
	    $auss4 = number_format($dt['drueck_auss_4'],0,',',' ');
	    $auss6 = number_format($dt['drueck_auss_6'],0,',',' ');
	    $obsahDiv.="<tr class='$rowStyle'>";
	    $obsahDiv.="<td id='abgnrfilter_".$abgnr."' acturl='./callStorno.php' style='cursor: pointer;text-align:right;'>$abgnr</td>";
	    $obsahDiv.="<td style='text-align:right;border-right:1px solid black;'>$imstk</td>";
	    $obsahDiv.="<td style='text-align:right;'>$stk</td>";
	    $obsahDiv.="<td style='text-align:right;'>$auss2</td>";
	    $obsahDiv.="<td style='text-align:right;'>$auss4</td>";
	    $obsahDiv.="<td style='text-align:right;'>$auss6</td>";
	    $obsahDiv.="</tr>";
	}
	$obsahDiv.="</table>";
    }


//    $ar = $apl->updateReparaturKopf($repid,'bemerkung',$value);

    $returnArray = array(
        'id'=>$id,
        'auftragsnr'=>$auftragsnr,
        'pal'=>$pal,
	'importStk'=>$importStk,
	'obsahDiv'=>$obsahDiv,
        'ar'=>$ar,
    );

    echo json_encode($returnArray);

?>
