<?
	require_once "../../fns_dotazy.php";
	//dbConnect();
	class Suggest
	{
	
		public function getSuggestions($keyword,$kunde)
		{
			if(strlen($keyword)>0)
			{
				$sql = "select teil,teillang,teilbez,kunde,gew,UPPER(status) as status,restmengen_verw as rest from dkopf where ((teil like '".$keyword."%') and (kunde='$kunde')) order by kunde,teil limit 30";
			}
			else
			{
				$sql="select teillang from dkopf where (teil='____')";
			}

                        // pomocny status / zobrazim cervene
			dbConnect();
			mysql_query('set names utf8');
			$result=mysql_query($sql);

			$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
			$output .= '<response>';
			// pokud mi dotaz vrati nejake zaznamy, tak je projdu
			if(mysql_affected_rows()>0)
				while ($row = mysql_fetch_array($result))
				{
					$output.="<teil>";
					$output .= '<teilnr>' . $row['teil'] . '</teilnr>';
					$output .= '<teillang>' . $row['teillang'] . '</teillang>';
					$output .= '<bezeichnung>' . $row['teilbez'] . '</bezeichnung>';
					$output .= '<gew>' . $row['gew'] . '</gew>';
                                        $output .= '<status>' . $row['status'] . '</status>';
					$output .= '<rest>' . $row['rest'] . '</rest>';
					$output.='<kunde>'.$row['kunde'].'</kunde>';
					$output.="</teil>";
				}
			// add the final closing tag
			$output .= '</response>';
			// return the results
			return $output;
		}
	}
