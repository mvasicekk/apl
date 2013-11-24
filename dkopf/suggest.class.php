<?
	require_once "../fns_dotazy.php";
	//dbConnect();
	class Suggest
	{
	
		public function getSuggestions($keyword)
		{
			if(strlen($keyword)>0)
			{
				// plus omezeni na maximalne 100 zaznamu
				// 2013-01-03 vyhledave i podle fremdauftr_dkopf
				$sql = "select teil,teillang,teilbez,kunde,gew,status from dkopf where ((teil regexp '.*".$keyword.".*') or (teillang regexp '.*".$keyword.".*') or (fremdauftr_dkopf regexp '.*".$keyword.".*')) order by kunde,teil limit 100";
			}
			else
			{
				$sql="select teillang from dkopf where (teil='____')";
			}

			dbConnect();
			$result=mysql_query($sql);

			$output = '<?xml version="1.0" encoding="windows-1250" standalone="yes"?>';
			$output .= '<response>';
			// pokud mi dotaz vrati nejake zaznamy, tak je projdu
			if(mysql_affected_rows()>0)
				while ($row = mysql_fetch_array($result))
				{
                                    $status = trim($row['status']);
                                    if(strlen($status)==0) $status=' ';
					$output.="<teil>";
					$output .= '<teilnr>' . $row['teil'] . '</teilnr>';
					$output .= '<teillang>' . $row['teillang'] . '</teillang>';
					$output .= '<bezeichnung>' . $row['teilbez'] . '</bezeichnung>';
					$output .= '<gew>' . $row['gew'] . '</gew>';
                                        $output .= '<status>' . $status . '</status>';
					$output.='<kunde>'.$row['kunde'].'</kunde>';
					$output.="</teil>";
				}
			// add the final closing tag
			$output .= '</response>';
			// return the results
			return $output;
		}
	}
