<?
session_start();
require "../fns_dotazy.php";
dbConnect();

	$id = $_GET['id'];
	//oddelim si jen ciselnou cast id, tato ciselna cast se shoduje s id_dauftr
	//najdu si pozici prvniho cisla
	
	for($i=0;$i<strlen($id);$i++)
	{
		if(($id{$i}>=0)&&($id{$i}<=9))
			break;
	}
	
	$num_pozice=$i;
	
	$id_dauftr=substr($id,$num_pozice);
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


	// vyberu vsechny sklady ze seznamu skladu
	$sql="select ";
	//$result=mysql_query($sql);
	
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<lager>";
			$output .= '<lagernr>' . $row['Lager'] . '</lagernr>';
			$output .= '<lagerbechreibung>' . $row['LagerBeschreibung'] . '</lagerbeschreibung>';
			$output.="</lager>";
		}
	}

	// pridam dpos_id, abych nasel spravny radek v tabulce
	
	$output.="<dauftr_id>";
	$output.=$id_dauftr;
	$output.="</dauftr_id>";
	
	$output .= '</response>';
	
	echo $output;
?>

