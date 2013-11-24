<?
session_start();
require "../../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru

    //echo $_GET;
    
	$teil=$_GET['teil'];
	$pal_nr=$_GET['pal_nr'];
	$fremdauftr=$_GET['fremdauftr'];
	$stk_pro_pal=$_GET['stk_pro_pal'];
	$fremdpos=$_GET['fremdpos'];
	$pal_erst=$_GET['pal_erst'];
	$fremdausauftrag=$_GET['fremdausauftrag'];
        $netgewicht=floatval($_GET['netgewicht']);
	$increment=$_GET['increment'];
	$exgeplannt=$_GET['exgeplannt'];
	$positionen=$_GET['positionen'];
	$auftragsnr=$_GET['auftragsnr'];
	$kunde=$_GET['kunde'];
	$minpreis=$_GET['minpreis'];

	$listArray = explode(':',$positionen);


 
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query("set names utf8");
	
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';

	$error = "OK";

	if ($stk_pro_pal < 0 || $pal_erst < 0 || $increment < 0) {
            $errorDescription = "stk_pro_pal=$stk_pro_pal,pal_erst=$pal_erst,increment=$increment";
            $error = "ERROR";
            $output.="<error>$error</error>";
            $output.="<error_description>$errorDescription</error_description>";
        }

        $output.="<auftragsnr>$auftragsnr</auftragsnr>";


        $posArray = explode(':', $positionen);
        array_pop($posArray);
    
        $user = get_user_pc();
        $paleta = $pal_erst;
        for ($i = 0; $i < $pal_nr; $i++) {
            $output.="<row>";
            $kgut = "";
            //projdu vsechny abgnr pro danou paletu
            foreach ($posArray as $position) {
                $fields = explode(";", $position);
                list($tatkz, $abgnr, $Gtat, $preis, $vzkd, $vzaby) = $fields;
                if ($Gtat == 'G') {
                    $sql = "insert into dauftr (auftragsnr,teil,`Stück`,preis,fremdauftr,fremdpos,kg_stk_bestellung,`mehrarb-kz`,`pos-pal-nr`,abgnr,kzgut,";
                    $sql.="vzkd,vzaby,comp_user_accessuser,inserted,termin) values";
                    $sql.="	('$auftragsnr','$teil','$stk_pro_pal','$preis','$fremdauftr','$fremdpos','$netgewicht','$tatkz',";
                    $sql.="'$paleta','$abgnr','$Gtat','$vzkd','$vzaby','$user',NOW(),'$exgeplannt')";
                }
                else {
                    $sql = "insert into dauftr (auftragsnr,teil,`Stück`,preis,fremdauftr,fremdpos,`mehrarb-kz`,`pos-pal-nr`,abgnr,kzgut,";
                    $sql.="vzkd,vzaby,comp_user_accessuser,inserted,termin) values";
                    $sql.="	('$auftragsnr','$teil','$stk_pro_pal','$preis','$fremdauftr','$fremdpos','$tatkz',";
                    $sql.="'$paleta','$abgnr','$Gtat','$vzkd','$vzaby','$user',NOW(),'$exgeplannt')";
                }
                $output.="<sql>$sql</sql>";
                if ($Gtat == 'G') $kgut = 'G';

                $result = mysql_query($sql);
                $affected_rows = mysql_affected_rows();
                $mysql_error = mysql_error();
            }


            if ($kgut == 'G') {
                // povolena vsuvka
                // mam zapsane operace, ted udelam zapis do lagru
                // ale jen v pripade ze mam na palete G operaci
                $el = erster_lager($teil, $auftragsnr, $paleta);
                //$el='0D';
                // 	nejdriv smazu eventuelni starou pozici v lagru
                $sql_delete = "delete from dlagerbew where ((teil='$teil') and (auftrag_import='$auftragsnr') and (pal_import='$paleta') and (lager_von='0'))";
                $sql_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
                $sql_insert.= "values ('$teil','$auftragsnr','$paleta','$stk_pro_pal',0,'0','$el','$user')";

                mysql_query($sql_delete);
                mysql_query($sql_insert);
		
		//zapis do versandlagru v pripade, ze cislo palety konci na 7
//		$palStr = strval($paleta);
//		if (substr($palStr, strlen($palStr) - 1) == "7") {
//		    $sql_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
//		    $sql_insert.= "values ('$teil','$auftragsnr','$paleta','$stk_pro_pal',0,'0','8V','$user')";
//		    mysql_query($sql_insert);
//		}
    }


            $paleta+=$increment;
            $output.="</row>";
}

$output .= '</response>';

echo $output;
?>

