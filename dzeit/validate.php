<?
require '../fns_dotazy.php';
require '../db.php';

$apl = AplDB::getInstance();

	if(isset($_GET['what'])&&($_GET['what']=="datum"))
	{
		// casti datumu povolim oddelovat znaky : ,.- a mezera
		$vymenit=array(",",".","-"," ");
		if(strlen($_GET['value'])>=3)
		{
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
				$rok=date("Y",$timestamp);
				$mesic=date("m",$timestamp);
				$den=date("d",$timestamp);
				// provedena jen mala kontrola datumu
                                $persnr = $_GET['persnr'];
                                // zkusim vytahnout naplanovane OE pro dany datum a persnr
                                $datum = sprintf("$den.$mesic.$rok");
                                $planOE = $apl->getPlanedOEForDatumPersNr($persnr,  make_DB_datum($datum));
                                if($planOE==NULL){
                                    // 2010-10-07 misto naplanovaneho OE pouziju releloe nebo alternativoe v zavislosti na tydnu lichy/sudy
                                    $planOE = $apl->getOEForPersNrUndDatum($persnr,make_DB_datum($datum));
                                }
                                // pokud nemam nic v planu pouziju variantu s regel nebo alternativOE
                                // <option value='e'>e<\/option><option value='EEK'>EEK<\/option> / format select boxu
                                // pokusny dialog pro zadani dochazky


                                $oe = ($planOE==NULL || $planOE=='-')?'?':$planOE;
				echo "$datum".':'.$oe;
			}
			else
				echo "ERROR";
		}
		else
			echo "ERROR";
	}
?>