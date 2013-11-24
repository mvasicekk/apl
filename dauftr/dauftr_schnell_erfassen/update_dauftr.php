<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$dauftr_id = $_GET['id'];
	
	$teil=chop($_GET['teil']);
	
	$pos_pal_nr=chop($_GET['pos_pal_nr']);
	$stk=chop($_GET['stk']);
	$preis=chop($_GET['preis']);
	$mehrarb_kz=chop($_GET['mehrarb_kz']);
	$abgnr=chop($_GET['abgnr']);
	$KzGut=chop($_GET['KzGut']);
	$termin=chop($_GET['termin']);
	$auftragsnr_exp=chop($_GET['auftragsnr_exp']);
	$pos_pal_nr_exp=chop($_GET['pos_pal_nr_exp']);
	$stk_exp=chop($_GET['stk_exp']);
	$fremdauftr=chop($_GET['fremdauftr']);
	$fremdpos=chop($_GET['fremdpos']);
	
	$KzGut=chop($_GET['KzGut']);
	//cokoliv jineho nez mezeru nahradim pismenem G
	if(strlen($KzGut)>0)
		$KzGut='G';

	$pocitac=gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	$ident=$pocitac."/".$_SESSION["user"]; 
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	$sql="update dauftr";
	$sql.=" set `Teil`='".$teil."',";
    $sql.=" `Stück`='".$stk."',";
	$sql.=" `Termin`='".$termin."',";
	$sql.=" `Preis`='".$preis."',";
	$sql.=" `MehrArb-KZ`='".$mehrarb_kz."',";
	$sql.=" `pos-pal-nr`='".$pos_pal_nr."',";
	$sql.=" `auftragsnr-exp`='".$auftragsnr_exp."',";
	$sql.=" `pal-nr-exp`='".$pos_pal_nr_exp."',";
	$sql.=" `stk-exp`='".$stk_exp."',";
	$sql.=" `fremdauftr`='".$fremdauftr."',";
	$sql.=" `fremdpos`='".$fremdpos."',";
	$sql.=" `KzGut`='".$KzGut."',";
	$sql.=" `abgnr`='".$abgnr."',";
	//$sql.=" `VzKd`='".$teil."',";
	//$sql.=" `VzAby`='".$teil."',";
	$sql.=" `comp_user_accessuser`='".$ident."'";
	$sql.=" where (id_dauftr=".$dauftr_id.")";
	
	//$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysql_error=mysql_error();
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<sql>';
	$output .= $sql;
	$output .= '</sql>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	$output .= $mysql_error;
	$output .= '</mysqlerror>';

	$output .= '</response>';
	
	echo $output;
	
?>

