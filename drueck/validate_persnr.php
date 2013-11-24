<?
session_start();
require "../fns_dotazy.php";
require '../db.php';
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
        $persnr = $value;
        $oeArray = split(';', $_GET['oe']);
        if(!is_array($oeArray)) $oeArray = array($_GET['oe']);
        $oeallArray = split(';', $_GET['oeall']);
        if(!is_array($oeallArray)) $oeallArray = array($_GET['oeall']);
        $pg = $_GET['pg'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    /////////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');

        $aplDB = AplDB::getInstance();
        //2010-01-06
	$sql="select  PersNr,Name,Vorname,Schicht from dpers where ((`PersNr`='".$value."') and ((`austritt` is null) or (eintritt>austritt)) and (dpers.dpersstatus='MA'))";
//        $sql="select  PersNr,Name,Vorname,Schicht from dpers where ((`PersNr`='".$value."'))";
	$result=mysql_query($sql);
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<pers>";
			$output .= '<persnr>' . $row['PersNr'] . '</persnr>';
			$output .= '<name>' . $row['Vorname']." ".$row['Name'] . '</name>';
			$output .= '<schicht>' . $row['Schicht'] . '</schicht>';
			$output.="</pers>";
		}
	}
	else
	{
		$output.="<pers>";
		$output .= '<persnr>' . "ERROR-NOPERSNR" . '</persnr>';
		$output .= '<sql>' . $sql . '</sql>';
		$output.="</pers>";
	}
	
		$output.="<controlid>";
		$output .= $controlid;
		$output.="</controlid>";

                $caby=$aplDB->getCopyVzAbyToVerbFlag($persnr);
                if($caby===TRUE)
                    $scaby='ano';
                else
                    $scaby='ne';
                $output .= "<copyvzaby>".$scaby."</copyvzaby>";
                $regelOE = $aplDB->getRegelOE($persnr);
                $output .= "<regeloe>".$regelOE."</regeloe>";
                if($pg==9) {
                    
                    $reducedOEArray = array();
                    if(in_array($regelOE, $oeArray)){
                        // regeloe je obsazeno v poli oe, tak ho ulozim do reducedarray
                        array_push($reducedOEArray, $regelOE);
                    }
                    else{
                        // releoe neni v poli oe obsazeno, tak ulozim do reducedarray puvodni oeArray
                        $reducedOEArray = $oeArray;
                    }

                    $output .= "<reducedoe>".join(';', $reducedOEArray)."</reducedoe>";

                }
                else{
                    // vlastne s tim nic nedelam
                    $output .= "<reducedoe>".join(';', $oeArray)."</reducedoe>";
                }
        $output .= "<alloe>".join(';', $oeallArray)."</alloe>";
	$output .= '</response>';
	
	echo $output;
?>

