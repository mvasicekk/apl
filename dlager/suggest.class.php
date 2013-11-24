<?
	require_once "../fns_dotazy.php";
	//dbConnect();
	class Suggest
	{
	
		public function getSuggestions($keyword)
		{
			if(strlen($keyword)>0)
			{
				$sql = "select teil,teillang,teilbez,kunde,gew from dkopf where ((teil like '".$keyword."%')) order by kunde,teil limit 50";
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
					$output.="<teil>";
					$output .= '<teilnr>' . $row['teil'] . '</teilnr>';
					$output .= '<teillang>' . $row['teillang'] . '</teillang>';
					$output .= '<bezeichnung>' . $row['teilbez'] . '</bezeichnung>';
					$output .= '<gew>' . $row['gew'] . '</gew>';
					$output.='<kunde>'.$row['kunde'].'</kunde>';
					$output.="</teil>";
				}
			// add the final closing tag
			$output .= '</response>';
			// return the results
			return $output;
		}
	}
