<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$teil = trim($_GET['teil']);
	
	$kunde=trim($_GET['kunde']);

	$teillang=trim($_GET['teillang']);
	

	$bezeichnung=trim($_GET['bezeichnung']);
	$gew=trim($_GET['gew']);
	$brgew=trim($_GET['brgew']);
	$wst=trim($_GET['wst']);
	$fa=trim($_GET['fa']);
	//$jb=trim($_GET['jb']);
	$vm=trim($_GET['vm']);
        $spg=trim($_GET['spg']);
//	$reklamation=trim($_GET['reklamation']);

	$status=trim($_GET['status']);

//	$letzte_reklamation=trim($_GET['letzte_reklamation']);
	$bemerk=trim($_GET['bemerk']);
	$art_guseisen=trim($_GET['art_guseisen']);
	
//	if(strlen($_GET['muster_vom'])>0)
//		$muster_vom=make_DB_datum($_GET['muster_vom']);
//	else
//		$muster_vom=trim($_GET['muster_vom']);
//		
//	$muster_platz=trim($_GET['muster_platz']);
//	
//	$muster_vorher_vom=trim($_GET['muster_vorher_vom']);
//	
//	if(strlen($_GET['muster_freigabe1_vom'])>0)
//		$muster_freigabe1_vom=make_DB_datum($_GET['muster_freigabe1_vom']);
//	else
//		$muster_freigabe1_vom=trim($_GET['muster_freigabe1_vom']);
//		
//		
//	$muster_freigabe1=trim($_GET['muster_freigabe1']);
//	
//	if(strlen($_GET['muster_freigabe2_vom'])>0)
//		$muster_freigabe2_vom=make_DB_datum($_GET['muster_freigabe2_vom']);
//	else
//		$muster_freigabe2_vom=trim($_GET['muster_freigabe2_vom']);
//		
//		
//	$muster_freigabe2=trim($_GET['muster_freigabe2']);
	

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');
	// limit 1 je pro jistotu, kdyby se pokazilo kriterium ve where
	
	$sql="update dkopf set ";
	$sql.="`Kunde`='".$kunde."',";
	$sql.="`Teilbez`='".$bezeichnung."',";
	$sql.="`Gew`='".$gew."',";
	$sql.="`BrGew`='".$brgew."',";
	$sql.="`FA`='".$fa."',";
	$sql.="`verpackungmenge`='".$vm."',";
        $sql.="`stk_pro_gehaenge`='".$spg."',";
//	$sql.="`Reklamation`='".$reklamation."',";
	$sql.="`status`='".$status."',";
	
//	if(strlen($letzte_reklamation)>0)
//		$sql.="`Letzte-Reklamation`='".$letzte_reklamation."',";

    
		
		
//	if(strlen($muster_vom)>0)	
//		$sql.="`Muster-vom`='".$muster_vom."',";
//	$sql.="`Muster-Platz`='".$muster_platz."',";
//	if(strlen($muster_vorher_vom)>0)
//		$sql.="`Muster-vorher-vom`='".$muster_vorher_vom."',";
//	$sql.="`Muster-Freigabe-1`='".$muster_freigabe1."',";
//	if(strlen($muster_freigabe1_vom)>0)
//		$sql.="`Muster-Freigabe-1-vom`='".$muster_freigabe1_vom."',";
//	$sql.="`Muster-Freigabe-2`='".$muster_freigabe2."',";
//	if(strlen($muster_freigabe2_vom)>0)
//		$sql.="`Muster-Freigabe-2-vom`='".$muster_freigabe2_vom."',";
	$sql.="`bemerk`='".$bemerk."',";
	$sql.="`teillang`='".$teillang."',";
	$sql.="`Art Guseisen`='".$art_guseisen."'";
	$sql.=" where (Teil='".$teil."') limit 1";
	
	$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysqlError=mysql_error();
	
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<sql>';
	$output .= $sql;
	$output .= '</sql>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	$output .= $mysqlError;
	$output .= '</mysqlerror>';
	
	$output .= '</response>';
	
	echo $output;
	
?>

