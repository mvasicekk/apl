<?
session_start();
require "../../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
	$kunde = $_GET['kunde'];
	$auftragsnr = $_GET['auftragsnr'];


	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');

	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	

    /////////////////////////////////////////////////////////////////////////////////////////
	// 1. zjistim si cenu za minutu pro aktualniho zakaznika
	mysql_query('set names utf8');
	$minpreis=$_GET['minpreis'];
	
	// 2. zjistim si podle cisla zakaznika na kolik desetinnych mist mam zaokrouhlovat
	$sql = "SELECT dksd.preis_runden as rn FROM dksd where (kunde=".$kunde.")";
	$r=mysql_query($sql);
	$row=mysql_fetch_array($r);
	$runden=$row['rn'];

	$sql="select daufkopf.auftragsnr,fremdauftr,fremdpos from dauftr join daufkopf using(auftragsnr)";
	$sql.="	where ((fremdauftr>0) and (teil='$value')) order by daufkopf.aufdat desc limit 1";
	$result=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($result);
		$fremdausauftrag = $row['auftragsnr'];
		$fremdauftr=$row['fremdauftr'];
		$fremdpos=$row['fremdpos'];
	}
	else
	{
		$fremdausauftrag = '';
		$fremdauftr='';
		$fremdpos='';
	}

	$output.="<fremdausauftrag>$fremdausauftrag</fremdausauftrag>";
	$output.="<fremdauftrag>$fremdauftr</fremdauftrag>";
	$output.="<fremdpos>$fremdpos</fremdpos>";
	$output.="<auftragsnr>$auftragsnr</auftragsnr>";
	$output.="<kunde>$kunde</kunde>";
	$output.="<minpreis>$minpreis</minpreis>";

	// TODO
	// dodelat upravu , pokud uz je v zakazce u daneho dilu naplanovan termin, tak ho predvyplnit do formulare
	//
	//
	// 3. vytahnu informace z pracovniho planu pro dany dil
	$sql="select dkopf.restmengen_verw as rest,dkopf.status as st,lager_von,lager_nach,bedarf_typ,teilbez as bezeichnung,gew,brgew,`TaetNr-Aby` as abgnr,dtaetkz as tat,`VZ-min-kunde` as vzkd, `vz-min-aby` as vzaby,";
	$sql.="KzGut as kzgut,`kz-druck` as kzdruck";
	$sql.=" from dpos join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby` ";
    $sql.=" join dkopf using(teil)";
	$sql.=" where ((dpos.teil='".$value."') and (dpos.`TaetNr-Aby`>3)) order by abgnr";
	$result=mysql_query($sql);
	$sqlerror=mysql_error();
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output .= '<teil>' . $value . '</teil>';
            $output .= '<bezeichnung>' . $row['bezeichnung'] . '</bezeichnung>';
            $output .= '<status>' . $row['st'] . '</status>';
	    $output .= '<rest>' . $row['rest'] . '</rest>';
			$output .= '<abgnr>' . $row['abgnr'] . '</abgnr>';
            $output .= '<gew>' . $row['gew'] . '</gew>';
            $output .= '<brgew>' . $row['brgew'] . '</brgew>';
			$output .= '<tat>' . $row['tat'] . '</tat>';
			$output .= '<vzkd>' . $row['vzkd'] . '</vzkd>';
			$output .= '<preis>' . round($row['vzkd']*$minpreis,$runden) . '</preis>';
			$output .= '<vzaby>' . $row['vzaby'] . '</vzaby>';
			$output .= '<kzgut>' . $row['kzgut'] . '</kzgut>';
			$output .= '<kzdruck>' . $row['kzdruck'] . '</kzdruck>';
            if(strlen($row['lager_von'])>0)
                $output .= '<lager_von>' . $row['lager_von'] . '</lager_von>';
            else
                $output .= '<lager_von> </lager_von>';

            if(strlen($row['lager_nach'])>0)
                $output .= '<lager_nach>' . $row['lager_nach'] . '</lager_nach>';
            else
                $output .= '<lager_nach> </lager_nach>';

            if(strlen($row['bedarf_typ'])>0)
                $output .= '<bedarf_typ>' . $row['bedarf_typ'] . '</bedarf_typ>';
            else
                $output .= '<bedarf_typ> </bedarf_typ>';

			$output .= '<minpreis>' . $minpreis . '</minpreis>';
			$output .= '<runden>' . $runden . '</runden>';
		}
	}
	else
	{
		$output.="<teile>";
		$output .= '<teil>' . "ERROR" . '</teil>';
		$output .= '<errordescription>apl has no positions</errordescription>';
		$output .= '<sql>' . $sql . '</sql>';
		$output .= '<sqlerror>' . $sqlerror . '</sqlerror>';
		$output.="</teile>";
	}
	
	// 4. zjistit posledni obsah fremdauftr a fremdpos pro tento dil
	// tento dotaz celkem zdrzuje = TODO zoptimalizovat
	$sql = "SELECT dauftr.fremdauftr, dauftr.fremdpos FROM dauftr INNER JOIN daufkopf ON dauftr.AuftragsNr = daufkopf.AuftragsNr where (((dauftr.Teil) = ".$value.") And ((dauftr.fremdauftr) Is Not Null And (dauftr.fremdauftr) > '0')) ORDER BY daufkopf.Aufdat DESC limit 1;";
	//$result=mysql_query($sql);
	$sqlerror=mysql_error();
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output .= '<fremdauftr>' . $row['fremdauftr'] . '</fremdauftr>';
			$output .= '<fremdpos>' . $row['fremdpos'] . '</fremdpos>';
		}
	}
	else
	{
		$output.="<teile>";
		$output .= '<teil>' . "ERROR-FREMD" . '</teil>';
		$output .= '<sql>' . $sql . '</sql>';
		$output .= '<sqlerror>' . $sqlerror . '</sqlerror>';
		$output .= '<errordescription>fremd error</errordescription>';
		$output.="</teile>";
	}

	// 5. zjistim, zda zadany dil uz neni v zakazce a ma vyplneny termin
	$sql = "select termin from dauftr where (((dauftr.Teil) = ".$value.") And ((dauftr.termin) Is Not Null) and (dauftr.auftragsnr=".$auftragsnr.")) order by dauftr.termin desc";
	$result=mysql_query($sql);
	$sqlerror=mysql_error();
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output .= '<termin>' . $row['termin'] . '</termin>';
		}
	}
	else
	{
		$output.="<teile>";
		$output .= '<teil>' . "ERROR-TERMIN" . '</teil>';
		$output .= '<sql>' . $sql . '</sql>';
		$output .= '<sqlerror>' . $sqlerror . '</sqlerror>';
		$output .= '<errordescription>notermin</errordescription>';
		$output .= '<auftragsnr>' . $auftragsnr . '</auftragsnr>';
		$output.="</teile>";
	}

	
		$output.="<controlid>";
		$output .= $controlid;
		$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>
