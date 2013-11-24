<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	
	$teil = urldecode($_GET['teil']);
	$taetnr=chop($_GET['tatnr']);
	
	$bez_d=urldecode(chop($_GET['bez_d']));
	$bez_t=urldecode(chop($_GET['bez_t']));
	
	$vzkd=chop($_GET['vzkd']);
	if($vzkd!='error')
		// 	nahradit desetinnou carku teckama
		$vzkd=strtr($vzkd,",",".");
	else
		$vzkd=0;
	
	$vzaby=chop($_GET['vzaby']);
	if($vzaby!='error')
		// 	nahradit desetinnou carku teckama
		$vzaby=strtr($vzaby,",",".");
	else
		$vzaby=0;
	
	
	
	$kzgut=trim($_GET['kzgut']);
	//cokoliv jineho nez mezeru nahradim pismenem G
	if($kzgut!='G')
		$kzgut='';
	else
		$kzgut='G';
		
	$bedarf_typ=chop($_GET['bedarf']);
	$lager_von=chop($_GET['lagervon']);
	$lager_nach=chop($_GET['lagernach']);


	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');
	$sql="insert into dpos (teil,kzgut,`taetnr-aby`,`taetbez-aby-d`,`taetbez-aby-t`,`vz-min-kunde`,`vz-min-aby`,";
	$sql.="lager_von,lager_nach,bedarf_typ)";
	$sql.=" values ('$teil','$kzgut','$taetnr','$bez_d','$bez_t','$vzkd','$vzaby',";
	$sql.=" '$lager_von','$lager_nach','$bedarf_typ')";
	
	$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysql_error=mysql_error();
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
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

