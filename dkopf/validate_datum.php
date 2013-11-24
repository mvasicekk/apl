<?


	$controlid=$_GET['controlid'];
	$muze_byt_null=$_GET['allownull'];
	
	if(isset($_GET['what'])&&($_GET['what']=="datum"))
	{
		// casti datumu povolim oddelovat znaky : ,.- a mezera
		$vymenit=array(",",".","-"," ");
		if(strlen($_GET['value'])>=5)
		{
			// sjednotim si oddelovaci znak
			$novy_datum=str_replace($vymenit,"/",$_GET['value']);
			// rozkouskuju na jednotlivy casti
			$dily=explode("/",$novy_datum);
			
			// trochu otestuju jednotlivy dily,jestli tam neni uplnej nesmysl
			if(($dily[1]<13)&&($dily[1]>0)&&($dily[0]>0)&&($dily[0]<32))
			{
				$timestamp=mktime(0,0,0,$dily[1],$dily[0],$dily[2]);
				$rok=date("Y",$timestamp);
				$mesic=date("m",$timestamp);
				$den=date("d",$timestamp);
				// provedena jen mala kontrola datumu
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
?>