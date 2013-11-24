<?
session_start();
require "../fns_dotazy.php";
dbConnect();

	$teil = $_GET['teilneu'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


	/////////////////////////////////////////////////////////////////////////////////////////

	mysql_query('set names utf8');
	
	$user=get_user_pc();

	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	
	// otestovat, zda uz takovy dil nemam
	$sql = "select teil from dkopf where (teil='$teil')";
	$result = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$output.="<error>DKOPF-TEIL-DUPLICATE</error>";
		$output.="<errordescription>Teil $teil, existiert schon / dil $teil uz existuje</errordescription>";	
	}
	else
	{
		$sql="insert into dkopf ";
		$sql.=" (teil,teilbez,kunde,teillang,comp_user_accessuser)";
		$sql.=" values ('$teil','Teilbezeichnung setzen !!!',999,'$teil','$user')";

		$result=mysql_query($sql);
		$affected_rows=mysql_affected_rows();
		$mysql_error=mysql_error();
	}
	
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	$output .= $mysql_error;
	$output .= '</mysqlerror>';
	$output.="<sql>$sql</sql>";
	$output.="<teilneu>$teil</teilneu>";
	$output.="<kunde>$kunde</kunde>";
	$output .= '</response>';
	
	echo $output;
?>

