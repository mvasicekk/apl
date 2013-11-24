<?
session_start();
require "../fns_dotazy.php";
dbConnect();

	$dauftr_id = $_GET['dauftr_id'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');



	// zjistim jaky dil je napozici s id_dauftr
	$sql="select teil,kunde from dauftr join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr where (id_dauftr='".$dauftr_id."')";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result);
	$teil=$row['teil'];
	$kunde=$row['kunde'];
	
	
	//hlavicka xml souboru
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';


	// vyberu vsechny mozne cinnosti pro dil 
	$sql="select dtaetkz,`TaetNr-Aby` as abgnr, Name as taetnrbeschreibung from `dpos` join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby` where (teil = '".$teil."') order by `TaetNr-Aby`";
	$result=mysql_query($sql);
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<taetigkeit>";
			$output .= '<taetnr>' . $row['abgnr'] . '</taetnr>';
			$output .= '<dtaetkz>' . $row['dtaetkz'] . '</dtaetkz>';
			$output .= '<taetnrbeschreibung>' . $row['taetnrbeschreibung'] . '</taetnrbeschreibung>';
			$output.="</taetigkeit>";
		}
	}


	// pridam vyber dilu pro daneho zakaznika
	$sql="select teil from `dkopf` where ( kunde = '".$kunde."') order by `teil`";
	$result=mysql_query($sql);
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<teile>";
			$output .= '<teil>' . $row['teil'] . '</teil>';
			$output.="</teile>";
		}
	}

	// pridam dauftr_id, abych nasel spravny radek v tabulce
	$output.="<dauftr_id>";
	$output.=$dauftr_id;
	$output.="</dauftr_id>";
	
	$output .= '</response>';
	
	echo $output;
?>

