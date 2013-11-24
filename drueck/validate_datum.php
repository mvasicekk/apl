<?
 	session_start();

	$controlid=$_GET['controlid'];
	$muze_byt_null=$_GET['allownull'];
	
	if(isset($_GET['what'])&&($_GET['what']=="datum"))
	{
		// casti datumu povolim oddelovat znaky : ,.- a mezera
		$vymenit=array(",",".","-"," ");
		if(strlen($_GET['value'])>=3)
		{	
			
			// datum byl zadan i s rokem
			// sjednotim si oddelovaci znak
			$novy_datum=str_replace($vymenit,"/",$_GET['value']);
			// rozkouskuju na jednotlivy casti
			$dily=explode("/",$novy_datum);
			$pocetDilu = count($dily);
			
			// trochu otestuju jednotlivy dily,jestli tam neni uplnej nesmysl
			if(($dily[1]<13)&&($dily[1]>0)&&($dily[0]>0)&&($dily[0]<32))
			{
				
				if($pocetDilu==2)
				{
					// nezadal rok				
					$dily[2]=date('Y');
				}
				if(($pocetDilu==3)&&(strlen($dily[2])==0))
				{
					// nezadal rok				
					$dily[2]=date('Y');
				}
				
				$timestamp=mktime(0,0,0,$dily[1],$dily[0],$dily[2]);
                                $now = time();
				$rok=date("Y",$timestamp);
				$mesic=date("m",$timestamp);
				$den=date("d",$timestamp);
				// provedena jen mala kontrola datumu

                                if($timestamp>$now)
                                    echo "ERROR>$controlid";
                                else
                                    echo "$den.$mesic.$rok>$controlid";

			}
			else
				echo "ERROR>$controlid";
		}
		else
		{
			if((strlen(trim($_GET['value']))==0)&&($muze_byt_null))
			{
				// mam prazdne policko a mam povoleno prazdne
				echo ">".$controlid;
			}
			else
				echo "ERROR>$controlid";
		}
	}
	
	if(isset($_GET['what'])&&($_GET['what']=="datumtime"))
	{
		// casti datumu povolim oddelovat znaky ,.- a mezeru ne, protoze s ni oddeluju cas
		$vymenit=array(",",".","-");
		if(strlen($_GET['value'])>=5)
		{
			// sjednotim si oddelovaci znak
			$novy_datum=str_replace($vymenit,"/",$_GET['value']);
			// rozkouskuju na jednotlivy casti
			// cast za mezerou predstavuje cas, cast pred mezerou je datum
			
			// cast pred mezerou
			$dily=substr($novy_datum,0,strpos($novy_datum,' '));
			$dily=explode("/",$dily);
			
			// cast za mezerou je cas
			$cas=substr($novy_datum,strpos($novy_datum,' '));
			$cas=explode(':',$cas);
			
			// trochu otestuju jednotlivy dily,jestli tam neni uplnej nesmysl
			if(($dily[1]<13)&&($dily[1]>0)&&($dily[0]>0)&&($dily[0]<32))
			{
				$timestamp=mktime($cas[0],$cas[1],0,$dily[1],$dily[0],$dily[2]);
				$rok=date("Y",$timestamp);
				$mesic=date("m",$timestamp);
				$den=date("d",$timestamp);
				$hodina=date("H",$timestamp);
				$minuta=date("i",$timestamp);
				
				// provedena jen mala kontrola datumu
				echo "$den.$mesic.$rok $hodina:$minuta>$controlid";
			}
			else
				echo "ERROR>$controlid";
		}
		else
		{
			if((strlen(trim($_GET['value']))==0)&&($muze_byt_null))
			{
				// mam prazdne policko a mam povoleno prazdne
				echo ">".$controlid;
			}
			else
				echo "ERROR>$controlid";
		}
	}

?>