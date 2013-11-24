<?
session_start();
require "../fns_dotazy.php";
dbConnect();

	$auftragsnr = $_GET['auftragsnr'];
	$kunde = $_GET['kunde'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


	/////////////////////////////////////////////////////////////////////////////////////////


	// zjistit cenu za minutu pro daneho zakaznika
	$sql = "select preismin,`waehr-kz` as waehr from dksd where (kunde='$kunde')";
	$result=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($result);
		$preismin = $row['preismin'];
                $waehrung = $row['waehr'];
	}
	else{
		$preismin = 0;
                $waehrung = 'EUR';
        }

	$sql="insert into daufkopf (auftragsnr,kunde,minpreis,aufdat,waehr_kz)";
	$sql.=" values('$auftragsnr','$kunde','$preismin',NOW(),'$waehrung')";

	$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysql_error=mysql_error();

	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	$output .= $mysql_error;
	$output .= '</mysqlerror>';

	$output.="<error>$error</error>";
	$output.="<auftragsnr>$auftragsnr</auftragsnr>";
	$output.="<kunde>$kunde</kunde>";
	$output .= '</response>';
	
	echo $output;
?>

