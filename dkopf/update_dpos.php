<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$dpos_id = $_GET['id'];
	
	$taetnr=chop($_GET['taetnr']);

	$bez_d=chop($_GET['bez_d']);
	$bez_t=chop($_GET['bez_t']);
	
	$vzkd=chop($_GET['vzkd']);
	// nahradit desetinnou carku teckama
	$vzkd=strtr($vzkd,",",".");
	
	$vzaby=chop($_GET['vzaby']);
	// nahradit desetinnou carku teckama
	$vzaby=strtr($vzaby,",",".");
	
	
	$KzGut=chop($_GET['KzGut']);
	//cokoliv jineho nez mezeru nahradim pismenem G
	if(strlen($KzGut)>0)
		$KzGut='G';
		
	$bedarf_typ=chop($_GET['bedarf_typ']);
	$lager_von=chop($_GET['lager_von']);
	if(strlen($lager_von)==1) $lager_von='';
	$lager_nach=chop($_GET['lager_nach']);
	if(strlen($lager_nach)==1) $lager_nach='';

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');
	$sql="update dpos set `TaetNr-Aby`='".$taetnr."' ,`TaetBez-Aby-D`='".$bez_d."' ,`TaetBez-Aby-T`='".$bez_t."' ,`VZ-min-kunde`='".$vzkd."' ,`vz-min-aby`='".$vzaby."' ";
	$sql.=" ,`kzgut`='".$KzGut."',lager_von='".$lager_von."' ,lager_nach='".$lager_nach."' ,bedarf_typ='".$bedarf_typ."'";
	$sql.=" where (dpos_id=".$dpos_id.")";
	
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

