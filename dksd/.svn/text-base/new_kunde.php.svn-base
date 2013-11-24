<?
session_start();
require "../fns_dotazy.php";
dbConnect();

	$kunde = $_GET['kunde'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


	/////////////////////////////////////////////////////////////////////////////////////////

	mysql_query('set names utf8');
	
	$sql="insert into dksd ";
	$sql.=" (kunde,name1,zahnlungziel)";
	$sql.=" values ('$kunde','NEU Kunde, name eingeben ! zadat jmeno !',14)";

	$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysql_error=mysql_error();

	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	$output .= $mysql_error;
	$output .= '</mysqlerror>';

	$output.="<error>$error</error>";
	$output.="<kunde>$kunde</kunde>";
	$output .= '</response>';
	
	echo $output;
?>

