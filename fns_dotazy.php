<?php


// definice konstant
// ---------------------------------------------------------------------------

// pripojeni k lokalni databazi na abyserveru

//define(LOCAL_HOST,'172.16.1.111');
define(LOCAL_HOST,'localhost');
define(LOCAL_USER,'root');
define(LOCAL_PASS,'nuredv');
define(LOCAL_DB,'apl');

// pripojeni ke vzdalene databazi, pro import faktur apod.
// aktualne se neimportuje vzdalene, takze jsem pripojen ke stejnemu serveru

//define(REMOTE_HOST,'172.16.1.111');
define(REMOTE_HOST,'localhost');
define(REMOTE_USER,'root');
define(REMOTE_PASS,'nuredv');
define(REMOTE_DB,'apl');

//----------------------------------------------------------------------------



function dbConnect(){
//mysql_connect('localhost', 'root', 'nuredv');
//mysql_select_db('apl');
mysql_connect(LOCAL_HOST,LOCAL_USER,LOCAL_PASS);
mysql_select_db(LOCAL_DB);

mysql_query('SET names cp1250');
}

function dbConnectRemote($host,$user,$pass,$db){
$link=mysql_connect($host,$user,$pass);
mysql_select_db($db,$link);
mysql_query('set names utf8',$link);
return $link;
}

function validateDatum($value){

		// casti datumu povolim oddelovat znaky : ,.- a mezera
		$vymenit=array(",",".","-"," ");
		if(strlen($value)>=3)
		{
			// datum byl zadan i s rokem
			// sjednotim si oddelovaci znak
			$novy_datum=str_replace($vymenit,"/",$value);
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
				return "$den.$mesic.$rok";
			}
			else
				return null;
		}
        else
            return null;
    }

// ulozeni pokusu o prihlaseni
function insertAccessLog($username,$password,$prihlasen,$host)
{
	dbConnect();
	$sql = "insert into accesslog (name,password,login_ok,host) values ('$username','$password','$prihlasen','$host')";
	mysql_query('set names utf8');
	mysql_query($sql);
}

// ma dana pozicevyplneny export ?
function hasExport($auftrag,$paleta)
{
	dbConnect();
	$sql = "select auftragsnr from dauftr where ((auftragsnr='$auftrag') and (`pos-pal-nr`='$paleta') and (`auftragsnr-exp`>0) and (`pal-nr-exp`>0)) limit 1";
	// dotaz vybere exportovane
	$res=mysql_query($sql);
	if(mysql_affected_rows()>0)
		return 1;
	else
		return 0;
}


// uprava vsech pozic na jedne palete
// pouziti pri editaci polozek v dauftr
// 080411 pridana upravapoctu importnich kusu

function updateDauftr_Termin_AuftragsnrExp_PalExp_fremdauftr_fremdpos($stk,$termin,$auftragsnr_exp,$pos_pal_nr_exp,$fremdauftr,$fremdpos,$dauftr_id)
{
	$dauftrRow = getDauftrRowFromId($dauftr_id);
	
	$pos_pal_nr_exp=chop($_GET['pos_pal_nr_exp']);
	if(strlen($pos_pal_nr_exp)==0)
		$pos_pal_nr_exp='NULL';
		
	$auftragsnr=$dauftrRow['auftragsnr'];
	$pal=$dauftrRow['pos-pal-nr'];
	$teil=$dauftrRow['teil'];
	$sql = "update dauftr set `stück`='$stk',termin='$termin',`auftragsnr-exp`=$auftragsnr_exp,`pal-nr-exp`=$pos_pal_nr_exp,fremdauftr='$fremdauftr',fremdpos='$fremdpos'";
	$sql.=" where ((auftragsnr='$auftragsnr') and (teil='$teil') and (`pos-pal-nr`='$pal')) limit 20";
	mysql_query('set names utf8');
	mysql_query($sql);
	$mysql_error=mysql_error();
	
	// musim vzhledem ke zmene poctu importnich kusu udelat i zmenu v dlagerbew
	// najdu di odpovidajici import_auftrag,teil,import_pal
	// mam z predesla

    // zmena nemuzu udelat jednoduchy update, protoze v pripade, ze mam udelanou inventuru, tak se mi posune
    // pri updatu i timestamp a ten nemuzu natvrdo zapsat.

    // musim udelat storno zaznam a vytvorit novy
    // nejdriv si vytahnu stary zaznam
    // musim vystornovat sumu vsech kusu daneho dilu
    // TODO: nemusi spravne fungovat pokud se v prubehu zmeni prvni sklad
    $sql_select = "select sum(gut_stk) as gut_stk,max(lager_nach) as lager_nach from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (teil='$teil') and (lager_von='0'))";
    $res = mysql_query($sql_select);
    $row = mysql_fetch_array($res);
    $gut_stk = $row['gut_stk'];
    $storno_stk = $gut_stk * (-1);
    $lager_nach = $row['lager_nach'];
    $user = get_user_pc();

    // pripravim storno zaznam
    $sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser)";
    $sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$storno_stk','0','$lager_nach','$user')";
    // pokud je co stornovat, provedu prikaz
    if($storno_stk!=0)
        mysql_query($sql_insert_storno);

    // pripravim novy zaznam
    $sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser)";
    $sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$stk','0','$lager_nach','$user')";
    mysql_query($sql_insert_storno);

    // pri uprave poctu kusu u dilu, ktery uz ma inventuru zobrazim hlaseni
    // co mam vratit za hodnotu a jak ji vyhodnotit ?
    // 1. zjistim si datum inventury dilu

//
//	$sql_update = "update dlagerbew set gut_stk='$stk' where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (teil='$teil') and (lager_von='0')) limit 1";
//	mysql_query($sql_update);
	
	return $mysql_error;
}

//function nuluj_sumy_pole(&$pole)
//{
//	foreach($pole as $key=>$prvek)
//	{
//		$pole[$key]=0;
//	}
//}

// vrati radek z tabulky dauftr podle zadaneho id
function getDauftrRowFromId($dauftr_id)
{
	dbConnect();
	mysql_query('set names utf8');
	$sql = "select * from dauftr where (id_dauftr='$dauftr_id')";
	$res=mysql_query($sql);
	$row = mysql_fetch_array($res);
	return $row;
}

/**
 * vrati pole palet, ktere patri k dane pozici urcene parametry podle tabulky dauftr
 *
 * @param $auftrag cislo zakazky
 * @param $teil cislo dilu
 * @param $abgnr cislo operace, pokud ho nezadam vyberu vsechny palety bez ohledu na operaci
 * @return array pole cisel vyjadrujici cisla palet
 */
function getPalArrayFromDauftrAuftragsnrTeilAbgnr($auftrag,$teil,$abgnr=0)
{
	dbConnect();
	
	$sql = "select `pos-pal-nr` as pal from dauftr left join daufkopf on dauftr.`auftragsnr-exp`=daufkopf.auftragsnr where ((dauftr.auftragsnr='$auftrag') and (teil='$teil') and (abgnr='$abgnr') and (fertig='2100-01-01' or fertig is null)) order by pal";
		
	$res = mysql_query($sql);
	
	//echo "sql=$sql<br>";
	
	//echo "getPalArrayFromDauftrAuftragsnrTeilAbgnr<br>";
	$affectedrow=mysql_affected_rows();
	//echo "affectedrow=$affectedrow<br>";
	
	if(mysql_affected_rows()>0)
	{
		$i=0;
		while($row=mysql_fetch_array($res))
		{
			$palety[$i]=$row['pal'];
			//echo "getPalArrayFromDauftrAuftragsnrTeilAbgnr<br>";
			$i++;
		}
		return $palety;
	}
	else
		return 0;
}


/**
 * vrati pole palet z druecku, ktere budu aktualizovat pro vybranou pozici ze zakazky
 *
 * @param unknown_type $auftrag
 * @param unknown_type $teil
 * @param unknown_type $abgnr
 * @param unknown_type $pal
 * @return unknown
 */

function getPalArrayFromDrueckAuftragsnrTeilAbgnrProPal($auftrag,$teil,$abgnr=0,$pal)
{
	dbConnect();
	$sql = "select `pos-pal-nr` as pal from drueck where ((auftragsnr='$auftrag') and (teil='$teil') and (taetnr='$abgnr') and (`pos-pal-nr`)='$pal') order by pal";
		
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$i=0;
		while($row=mysql_fetch_array($res))
		{
			$palety[$i]=$row['pal'];
			$i++;
		}
		return $palety;
	}
	else
		return 0;
	
}

/**
 * vrati pole palet, ktere patri k dane pozici urcene parametry podle tabulky drueck
 *
 * @param $auftrag cislo zakazky
 * @param $teil cislo dilu
 * @param $abgnr cislo operace, pokud ho nezadam vyberu vsechny palety bez ohledu na operaci
 * @return array pole cisel vyjadrujici cisla palet
 */
function getPalArrayFromDrueckAuftragsnrTeilAbgnr($auftrag,$teil,$abgnr=0)
{
	dbConnect();
	
	$sql = "select drueck.`pos-pal-nr` as pal from drueck join dauftr on dauftr.auftragsnr=drueck.auftragsnr and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.teil=drueck.teil and dauftr.abgnr=drueck.taetnr left join daufkopf on dauftr.`auftragsnr-exp`=daufkopf.auftragsnr where ((drueck.auftragsnr='$auftrag') and (drueck.teil='$teil') and (taetnr='$abgnr') and (fertig='2100-01-01' or fertig is null)) order by pal";
		
	//echo "sql=$sql";
	$res = mysql_query($sql);
	
	
	if(mysql_affected_rows()>0)
	{
		$i=0;
		while($row=mysql_fetch_array($res))
		{
			$palety[$i]=$row['pal'];
			$i++;
		}
		return $palety;
	}
	else
		return 0;
}

// zjistim na kolik mist se zaokrouhluje cena pro daneho zakaznika
function getRundenFromKunde($kunde)
{
	dbConnect();
	$sql = "select preis_runden as r from dksd where (kunde='$kunde')";
	$res=mysql_query($sql);
	$row = mysql_fetch_array($res);
	return $row['r'];
}

// upravi hodnoty vzkd v druecku na novou hodnotu
function updateDrueckVzKdFromAuftrag($vzkd_neu,$pal,$teil,$abgnr,$auftragsnr)
{
	dbConnect();
	
	$vzkd_neu=round($vzkd_neu,4);
	
	$sql="update drueck set `vz-soll`='$vzkd_neu' where ((auftragsnr='$auftragsnr') and (`pos-pal-nr`='$pal') and (teil='$teil') and (taetnr='$abgnr'))";
	mysql_query($sql);
	return mysql_affected_rows();
}



/**
 * upravi hodnoty vzkd a vzaby v druecku na zadanou hodnotu
 *
 * @param unknown_type $preis
 * @param unknown_type $auftrag
 * @return int pocet upravenych radku
 */
function updateDrueckVzKdVzAbyFromAuftrag($vzkd_neu,$vzaby_neu,$pal,$teil,$abgnr,$auftragsnr)
{
	dbConnect();
	
	$vzkd_neu=round($vzkd_neu,4);
	
	// nemelo by se prepocitat i verb ?
	
	$sql="update drueck set `vz-soll`='$vzkd_neu',`vz-ist`='$vzaby_neu' where ((auftragsnr='$auftragsnr') and (`pos-pal-nr`='$pal') and (teil='$teil') and (taetnr='$abgnr'))";
	mysql_query($sql);
	return mysql_affected_rows();
}


function updateDauftrPreisVzKdVzAbyFromAuftrag($auftragsnr,$teil,$pal,$abgnr,$preis,$vzkd,$vzaby,$allepaletten)
{
	dbConnect();

	if($allepaletten){
		$sql = "update dauftr left join daufkopf on dauftr.`auftragsnr-exp`=daufkopf.auftragsnr set preis='$preis',vzkd='$vzkd',vzaby='$vzaby' where ((dauftr.auftragsnr='$auftragsnr') and (teil='$teil')  and (abgnr='$abgnr') and (fertig='2100-01-01' or fertig is null))";
	}
	else{
		$sql = "update dauftr left join daufkopf on dauftr.`auftragsnr-exp`=daufkopf.auftragsnr set preis='$preis',vzkd='$vzkd',vzaby='$vzaby' where ((dauftr.auftragsnr='$auftragsnr') and (teil='$teil') and (`pos-pal-nr`='$pal') and (abgnr='$abgnr') and (fertig='2100-01-01' or fertig is null))";		
	}
	
	mysql_query($sql);
	return mysql_affected_rows();
}


function updateDpos($teil,$abgnr,$vzkd,$vzaby){
	dbConnect();
	$sql="update dpos set `vz-min-kunde`='$vzkd',`vz-min-aby`='$vzaby' where ((teil='$teil') and (`taetnr-aby`='$abgnr')) limit 1";
	//echo $sql;
	mysql_query($sql);
	return mysql_affected_rows();
}

// prepocita novy cas vzkd podle zadane ceny a cisla zakazky
function getVzKdFromPreisAuftrag($preis,$auftrag)
{
	
	$vzkd = 0;
	dbConnect();
	mysql_query("set names utf8");
	$sql = "select minpreis from daufkopf where (auftragsnr='$auftrag')";
	$res=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$minpreis = $row['minpreis'];
		if($minpreis!=0)
			$vzkd = $preis / $minpreis;
	}

	return $vzkd;
}

/**
 *
 * @param string $reportname
 * @param string $password
 * @param string $user
 * @param int $usePassword <>0 kontrolovat podle hesla zadaneho v tabulce reportsecurity, 0 zkontroluje podle uzivatelova prihlasovaciho hesla do programu
 * @return boolean - true pokud ma uzivatel pristup k reportu
 */
function testReportPassword($reportname,$password,$user,$usePassword=1)
{
	dbConnect();
        if($usePassword!=0)
            $sql = "select user from reportsecurity where((reportname='$reportname') and (user='$user') and (password='$password'))";
        else
            $sql = "select user from reportsecurity where((reportname='$reportname') and (user='$user'))";
            
	$res=mysql_query($sql);

        if(mysql_affected_rows()>0){
                if($usePassword==1)
                    return true;
                else{
                    // zkontroluju znovu zadane heslo uzivatele
                    $sql = "select dbenutzer.name from dbenutzer where name='$user' and password='$password'";
                    $res=mysql_query($sql);
                    if(mysql_affected_rows()>0)
                        return true;
                    else
                        return false;
                }
        }
	else
		return false;
}
///////////////////////////////////////////////////////////////////////////////////////////////////
function get_rechnung_datum($auftrag)
{
	$sql="select DATE_FORMAT(fertig,'%d.%m.%Y') as rechdatum from daufkopf where (auftragsnr='$auftrag') limit 1";
	$res=mysql_query($sql);
	$row = mysql_fetch_array($res);
	return $row['rechdatum'];	
}

/**
 * otestuje, zda ma zakazka hotovou fakturu
 *
 * @param unknown_type $auftrag	cislo zakazky
 * @return unknown 1 ma fakturu, 0 nema fakturu
 */

function has_rechnung($auftrag)
{
	$sql="select DATE_FORMAT(fertig,'%d.%m.%Y') as fertig from daufkopf where (auftragsnr='$auftrag') limit 1";
	$res=mysql_query($sql);
        if(mysql_affected_rows()>0){
	$row = mysql_fetch_array($res);
            if($row['fertig']=="01.01.2100")
            	return 0;
            else
		return 1;
        }
        else{
            return 0;
        }
}

function set_rechnung_datum($auftrag,$datum)
{
	$sql="update daufkopf set fertig='$datum' where (auftragsnr='$auftrag') limit 1";
	$res=mysql_query($sql) or die(mysql_error());
}

function get_minpreis_von_auftrag($auftrag)
{
	$sql="select minpreis from daufkopf where (auftragsnr='$auftrag')";
	$res=mysql_query($sql) or die(mysql_error());
	$row=mysql_fetch_array($res);
	return $row['minpreis'];
}

function get_kunde_von_auftrag($auftrag)
{
	$sql="select kunde from daufkopf where (auftragsnr='$auftrag')";
	$res=mysql_query($sql) or die(mysql_error());
	$row=mysql_fetch_array($res);
	return $row['kunde'];
}

function get_teil_bemerk($teil,$znaku)
{
	mysql_query('set names utf8');
	$sql=" SELECT dpos.`TaetBez-Aby-D` as bez_d, dpos.`TaetBez-Aby-T` as bez_t";
	$sql.=" FROM dpos";
	$sql.=" WHERE (((dpos.Teil)='$teil') AND ((dpos.`TaetNr-Aby`)=3)) order by stamp desc limit 5";

	$vystup="";
	$res = mysql_query($sql) or die(mysql_error());
	
	while($row=mysql_fetch_array($res))
	{
		$vystup.=$row['bez_d']." ".$row['bez_t'].",";
	}

	return substr($vystup,0,$znaku);
}

function lager_von($teil, $t)
{
	$sql = "SELECT dpos.lager_von FROM dpos WHERE (((dpos.teil) = '" . $teil . "') And ((dpos.lager_von) Is Not Null) and (dpos.`taetnr-aby`='" . $t . "'))";
	$res = mysql_query($sql) or die(mysql_error());
	
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		if(strlen($row['lager_von'])>0)
			return $row['lager_von'];
		else
			return "0D";
	}
	else
		return "0D";
}

function get_preis_laut_auftrag($export)
{
	$sql = "SELECT sum(`preis`*(`stk-exp`+auss4_stk_exp)) AS gespreis FROM dauftr";
	$sql.=" where (dauftr.`auftragsnr-exp`='".$export."')";
	$res = mysql_query($sql) or die(mysql_error());
	
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		return $row['gespreis'];
	}
	else
		return 0;
}


function lager_nach($teil, $t)
{
	$sql = "SELECT dpos.lager_nach FROM dpos WHERE (((dpos.teil) = '" . $teil . "') And ((dpos.lager_nach) Is Not Null) and (dpos.`taetnr-aby`='" . $t . "'))";
	$res = mysql_query($sql) or die(mysql_error());
	
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		if(strlen($row['lager_nach'])>0)
			return $row['lager_nach'];
		else
			return "0D";
	}
	else
		return "0D";
}

// vrati oznaceni prvniho skladu pro dany dil auftrag a paletu
function erster_lager($teil,$auftrag,$paleta)
{
	//dbConnect();
	// nejdriv si zjistim cislo operace
	$sql = "select abgnr from dauftr where ((teil='$teil') and (auftragsnr='$auftrag') and (`pos-pal-nr`='$paleta') and (abgnr>3)) order by abgnr";
	$res = mysql_query($sql);
	$abgnr=0;
	$ela='OD';
	
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$abgnr = $row['abgnr'];
	}
	
	// pro zjistinene abgnr si zjistim z tabulky dpos jmeno skladu
	// pokud pro dane abgnr nic nenajdu vratim 0D
	$sql = "select lager_von from dpos where ((teil='$teil') and (lager_von is not null) and (lager_von<>'0D') and (`taetnr-aby`='$abgnr'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$lVon = $row['lager_von'];
		if(strlen($lVon)>0)
			$ela = $lVon;
	}
	else
	{
		// jeste posledni moznost, jestli nemam jmeno skladu u zaskrtnute G operace
		$sql = "select lager_von from dpos where ((teil='$teil') and (kzgut='G') and (`kz-druck`<>0))";
		$res = mysql_query($sql);
		if(mysql_affected_rows()>0)
		{
			$row = mysql_fetch_array($res);
			$lVon = $row['lager_von'];
			if(strlen($lVon)>0)
				$ela = $lVon;
		}
	}
	return $ela;
}



function get_pc_ip()
{
 $ip=$_SERVER["REMOTE_ADDR"];
 $ip=strtr($ip,".","_");
 return $ip;
}

function get_user()
{
  $ident=$_SESSION["user"];
 return $ident;
}

/**
 * vrati adresu pocitace / prihlaseny uzivatel
 *
 * @return string
 */
function get_user_pc()
{
 $pocitac="PHP_".$_SERVER["REMOTE_ADDR"];
 $ident=$pocitac."/".$_SESSION["user"];
 return $ident;
}

function make_DB_datetime($time,$datum)
{
	$vonHod = substr($time,0,2); // roz�e�eme p��chod na �daje
	$vonMin = substr($time,3,2); // roz�e�eme p��chod na �daje
	$datumRoz = explode(".",$datum); // Roz�e�eme datum na jednotliv� �daje
	
	$von = date("Y-m-d H:i:s",mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2])); // sestav�me nov� p��chod i s datumem

	return $von;
}

function make_DB_datum($datum)
{
	$datumRoz = explode(".",$datum); // Roz�e�eme datum na jednotliv� �daje
	$datum = $datumRoz[2]."-".$datumRoz[1]."-".$datumRoz[0]; // Op�t ho spoj�me
	return $datum;
}
 
function ganzMonatProgress(){
dbConnect();

$guss = 0;
$alu = 0;
$ne = 0;
$sonst = 0;

$sql1="SELECT `DProduktGruppen`.`PG_Nr`, `DKSD`.`Kunde`, `DKOPF`.`Teil`
FROM (`DKOPF` INNER JOIN `DKSD` ON `DKOPF`.`Kunde`=`DKSD`.`Kunde`) LEFT JOIN DProduktGruppen ON `DKSD`.`Kunden_Stat_Nr`=`DProduktGruppen`.`PG_Nr`
ORDER BY `DProduktGruppen`.`PG_Nr`, `DKSD`.`Kunde`, `DKOPF`.`Teil`;";

$res1 = mysql_query($sql1) or die(mysql_error());
while($zaznam1 = mysql_fetch_array($res1)){
  $sql2="SELECT `Datum`, Sum(If(`auss_typ`=4,(`St�ck`+`auss-st�ck`)*`vz-soll`,`St�ck`*`vz-soll`)) AS kdmin
FROM DRUECK WHERE `Teil` = '".$zaznam1['Teil']."' and month(`Datum`) = month(now()) and year(`Datum`) = year(now()) GROUP BY `Teil`;";
$res2 = mysql_query($sql2) or die("sql2: ".mysql_error()); 
$zaznam2 = mysql_fetch_array($res2);

  Switch ($zaznam1["PG_Nr"]){
    case 1: $guss = $guss + $zaznam2["kdmin"]; break;
    case 3: $alu = $alu + $zaznam2["kdmin"]; break;
    case 4: $ne = $ne + $zaznam2["kdmin"]; break;
    case 9: $sonst = $sonst + $zaznam2["kdmin"]; break;
  }
}
$summ = $guss + $alu + $ne + $sonst;

$summ = round($summ);
$monat = date("m");
$guss = round($guss);
$alu = round($alu);
$ne = round($ne);
$sonst = round($sonst);


echo "<table id='table1'>";
echo "<tr><th>Monat</th><th>1</th><th>3</th><th>4</th><th>Sonst.</th><th>Summe</th></tr>";
echo "<tr><td>$monat</td><td>$guss</td><td>$alu</td><td>$ne</td><td>$sonst</td><td>$summ</td></tr>";
echo "</table>";
}

function ganzMonatProgress2(){
dbConnect();

$summ = 0;
mysql_connect('abyserver', 'root', 'nuredv');
mysql_select_db('apl');
$sql1="select dksd.kunden_stat_nr as pg, sum(if(`auss_typ`=4, ((`Auss-St�ck`+`St�ck`)*`VZ-SOLL`), (`St�ck`*`VZ-SOLL`))) as summin FROM `drueck` join dkopf using (teil) join dksd using (kunde) where (month(datum)=(month(now()) and year(datum) = year(now()))) group by pg order by pg;";

$res1 = mysql_query($sql1) or die(mysql_error());

echo "<table id='table1'>";
echo "<tr><th>Monat</th><th>1</th><th>3</th><th>4</th><th>Sonst.</th><th>Summe</th></tr><tr>";
$monat = date("M");
echo "<td>$monat</td>";
while($zaznam1 = mysql_fetch_array($res1)){
echo "<td>".round($zaznam1["summin"])."</td>";
$summ += $zaznam1["summin"];
 }
 echo "<td>".round($summ)."</td></tr></table>";
}

function lastDayProgress(){
  dbConnect();
  
  $sql="select dksd.kunden_stat_nr as pg, drueck.datum, sum(if(`auss_typ`=4, ((`Auss-St�ck`+`St�ck`)*`VZ-SOLL`), (`St�ck`*`VZ-SOLL`))) as summin FROM `drueck` join dkopf using (teil) join dksd using (kunde) where (month(datum)=month(now()) and year(datum) = year(now())) group by pg, drueck.datum order by drueck.datum desc, pg desc;";
  $res=mysql_query($sql) or die("Last day progres: ". mysql_error());
  echo "<table id='table2'>";
  echo "<tr><th>Datum</th><th>PG1 - Guss</th><th>PG3 - ALU</th><th>PG4 - NE</th><th>Sonst:</th><th>Summe</th></tr>";
  

  $summ = 0;
  $guss = 0;
  $alu = 0;
  $ne = 0;
  $sonst = 0;
  $i = 0;
    while($zaznam = mysql_fetch_array($res)){
      if($i==9){break;}
      
            $datum = substr($zaznam["datum"],0,10);
      if($zaznam["pg"] == 1){
        $guss = round($zaznam["summin"]);
        $summ = $guss + $alu + $ne + $sonst;              
        echo "<tr><td>$datum</td><td>$guss</td><td>$alu</td><td>$ne</td><td>$sonst</td><td>$summ</td></tr>";          
        $summ = 0; $guss = 0; $alu = 0; $ne = 0; $sonst = 0; $i++;}
      
      elseif($zaznam["pg"] == 3){$alu = round($zaznam["summin"]);}
      elseif($zaznam["pg"] == 4){$ne = round($zaznam["summin"]);}
      elseif($zaznam["pg"] == 9){$sonst = round($zaznam["summin"]);}
               
    }
  echo "</table>";
}

function monatProgresGraf(){
dbConnect();
  
  $sql="select dksd.kunden_stat_nr as pg, drueck.datum, sum(if(`auss_typ`=4, ((`Auss-St�ck`+`St�ck`)*`VZ-SOLL`), (`St�ck`*`VZ-SOLL`))) as summin FROM `drueck` join dkopf using (teil) join dksd using (kunde) where (month(datum)=month(now()) and year(datum) = year(now())) group by pg, drueck.datum order by drueck.datum desc, pg desc;";
  $res=mysql_query($sql);
  
  $height = 150;
  $width = 800;
  $im = ImageCreate($width,$height);

  $white = imagecolorallocate($im, 255, 255, 255);
  $black = imagecolorallocate($im, 0, 0, 0);
  
 imagefill($im, 0, 0, $black);
  imageline($im, 0, 0, $width, $height, $white);
  imagestring($im,54, 50, 150, "sales", $white);
  Header("content-type: image/png");
  imagepng($im);


}

function tableExists($table_name)
         {
         $Table = mysql_query("show tables like '" . 
                  $table_name . "'");
         
         if(mysql_fetch_row($Table) === false)
            return(false);
         
         return(true);
         }
    
// funkce vrati pole sloupcu obsazenych ve vysledku dotazu     
function getFieldsArray($result)
{
	$pocetSloupcu = mysql_num_fields($result);
		for($cisloSloupce=0;$cisloSloupce<$pocetSloupcu;$cisloSloupce++)
			$nodes[$cisloSloupce]=mysql_field_name($result,$cisloSloupce);
	return $nodes;
}

function getLagerGutAuftragPalette($auftrag,$pal,$lager)
{
	$in = 0;
	$out = 0;
	
	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(gut_stk) as nach from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_nach='$lager'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$in = $row['nach'];
	}
	
	// pocek kusu ze skladu odebranych
	$sql = "select sum(gut_stk) as ven from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_von='$lager'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$out = $row['ven'];
	}
	
	return $in-$out;
}

function getTeilFromAuftragPal($import,$pal)
{
	$dil = "";
	$sql = "select teil from dauftr where ((auftragsnr='$import') and (`pos-pal-nr`='$pal')) limit 1";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$dil = $row['teil'];
	}

	return $dil;
}

function getLagerAussAuftragPalette($auftrag,$pal,$lager)
{
	$in = 0;
	$out = 0;
	
	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(auss_stk) as nach from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_nach='$lager'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$in = $row['nach'];
	}
	
	// pocek kusu ze skladu odebranych
	$sql = "select sum(auss_stk) as ven from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_von='$lager'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$out = $row['ven'];
	}
	
	return $in-$out;
}

function getLagerGutIn($auftrag,$pal,$lager)
{
	$in = 0;
	
	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(gut_stk) as nach from dlagerbew where ((auftrag_import=$auftrag) and (pal_import=$pal) and (lager_nach='$lager'))";
	//echo "sql=$sql";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$in = $row['nach'];
	}
	return $in;
}

function getLagerGesamtOut($auftrag,$pal,$lager)
{
	$in = 0;
	
	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(gut_stk+auss_stk) as ven from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_von='$lager'))";
	//echo "sql=$sql<br>";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$in = $row['ven'];
	}
	return $in;
}

function getAussFromDrueckAuftragPalTyp($auftrag,$pal,$ausstyp)
{
	$aussCount = 0;
	mysql_query('set names utf8');
	$sql="select sum(`Auss-Stück`) as auss from drueck where ((auftragsnr='$auftrag') and (`pos-pal-nr`='$pal') and (auss_typ='$ausstyp'))";
	//echo "sql=$sql<br>";
	$res=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		$aussCount = $row['auss'];
	}
	
	return $aussCount;
}

function moveAussLagerFromA2B($import,$dil,$pal,$auss,$von,$nach)
{
	$ident = get_user_pc();
	
	$sql = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
	$sql.= "values ('$dil','$import','$pal',0,'$auss','$von','$nach','$ident')";
	mysql_query($sql);
}

function insertLagerVonNach($dil,$import,$pal,$gut,$auss,$von,$nach,$ident)
{
	$sql = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
	$sql.= "values ('$dil','$import','$pal','$gut','$auss','$von','$nach','$ident')";
	mysql_query($sql);
}


// vrati pocet kusu z inventury pro dany dil
function lagerInventurStk($dil,$lager,$datum)
{
	$pocetKusu = 0;
	// budu brat jen platnou inventuru, tj. ktera byla provedena pred dnesnim datumem
	//$dnesniDatum = date("Y-m-d H:i:s");
	
	$sql = "select stk from dlagerstk where((teil='$dil') and (lager='$lager') and (datum_inventur<='$datum'))";
	$res=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		$pocetKusu=$row['stk'];
	}
	if(strlen($pocetKusu)==0) $pocetKusu=0;
	return $pocetKusu;
}

// -------------------------------------------------------------------------------------------------
// vrati datum inventury pro dany dil
// pokud inventura neni vratim 1.1.2099 0:0:0
function getLagerInventurDatum($dil)
{
	$inventuraStamp="2099-01-01 00:00:00";
	$sql = "select DATE_FORMAT(datum_inventur,'%Y-%m-%d %H:%i:%s') as inventurstamp from dlagerstk where ((teil='$dil')) limit 1";
	$res=mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$inventuraStamp=$row['inventurstamp'];
	}
	return $inventuraStamp;
}


// ------------------------------------------------------------------------------------------------
// vrati pocet dobrych kusu vlozenych do daneho skladu v obdobi od do

function getLagerGutPlusTeilDatum($dil,$lager,$stampVon,$stampBis)
{

	$nach = 0;
	
	
	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(gut_stk) as nach from dlagerbew where ((date_stamp between '$stampVon' and '$stampBis') and (lager_nach='$lager') and (teil='$dil'))";
	//echo "sql=$sql";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$nach = $row['nach'];
	}
	if(strlen($nach)==0) $nach=0;
	return $nach;
}

// ------------------------------------------------------------------------------------------------
// vrati pocet dobrych kusu vybranych z daneho skladu v obdobi od do

function getLagerGutMinusTeilDatum($dil,$lager,$stampVon,$stampBis)
{

	$nach = 0;
	
	
	// zjistim pocet kusu do skladu vlozenych
	if($lager=='8X')
		$sql = "select sum(gut_stk) as nach from dlagerbew where ((date_stamp between '$stampVon' and '$stampBis') and (lager_von='$lager') and (teil='$dil'))";
	else
		$sql = "select sum(gut_stk+auss_stk) as nach from dlagerbew where ((date_stamp between '$stampVon' and '$stampBis') and (lager_von='$lager') and (teil='$dil'))";
	//echo "sql=$sql";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$nach = $row['nach'];
	}
	if(strlen($nach)==0) $nach=0;
	return $nach;
}

// ------------------------------------------------------------------------------------------------
// vrati pocet auss vlozenych do daneho skladu v obdobi od do

function getLagerAussPlusTeilDatum($dil,$lager,$stampVon,$stampBis)
{

	$nach = 0;
	
	
	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(auss_stk) as nach from dlagerbew where ((date_stamp between '$stampVon' and '$stampBis') and (lager_nach='$lager') and (teil='$dil'))";
	//echo "sql=$sql";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$nach = $row['nach'];
	}
	if(strlen($nach)==0) $nach=0;
	return $nach;
}

// ------------------------------------------------------------------------------------------------
// vrati pocet auss vybranych daneho skladu v obdobi od do

function getLagerAussMinusTeilDatum($dil,$lager,$stampVon,$stampBis)
{

	$nach = 0;
	
	
	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(auss_stk) as nach from dlagerbew where ((date_stamp between '$stampVon' and '$stampBis') and (lager_von='$lager') and (teil='$dil'))";
	//echo "sql=$sql";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$nach = $row['nach'];
		
	}
	
	if(strlen($nach)==0) $nach=0;
	return $nach;
}

// --------------------------------------------------------------------------------
// vrati asociativni pole se seznamem definovanych lagru
function getLagerArray()
{
	
	$poleSkladu = array();
	
	$sql = "select lager,lagerbeschreibung from dlager order by lager";
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res))
	{
		$poleSkladu[$row['lager']]=$row['lagerbeschreibung'];
	}
	
	return $poleSkladu;
}

function getPreisVorschlagInfo($kunde,$tatnr,$typ)
{
	dbConnect();
	mysql_query('set names utf8');
	
	// zjistim si pro dany $tatnr statnr
	$sql = "select stat_nr from `dtaetkz-abg` where (`abg-nr`='$tatnr')";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$statnr=$row['stat_nr'];
	
	// zkusim info podle cisla operace
	$sql = "select * from dzeitvorschlag where ((kunde='$kunde') and (abgnr='$tatnr') and (typ='$typ'))";	
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		return $row;
	}
	else
	{
		// zkusim jeste info podle stat_nr
		$sql = "select * from dzeitvorschlag where ((kunde='$kunde') and (statnr='$statnr') and (typ='$typ'))";	
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		return $row;
	}
}

// vrati hmotnost dilu, pokud je bruttoflag>0 a dil ma zadanou hrubou vahuvrati hrubou vahu, jinak cistou vahu
function getGewichtLautZeitVorschlag($bruttoflag,$teil)
{
	$gewicht=0;
	dbConnect();
	$sql = "select gew,brgew from dkopf where (teil='$teil')";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		if(($bruttoflag)&&($row['brgew']>0))
			$gewicht=$row['brgew'];
		else
			$gewicht=$row['gew'];
	}
	
	return $gewicht;
}

// minutovou sazbu pro zakaznika
function getMinPreisVomKunde($kunde)
{
	$minpreis=0;
	dbConnect();
	$sql = "select preismin from dksd where (kunde='$kunde')";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$minpreis = $row['preismin'];
	}
	
	return $minpreis;
}

// vynasobi cas danym koeficientem v zavislosti na vaze, definovano v tabulce dgewabhang
function getGewAbhangKorrektur($cas,$gewicht,$kunde,$gewabhang)
{
	$korrzeit=$cas;
	if($gewabhang)
	{
		dbConnect();
		$sql = "select multi from dgewabhang where ((kunde='$kunde') and (gewvon<'$gewicht') and (gewbis>='$gewicht'))";
		$res = mysql_query($sql);
		if(mysql_affected_rows()>0)
		{
			$row = mysql_fetch_array($res);
			$korrzeit = $row['multi']*$cas;
		}
	}
	return $korrzeit;
}

// vrati navrhnutou hodnotu casu podle tabulek dzeitvorschlag a dgewabhang
function getZeitVorschlag($kunde,$teil,$tatnr,$typ)
{
	$zeitVorschlag = 0;
	$vorschlagInfo = getPreisVorschlagInfo($kunde,$tatnr,$typ);
	// pokud mam neco v poli $vorschlagInfopokracuju dal
	if(count($vorschlagInfo)>1)
	{
		$preisProKg = $vorschlagInfo['preisprokg'];
		$gewicht = getGewichtLautZeitVorschlag($vorschlagInfo['brutto'],$teil);
		$minpreis = getMinPreisVomKunde($kunde);
		$zeitVorschlag = round(($preisProKg*$gewicht)/$minpreis,4);
		// a jeste uprava podle zavislosti na hmotnosti
		$zeitVorschlag = getGewAbhangKorrektur($zeitVorschlag,$gewicht,$kunde,$vorschlagInfo['gewabhang']);
	}
	return $zeitVorschlag;
}		

function getTatAktivFromTeilDpos($teil,$tatnummer,$aktiv)
{
	dbConnect();
	mysql_query('set names utf8');
	$sql = "select `taetnr-aby` as taetnr,`taetbez-aby-d` as tatbez_d,`taetbez-aby-t` as tatbez_t,`vz-min-aby` as vzaby,if(`vz-min-aby`<>0,60/`vz-min-aby`,0) as ks_hod";
	if($aktiv)
		$sql.=" from dpos where ((teil='$teil') and (`taetnr-aby`='$tatnummer') and (`kz-druck`<>0))";
	else
		$sql.=" from dpos where ((teil='$teil') and (`taetnr-aby`='$tatnummer'))";
	$sql.=" order by taetnr,sorting,stamp DESC";

	//echo $sql;
	$res=mysql_query($sql);
	
	if(mysql_affected_rows()>0)
	{
		$cisloRadku=0;
		while($radek=mysql_fetch_array($res))
		{
			$radky[$cisloRadku++]=$radek;
			//echo($radek);
		}
		return $radky;		
	}
	return 0;
}

/**
 * vrati rowset z druecku podle zadaneho id
 *
 * @param integer $drueck_id
 * @return rowset nebo 0 kdyz zadanemu id neodpovida zadny radek
 */

function getDrueckRowFromId($drueck_id)
{
	dbConnect();
	mysql_query('set names utf8');
	
	$sql = "select auftragsnr,teil,`pos-pal-nr` as pal,datum,persnr,`verb-von` as von,`verb-bis` as bis,insert_stamp";
	$sql.=" from drueck where (drueck_id='$drueck_id')";
	
	$result = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$rowset = mysql_fetch_array($result);
		return $rowset;
	}
	else
	{
		return 0;
	}
}

/**
 * otestuje zda uz byla faktura s cislem auftrag vyexportovana
 * tj. ulozena do tabulky drechbew
 *
 * @param integer $auftrag
 * @return bool
 */
function drechExportiert($auftrag)
{
	dbConnect();
	mysql_query('set names utf8');
	$sql = "select auftragsnr from drechbew where (auftragsnr='$auftrag')";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
		return 1;
	else
		return 0;
}
/**
 * vrati sadu zaznamu z druecku, ktere patri k sobe, tj. byly zadany jako jesna sada ( nekolik operaci s rozpocitanym spotrebovanym casem
 *
 * @param rowset $drueckWhereRow
 * @return rowset sada zaznamu
 */
function getDrueckSadaRowset($drueckWhereRow)
{
	dbConnect();
	mysql_query('set names utf8');
	
	$auftragsnr=$drueckWhereRow['auftragsnr'];
	$teil=$drueckWhereRow['teil'];
	$pal=$drueckWhereRow['pal'];
	$datum=$drueckWhereRow['datum'];
	$persnr=$drueckWhereRow['persnr'];
	$von=$drueckWhereRow['von'];
	$bis=$drueckWhereRow['bis'];
	$insert_stamp=$drueckWhereRow['insert_stamp'];
	
	$sql = $sql = "select drueck_id,auftragsnr,teil,`pos-pal-nr`as pal,taetnr,`Stück` as stk,`auss-stück` as aussstk,`auss-art` as aart,auss_typ as atyp";
	$sql.= ",`vz-soll` as vzkd,`vz-ist` as vzaby,DATE_FORMAT(datum,'%d.%m.%Y') as datum,persnr,DATE_FORMAT(`verb-von`,'%H:%i') as von,DATE_FORMAT(`verb-bis`,'%H:%i') as bis,`verb-zeit` as verb,`verb-pause` as pause";
	$sql.= ",schicht,oe,`marke-aufteilung` as aufteilung,comp_user_accessuser as user";
	$sql.= " from drueck";
	$sql.= " where (";
	$sql.= " (auftragsnr='$auftragsnr')";
	$sql.= " and (teil='$teil')";
	$sql.= " and (`pos-pal-nr`='$pal')";
	$sql.= " and (datum='$datum')";
	$sql.= " and (persnr='$persnr')";
	$sql.= " and (`verb-von`='$von')";
	$sql.= " and (`verb-bis`='$bis')";
	$sql.= " and (ABS(TIMESTAMPDIFF(SECOND,`insert_stamp`,'$insert_stamp'))<2)";
//	$sql.= " and (`insert_stamp`='$insert_stamp')";
	$sql.= ") order by drueck_id limit 6";
	
	$result = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		while($row = mysql_fetch_array($result))
		{
			$rowArray[$row['drueck_id']]=$row;
		}
		return $rowArray;
	}
	else
	{
		return 0;
	}
}

/**
 * zjisti menu pro fakturu pro par zakazniku vom a an
 *
 * @param unknown_type $vom
 * @param unknown_type $an
 */
function umrechnungGetWahr($vom,$an)
{
	dbConnect();
	mysql_query('set names utf8');
	$sql = "select wahr from dkndumrech where ((vom='$vom') and (an='$an'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		return $row['wahr'];
	}
	else
		return "NODEF";
}

/**
 * zjisti cenu za minutu u puvodni faktury , zjistuje ze vzdalene tabulky
 * 
 */
function umrechnungGetMinpreisOriginal($host,$user,$pass,$db,$rechnung)
{
	$link = dbConnectRemote($host,$user,$pass,$db);
	$sql = "select minpreis from daufkopf where (auftragsnr='$rechnung')";
	$res = mysql_query($sql,$link);
	if(mysql_affected_rows($link)>0)
	{
		$row = mysql_fetch_array($res);
		return $row['minpreis'];
	}
	else
		return 0;
		
	mysql_close($link);
}

/**
 * zjisti minutovou sazbu pro dany par vom a an
 *
 * @param unknown_type $vom
 * @param unknown_type $an
 */
function umrechnungGetMinpreisNeu($vom,$an)
{
	dbConnect();
	$sql = "select minpreis from dkndumrech where ((vom='$vom') and (an='$an'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		return $row['minpreis'];
	}
	else
		return 0;
}

/**
 * vrati cislo posledni faktury pro dany par vom a an
 *
 * @param unknown_type $vom
 * @param unknown_type $an
 * @return unknown
 */
function umrechnungGetLetzteRechnung($vom,$an)
{
	dbConnect();
	$sql = "select letzterechnung from dkndumrech where ((vom='$vom') and (an='$an'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		return $row['letzterechnung'];
	}
	else
		return 0;
}

/**
 * vrati pole radku ze vzdalenetabulky drechbew
 *
 * @param unknown_type $rechnung
 */
function umrechnungGetDrechbewRows($host,$user,$pass,$db,$rechnung)
{
	$link = dbConnectRemote($host,$user,$pass,$db);
	$sql = "select * from drechbew where (auftragsnr='$rechnung')";
	$res = mysql_query($sql,$link);
	mysql_close($link);
	return $res;
}

/**
 * vlozi prepoctena data do tabulky drechneu
 *
 * @param unknown_type $drechbewrow
 * @param unknown_type $vom
 * @param unknown_type $an
 * @param unknown_type $mena
 * @param unknown_type $minpreisOriginal
 * @param unknown_type $minpreisNeu
 * @param unknown_type $letzterechnung
 * @param unknown_type $rechnungsdatum
 * @param unknown_type $lieferdatum
 * @param unknown_type $sonstpreisearray
 */
function insertToDrechNeu($drechbewrow,$vom,$an,$mena,$minpreisOriginal,$minpreisNeu,$letzterechnung,$rechnungsdatum,$lieferdatum,$sonstPreiseArray)
{
	dbConnect();
	mysql_query('set names utf8');
	
	$auftragsnr=$letzterechnung+1;
	$teil=$drechbewrow['Teil'];
	$stk=$drechbewrow['Stück'];
	$auss=$drechbewrow['Ausschuss'];
	$tatkz=$drechbewrow['Taet-kz'];
	$preis=round($drechbewrow['DM']/$minpreisOriginal*$minpreisNeu,4);
	
	//TODO: vyjimka pro fracht,zoll,sonst
	if($drechbewrow['Taet-kz']=='F') $preis = $sonstPreiseArray['F'];
	if($drechbewrow['Taet-kz']=='Z') $preis = $sonstPreiseArray['Z'];
	if($drechbewrow['Taet-kz']=='S') $preis = $sonstPreiseArray['S'];
	
	
	$datum=$rechnungsdatum;
	$text1=$drechbewrow['Text1'];
	$text2=$drechbewrow['Text2'];
	$bestnr=$drechbewrow['Best-Nr'];
	$liefdatum=$lieferdatum;
	$pal=$drechbewrow['pos-pal-nr'];
	$fremdauftr=$drechbewrow['fremdauftr'];
	$fremdpos=$drechbewrow['fremdpos'];
	$waehrung=$mena;
	$origauftrag=$drechbewrow['AuftragsNr'];
    $teilbez=mysql_real_escape_string($drechbewrow['teilbez']);
	$kunde=$drechbewrow['kunde'];
	$abgnr=$drechbewrow['abgnr'];
	
	$sql_insert = "insert into drechneu (";
	$sql_insert.=" AuftragsNr,";
	$sql_insert.=" Teil,";
	$sql_insert.=" `Stück`,";	
	$sql_insert.=" Ausschuss,"; 	
	$sql_insert.=" DM,";
	$sql_insert.=" `DM-Mehr`,"; 	
	$sql_insert.=" Datum,";
	$sql_insert.=" Text1,";
	$sql_insert.=" Text2,";
	$sql_insert.=" `Taet-kz`,";	
	$sql_insert.=" `Best-Nr`,";
	$sql_insert.=" `datum-auslief`,"; 	
	$sql_insert.=" `pos-pal-nr`,";
	$sql_insert.=" fremdauftr,";
	$sql_insert.=" fremdpos,";
	$sql_insert.=" vom,";
	$sql_insert.=" an,";
	$sql_insert.=" waehrung,"; 	
	$sql_insert.=" origauftrag,";
	$sql_insert.=" kunde,";
	$sql_insert.=" abgnr,";
	$sql_insert.=" teilbez)";
	$sql_insert.=" values (";
	$sql_insert.=" '$auftragsnr',";
	$sql_insert.=" '$teil',";
	$sql_insert.=" '$stk',";
	$sql_insert.=" '$auss',";
	$sql_insert.=" '$preis',";
	$sql_insert.=" '0',";
	$sql_insert.=" '$rechnungsdatum',";
	$sql_insert.=" '$text1',";
	$sql_insert.=" '$text2',";
	$sql_insert.=" '$tatkz',";
	$sql_insert.=" '$bestnr',";
	$sql_insert.=" '$liefdatum',";
	$sql_insert.=" '$pal',";
	$sql_insert.=" '$fremdauftr',";
	$sql_insert.=" '$fremdpos',";
	$sql_insert.=" '$vom',";
	$sql_insert.=" '$an',";
	$sql_insert.=" '$waehrung',";
	$sql_insert.=" '$origauftrag',";
	$sql_insert.=" '$kunde',";
	$sql_insert.=" '$abgnr',";
	$sql_insert.=" '$teilbez' )";
	mysql_query($sql_insert);	
}

/**
 * vrati ceny pro fracht,zoll,sonst pro zadanou dvojici vom a an
 *
 * @param unknown_type $vom
 * @param unknown_type $an
 * @param unknown_type $an
 */
function umrechnungGetFrachtZollSonst($vom,$an)
{
	dbConnect();
	$sql = "select fracht,zoll,sonst from dkndumrech where ((vom='$vom') and (an='$an'))";
	$res = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		$row=mysql_fetch_array($res);
		$s['F']=$row['fracht'];
		$s['Z']=$row['zoll'];
		$s['S']=$row['sonst'];
		return $s;
	}
	else
		return 0;
}

/**
 * zazalohuje fakturu do tabulky drechdeleted
 *
 * @param unknown_type $rechnung cislo faktury k zazalohovani
 * @param unknown_type $mazac uzivatel ktery chce fakturu zalohovat
 */
function backupRechnung($rechnung,$mazac){
	dbConnect();
	mysql_query('set names utf8');
	
	$sqlSelect = "select * from drech where (auftragsnr='$rechnung')";
	$resSelect = mysql_query($sqlSelect);
	if(mysql_affected_rows()>0){
		while($row = mysql_fetch_array($resSelect)){
			$sqlInsert = "insert into drechdeleted ";
			$sqlInsert.= "(AuftragsNr,";
			$sqlInsert.= "Teil,";
			$sqlInsert.= "`Stück`,";
			$sqlInsert.= "Ausschuss,";
			$sqlInsert.= "DM,";
			$sqlInsert.= "`DM-Mehr`,";
			$sqlInsert.= "Datum,";
			$sqlInsert.= "Text1,";
			$sqlInsert.= "Text2,";
			$sqlInsert.= "`Taet-kz`,";
			$sqlInsert.= "`Best-Nr`,";
			$sqlInsert.= "`datum-auslief`,";
			$sqlInsert.= "`pos-pal-nr`,";
			$sqlInsert.= "fremdauftr,";
			$sqlInsert.= "fremdpos,";
			$sqlInsert.= "teilbez,";
			$sqlInsert.= "kunde,";
			$sqlInsert.= "vom,";
			$sqlInsert.= "an,";
			$sqlInsert.= "waehrung,";
			$sqlInsert.= "origauftrag,";
			$sqlInsert.= "deluser)";
			$sqlInsert.= " values(";
			$sqlInsert.= "'".$row['AuftragsNr']."',";
			$sqlInsert.= "'".$row['Teil']."',";
			$sqlInsert.= "'".$row['Stück']."',";
			$sqlInsert.= "'".$row['Ausschuss']."',";
			$sqlInsert.= "'".$row['DM']."',";
			$sqlInsert.= "'".$row['DM-Mehr']."',";
			$sqlInsert.= "'".$row['Datum']."',";
			$sqlInsert.= "'".$row['Text1']."',";
			$sqlInsert.= "'".$row['Text2']."',";
			$sqlInsert.= "'".$row['Taet-kz']."',";
			$sqlInsert.= "'".$row['Best-Nr']."',";
			$sqlInsert.= "'".$row['datum-auslief']."',";
			$sqlInsert.= "'".$row['pos-pal-nr']."',";
			
			if(strlen($row['fremdauftr'])==0)
				$sqlInsert.="null,";
			else
				$sqlInsert.= "'".$row['fremdauftr']."',";
				
			if(strlen($row['fremdpos'])==0)
				$sqlInsert.="null,";
			else
				$sqlInsert.= "'".$row['fremdpos']."',";
				
			$sqlInsert.= "'".$row['teilbez']."',";
			$sqlInsert.= "'".$row['kunde']."',";
			$sqlInsert.= "'".$row['vom']."',";
			$sqlInsert.= "'".$row['an']."',";
			$sqlInsert.= "'".$row['waehrung']."',";
			
			if(strlen($row['origauftrag'])==0)
				$sqlInsert.="null,";
			else
				$sqlInsert.= "'".$row['origauftrag']."',";
				
			$sqlInsert.= "'"."$mazac')";
			mysql_query($sqlInsert);
			$chyby=mysql_error();
		}
	}
	return $chyby;
}

/**
 * vraci pole obsahujici datumy faktury a ausliefer
 *
 * @param <type> $rechnung cislo faktury
 * @return array
 * datum['fertig']
 * datum['ausliefer_datum']
 */
function getRechnungDatums($rechnung){
    dbConnect();
    $sql = "select daufkopf.fertig,daufkopf.ausliefer_datum from daufkopf where daufkopf.auftragsnr='$rechnung'";
    $res = mysql_query($sql);
    if(mysql_affected_rows()>0){
        $row = mysql_fetch_array($res);
        $datumy['fertig']  = $row['fertig'];
        $datumy['ausliefer_datum'] = $row['ausliefer_datum'];
    }
    else{
        $datumy['fertig']  =  "";
        $datumy['ausliefer_datum'] =  "";
    }

    return $datumy;
}
/**
 * smaze fakturu
 *
 * @param unknown_type $auftrag cislo faktury, kterou mam smazat
 */
function deleteRechnung($auftrag)
{
	dbConnect();
	$sqlDelete = "delete from drech where (auftragsnr='$auftrag')";
	mysql_query($sqlDelete);
	$smazano = mysql_affected_rows();
	$sqlUpdate = "update daufkopf set fertig='2100-01-01',ma_rechnr=0 where auftragsnr='$auftrag' limit 1";
	mysql_query($sqlUpdate);
	return $smazano;

}

/**
 * vrati jmeno zakaznika podle jeho cisla
 *
 * @param unknown_type $kunde
 */
function getKndName($kunde){
	dbConnect();
	mysql_query('set names utf8');
	$sql = "select name1,name2 from dksd where (kunde='$kunde')";
	$rs = mysql_query($sql);
	$row = mysql_fetch_array($rs);
	return $row['name1']." ".$row['name2'];
}

/**
 * zvysi cislo posledni prepoctene faktury u dvojice zakazniku o jednicku
 *
 * @param unknown_type $vom
 * @param unknown_type $an
 */
function incrementRechnungNummer($vom,$an){
	dbConnect();
	$sql = "update dkndumrech set letzterechnung = letzterechnung+1,letzterechnung_sonst=letzterechnung_sonst+1 where ((vom='$vom') and (an='$an'))";
	mysql_query($sql);
}

/**
 *
 * @param array $arrayToAdd
 * @param array $arrayFromAdd 
 */
function makeSumZapati(&$arrayToAdd,$arrayFromAdd){
        foreach ($arrayToAdd as $key => $prvek) {
	    if(array_key_exists($key, $arrayFromAdd))
		$hodnota = $arrayFromAdd[$key];
	    else
		$hodnota = 0;
	    $arrayToAdd[$key]+=$hodnota;
	}
}
/**
 * zkontroluje, zda podle zadanych udaju povolit pristup do databaze
 * 
 *
 * @param unknown_type $user
 * @param unknown_type $pass
 * @param unknown_type $ip
 */
function grantAccess($user,$pass,$ip){
	$sql_select="select name,password,realname,level from dbenutzer where ((name='$user') and (password='$pass'))";
		$res = mysql_query($sql_select) or die(mysql_error());
		if(mysql_affected_rows()>0){
			// nasel jsem jmeno v databazi uzivatelu
			$row=mysql_fetch_array($res);
			$pole['realname']=$row['realname'];
			$pole['name']=$row['name'];
			$pole['level']=$row['level'];
			// jeste zkontroluju ip adresu
			// vytahnu si seznam povolenych adres z databaze
			$sql = "select ip from ipaccess";
			$res = mysql_query($sql);
			$i=0;
			while($row=mysql_fetch_array($res)){
				// vemu jen cast adresy k prvni hvezdicce
				$delka = strpos($row['ip'],"*");
				if($delka>0)
					$poleIPAdres[$i] = substr($row['ip'],0,$delka);
				else
					$poleIPAdres[$i] = $row['ip'];
					
				//echo "poleipadres[$i]=".$poleIPAdres[$i]."<br>";
				$i++;
			}
			$ip = str_replace("_",".",$ip);
			//echo "ip = $ip<br>";
			// zkontrolu zda mi adresa padne do pole se vzorkama
			foreach($poleIPAdres as $vzorek){
				$vysledek = strstr($ip,$vzorek);
				//echo "vysledek = $vysledek<br>";
				if(strlen($vysledek)>0) break;
			}
			if(strlen($vysledek)>0){
				// kontrola ip probehl uspesne
				$pole['loginok']=1;
				
			}
			else{
				// ip naprosla kontrolou
				$pole['loginok']=0;
			}
		}
		else
		{
			$pole['loginok']=0;
		}
		
		return $pole;
}
?>
