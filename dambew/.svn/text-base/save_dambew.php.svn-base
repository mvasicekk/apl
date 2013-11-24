<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	mysql_query('set names utf8');
	
	// TODO: dodelat validaci parametru

	$validaceOk=true;
	
	$persnr=trim($_GET['persnr']);
	if(is_nan($persnr)) $validaceOk=false;
     	$oeselect=trim($_GET['oeselect']);
//	if(is_nan($oeselect)) $validaceOk=false;

	$datum=make_DB_datum(trim($_GET['datum']));
	$amnr=trim($_GET['amnr']);
	if(is_nan($amnr)) $validaceOk=false;
	$invnr=trim($_GET['invnr']);
	if(is_nan($invnr)) $validaceOk=false;
	$ausstk=trim($_GET['ausstk']);
	if(is_nan($ausstk)) $validaceOk=false;
	$rueckstk=trim($_GET['rueckstk']);
	if(is_nan($rueckstk)) $validaceOk=false;
	$grund=trim($_GET['grund']);
	if(is_nan($grund)) $validaceOk=false;
	$bemerkung=trim($_GET['bemerkung']);

	if($validaceOk)
	{
		// dalsi testy
		// pokud jsou vracene i vydane kusy nula nema smysl zapisovat do databaze
		if(($ausstk==0)&&($rueckstk==0))
			$validaceOk=false;
	}
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


	$userpc=get_user_pc();

	// pokud mam nenulove inventarni cislo jde o typ vlozeni 2, tj. vydej stroje podle inventarniho cisla
	if($invnr>0)
	{
		$insertAmnr = $invnr;
		$amnrTyp = 2;
	}
	else
	{
		$insertAmnr = $amnr;
		$amnrTyp = 1;
	}
	
	$sql = "insert into dambew (persnr,oe,datum,amnr,amnr_typ,ausgabestk,rueckgabestk,rueckgrund,bemerkung,comp_user_accessuser,insert_stamp) ";
	$sql.= " values(";
	$sql.= " '$persnr',";
        $sql.= " '$oeselect',";
	$sql.= " '$datum',";
	$sql.= " '$insertAmnr',";
	$sql.= " '$amnrTyp',";
	$sql.= " '$ausstk',";
	$sql.= " '$rueckstk',";
	$sql.= " '$grund',";
	$sql.= " '$bemerkung',";
	$sql.= " '$userpc',";
	$sql.= " NOW())";
	
	mysql_query($sql);

	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';

	$affected_rows=mysql_affected_rows();
	$mysqlerror=mysql_error();
	$output .= '<sql>';
	$output .= $sql;
	$output .= '</sql>';
	$output .= '<affected_rows>';
	$output .= $affected_rows;
	$output .= '</affected_rows>';
	$output .= '<mysqlerror>';
	$output .= $mysqlerror;
	$output .= '</mysqlerror>';
	$output .= '</response>';
	
	echo $output;
	
?>