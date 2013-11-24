<?
session_start();
require "../../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$pal = $_GET['value'];
	$auftragsnr = $_GET['auftragsnr'];


	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');

	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	

	mysql_query('set names utf8');

	$sql = "SELECT `pos-pal-nr` FROM dauftr where ((`pos-pal-nr`=".$pal.") and (auftragsnr='$auftragsnr')) limit 1";
	$r=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		// jeste najdu paletu s nejvyssim cislem v zakazce
		$sql = "select max(`pos-pal-nr`) as maxpal from dauftr where ((auftragsnr='$auftragsnr'))";
		$r=mysql_query($sql);
		$row=mysql_fetch_array($r);
		$maxPal = $row['maxpal'];
		$output.= "<error>";
		$output.= 	"<errordescription>paleta $pal jiz v zakazce $auftragsnr existuje, posledni cislo obsazene palety je $maxPal</errordescription>";
		$output.= "</error>";
	}
	else
	{
		// paleta v zakazce neni, zkusim jestli neni mensi nez nula
		if($pal<0)
		{
			$output.= "<error>";
			$output.= 	"<errordescription>cislo palety <0 ?</errordescription>";
			$output.= "</error>";
		}
		else
		{
			// cislo palety by melo byt v poradku
		}
	}

	
	$output.="<controlid>";
	$output .= $controlid;
	$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>
