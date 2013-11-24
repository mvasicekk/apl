<?
 session_start();
?>
<?
include "../fns_dotazy.php";
dbConnect();
require("../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}

	// vytvorit rozsah od prvniho do posledniho minuleho mesice
	
	$akt_den=date("d");
	$akt_mesic=date("m");
	$akt_rok=date("Y");
	
	// posledni datum minuleho mesice ziskam jako den 0 aktualniho mesice
	
	$lastday = mktime(0,0,0,$akt_mesic,0,$akt_rok);
	$firstday = mktime(0,0,0,date('m',$lastday),1,$akt_rok);
	
	$min_mesic_od=date('d.m.Y',$firstday);
	$min_mesic_do=date('d.m.Y',$lastday);
	
	$predchozi_den=date('d.m.Y',mktime(0,0,0,$akt_mesic,$akt_den-1,$akt_rok));
	
	$smarty->assign("min_mesic_od",$min_mesic_od);
	$smarty->assign("min_mesic_do",$min_mesic_do);
	
	$smarty->assign("predchozi_den",$predchozi_den);
	
	$smarty->assign("now",date("d.m.Y"));
	
	$sql = urldecode($_GET['sql']);
	
	// budu parsovat sql prikaz pro nastaveni razeni podle vybraneho sloupce
	$pozice = strpos($sql,"order by");
	$orderby = substr($sql,$pozice);
	if(strlen($orderby)>0){
		$pozice = strpos($orderby,"by");
		$pozice = $pozice+2;
		$ordersloupec = trim(substr($orderby,$pozice));
		
		// zjistim smer razeni
		// najdu prvni mezeru od konce
		$poziceMezery=strlen($orderby);
		while(($orderby[$poziceMezery]!=' ')&&($poziceMezery>$pozice)) $poziceMezery--;
		if($poziceMezery>$pozice)
			$razeni=substr($orderby,strlen($orderby)-$poziceMezery);
		else
			$razeni="";
	}
	
	$smarty->assign("razeni",$razeni);
	$smarty->assign("order",$orderby);
	$smarty->assign("ordersloupec",$ordersloupec);
	
	
	$smarty->assign("sql",$sql);
	
	// provedu dotaz v databazi
	mysql_query('set names utf8');
	
	$res = mysql_query($sql);
	$numrows = mysql_affected_rows();
	//echo "numrows=$numrows<br>";
	$smarty->assign("numrows",$numrows);
	
	if($numrows>0)
	{
		//echo "<table border='1' cellspacing='0'>";
		// ulozim si nazvy sloupcu
		$pocetsloupcu = mysql_num_fields($res);
		for($cislosloupce=0;$cislosloupce<$pocetsloupcu;$cislosloupce++)
		{
			$sloupce[$cislosloupce]=mysql_field_name($res,$cislosloupce);
			$typy[$cislosloupce]=mysql_field_type($res,$cislosloupce);
		}
		$smarty->assign("sloupce",$sloupce);
		$smarty->assign("typsloupce",$typy);
		
		
		//echo "<tr>";
		$cislosloupce=0;
		foreach($sloupce as $sloupec)
		{
			//echo "<td>$sloupec ($typy[$cislosloupce])</td>";
			$cislosloupce++;
		}
		//echo "</tr>";
		
		//print_r($sloupce);
		$smarty->assign("numrows",$numrows);
		$myindex = 0;
		while($radek=mysql_fetch_assoc($res))
		{
			//echo "<tr>";
			$mysmartyindex="myidx".$myindex++;
			$smartyrow[$mysmartyindex]=$radek;
			$smarty->assign("radky",$smartyrow);
			/*
			foreach($radek as $key=>$item)
			{
				if(strlen($item)>0)
					echo "<td>$item</td>";
				else
					echo "<td>&nbsp;</td>";
			}
			
			echo "</tr>";
			*/
		}
		//echo "</table>";
	}
	else
		$smarty->assign("numrows",$numrows);
	
	$smarty->display('showquery.tpl');
?>
