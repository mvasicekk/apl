<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
	$teil = $_GET['teil_value'];
	$typ = $_GET['typ'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    /////////////////////////////////////////////////////////////////////////////////////////
	
	
	$sql = "select attachment_path from dkopf_attachment where ((teil='".$teil."') and (attachment_typ='".$typ."'))";
	$result=mysql_query($sql);
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<attachment>";
			$output .= '<teil>' . $teil . '</teil>';
			$output .= '<typ>' . $typ . '</typ>';
			$output .= '<attachment_path>' . $row['attachment_path'] . '</attachment_path>';
			$output.="</attachment>";
		}
	}
	else
	{
		$output.="<attachment>";
		$output .= '<attachment_path>' . 'ERROR-NOPATH' . '</attachment_path>';
		$output .= '<sql>' . $sql . '</sql>';
		$output.="</attachment>";
	}
	
		$output.="<controlid>";
		$output .= $controlid;
		$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

