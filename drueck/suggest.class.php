<?
	require_once "../fns_dotazy.php";
	//dbConnect();
	class Suggest
	{
	
		public function getSuggestions($keyword)
		{
			if(strlen($keyword)>0)
			{
				$sql = "select auftragsnr,kunde,bestellnr,DATE_FORMAT(Aufdat,'%Y-%m-%d') as Aufdat,DATE_FORMAT(fertig,'%Y-%m-%d') as fertig,DATE_FORMAT(ausliefer_datum,'%Y-%m-%d') as ausliefer_datum from daufkopf where ((auftragsnr like '".$keyword."%')) order by kunde,auftragsnr limit 100";
			}

			dbConnect();
			$result=mysql_query($sql);

			$output = '<?xml version="1.0" encoding="windows-1250" standalone="yes"?>';
			$output .= '<response>';
			// pokud mi dotaz vrati nejake zaznamy, tak je projdu
			if(mysql_affected_rows()>0)
				while ($row = mysql_fetch_array($result))
				{
					$output.="<auftrag>";
					$output .= '<auftragsnr>' . $row['auftragsnr'] . '</auftragsnr>';
					$output .= '<kunde>' . $row['kunde'] . '</kunde>';
					$output .= '<bestellnr>' . $row['bestellnr'] . '</bestellnr>';
					$output .= '<aufdat>' . $row['Aufdat'] . '</aufdat>';
					$output.='<fertig>'.$row['fertig'].'</fertig>';
					$output.='<ausliefer_datum>'.$row['ausliefer_datum'].'</ausliefer_datum>';
					$output.="</auftrag>";
				}
			// add the final closing tag
			$output .= '</response>';
			// return the results
			return $output;
		}
	}
