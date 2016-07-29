<?php

/**
 * apl main utility class 
 */
class AplDB {

    private static $instance = null;
    private $user = 'root';
    private $pass = 'nuredv';
    private $dbName = 'apl';
    private $dbHost = 'localhost';
//    private $dbHost = '172.16.1.111';
    private $con = null;

    const TABLE_USERROLES = 'dbenutzerroles';
    const TABLE_ROLES = 'roles';
    const TABLE_RESOURCES = 'resources';
    const TABLE_PRIVILEGES = 'privileges';
    const TABLE_DPERS = 'dpers';
    const TABLE_DPERSDETAIL = 'dpersdetail1';
    const TABLE_URLAUB = 'durlaub1';
    const TABLE_DPERSSTEMPEL = 'dpersstempel';
    const TABLE_VORSCHUSS = 'dvorschuss';
    const TABLE_DZEIT = 'dzeit';
    const TABLE_DESSEN = 'dessen';
    const TABLE_TRANSPORT = 'dperstransport';
    const TABLE_KFZ = 'dkfz';
    const TABLE_ROUTE = 'dtransportroute';
    const TABLE_TATTYPEN = 'dtattypen';
    const TABLE_DPERSPREMIE = 'dperspremie';
    const TABLE_PREMIETYPEN = 'dpremietypen';
    const TABLE_DPERSABMAHNUNG = 'dabmahnung';
    const TABLE_ABMAHNUNGTYPEN = 'dabmahnpplan';
    const TABLE_DZEITSOLL = 'dzeitsoll';
    const TABLE_DKOPF = 'dkopf';
    const TABLE_DOG = 'dog';
    const DOKUNR_MUSTER = 12; // soll 12

    static $ATT2FOLDERARRAY = array(
	"muster"=>"010",
	"empb"=>"020",
	"ppa"=>"030",
	"gpa"=>"040",
	"vpa"=>"050",
	"qanf"=>"060",
	"zeit"=>"070",
	"liefer"=>"080",
	"mehr"=>"090",
	"rekl"=>"100",
    );
    
    static $DIRS_FOR_TEIL_FINAL = array(
	"010"=>"010 Muster",
	"020"=>"020 EMPB",
	"030"=>"030 PPA",
	"040"=>"040 GPA",
	"050"=>"050 VPA",
	"060"=>"060 Q-Anforderungen",
	"070"=>"070 Zeitwirtschaft",
	"080"=>"080 Lieferbedingung",
	"090"=>"090 Mehrarbeit",
	"100"=>"100 Reklamation",
    );    

    /**
     * 
     * @return string
     */
    public function getGdatPath(){
	$gdatPath = "/mnt/gdat/Dat/";
	return $gdatPath;
    }
    
    public function getDat99Path(){
	return "Aby 99 Nezarazene";
    }
    
    public function getArbMittelAnlagenPath(){
	return "Aby 20 Technik, Produktivitat/Arbeitsmittel - Messmittel";
    }
    
    public function getArbMittelAnlagenFullPath(){
	return $this->getGdatPath().$this->getArbMittelAnlagenPath();
    }
    
    
    /**
     * get an instance of then utility class
     * @return AplDB
     */
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __clone() {
        trigger_error('Clone not allowed', E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Deserialisation is not allowed', E_USER_ERROR);
    }

    public function __construct() {
        $this->con = mysql_connect($this->dbHost, $this->user, $this->pass)
                or die("nemuzu se pripojit k databazi " . mysql_error());
        mysql_query('set names utf8');
        mysql_select_db($this->dbName, $this->con)
                or die('Nemuzu zvolit databazi ' . mysql_error());
    }

    
// ------------------------------------------------------------------------
// public methods

    /**
     * vrati pole osobnich cisel s nastupem od zadaneho datumu
     * @param string $dbEintrittVom - datum ve formatu YYYY-MM-DD
     * @param boolean $ohneAustritt TRUE - jen aktivni MA bez ukonceni
     * @return array - pole osobnich cisel
     */
    public function getPersnrFromEintritt($dbEintrittVom, $ohneAustritt = TRUE) {
        if ($ohneAustritt === TRUE)
            $sql = "select persnr from dpers where eintritt>='$dbEintrittVom' and (austritt is null or austritt<eintritt) order by persnr";
        else
            $sql = "select persnr from dpers where eintritt>='$dbEintrittVom' order by persnr";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $persnrArray = array();
            while ($row = mysql_fetch_assoc($res)) {
                array_push($persnrArray, $row['persnr']);
            }
            return $persnrArray;
        }
        else
            return NULL;
    }


    /**
     * 
     * @param type $t
     * @return type
     */
    public static function toDBDate($t) {
	$d = date('Y-m-d', strtotime($t));
	if ($d == "1970-01-01") {
	    return NULL;
	} else {
	    return $d;
	}
    }

    /**
     * 
     * @param type $retez
     * @param type $delka
     * @param type $append
     */
    public static function aplSubstr($retez,$delka,$append='...'){
	$retStr = $retez;
	$len = strlen($retez);
	if($len>$delka){
	    $retStr = substr($retez, 0, $delka).$append;
	}
	return $retStr;
    }
    
    /**
     * 
     * @param array $rolesArray - pole s id roli jejichz clenove s emailovou adresou budou vybrani do seznamu
     * @param array $emailsArray - pole emailovych adres, ktere budou pridany do seznamu
     * @param array $noSendEmailsArray - pole emalovych adres, ktere budou nakonec ze seznamu odebrany
     */
    public function getRecipientsArray($rolesArray, $emailsArray = NULL, $noSendEmailsArray = NULL) {
	$recipients = array();
	if($emailsArray!==NULL){
	    if(is_array($emailsArray)){
		foreach ($emailsArray as $e){
		    array_push($recipients, $e);
		}
	    }
	}
	$rolesIdArray = array(2, 3, 16);
	foreach ($rolesIdArray as $roleId) {
	    $usersarray = $this->getUsersForRoleId($roleId);
	    if ($usersarray !== NULL) {
		foreach ($usersarray as $userrow) {
		    $userinfo = $this->getUserInfoArray($userrow['benutzername']);
		    if ($userinfo !== NULL) {
			$email = trim($userinfo['email']);
			if (strlen($email) > 0) {
			    array_push($recipients, $email);
			}
		    }
		}
	    }
	}
	// TODO odstranit vicenasobne emailove adr.
	$recipients = array_unique($recipients);
	//odebrat nechtene emailove adresy
	if($noSendEmailsArray!==NULL){
	    if(is_array($noSendEmailsArray)){
		foreach ($noSendEmailsArray as $rr) {
		    if (($key = array_search($rr, $recipients)) !== false) {
			unset($recipients[$key]);
		    }
		}
	    }
	}
	
	return $recipients;
    }

    /**
     * vynasobi cas danym koeficientem v zavislosti na vaze, definovano v tabulce dgewabhang
     * @param type $cas
     * @param type $gewicht
     * @param type $kunde
     * @param type $gewabhang
     * @return type
     */
public function getGewAbhangKorrektur($cas,$gewicht,$kunde,$gewabhang)
{
	$korrzeit=$cas;
	if($gewabhang)
	{
		$sql = "select multi from dgewabhang where ((kunde='$kunde') and (gewvon<'$gewicht') and (gewbis>='$gewicht'))";
		$rows = $this->getQueryRows($sql);
		if($rows!==NULL)
		{
			$row = $rows[0];
			$korrzeit = $row['multi']*$cas;
		}
	}
	return $korrzeit;
}

public function getMinPreisVomKunde($kunde)
{
	$minpreis=0;
	$sql = "select preismin from dksd where (kunde='$kunde')";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL)
	{
		$row = $rows[0];
		$minpreis = $row['preismin'];
	}
	
	return $minpreis;
}

public function getGewichtLautZeitVorschlag($bruttoflag,$teil)
{
	$gewicht=0;
	$sql = "select gew,brgew from dkopf where (teil='$teil')";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL)
	{
		$row = $rows[0];
		if(($bruttoflag)&&($row['brgew']>0))
			$gewicht=$row['brgew'];
		else
			$gewicht=$row['gew'];
	}
	
	return $gewicht;
}

public function getPreisVorschlagInfo($kunde,$tatnr,$typ)
{
	// zjistim si pro dany $tatnr statnr
	$sql = "select stat_nr from `dtaetkz-abg` where (`abg-nr`='$tatnr')";
	$rows  = $this->getQueryRows($sql);
	$statnr=$rows[0]['stat_nr'];
	
	// zkusim info podle cisla operace
	$sql = "select * from dzeitvorschlag where ((kunde='$kunde') and (abgnr='$tatnr') and (typ='$typ'))";	
	$rows = $this->getQueryRows($sql);
	
	if($rows!==NULL)
	{
		return $rows[0];
	}
	else
	{
		// zkusim jeste info podle stat_nr
		$sql = "select * from dzeitvorschlag where ((kunde='$kunde') and (statnr='$statnr') and (typ='$typ'))";
		$rows = $this->getQueryRows($sql);
		return $rows[0];
	}
}

/**
 * vrati navrhnutou hodnotu casu podle tabulek dzeitvorschlag a dgewabhang
 * @param type $kunde
 * @param type $teil
 * @param type $tatnr
 * @param type $typ
 * @return type
 */
public function getZeitVorschlag($kunde,$teil,$tatnr,$typ)
{
	$zeitVorschlag = 0;
	$vorschlagInfo = $this->getPreisVorschlagInfo($kunde,$tatnr,$typ);
	// pokud mam neco v poli $vorschlagInfopokracuju dal
	if(count($vorschlagInfo)>1)
	{
		$preisProKg = $vorschlagInfo['preisprokg'];
		$gewicht = $this->getGewichtLautZeitVorschlag($vorschlagInfo['brutto'],$teil);
		$minpreis = $this->getMinPreisVomKunde($kunde);
		$zeitVorschlag = round(($preisProKg*$gewicht)/$minpreis,4);
		// a jeste uprava podle zavislosti na hmotnosti
		$zeitVorschlag = $this->getGewAbhangKorrektur($zeitVorschlag,$gewicht,$kunde,$vorschlagInfo['gewabhang']);
	}
	return $zeitVorschlag;
}		
    
    /**
     * 
     * @param type $reportname
     * @param type $password
     * @param type $user
     * @param type $usePassword
     * @return boolean
     */
    public function testReportPassword($reportname,$password,$user,$usePassword=0)
{
	//dbConnect();
        if($usePassword!=0)
            $sql = "select user from reportsecurity where((reportname='$reportname') and (user='$user') and (password='$password'))";
        else
            $sql = "select user from reportsecurity where((reportname='$reportname') and (user='$user'))";

//	echo $sql;
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
    /**
     * vrati cislo zakaznika podle zadaneho dilu
     * 
     * @param string $teil
     * @return int cislo zakaznika, pokud dil nenajde vrati 0
     */
    public function getKundeFromTeil($teil) {
        $sql = "select kunde from dkopf where teil='$teil'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['kunde'];
        }
        else
            return 0;
    }

    public function insert($sql){
	mysql_query($sql);
	return mysql_insert_id();
    }
    /**
     * wrapper pro mysql_query
     * @param string $sql 
     */
    public function query($sql){
	mysql_query($sql);
	return mysql_affected_rows();
    }
    
    public function insertToSecurityLog($user,$action){
	$sql = "insert into securitylog (`user`,`action`) values('$user','$action')";
	$this->query($sql);
    }
    /**
     *  tabulka vykonu pro kazdou produktgruppe zvlast
     *  radky - datumy
     *  sloupce - typy minut
     */
    public function getLeistungTable($daysBack=NULL) {
	if($daysBack===NULL){
	    $sql_leistung = "select DATE_FORMAT(drueck.datum,'%d.%m.%Y') as datum,sum(if(kunden_stat_nr=1,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg1,sum(if(kunden_stat_nr=3,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg3,sum(if(kunden_stat_nr=4,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg4,sum(if(kunden_stat_nr=9,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg9,sum(if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`)) as celkem from drueck join dkopf using (teil) join dksd using (kunde) where (datum between  subdate(current_date(),day(current_date())-1) and CURRENT_DATE()) group by drueck.datum order by drueck.datum desc limit 31";
	}
	else{
	    $sql_leistung = "select DATE_FORMAT(drueck.datum,'%d.%m.%Y') as datum,sum(if(kunden_stat_nr=1,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg1,sum(if(kunden_stat_nr=3,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg3,sum(if(kunden_stat_nr=4,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg4,sum(if(kunden_stat_nr=9,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg9,sum(if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`)) as celkem from drueck join dkopf using (teil) join dksd using (kunde) where (datum between  subdate(current_date(),$daysBack) and CURRENT_DATE()) group by drueck.datum order by drueck.datum desc limit $daysBack";
	}
	
	//echo $sql_leistung;
	mysql_query('set names utf8');

	$res = mysql_query($sql_leistung) or die(mysql_error());
	$i = 0;
	while ($row = mysql_fetch_array($res)) {
	    $datum = $row['datum'];
	    $pole[$i]['datum'] = $datum;
	    $pg1 = $row['pg1'];
	    $pole[$i]['pg1'] = $pg1;
	    $sum_pg1+=$pg1;
	    $pg3 = $row['pg3'];
	    $pole[$i]['pg3'] = $pg3;
	    $sum_pg3+=$pg3;
	    $pg4 = $row['pg4'];
	    $pole[$i]['pg4'] = $pg4;
	    $sum_pg4+=$pg4;
	    $pg9 = $row['pg9'];
	    $pole[$i]['pg9'] = $pg9;
	    $sum_pg9+=$pg9;
	    $celkem = $row['celkem'];
	    $pole[$i]['celkem'] = $celkem;
	    $sum_celkem+=$celkem;
	    $i++;
	}
	return array(
	    'datum'=>$datum,
	    'pole'=>$pole,
	    'sum_pg1'=>$sum_pg1,
	    'sum_pg3'=>$sum_pg3,
	    'sum_pg4'=>$sum_pg4,
	    'sum_pg9'=>$sum_pg9,
	    'sum_celkem'=>$sum_celkem,
	    );
    }

    
    /**
     * 
     * @param type $atyp
     * @param type $year
     * @param type $month
     * @param type $persnr
     * @return int
     */
    public function getGewAussTypYearMonthPersnr($atyp, $year, $month, $persnr) {
	$sql.=" select";
	$sql.=" drueck.PersNr,";
	$sql.=" sum(drueck.`Auss-Stück`*dkopf.Gew) as auss_gew";
	$sql.=" from";
	$sql.=" drueck";
	$sql.=" join dkopf on dkopf.Teil=drueck.Teil";
	$sql.=" where";
	$sql.=" persnr='$persnr'";
	$sql.=" and";
	$sql.=" MONTH(drueck.Datum)='$month'";
	$sql.=" and";
	$sql.=" YEAR(drueck.Datum)='$year'";
	$sql.=" and";
	$sql.=" drueck.auss_typ='$atyp'";
	$sql.=" group by";
	$sql.=" drueck.PersNr";

	$rows = $this->getQueryRows($sql);
	if ($rows !== NULL) {
	    return floatval($rows[0]['auss_gew']);
	} else {
	    return 0;
	}
    }

    /**
     *
     * @param int $persnr
     * @return array  
     */
    public function getDpersDatumZuschlagArray($persnr) {
        $zuschlagArray = array();
        $sql = "select persnr,stat_nr,DATE_FORMAT(datum,'%Y-%m-%d') as datum,zuschlag from dpersdatumzuschlag where persnr='$persnr'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            while ($row = mysql_fetch_assoc($res)) {
                $zuschlagArray[$row['stat_nr']][$row['datum']] = $row['zuschlag'];
            }
            return $zuschlagArray;
        }
        else
            return NULL;
    }

    /**
     * vrati hmotnost dilu
     * @param string $teil
     * @return float hmotnost dilu, pokud dil nenajde vrati 0 
     */
    public function getTeilGewicht($teil) {
        $sql = "select gew from dkopf where teil='$teil'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['gew'];
        }
        else
            return 0;
    }


    /**
     * 
     */
    public function getTatTageBetweenDatums($tat, $von, $bis, $persnr) {
	$sql.=" select";
	$sql.=" dzeit.PersNr as persnr,";
	$sql.=" dzeit.tat,";
	$sql.=" count(dzeit.Datum) as pocet";
	$sql.=" from";
	$sql.=" dzeit";
	$sql.=" where";
	$sql.=" dzeit.persnr='$persnr'";
	$sql.=" and dzeit.datum between '$von' and '$bis'";
	$sql.=" group by";
	$sql.=" dzeit.persnr,";
	$sql.=" dzeit.tat";
	$sql.=" having";
	$sql.=" dzeit.tat='$tat'";
	
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    return intval($rows[0]['pocet']);
	}
	else{
	    return 0;
	}
    }
    

    /**
     *
     * @param int $export
     * @return array or null if $export does not exists
     */
    public function getExDatumSoll($export) {
        $sql = "select auftragsnr as export,DATE_FORMAT(ex_datum_soll,'%d.%m.%Y') as ex_datum_soll,DATE_FORMAT(ex_datum_soll,'%Y-%m-%d %H:%i') as ex_datetime_soll from daufkopf where auftragsnr='$export'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $row['sql'] = $sql;
            return $row;
        } else {
            return NULL;
        }
    }

    /**
     * 
     * @param type $import
     * @return type
     */
    public function getImDatumSoll($import) {
        $sql = "select auftragsnr as import,DATE_FORMAT(im_datum_soll,'%d.%m.%Y') as im_datum_soll,DATE_FORMAT(im_datum_soll,'%Y-%m-%d %H:%i') as im_datetime_soll from daufkopf where auftragsnr='$import'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $row['sql'] = $sql;
            return $row;
        } else {
            return NULL;
        }
    }
    
    public function getTatRechnungBezeichnung($tat){
	$bez='';
	$sql.=" select";
	$sql.=" dtaetkz.Rechtext as rechtext";
	$sql.=" from `dtaetkz-abg`";
	$sql.=" join dtaetkz on dtaetkz.Dtaetkz=`dtaetkz-abg`.dtaetkz";
	$sql.=" where";
	$sql.=" `dtaetkz-abg`.`abg-nr`=$tat";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    $bez = $rows[0]['rechtext'];
	}
	return $bez;
    }
    /**
     *
     * @param string $teil
     * @return array or null if $teil is not found
     */
    public function getVerpackungMenge($teil) {
        $sql = "select teil,verpackungmenge from dkopf where teil='$teil'";
        //return $sql;
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $row['sql'] = $sql;
            return $row;
        } else {
            return NULL;
        }
    }

    /**
     * 
     * @param type $reklId
     */
    public function getVerursacherForReklId($reklId){
	$sql = "select dpersschulung.persnr from dpersschulung where dpersschulung.rekl_id='$reklId' and dpersschulung.rekl_verursacher<>0 order by dpersschulung.persnr";
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $persnr
     * @param type $von
     * @param type $bis
     * @return type
     */
    public function getReklamationenMitVerursacherVonBis($persnr, $von, $bis) {
	$sql = " select";
	$sql.= " dreklamation.id,";
	$sql.= " dreklamation.rekl_nr,";
	$sql.= " dreklamation.teil,";
	$sql.= " dreklamation.beschr_abweichung,";
	$sql.= " dreklamation.beschr_ursache,";
	$sql.= " dreklamation.interne_bewertung";
	$sql.= " from";
	$sql.= " dreklamation";
	$sql.= " join dpersschulung on dpersschulung.rekl_id=dreklamation.id";
	$sql.= " where";
	$sql.= " dreklamation.rekl_datum between '$von' and '$bis'";
	$sql.= " and dpersschulung.persnr='$persnr'";
	$sql.= " and dpersschulung.rekl_verursacher<>0";
	$sql.= " group by";
	$sql.= " dpersschulung.persnr,";
	$sql.= " dreklamation.rekl_nr";
	
	return $this->getQueryRows($sql);
    }

    /**
     * vrati kurs pro prepocet mezi menami, kurs platny pro datum
     * 
     * @param string $datumDB obdobi, kdy me kurs zajima, in format YYYY-MM-DD
     * @param string $waehrungVon mena z ktere prepocitavam
     * @param string $waehrungNach mena na kterou chci prepocitat
     * @return float if kurs is found, 0 otherwise
     */
    public function getKurs($datumDB, $waehrungVon, $waehrungNach) {
        $sql = "select kurs from dkurs where gilt_von<='$datumDB' and gilt_bis>='$datumDB' and waehr_von='$waehrungVon' and  waehr_nach='$waehrungNach' limit 1";
        $res = mysql_query($sql);
        $kurs = 0;
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $kurs = floatval($row['kurs']);
        }
        return $kurs;
    }

    /**
     *
     * @param type $old
     * @param type $neu 
     */
    public function changeTermin($old, $neu, $test = 1) {
	if ($test == 1) {
	    $sql.=" select ";
	    $sql.=" dauftr.id_dauftr,";
	    $sql.=" dauftr.termin";
	    $sql.=" from dauftr";
	    $sql.=" join tmp_dauftr_termin on tmp_dauftr_termin.id_dauftr=dauftr.id_dauftr";
	    $sql.=" where";
	    $sql.=" tmp_dauftr_termin.termin='$old'";
	    $sql.=" and dauftr.`auftragsnr-exp` is null and dauftr.`pal-nr-exp` is null";
	} else {
	    $sql.=" update dauftr";
   	    $sql.=" join tmp_dauftr_termin on tmp_dauftr_termin.id_dauftr=dauftr.id_dauftr";
	    $sql.=" set dauftr.termin='$neu'";
	    $sql.=" where";
	    $sql.=" tmp_dauftr_termin.termin='$old'";
	    $sql.=" and dauftr.`auftragsnr-exp` is null and dauftr.`pal-nr-exp` is null";
	}

	mysql_query($sql);

	return mysql_affected_rows();
    }

    /**
     * 
     * @param type $reklId
     * @param type $vorschlag true = betrag pro vorschlag, false pro skutecne strzene penize
     * @return int
     */
    public function getBetragSumAbmahnungenForReklId($reklId,$vorschlag=TRUE){
	if($vorschlag===TRUE){
	    $sql.=" select";
	$sql.=" sum(dabmahnung.vorschlag_betrag) as betrag";
	$sql.=" from dabmahnung";
	$sql.=" where";
	$sql.=" dabmahnung.dreklamation_id='$reklId'";
	}
	else{
	    $sql = "select sum(dabmahnung.betr) as betrag from dabmahnung where dabmahnung.dreklamation_id='$reklId' and vorschlag=0";
	}
	
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return floatval($r[0]['betrag']);
	}
	else{
	    return 0;
	}
    }
    /**
     * 
     * @param type $datumVon
     * @param type $datumBis
     */
    public function getRundlaufMatch($datumVon,$datumBis,$spediteur){
	if($spediteur=='*'){
	    $sql = "select * from drundlauf where ab_aby_soll_datetime between '$datumVon' and '$datumBis'";
	}
	else{
	    $sql = "select * from drundlauf where ab_aby_soll_datetime between '$datumVon' and '$datumBis' and dspediteur_id='$spediteur'";
	}
	
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $auftrag
     */
    public function isAuftragImRundlauf($auftrag,$ie){
	$sql = "select rundlauf_id from drundlaufimex where auftragsnr='$auftrag' and imex='$ie' limit 1";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return TRUE;
	}
	else{
	    return FALSE;
	}
    }
    
    /**
     * 
     * @param type $form_id
     * @param type $elementId
     * @param type $user
     */
    public function getHelpInfo($form_id, $elementId, $user){
	$sql = "select id,help_text,popis,element_id,form_id from resources where form_id='$form_id' and element_id='$elementId'";
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $id
     */
    public function getPayloadInfo($id){
	$sql.="select * from drundlaufimex where id='$id'";
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $id
     * @param type $rundlaufid
     */
    public function deletePayloadRundlauf($id,$rundlaufid){
	$sql = "delete from drundlaufimex where rundlauf_id='$rundlaufid' and id='$id'";
	mysql_query($sql);
    }
    
    public function getRundlaufIdForExport($auftragsnr){
	$sql = "select drundlaufimex.rundlauf_id from drundlaufimex where auftragsnr='$auftragsnr' and imex='E'";
	$r = $this->getQueryRows($sql);
	if($r===NULL){
	    return NULL;
	}
	else{
	    return $r[0]['rundlauf_id'];
	}
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function getRundlaufImExArray($id){
	$sql.= " select ";
	$sql.= " id,";
	$sql.= " rundlauf_id,";
	$sql.= " auftragsnr,";
	$sql.= " imex";
	$sql.= " from drundlaufimex";
	$sql.= " where drundlaufimex.rundlauf_id='$id'";
	$sql.= " order by imex desc,auftragsnr";
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $rundlaufid
     * @param type $ie
     * @param type $auftragsnr
     */
    public function addRundlaufPayload($rundlaufid,$ie,$auftragsnr){
	// smazu z puvodniho umisteni
	$sql="delete from drundlaufimex where auftragsnr='$auftragsnr' and imex='$ie'";
	$this->query($sql);
	//vlozim novy
	$sql="insert into drundlaufimex (rundlauf_id,auftragsnr,imex) values('$rundlaufid','$auftragsnr','$ie')";
	mysql_query($sql);
	return mysql_insert_id();
    }
    
    /**
     * 
     * @param type $datum
     */
    public function makeNewRundlauf($datum){
	$sql = "insert into drundlauf (ab_aby_soll_datetime,dspediteur_id)";
	$sql.=" values('$datum',1)";
	$this->query($sql);
	return mysql_insert_id();
    }
    /**
     * 
     */
    public function getRundlaufInfoArray($id){
	$sql.=" select";
	$sql.=" DATE_FORMAT(drundlauf.ab_aby_soll_datetime,'%Y-%m-%d') as ab_datum_f,drundlauf.*";
	$sql.=" from drundlauf";
	$sql.=" where drundlauf.id='$id'";
	return $this->getQueryRows($sql);
    }
    
    
    /**
     * 
     */
    public function getSpediteurArray($id=NULL){
	if($id===NULL){
	    $sql.=" select id,`name` from dspediteur order by name";
	}
	else{
	    $sql.=" select id,`name` from dspediteur where id='$id'";
	}
	
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $datumVon
     * @param type $datumBis
     */
    public function getLkwDatumArray($datumVon,$datumBis){
	$sql.=" select ";
	$sql.=" DATE_FORMAT(ab_aby_soll_datetime,'%Y-%m-%d') as ab_aby,";
	$sql.=" DATE_FORMAT(an_aby_soll_datetime,'%Y-%m-%d') as an_aby,";
	$sql.=" if(drundlauf.archiv=1,'archived','') as archived,";
	$sql.=" drundlauf.id,drundlauf.dspediteur_id,drundlauf.lkw_kz";
	$sql.=" from drundlauf";
	$sql.=" where ";
	$sql.=" drundlauf.ab_aby_soll_datetime between '$datumVon 00:00:00' and '$datumBis 23:59:59'";
//	$sql.=" or";
//	$sql.=" drundlauf.an_aby_soll_datetime between '$datumVon 00:00:00' and '$datumBis 23:59:59'";
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $persnr
     * @param type $von
     * @param type $bis
     */
    public function getPersLeistungArray($persnr, $von, $bis) {
	$sql.=" select";
	$sql.=" drueck.PersNr,";
	$sql.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
	$sql.=" sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_akkord";
	$sql.=" from";
	$sql.=" drueck";
	$sql.=" join dtattypen on dtattypen.tat=drueck.oe";
	$sql.=" where";
	$sql.=" drueck.PersNr='$persnr'";
	$sql.=" and drueck.Datum between '$von' and '$bis'";
	$sql.=" group by PersNr";
	
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    return $rows[0];
	}
	else{
	    return NULL;
	}
    }

    /**
     * 
     * @param type $kundeVon
     * @param type $kundeBis
     * @param type $datumVon
     * @param type $datumBis
     */
    public function getImporteDatumKunde($kundeVon,$kundeBis,$datumVon,$datumBis){
	$sql.=" select ";
	$sql.=" DATE_FORMAT(daufkopf.Aufdat,'%Y-%m-%d') as import_datum,";
	$sql.=" DATE_FORMAT(daufkopf.im_datum_soll,'%Y-%m-%d') as im_soll_datum,";
	$sql.=" DATE_FORMAT(daufkopf.im_datum_soll,'%H:%i') as im_soll_time,";
	$sql.=" daufkopf.kunde,";
	$sql.=" daufkopf.auftragsnr as import,";
	$sql.=" daufkopf.bestellnr";
	$sql.=" ,if(daufkopf.ausliefer_datum is null,'noex',DATE_FORMAT(daufkopf.ausliefer_datum,'%Y%m%d')) as ausliefer_datum";
	$sql.=" ,if(daufkopf.fertig='2100-01-01','norech',DATE_FORMAT(daufkopf.fertig,'%Y%m%d')) as fertig";
	$sql.=" from daufkopf";
	$sql.=" where";
	$sql.=" (DATE_FORMAT(daufkopf.Aufdat,'%Y-%m-%d') between '$datumVon' and '$datumBis')";
	$sql.=" and";
	$sql.=" (daufkopf.kunde between $kundeVon and $kundeBis)";
	$sql.=" order by";
	$sql.=" DATE_FORMAT(daufkopf.Aufdat,'%Y-%m-%d'),";
	$sql.=" daufkopf.kunde,";
	$sql.=" daufkopf.auftragsnr";
	return $this->getQueryRows($sql);
    }

    public function checkUserPIN($login,$pin){
	$salt = "abydosgates";
	$pin = md5($pin);
	$rows = $this->getQueryRows("select id_benutzer from dbenutzer where name='$login' and pin='$pin'");
	if($rows===NULL){
	    return FALSE;
	}
	return TRUE;
    }
    /**
     * 
     * @param type $ex
     */
    public function getRestVzkdForEx($ex){
	$sql.=" select";
	$sql.=" sum(dauftr.`stück`*dauftr.VzKd) as vzkd_gepl";
	$sql.=" from dauftr";
	$sql.=" where";
	$sql.=" (dauftr.termin='P$ex') and (dauftr.`auftragsnr-exp` is null) and (dauftr.`pal-nr-exp` is null)";
	$r = $this->getQueryRows($sql);
	$vzkdSoll = 0;
	if($r!==NULL){
	    $vzkdSoll = intval($r[0]['vzkd_gepl']);
	}
	
	$sql=" select";
	$sql.=" sum(if(auss_typ=4,(drueck.`Stück`+`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`)) as sumvzkd_fertig";
	$sql.=" from dauftr";
	$sql.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.Teil=dauftr.teil and drueck.TaetNr=dauftr.abgnr";
	$sql.=" where";
	$sql.=" (dauftr.termin='P$ex') and (dauftr.`auftragsnr-exp` is null) and (dauftr.`pal-nr-exp` is null)";
	
	$r = $this->getQueryRows($sql);
	$vzkdFertig = 0;
	if($r!==NULL){
	    $vzkdFertig = intval($r[0]['sumvzkd_fertig']);
	}

	return $vzkdSoll-$vzkdFertig;
    }

    /**
     * 
     * @param type $ohneRechnung
     */
    public function getExporteMatch($e, $ohneRechnung = TRUE) {
	
	$sql.=" select ";
	$sql.=" daufkopf.kunde,";
	$sql.=" daufkopf.auftragsnr as ex,";
	$sql.=" if(daufkopf.ex_datum_soll is not null,DATE_FORMAT(daufkopf.ex_datum_soll,'%d.%m.%Y %H:%i'),'') as ex_datum_soll,";
	$sql.=" if(daufkopf.ausliefer_datum is not null,DATE_FORMAT(daufkopf.ausliefer_datum,'%d.%m.%Y %H:%i'),'') as ausliefer_datum,";
	$sql.=" if(daufkopf.Aufdat is not null,DATE_FORMAT(daufkopf.Aufdat,'%d.%m.%Y'),'') as aufdat,";
	$sql.=" if(daufkopf.fertig is not null,DATE_FORMAT(daufkopf.fertig,'%d.%m.%Y'),'') as fertig";
	$sql.=" from daufkopf";
	$sql.=" where";
	$sql.=" (1)";
	if($ohneRechnung===TRUE){
	    $sql.=" and (daufkopf.fertig='2100-01-01')";
	}
	$sql.=" and";
	$sql.=" (daufkopf.auftragsnr like '$e%')";
	$sql.=" order by";
	$sql.=" daufkopf.kunde,";
	$sql.=" daufkopf.auftragsnr";
	return $this->getQueryRows($sql);
    }

    
public function getDMAInfo($imanr){
    $sql = "select * from dma where imanr='$imanr'";
    return $this->getQueryRows($sql);
}


/**
 * 
 * @global type $apl
 * @param type $imanr
 * @param type $didTyp
 * @return type
 */
function getDauftrIdArrayForIMA($imanr, $didTyp) {
    
    $dmaA = $this->getDMAInfo($imanr);

    if ($dmaA !== NULL) {
	$dma = $dmaA[0];

//	echo "<h1>" . $dma['imanr'] . "</h1>";
	//AplDB::varDump($dma);
	// pro kazdy import z auftragsnrarray, zkusim najit id_dauftr
	$teil = $dma['teil'];
	$imaAntrag_AuftragArray = split(';', $dma['auftragsnrarray']);
	$imaAntrag_PalArray = split(';', $dma['palarray']);
	$imaAntrag_DIdArray = split(';', $dma['ima_dauftrid_array']);

	$emaAntrag_AuftragArray = split(';', $dma['ema_auftragsarray']);
	$emaAntrag_PalArray = split(';', $dma['ema_palarray']);
	$emaAntrag_DIdArray = split(';', $dma['ema_dauftrid_array']);

	$imaGenehmigt_AuftragArray = split(';', $dma['ima_auftragsnrarray_genehmigt']);
	$imaGenehmigt_PalArray = split(';', $dma['ima_palarray_genehmigt']);
	$imaGenehmigt_DIdArray = split(';', $dma['ima_dauftrid_array_genehmigt']);

	$emaGenehmigt_AuftragArray = split(';', $dma['ema_auftragsarray_genehmigt']);
	$emaGenehmigt_PalArray = split(';', $dma['ema_palarray_genehmigt']);
	$emaGenehmigt_DIdArray = split(';', $dma['ema_dauftrid_array_genehmigt']);


	$importPalArray = array(
	    'imaAntrag' => array(
		'imArray' => $imaAntrag_AuftragArray,
		'palArray' => $imaAntrag_PalArray,
		'DIdArray' => $imaAntrag_DIdArray,
	    ),
	    'emaAntrag' => array(
		'imArray' => $emaAntrag_AuftragArray,
		'palArray' => $emaAntrag_PalArray,
		'DIdArray' => $emaAntrag_DIdArray,
	    ),
	    'imaGenehmigt' => array(
		'imArray' => $imaGenehmigt_AuftragArray,
		'palArray' => $imaGenehmigt_PalArray,
		'DIdArray' => $imaGenehmigt_DIdArray,
	    ),
	    'emaGenehmigt' => array(
		'imArray' => $emaGenehmigt_AuftragArray,
		'palArray' => $emaGenehmigt_PalArray,
		'DIdArray' => $emaGenehmigt_DIdArray,
	    ),
	);

	$imPalA = $importPalArray[$didTyp];
	$typ = $didTyp;

//	echo "<h3>" . $typ . "</h3>";
	$DIdArray = array();
	if (is_array($imPalA['imArray'])) {
	    foreach ($imPalA['imArray'] as $imaAntrag_Auftrag) {
		// kazdej auftrag zkusim zkombinovat s kazdou paletou a najit odpovidajici id_dauftr v kombinaci s teil a kzgut='G'
		if (is_array($imPalA['palArray'])) {
		    foreach ($imPalA['palArray'] as $imaAntrag_Pal) {
			$sql = "select dauftr.id_dauftr from dauftr where auftragsnr='$imaAntrag_Auftrag' and `pos-pal-nr`='$imaAntrag_Pal' and teil='$teil' and KzGut='G'";
			$dauftrRows = $this->getQueryRows($sql);
			$id_dauftr = 0;
			if ($dauftrRows !== NULL) {
			    $id_dauftr = $dauftrRows[0]['id_dauftr'];
			}
			if ($id_dauftr > 0) {
//			    echo "$imaAntrag_Auftrag - $imaAntrag_Pal ($teil) - $id_dauftr<br>";
			    array_push($DIdArray, $id_dauftr);
			}
		    }
		}
//		echo "<hr>";
	    }
	}
	sort($DIdArray);
//	echo "DIdArray (read):" . join(',', $DIdArray) . "<br>";
	sort($imPalA['DIdArray']);
//	echo "DIdArray (saved) :" . join(',', $imPalA['DIdArray']);
    }
    return array('did_read'=>$DIdArray,'did_saved'=>$imPalA['DIdArray']);
}

    /**
     * 
     * @param type $kundeVon
     * @param type $kundeBis
     * @param type $datumVon
     * @param type $datumBis
     * @return type
     */
    public function getExporteDatumKunde($kundeVon,$kundeBis,$datumVon,$datumBis){
	$sql.=" select ";
	$sql.=" DATE_FORMAT(daufkopf.ex_datum_soll,'%Y-%m-%d') as export_datum,";
	$sql.=" DATE_FORMAT(daufkopf.ex_datum_soll,'%H:%i') as export_time,";
	$sql.=" daufkopf.kunde,";
	$sql.=" daufkopf.auftragsnr as export,";
	$sql.=" if(daufkopf.ausliefer_datum is null,'noex',DATE_FORMAT(daufkopf.ausliefer_datum,'%Y%m%d')) as ausliefer_datum,";
	$sql.=" if(daufkopf.fertig='2100-01-01','norech',DATE_FORMAT(daufkopf.fertig,'%Y%m%d')) as fertig";
	$sql.=" ,if(zielorte.zielort is null,'',zielorte.zielort) as zielort";
	$sql.=" from daufkopf";
	$sql.=" left join zielorte on zielorte.id=daufkopf.zielort_id";
	$sql.=" where";
	$sql.=" (DATE_FORMAT(daufkopf.ex_datum_soll,'%Y-%m-%d') between '$datumVon' and '$datumBis')";
	$sql.=" and";
	$sql.=" (daufkopf.kunde between $kundeVon and $kundeBis)";
	$sql.=" order by";
	$sql.=" DATE_FORMAT(daufkopf.ex_datum_soll,'%Y-%m-%d'),";
	$sql.=" daufkopf.kunde,";
	$sql.=" daufkopf.auftragsnr";
	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $teil
     * @param type $tat
     * @param type $vzaby
     * @param type $vzkd
     * @return type
     */
    public function insertNewDPOS($teil,$tat,$vzaby,$vzkd){
	// vytahnout texty
	$sql = "select `dtaetkz-abg`.oper_CZ,`dtaetkz-abg`.oper_D from `dtaetkz-abg` where `abg-nr`=$tat";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    $textCZ = $r[0]['oper_CZ'];
	    $textDE = $r[0]['oper_D'];
	    $sql = "insert into dpos (Teil,`TaetNr-Aby`,`TaetBez-Aby-D`,`TaetBez-Aby-T`,`VZ-min-kunde`,`vz-min-aby`,`kz-druck`)";
	    $sql.=" values('$teil','$tat','$textDE','$textCZ','$vzkd','$vzaby',0)";
	    mysql_query($sql);
	    return mysql_insert_id();
	}
	return -1;
    }
    /**
     *
     * @param type $kunde 
     * @return number of affected rows
     * 
     */
    public function saveDauftrTermine($kunde){
	// smazu predchozi obsah
	mysql_query('delete from tmp_dauftr_termin');
	// vlozim novy
	$sql.= " insert into tmp_dauftr_termin";
	$sql.= " select ";
	$sql.= " dauftr.id_dauftr,";
	$sql.= " dauftr.termin";
	$sql.= " from dauftr";
	$sql.= " join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
	$sql.= " where";
	$sql.= " daufkopf.kunde='$kunde'";
	$sql.= " and (dauftr.`auftragsnr-exp` is null and dauftr.`pal-nr-exp` is null)";
	$sql.= " and (termin is not null and length(termin)>0)";
	$res=mysql_query($sql);
	return mysql_affected_rows();
    }
    
    /**
     *
     * oznaci v tabulce drech radky faktury jako mehrarbeit
     * @param type $rechnr_regular cislo faktury ve ktere chci oznacit radky s ma
     * @param type $rechnr_ma cislo faktury, ktera bua znamenat faktutu s mehrarbeit
     * @param type $abgnr_von tatnr od pro mehrarbeit
     * @param type $abgnr_bis tatnr do pro mehrarbeit
     * 
     * @return int pocet zmenenych radku 
     */
    public function markMARechnung($rechnr_regular, $rechnr_ma, $abgnr_von, $abgnr_bis) {
        $sql = "update drech set rechnr_druck='$rechnr_ma' where auftragsnr='$rechnr_regular' and abgnr between '$abgnr_von' and '$abgnr_bis'";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     * zapise cislo ma faktury v hlavicce daufkopf
     * @param type $export cislo zakazky, kde chci oznacit ma rechnung
     * @param type $ma_rechnr cislo me zakazky
     * 
     */
    public function updateMARechnr($export, $ma_rechnr) {
        $sql = "update daufkopf set ma_rechnr='$ma_rechnr' where auftragsnr='$export'";
        mysql_query($sql);
    }

    /**
     * gives ma_rechnr for given import
     * @param int $auftragsnr
     * @return int ma rechnr 
     */
    public function getMARechNr($auftragsnr) {
        $ma_rechnr = 0;
        $sql = "select daufkopf.ma_rechnr from daufkopf where auftragsnr='$auftragsnr' limit 1";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $ma_rechnr = $row['ma_rechnr'];
        }
        return $ma_rechnr;
    }

    /**
     * test if auftragsnr has maRechnung = wurde geteilt
     * @param <type> $auftragsnr
     * @return <type>
     */
    public function hatMARechnung($auftragsnr) {
        $maR = $this->getMARechNr($auftragsnr);
        //echo "maR=$maR";
        if (($maR != $auftragsnr) && ($maR != 0))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * pokud je ip adresa uvedena v tabulce ipbenutzer_access musi souhlasit i zadane prihlasovaci jmeno,
     * tj. tabulka funguje funguje jako restriktivni pro danou ip, pokud ip v tabulce neni uvedena, muze se
     * z dane ip prihlasit kdokoliv
     * 
     * aby byl pristu povolen
     * @param char $ip
     * @param char $user
     */
    public function getIpBenutzerAccess($ip,$user){
	$sql = "select ip from ip_pcname where ip='$ip'";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL){
	    // pristup je povolen, protoze ip adresa neni uvedena v tabulce
	    return 1;
	}
	$sql = "select name from ipbenutzer_access join ip_pcname on ip_pcname.id=ipbenutzer_access.pc_id where (ip_pcname.ip='$ip') and (name='$user')";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL)
	    return 0;
	else
	    return 1;
    }
    
    /**
 * zkontroluje, zda podle zadanych udaju povolit pristup do databaze
 * 
 *
 * @param unknown_type $user
 * @param unknown_type $pass
 * @param unknown_type $ip
 */
public function grantAccess($user,$pass,$ip){
	$sql_select="select name,password,realname,level from dbenutzer where ((name='$user') and (password='$pass') and (level>0))";
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
				// kontrola, zda se dany uzivatel muze ze zadane ip adresy prihlasit
				$pole['loginok']=1;
				$pole['loginok']=$this->getIpBenutzerAccess($ip,$user);
				
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


    /**
     * 
     * @param type $rechnung
     * @param type $mazac
     * @return type
     */
    public function backupRechnung($rechnung,$mazac){
	$sqlSelect = "select * from drech where (auftragsnr='$rechnung')";
	$rows = $this->getQueryRows($sqlSelect);
//	$resSelect = mysql_query($sqlSelect);
	if($rows!==NULL){
		foreach($rows as $row){
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
			$this->query($sqlInsert);
		}
	}
	return $chyby;
}

/**
 * 
 * @param type $auftragsnr
 */
    public function ImStkSpeichern($auftragsnr){
	$sql = "update dauftr set im_stk=`stück` where auftragsnr='$auftragsnr'";
	$this->query($sql);
	$sql = "update daufkopf set im_stk_gespeichert=1 where auftragsnr='$auftragsnr'";
	$this->query($sql);
    }
    
    /**
     *
     * @return type 
     */
    public function get_pc_ip() {
	$ip = $_SERVER["REMOTE_ADDR"];
	$ip = strtr($ip, ".", "_");
	return $ip;
    }

    /**
     * 
     * @param type $report
     * @param type $reporturl
     * @param type $userpc
     */
    public function reportUsageLog($report,$reporturl,$userpc){
	$sql = "insert into reportusagelog";
	$sql.=" (report,reporturl,user)";
	$sql.=" values('$report','$reporturl','$userpc')";
	mysql_query($sql);
	return mysql_insert_id();
    }
	    
    /**
     *
     * @param type $username
     * @param type $password
     * @param type $prihlasen
     * @param type $host 
     */
public function insertAccessLog($username,$password,$prihlasen,$host)
{
	$sql = "insert into accesslog (name,password,login_ok,host) values ('$username','$password','$prihlasen','$host')";
	mysql_query('set names utf8');
	mysql_query($sql);
}

    /**
     *
     * @param type $kunde
     * @param type $abgnr
     * @param type $teil
     * @return array
     */
    public function getAbgnrArrayForKundeAbgnr($kunde, $abgnr, $teil = '%') {
        if ($teil == '%') {
            $sql = "";
            $sql.=" select ";
            $sql.=" sum(dkopf.jahr_bedarf_stk_2011*dpos.`VZ-min-kunde`*dksd.preismin) as korr_2011_preis,";
            $sql.=" sum(dkopf.jahr_bedarf_stk_2012*dpos.`VZ-min-kunde`*dksd.preismin) as korr_2012_preis";
            $sql.=" from";
            $sql.=" dkopf";
            $sql.=" join";
            $sql.=" dpos on dpos.Teil=dkopf.Teil";
            $sql.=" join";
            $sql.=" dksd on dksd.Kunde=dkopf.Kunde";
            $sql.=" where";
            $sql.=" dkopf.Kunde=$kunde";
            $sql.=" and dpos.`TaetNr-Aby`=$abgnr";
        } else {
            $sql = "";
            $sql.=" select ";
            $sql.=" sum(dkopf.jahr_bedarf_stk_2011*dpos.`VZ-min-kunde`*dksd.preismin) as korr_2011_preis,";
            $sql.=" sum(dkopf.jahr_bedarf_stk_2012*dpos.`VZ-min-kunde`*dksd.preismin) as korr_2012_preis";
            $sql.=" from";
            $sql.=" dkopf";
            $sql.=" join";
            $sql.=" dpos on dpos.Teil=dkopf.Teil";
            $sql.=" join";
            $sql.=" dksd on dksd.Kunde=dkopf.Kunde";
            $sql.=" where";
            $sql.=" dkopf.Kunde=$kunde";
            $sql.=" and dpos.`TaetNr-Aby`=$abgnr";
            $sql.=" and dkopf.teil like '$teil'";
        }

        return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $import
     * @param type $tat
     */
    public function getDrueckGutStkForImportAbgnr($import,$teil,$tat){
	$stk = 0;
	$sql.=" select sum(drueck.`Stück`) as gstk";
	$sql.=" from drueck";
	$sql.=" where";
	$sql.=" auftragsnr='$import'";
	$sql.=" and";
	$sql.=" TaetNr='$tat'";
	$sql.=" and";
	$sql.=" teil='$teil'";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    $stk = intval($rows[0]['gstk']);
	}
	return $stk;
    }
    
    public function getDrechGutStkForImportAbgnr($import,$teil,$tat){
	$stk = 0;
	$rechnr = '';
	
	$sql.=" select ";
	$sql.=" AuftragsNr as rechnr,";
	$sql.=" sum(drech.`Stück`) as gstk";
	$sql.=" from drech";
	$sql.=" where";
	$sql.=" origauftrag='$import'";
	$sql.=" and";
	$sql.=" abgnr='$tat'";
	$sql.=" and";
	$sql.=" teil='$teil'";
	$sql.=" group by AuftragsNr";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    foreach ($rows as $row){
		$stk+= intval($row['gstk']);
		$rechnr.= $row['rechnr'].",";
	    }
	}
	//odstranit carku na konci
	if(strlen($rechnr)>0){
	    $rechnr = substr($rechnr, 0,strlen($rechnr)-1);
	}
	return array('gstk'=>$stk,'rechnr'=>$rechnr);
    }
    
    /**
     * 
     * @param type $persnr
     * @param type $von
     * @param type $bis
     * @return type
     */
    public function getLeistungArrayPersVonBis($persnr, $von, $bis) {
	$sql.=" select";
	$sql.=" drueck.PersNr as persnr,";
	$sql.=" dpers.leistfaktor,";
	$sql.=" daufkopf.kunde,";
	$sql.=" drueck.TaetNr as abgnr,";
	$sql.=" `dtaetkz-abg`.`Name` as abgnr_name,";
	$sql.=" sum(if(auss_typ=4,(`Stück`+`Auss-Stück`)*`VZ-IST`,(`Stück`)*`VZ-IST`)) as sum_vzaby,";
	$sql.=" sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_akkord";
	$sql.=" from drueck";
	$sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
	$sql.=" join dpers on dpers.PersNr=drueck.PersNr";
	$sql.=" join dtattypen on dtattypen.tat=drueck.oe";
	$sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
	$sql.=" where";
	$sql.=" drueck.persnr='$persnr'";
	$sql.=" and datum between '$von' and '$bis'";
	$sql.=" group by";
	$sql.=" drueck.PersNr,";
	$sql.=" daufkopf.kunde,";
	$sql.=" drueck.TaetNr";

	return $this->getQueryRows($sql);
    }

    /**
     * pro uvodni stranku apl
     * @return array
     */
    public function getAbyInfoMinuten(){
	$sql.=" select ";
	$sql.=" DATE_FORMAT(drueck.datum,'%Y-%m-%d') as datum,";
	$sql.=" sum(if(kunden_stat_nr=1,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg1_vzkd,";
	$sql.=" sum(if(kunden_stat_nr=3,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg3_vzkd,";
	$sql.=" sum(if(kunden_stat_nr=4,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg4_vzkd,";
	$sql.=" sum(if(kunden_stat_nr=9,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg9_vzkd,";
	$sql.=" sum(if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`)) as celkem_vzkd, ";
	$sql.=" sum(if(kunden_stat_nr=1,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,`stück`*`vz-ist`),0)) as pg1_vzaby,";
	$sql.=" sum(if(kunden_stat_nr=3,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,`stück`*`vz-ist`),0)) as pg3_vzaby,";
	$sql.=" sum(if(kunden_stat_nr=4,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,`stück`*`vz-ist`),0)) as pg4_vzaby,";
	$sql.=" sum(if(kunden_stat_nr=9,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,`stück`*`vz-ist`),0)) as pg9_vzaby,";
	$sql.=" sum(if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,`stück`*`vz-ist`)) as celkem_vzaby, ";
	$sql.=" sum(if(kunden_stat_nr=1,`Verb-Zeit`,0)) as pg1_verb,";
	$sql.=" sum(if(kunden_stat_nr=3,`Verb-Zeit`,0)) as pg3_verb,";
	$sql.=" sum(if(kunden_stat_nr=4,`Verb-Zeit`,0)) as pg4_verb,";
	$sql.=" sum(if(kunden_stat_nr=9,`Verb-Zeit`,0)) as pg9_verb,";
	$sql.=" sum(`Verb-Zeit`) as celkem_verb";
	$sql.=" from drueck ";
	$sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
	$sql.=" join dksd on dksd.Kunde=daufkopf.kunde";
	$sql.=" where (datum between  subdate(current_date(),day(current_date())-1) and CURRENT_DATE()) group by drueck.datum order by drueck.datum desc limit 30";
	return $this->getQueryRows($sql);
    }
    
    
    /**
     * 
     * @param type $import
     * @param type $pal1
     * @param type $abgnr
     */
    public function getVZFromDauftr($import,$pal1,$abgnr){
	$sql="select dauftr.VzKd as vzkd,dauftr.VzAby as vzaby from dauftr where dauftr.auftragsnr='$import' and dauftr.`pos-pal-nr`='$pal1' and dauftr.abgnr='$abgnr'";
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $import
     * @param type $impal
     */
    public function getRMArray($import,$impal){
	$sql.=" select";
	$sql.=" drueck.AuftragsNr as import,";
	$sql.=" drueck.Teil as teil,";
	$sql.=" drueck.`pos-pal-nr` as pal,";
	$sql.=" drueck.TaetNr as abgnr,";
	$sql.=" drueck.`auss-art` as aussart,";
	$sql.=" drueck.`auss_typ`,";
	$sql.=" 1 as gutFlag,";
	$sql.=" 0 as aussFlag,";
	$sql.=" sum(drueck.`Stück`) as gutstk,";
	$sql.=" sum(drueck.`Auss-Stück`) as aussstk";
	$sql.=" from drueck";
	$sql.=" where";
	$sql.=" (drueck.AuftragsNr='$import')";
	$sql.=" and (drueck.`pos-pal-nr`='$impal')";
	$sql.=" group by";
	$sql.=" drueck.AuftragsNr,";
	$sql.=" drueck.Teil,";
	$sql.=" drueck.`pos-pal-nr`,";
	$sql.=" drueck.TaetNr,";
	$sql.=" drueck.`auss-art`";
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $persnr
     * @param type $datum
     */
    public function getVorschussArray($persnr,$datum){
	if(($persnr===NULL) && ($datum===NULL)){
	    return array();
	}
	if($datum===NULL){
	    $sql = "select CONCAT(dpers.name,' ',dpers.vorname) as name,id_vorschuss,dvorschuss.persnr,DATE_FORMAT(datum,'%d.%m.%Y') as datumF,vorschuss from dvorschuss join dpers on dpers.persnr=dvorschuss.persnr where (dvorschuss.persnr='$persnr') order by datum desc";
	}
	else if($persnr===NULL){
	    $sql = "select CONCAT(dpers.name,' ',dpers.vorname) as name,id_vorschuss,dvorschuss.persnr,DATE_FORMAT(datum,'%d.%m.%Y') as datumF,vorschuss from dvorschuss join dpers on dpers.persnr=dvorschuss.persnr  where (datum='$datum') order by dvorschuss.persnr asc";
	}
	else{
	    $sql = "select CONCAT(dpers.name,' ',dpers.vorname) as name,id_vorschuss,dvorschuss.persnr,DATE_FORMAT(datum,'%d.%m.%Y') as datumF,vorschuss from dvorschuss  join dpers on dpers.persnr=dvorschuss.persnr where (datum='$datum') and (dvorschuss.persnr='$persnr')";
	}
	
	$rows = $this->getQueryRows($sql);
	if($rows===NULL){
	    return array();
	}
	else{
	    return $rows;
	}
    }
    
    /**
     * 
     * @param type $kunde
     * @param type $bereich
     * @param type $value
     * @param type $yearMonth (RR-MM)
     */
    public function getBewertungKriterium($kunde,$bereich,$value,$vonbis,$yearMonth,$interval=NULL){
	// $yearMonth = 15-01
	$year = 2000+intval(substr($yearMonth, 0, 2));
	$month = substr($yearMonth,3,2);
	$day = 1;
	$dateTime = date('Y-m-d',strtotime("$year-$month-$day"));
	$sql="";
	$bewertung = 0;
	
	if($vonbis=='bis'){
	    $sql.=" select";
	    $sql.=" bewertung_kriteria.bewertung,";
	    $sql.=" bewertung_kriteria.grenze";
	    $sql.=" from bewertung_kriteria";
	    $sql.=" where";
	    $sql.=" grenze>='$value'";
	    $sql.=" and";
	    $sql.=" bereich='$bereich'";
	    $sql.=" and";
	    $sql.=" kunde='$kunde'";
	    $sql.=" and";
	    $sql.=" gilt_ab<='$dateTime'";
	    $sql.=" and";
	    $sql.=" interval_monate='$interval'";
	    $sql.=" order by";
	    $sql.=" gilt_ab desc,";
	    $sql.=" grenze asc";
	    $rows = $this->getQueryRows($sql);
	    if($rows!==NULL){
		$bewertung = $rows[0]['bewertung'];
	    }
	    else{
		$bewertung = 6;
	    }
	}
	
	return $bewertung;
    }
    
    /**
     * 
     * @param type $kunde
     * @param type $bereich
     * @param type $value
     * @param type $vonbis
     * @param type $yearMonth
     * @param type $interval
     * @return int
     */
    public function getBewertungKriteriumArray($kunde,$bereich,$value,$vonbis,$yearMonth,$interval=NULL,$oe=NULL){
	// $yearMonth = 15-01
	$year = 2000+intval(substr($yearMonth, 0, 2));
	$month = substr($yearMonth,3,2);
	$day = 1;
	$dateTime = date('Y-m-d',strtotime("$year-$month-$day"));
	$sql="";
	$bewertung = 0;
	
	if($vonbis=='bis'){
	    $sql.=" select";
	    $sql.=" bewertung_kriteria.bewertung,";
	    $sql.=" bewertung_kriteria.grenze";
	    $sql.=" ,bewertung_kriteria.betrag";
	    $sql.=" ,oe";
	    $sql.=" from bewertung_kriteria";
	    $sql.=" where";
	    $sql.=" grenze>='$value'";
	    $sql.=" and";
	    $sql.=" bereich='$bereich'";
	    $sql.=" and";
	    $sql.=" kunde='$kunde'";
	    $sql.=" and";
	    $sql.=" gilt_ab<='$dateTime'";
	    $sql.=" and";
	    $sql.=" interval_monate='$interval'";
	    $sql.=" order by";
	    $sql.=" gilt_ab desc,";
	    $sql.=" grenze asc";
	    $rows = $this->getQueryRows($sql);
	    if($rows!==NULL){
		$bewertung = $rows[0];
	    }
	    else{
		$bewertung = NULL;
	    }
	}
	
	if(strstr($vonbis, 'von')){
	    $sql.=" select";
	    $sql.=" bewertung_kriteria.bewertung,";
	    $sql.=" bewertung_kriteria.grenze";
	    $sql.=" ,bewertung_kriteria.betrag";
	    $sql.=" ,oe";
	    $sql.=" from bewertung_kriteria";
	    $sql.=" where";
	    $sql.=" grenze<='$value'";
	    $sql.=" and";
	    $sql.=" bereich='$bereich'";
	    $sql.=" and";
	    $sql.=" kunde='$kunde'";
	    $sql.=" and";
	    $sql.=" gilt_ab<='$dateTime'";
	    $sql.=" and";
	    $sql.=" interval_monate='$interval'";
	    $sql.=" order by";
	    $sql.=" gilt_ab desc,";
	    $sql.=" grenze desc";
	    $rows = $this->getQueryRows($sql);
	    if($rows!==NULL){
		$bewertung = $rows[0];
	    }
	    else{
		$bewertung = NULL;
	    }
	}
	
	//test zavislosti na OE pomoci regex
	if($bewertung!==NULL){
	    $pattern = "/".$bewertung['oe']."/i";
	    $subject = $oe;
	    if(preg_match_all($pattern, $subject,$out)){
		
	    }
	    else{
		$bewertung = NULL;
	    }
	}
	
	return $bewertung;
    }

    /**
     * 
     * @param type $kunde
     */
    public function getKundeBAAnteilStk($kunde){
	$stk = 'kg'; // vychozi budou vzdy kg
	$sql = "select dksd.S370_ba_anteil_stk as ba from dksd where kunde='$kunde'";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    $stk = $rows[0]['ba'];
	}
	return $stk;
    }
    /**
     * 
     * @param type $kunde
     * @param type $bereich
     * @param type $yearMonth
     * @return type
     */
    public function getBewertungKriteriumInfo($kunde,$bereich,$yearMonth){
	// $yearMonth = 15-01
	$year = 2000+intval(substr($yearMonth, 0, 2));
	$month = substr($yearMonth,3,2);
	$day = 1;
	$dateTime = date('Y-m-d',strtotime("$year-$month-$day"));
	$sql="";
	
	$sql.=" select";
	$sql.=" kunde,";
	$sql.=" bereich,";
	$sql.=" grenze,";
	$sql.=" bis_von,";
	$sql.=" interval_monate,";
	$sql.=" bewertung";
	$sql.=" from bewertung_kriteria";
	$sql.=" where";
	$sql.=" kunde='$kunde'";
	$sql.=" and";
	$sql.=" bereich='$bereich'";
	$sql.=" and";
	$sql.=" gilt_ab<='$dateTime'";
	$sql.=" order by";
	$sql.=" gilt_ab desc,";
	$sql.=" interval_monate asc,";
	$sql.=" grenze asc";
	
	return $this->getQueryRows($sql);
	//return $sql;
    }
    
    /**
 * vraci pole obsahujici datumy faktury a ausliefer
 *
 * @param <type> $rechnung cislo faktury
 * @return array
 * datum['fertig']
 * datum['ausliefer_datum']
 */
public function getRechnungDatums($rechnung) {
	$sql = "select daufkopf.fertig,daufkopf.ausliefer_datum from daufkopf where daufkopf.auftragsnr='$rechnung'";
	$rows = $this->getQueryRows($sql);
	if ($rows !== NULL) {
	    $row = $rows[0];
	    $datumy['fertig'] = $row['fertig'];
	    $datumy['ausliefer_datum'] = $row['ausliefer_datum'];
	} else {
	    $datumy['fertig'] = "";
	    $datumy['ausliefer_datum'] = "";
	}

	return $datumy;
    }

    /**
     * vrati posledni cislo MA faktury ulozene u zakaznika
     * 
     * @param type $kunde 
     */
    public function getLetzteMARechNrKunde($kunde){
	$ma_rechnr = 0;

        $sql = "select letzte_ma_rechnr from dksd where kunde='$kunde'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $ma_rechnr = $row['letzte_ma_rechnr'];
        }
        return $ma_rechnr;
    }
    
    /**
     * get the last ma_rechnr from daufkopf
     * 
     * @param type $auftragsnr
     * @return type 
     */
    public function getLetzteMARechNr($auftragsnr) {
        $kunde = $this->getKundeFromAuftransnr($auftragsnr);
        //echo $kunde;
        $ma_rechnr = 0;

        $sql = "select max(daufkopf.ma_rechnr) as letzte_ma_rechnr from daufkopf where kunde='$kunde' limit 1";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $ma_rechnr = $row['letzte_ma_rechnr'];
        }
        return $ma_rechnr;
    }

    /**
     * 
     * @return type
     */
    public function getAnlagenTypen(){
	$sql.=" select * from anlagentypen order by beschreibung";
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @return type 
     */
    public function getSchulungenArray() {
        $sql = "select dschulung.beschreibung from dschulung order by dschulung.beschreibung";
        return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $im
     * @param type $pal
     */
    public function getGiesstagImportPalette($im,$pal){
	$sql = "select giesstag from dauftr where auftragsnr='$im' and `pos-pal-nr`='$pal' and kzgut='G'";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    return trim($rows[0]['giesstag']);
	}
	else{
	    return '';
	}
    }
    
    /**
     * 
     * @param type $place_id
     * @return type
     */
    public function getInfoPanelsForPlaceId($place_id){
    $sql.=" select ";
    $sql.=" dinfotable.id,";
    $sql.=" dinfotable.text1,";
    $sql.=" dinfotable.text2,";
    $sql.=" dinfotable.text3,";
    $sql.=" dinfotable.text4,";
    $sql.=" dinfotable.text5";
    $sql.=" from dinfotable";
    $sql.=" join dinfopanel on dinfopanel.dinfotable_id=dinfotable.id";
    $sql.=" join dinfopanelplaces on dinfopanelplaces.id=dinfopanel.place_id";
    $sql.=" where";
    $sql.=" dinfopanelplaces.id=$place_id";
    $sql.=" order by";
    //CAST(field_name as SIGNED INTEGER)
    $sql.=" CAST(dinfotable.text1 as SIGNED INTEGER),";
    $sql.=" dinfotable.id";
    
    return $this->getQueryRows($sql);
    }
    
    public function getInfoPanelPlaces(){
	$sql = "select dinfopanelplaces.id,dinfopanelplaces.place from dinfopanelplaces order by place";
        return $this->getQueryRows($sql);
    }
    
    /**
     *
     * @param type $datumDB 
     */
    public function getPersnrRowsFromDrueckDatum($datumDB,$ohneStornoRows=TRUE){
        $sql = "";
        $sql.=" select distinct persnr";
        $sql.=" from drueck";
        $sql.=" where";
        $sql.=" datum='$datumDB'";
        $sql.=" order by persnr";
        return $this->getQueryRows($sql);
    }
    /**
     *
     * @param type $datumDB
     * @param type $persnr 
     */
    public function getLeistungAnwesenheitRows($datumDB,$persnr){
        $sql = "";
        $sql.= " select";
        $sql.= " PersNr,";
        $sql.= " DATE_FORMAT(Datum,'%Y-%m-%d') as datum,";
        $sql.= " DATE_FORMAT(`verb-von`,'%H:%i') as von,";
        $sql.= " DATE_FORMAT(`verb-bis`,'%H:%i') as bis,";
        $sql.= " oe,";
        $sql.= " sum(`Verb-Zeit`) as sumverb,";
        $sql.= " sum(`verb-pause`) as sumpause";
        $sql.= " from drueck";
        $sql.= " where ";
        $sql.= " datum='$datumDB'";
        $sql.= " and PersNr='$persnr'";
        $sql.= " and DATE_FORMAT(`verb-von`,'%H:%i')<>'00:00'";
        $sql.= " and DATE_FORMAT(`verb-bis`,'%H:%i')<>'00:00'";
        $sql.= " group by";
        $sql.= " PersNr,";
        $sql.= " oe,";
        $sql.= " DATE_FORMAT(Datum,'%Y-%m-%d'),";
        $sql.= " DATE_FORMAT(`verb-von`,'%H:%i'),";
        $sql.= " DATE_FORMAT(`verb-bis`,'%H:%i')";
        $sql.= " having sumverb<>0";
        $sql.= " order by PersNr,`verb-von`";
        return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $import
     * @param type $impal
     */
public function istExportiert($import, $impal){
    $isex = FALSE;
    $sql.="select dauftr.`auftragsnr-exp` as ex from dauftr where auftragsnr='$import' and `pos-pal-nr`='$impal' and kzgut='G'";
    $rows = $this->getQueryRows($sql);
    if($rows!==NULL){
	$r = $rows[0];
	$ex = $r['ex'];
	if(strlen(trim($ex))>0) $isex = TRUE;
    }
    return $isex;
}
    /**
     *
     * @return <type>
     */
    public function getQualifikationsTypenArray($statnr=NULL) {
        if($statnr===NULL)
            $sql = "select dfaehigkeittyp.beschreibung as typ from dfaehigkeittyp order by stat_nr";
        else
            $sql = "select dfaehigkeittyp.beschreibung as typ from dfaehigkeittyp where stat_nr='$statnr'";
        
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @return type 
     */
    public function getQualifikationsTypenArrayS171() {
        $sql = "select dfaehigkeittyp.beschreibung as typ from dfaehigkeittyp where stat_nr between 'Q0011G' and 'Q0061' order by stat_nr";
        
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param int $persnr
     * @param array $qalid
     * @param int $soll
     * @param int $ist 
     */
    public function addQualifikationen($persnr, $qalid, $soll, $ist) {
        if (is_array($qalid)) {
            foreach ($qalid as $q) {
                $faehigkeit_id = $q['id'];
                // pokud uz tuto kvalifikaci ma nebudu delat nic
                $sql = "select persnr from dpersfaehigkeit where faehigkeit_id='$faehigkeit_id' and persnr='$persnr'";
                echo "<br>$sql";
                $res = mysql_query($sql);
                if (mysql_affected_rows() == 0) {
                    $sql = "insert into dpersfaehigkeit (persnr,faehigkeit_id,soll,ist) values('$persnr','$faehigkeit_id',$soll,$ist)";
                    echo "<br>$sql";
                    mysql_query($sql);
                }
            }
        }
    }

    /**
     * 
     * @param type $ex
     * @param type $teil
     * @return type
     */
    public function getLastPalBehExport($ex,$teil=NULL){
	$lastPal = 0;
	if($teil==NULL){
	    $sql = "select max(dbehexport.ex_pal) as lastpal from dbehexport where export='$ex'";
	}
	else{
	    $sql = "select max(dbehexport.ex_pal) as lastpal from dbehexport where export='$ex' and teil='$teil'";
	}
	
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    $lastPal = intval($r[0]['lastpal']);
	}
	return $lastPal;
    }
    /**
     *
     * @param int $typid 
     */
    public function getQualifikationenProQTyp($typid) {
        if ($typid === NULL)
            $sql = "select dfaehigkeiten.id,dfaehigkeiten.beschreibung,dfaehigkeiten.faeh_abkrz from dfaehigkeiten order by faeh_abkrz";
        else
            $sql = "select dfaehigkeiten.id,dfaehigkeiten.beschreibung,dfaehigkeiten.faeh_abkrz from dfaehigkeiten where dfaehigkeiten.faehigkeit_typ='$typid' order by faeh_abkrz";
        return $this->getQueryRows($sql);
    }

    public function getAussArtText($artnr){
	$retString = "";
	$sql = "select `a-bez` as aussbeschreibung from `auss-art` where `a-nr`='$artnr'";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    $retString = $r[0]['aussbeschreibung'];
	}
	return $retString;
    }
    
    
    /**
     * 
     * @param type $reklnr
     * @return type
     */
    public function getReklInfoFromReklNr($reklnr){
	$sql = "select * from dreklamation where rekl_nr='$reklnr'";
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     */
    public function deleteDummyReklamationen($id=NULL){
	if($id===NULL){
	    $sql = "delete from dreklamation where kunde=0 and teil=''";
	    $this->query($sql);
	}
    }
    /**
     * 
     */
    public function getReklamationenArray($reklid = NULL) {
	if ($reklid === NULL) {
	    $sql.=" select";
	    $sql.=" dreklamation.id,";
	    $sql.=" dreklamation.rekl_nr,";
	    $sql.=" kunde,";
	    $sql.=" kd_rekl_nr,";
	    $sql.=" kd_kd_rekl_nr,";
	    $sql.=" import,";
	    $sql.=" export,";
	    $sql.=" DATE_FORMAT(rekl_erledigt_am,'%Y-%m-%d') as rekl_erledigt_am1,";
	    $sql.=" DATE_FORMAT(rekl_datum,'%Y-%m-%d') as rekl_datum,";
	    $sql.=" teil,";
	    $sql.=" stk_reklammiert,";
	    $sql.=" beschr_abweichung,";
	    $sql.=" bemerkung";
	    $sql.=" from";
	    $sql.=" dreklamation";
	    $sql.=" order by";
	    $sql.=" dreklamation.rekl_datum desc,";
	    $sql.=" dreklamation.rekl_nr";
	} else {
	    $sql = "select *";
	    $sql.=" ,DATE_FORMAT(rekl_datum,'%Y-%m-%d') as rekl_datum1";
	    $sql.=" ,DATE_FORMAT(rekl_erledigt_am,'%Y-%m-%d') as rekl_erledigt_am1";
	    $sql.=" ,DATE_FORMAT(mt_datum,'%Y-%m-%d') as mt_datum1";
	    $sql.=" ,DATE_FORMAT(termin_8D,'%Y-%m-%d') as termin_8D1";
	    $sql.=" ,DATE_FORMAT(gesendet_am_8D,'%Y-%m-%d') as gesendet_am_8D1";
	    $sql.=" ,DATE_FORMAT(report8D_3a_einsatzdatum,'%Y-%m-%d') as report8D_3a_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(report8D_3b_einsatzdatum,'%Y-%m-%d') as report8D_3b_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(report8D_3c_einsatzdatum,'%Y-%m-%d') as report8D_3c_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(report8D_6a_einsatzdatum,'%Y-%m-%d') as report8D_6a_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(report8D_6b_einsatzdatum,'%Y-%m-%d') as report8D_6b_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(report8D_6c_einsatzdatum,'%Y-%m-%d') as report8D_6c_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(report8D_7a_einsatzdatum,'%Y-%m-%d') as report8D_7a_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(report8D_7b_einsatzdatum,'%Y-%m-%d') as report8D_7b_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(report8D_7c_einsatzdatum,'%Y-%m-%d') as report8D_7c_einsatzdatum1";
	    $sql.=" ,DATE_FORMAT(analyse_erhalten_am,'%Y-%m-%d') as analyse_erhalten_am1";
	    $sql.=" ,DATE_FORMAT(analyse_erledigt_am,'%Y-%m-%d') as analyse_erledigt_am1";
	    $sql.=" ,DATE_FORMAT(rekl_datum,'%d.%m.%Y') as rekl_erhalten_am_abmahnung";
	    $sql.=" from dreklamation where id='$reklid'";
	}

	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $id
     * @param type $field
     * @param type $value
     */
    public function updateBewertungKriteriaField($id,$field,$value){
	$sql = "update bewertung_kriteria set `$field`='$value' where id='$id' limit 1";
	return $this->query($sql);
    }
    /**
     * 
     * @param type $field
     * @param type $value
     * @param type $id
     */
    public function updateBehExport($field,$value,$id){
	$sql = "update dbehexport set `$field`='$value' where id='$id' limit 1";
	return $this->query($sql);
    }
    /**
     * smaze fakturu
     *
     * @param unknown_type $auftrag cislo faktury, kterou mam smazat
     */
    function deleteRechnung($auftrag) {
	$sqlDelete = "delete from drech where (auftragsnr='$auftrag')";
	$smazano = $this->query($sqlDelete);
	$sqlUpdate = "update daufkopf set fertig='2100-01-01',ma_rechnr=0 where auftragsnr='$auftrag' limit 1";
	$this->query($sqlUpdate);
	return $smazano;
    }

    /**
     * formatter -> parser in angularModel
     */
    /**
     * 
     */
    public function getSchlTabellenArray($tabid=NULL,$formtyp='schltabelle'){
	if($tabid===NULL){
	    $sql = "select dschltabellen.* from dschltabellen where form_typ like '$formtyp' order by buttonName";
	}
	else{
	    $sql = "select dschltabellen.* from dschltabellen where tabid='$tabid' and form_typ like '$formtyp' ";
	}
	return $this->getQueryRows($sql);
    }
    
    /**
     *
     * @param type $aussNr 
     */
    public function getAussArtArray($arbpapier=FALSE){
        if($arbpapier===TRUE)
	    $sql = "select `a-nr` as aussnr,`a-bez` as aussbeschreibung from `auss-art` where druck_arbpapier<>0 order by `a-nr`";
	else
	    $sql = "select `a-nr` as aussnr,`a-bez` as aussbeschreibung from `auss-art` where 1 order by `a-nr`";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @return type 
     */
    public function getGeeignetArray() {
        $sql = "select text_kurz from dtextbuch where kategorie='bew_geeignet' order by zahl";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $sql
     * @return array 
     */
    public function getQueryRows($sql) {
        $rows = array();
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            while ($row = mysql_fetch_assoc($res)) {
                array_push($rows, $row);
            }
            return $rows;
        }
        else
            return NULL;
    }

    /**
     *
     * @param type $model
     * @return array|null 
     */
    public function getGrundModelArray($model) {
        $sql = "select dgewvzabymodel.gewicht,dgewvzabymodel.vzaby from dgewvzabymodel where model_id='$model' order by gewicht";

        $modelArray = array();

        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            while ($row = mysql_fetch_assoc($res)) {
                $gewicht = $row['gewicht'];
                $vzaby = $row['vzaby'];
                array_push($modelArray, array($gewicht, $vzaby));
            }
            return $modelArray;
        }
        else
            return NULL;
    }

    /**
     *
     * @param type $formid 
     */
    public function getResourcesForFormId($formid){
	$sql = "select element_id from resources where form_id='$formid'";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    $elements = array();
	    foreach ($r as $row) array_push($elements, $row['element_id']);
	    return $elements;
	}
	return NULL;
    }

    /**
     * 
     * @param type $im
     * @param type $teil
     * @param type $preis
     * @param type $stk
     * @param type $pal
     * @param type $abgnr
     * @param type $vzkd
     * @param type $vzaby
     * @param type $user
     */
    public function insertDauftrRowFromTemplate($im, $teil, $preis, $stk, $pal, $abgnr, $vzkd, $vzaby, $user){
	// informace, ktere budu potrebovat z pozice G na stejne palete
	$dauftrId = $this->getDauftrIdGPal1($im, $pal);
	if($dauftrId!=NULL){
	    $dr = $this->getDauftrRow($dauftrId);
	    if($dr!==NULL){
		$termin = $dr['termin'];
		$fremdpos = $dr['fremdpos'];
		$fremdauftr = $dr['fremdauftr'];
		$im_stk = $dr['im_stk'];
		$giesstag = $dr['giesstag'];
		//pismeno do faktury
		$kz = $this->getKZForAbgnr($abgnr);
		$sql = "insert into dauftr (im_stk,giesstag,auftragsnr,teil,`stück`,termin,fremdauftr,fremdpos,`mehrarb-kz`,`pos-pal-nr`,abgnr,preis,`VzAby`,`VzKd`,comp_user_accessuser)";
		$sql.="values('$im_stk','$giesstag','$im','$teil','$stk','$termin','$fremdauftr','$fremdpos','$kz','$pal','$abgnr','$preis','$vzaby','$vzkd','$user')";
		mysql_query($sql);
		return mysql_insert_id();
	    }
	    return -2;
	}
	return -1;
    }
    
    /**
     * 
     * @param type $abgnr
     */
    public function getKZForAbgnr($abgnr){
	$sql = "select `dtaetkz-abg`.dtaetkz from `dtaetkz-abg` where `dtaetkz-abg`.`abg-nr`=$abgnr";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return $r[0]['dtaetkz'];
	}
	return NULL;
    }
    /**
     *
     * @param type $auftragsnr
     * @param type $teil
     * @param type $preis
     * @param type $stk
     * @param type $termin
     * @param type $tat
     * @param type $pal
     * @param type $abgnr
     * @param type $vzkd
     * @param type $vzaby
     * @param type $user
     * @return type 
     */
    public function insertDauftrRow($auftragsnr, $teil, $preis, $stk, $termin, $tat, $pal, $abgnr, $vzkd, $vzaby, $user) {
        $sql = "insert into dauftr (auftragsnr,teil,`stück`,termin,`mehrarb-kz`,`pos-pal-nr`,abgnr,preis,`VzAby`,`VzKd`,comp_user_accessuser)";
        $sql.= " values($auftragsnr,'$teil',$stk,'$termin','$tat',$pal,$abgnr,$preis,$vzaby,$vzkd,'$user')";
        echo "<br>$sql";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param type $teil
     * @param type $abgnr
     * @return null 
     */
    public function getAbgNrInfoArrayForTeil($teil, $abgnr) {
        $sql = "select round(dpos.`VZ-min-kunde`*dksd.preismin,dksd.preis_runden) as preis,`dtaetkz-abg`.dtaetkz as tat,dksd.`Kunde`,dksd.preismin,dpos.`VZ-min-kunde` as vzkd,dpos.`vz-min-aby` as vzaby";
        $sql .=" from dpos";
        $sql .=" join dkopf on dkopf.`Teil`=dpos.`Teil`";
        $sql .=" join dksd on dkopf.`Kunde`=dksd.`Kunde`";
        $sql .=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby`";
        $sql .=" where dpos.`Teil`='$teil' and dpos.`TaetNr-Aby`=$abgnr";
        $res = mysql_query($sql);
        if (mysql_affected_rows() == 0) {
            return NULL;
        } else {
            $row = mysql_fetch_assoc($res);
            return $row;
        }
    }

    /**
     * 
     * @param type $datum
     * @param type $persnr
     * @param type $vorschuss
     */
    public function insertVorschuss($datum,$persnr,$vorschuss,$user){
	$sql = "insert into dvorschuss (PersNr,Datum,Vorschuss,user) values('$persnr','$datum','$vorschuss','$user')";
	$res = mysql_query($sql);
	return mysql_insert_id();
    }
    /**
     *
     * @param type $teil
     * @return null|array 
     */
    public function getNoExRows($teil) {
        $query = "select dauftr.auftragsnr,dauftr.`stück` as stk,dauftr.termin,dauftr.`pos-pal-nr`,dauftr.fremdauftr,dauftr.fremdpos from dauftr where dauftr.teil='$teil' and dauftr.`auftragsnr-exp` is null and dauftr.`KzGut`='G'";
        $res = mysql_query($query);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($res))
                array_push($rows, $row);
            return $rows;
        }
    }

    /**
     *
     * @param type $dauftrId
     * @return null 
     */
    public function getDauftrWaageParameters($dauftrId) {
        $sql = "select dauftr.stk_laut_waage,dauftr.auss_stk_laut_waage,dauftr.auss_abywaage_brutto,dauftr.auss_abywaage_behaelter_ist,dauftr.auss_abywaage_kg_stk10,dauftr.`stück` as stkimp,dauftr.`stk-exp` as stkexp,dauftr.abywaage_brutto,dauftr.abywaage_behaelter_ist,dauftr.abywaage_kg_stk10,dauftr.kg_stk_bestellung,dauftr.kunde_behaelter_bestellung_netto from dauftr where id_dauftr='$dauftrId'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() == 0) {
            return NULL;
        } else {
            $row = mysql_fetch_assoc($res);
            return $row;
        }
    }

    /**
     *
     * @param <type> $abgnrG 
     */
    public function getStatNrForAbgnr($abgnr) {
        $query = "select `dtaetkz-abg`.`Stat_Nr` as statnr from `dtaetkz-abg` where `dtaetkz-abg`.`abg-nr`='$abgnr'";
        $res = mysql_query($query);
        if (mysql_affected_rows() == 0) {
            return NULL;
        } else {
            $row = mysql_fetch_assoc($res);
            return $row['statnr'];
        }
    }

    /**
     * 
     * @param type $id
     */
    public function delAbmahnung($id){
	$sql.="delete from dabmahnung where id='$id' limit 1";
	$this->query($sql);
	return mysql_affected_rows();
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function delSchulung($id){
	$sql.="delete from dpersschulung where id='$id' limit 1";
	$this->query($sql);
	return mysql_affected_rows();
    }

    /**
     * 
     * @param type $persnr
     * @param type $schulung_id
     * @param type $datum
     * @param type $dreklamation_id
     * @param type $ergebniss
     */
    public function addSchulungVorschlag($persnr,$schulung_id,$datum,$dreklamation_id,$ergebniss){
	$sql.="insert into dpersschulung (persnr,schulung_id,letzte_datum,ergebniss,rekl_id)";
	$sql.=" values('$persnr','$schulung_id','$datum','$ergebniss','$dreklamation_id')";
	$this->query($sql);
	return mysql_insert_id();
    }
    /**
     * 
     * @param type $persnr
     * @param type $grund
     * @param type $datum
     * @param type $betr
     * @param type $dreklamation_id
     * @param type $vorschlag
     * @param type $vorschlag_von
     * @param type $vorschlag_betrag
     * @param type $vorschlag_bemerkung
     */
    public function addAbmahnung($persnr,$grund,$datum,$betr,$dreklamation_id,$vorschlag,$vorschlag_von,$vorschlag_betrag,$vorschlag_bemerkung){
	$sql.="insert into dabmahnung ";
	$sql.="(persnr,grund,datum,betr,dreklamation_id,vorschlag,vorschlag_von,vorschlag_betrag,vorschlag_bemerkung)";
	$sql.="values('$persnr','$grund','$datum','$betr','$dreklamation_id','$vorschlag','$vorschlag_von','$vorschlag_betrag','$vorschlag_bemerkung')";
	$this->query($sql);
	return mysql_insert_id();
    }
    
    /**
     * 
     * @param type $ausstyp
     * @param type $persnr
     * @param type $von
     * @param type $bis
     * @return type
     */
    public function getAussArrayPersnrVonBis($ausstyp,$persnr,$von,$bis){
	$sql.=" select";
	$sql.=" drueck.Datum as datum,";
	$sql.=" drueck.auftragsnr,";
	$sql.=" drueck.Teil as teil,";
	$sql.=" dkopf.Gew as gew,";
	$sql.=" drueck.`pos-pal-nr` as pal,";
	$sql.=" drueck.TaetNr as abgnr,";
	$sql.=" drueck.`Auss-Stück` as auss_stk";
	$sql.=" from drueck";
	$sql.=" join dkopf on dkopf.Teil=drueck.Teil";
	$sql.=" where";
	$sql.=" persnr='$persnr'";
	$sql.=" and datum between '$von' and '$bis'";
	$sql.=" and auss_typ='$ausstyp'";
	$sql.=" order by";
	$sql.=" drueck.Datum,";
	$sql.=" drueck.auftragsnr,";
	$sql.=" drueck.teil";
//	return $sql;
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $reklid
     */
    public function getSchulungenForReklamation($reklid){
	$sql.="select dpersschulung.*,CONCAT(dpers.name,' ',dpers.vorname) as name from dpersschulung join dpers on dpers.persnr=dpersschulung.persnr where dpersschulung.rekl_id='$reklid' order by dpers.persnr";
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $grund
     */
    public function getAbmahnungGrundIdFromText($grund){
	$sql="select id from dabmahnpplan where bezeichnung='$grund'";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return $r[0]['id'];
	}
	else{
	    return 0;
	}
    }
    
    /**
     * 
     * @param type $id
     */
    public function getAbmahnungTexte($id){
	$sql = "select * from dabmahnungtexte where abmahnung_id='$id'";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return $r[0];
	}
	else{
	    return $r;
	}
    }
    /**
     * 
     * @param type $abmahnungid
     * @return type
     */
    public function getAbmahnungInfo($abmahnungid){
	$sql.="select *,DATE_FORMAT(datum,'%d.%m.%Y') as datumFormatted,DATE_FORMAT(betrdat,'%m / %Y') as betrdatum_monat_jahr from dabmahnung where dabmahnung.id='$abmahnungid'";
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $reklid
     */
    public function getAbmahnungenForReklamation($reklid){
	$sql.="select *,DATE_FORMAT(datum,'%d.%m.%Y') as datumFormatted from dabmahnung where dabmahnung.dreklamation_id='$reklid' order by dabmahnung.persnr";
	return $this->getQueryRows($sql);
    }
    
    /**
     *
     * @param type $gewicht
     * @return null|array 
     */
    public function getBehaelterTypen($gewicht = NULL) {
        if ($gewicht === NULL)
            $query = "select * from dbehaelter order by typ";
        else {
            $tolerance = 5;
            $gewVon = $gewicht - $tolerance;
            $gewBis = $gewicht + $tolerance;
            $query = "select * from dbehaelter where dbehaelter.gewicht between '$gewVon' and '$gewBis' order by typ";
        }

        $res = mysql_query($query);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($res))
                array_push($rows, $row);
            return $rows;
        }
    }

    
    /**
     * 
     * @param type $dposId
     * @param type $vzaby
     * @param type $vzkd
     */
    public function updateDposVZ($dposId,$vzaby,$vzkd){
	$sql = "update dpos set `VZ-min-kunde`='$vzkd',`vz-min-aby`='$vzaby' where dpos_id=$dposId limit 1";
	mysql_query($sql);
	return mysql_affected_rows();
    }
    /**
     *
     * @param type $im
     * @param type $ex
     * @param type $datumDB
     * @return type 
     */
    public function updateBehaelterBewegungDatum($im, $ex, $datumDB) {
        if ($im > 0)
            $sql = "update dbehaelterbew set datum='$datumDB' where import='$im'";
        if ($ex > 0)
            $sql = "update dbehaelterbew set datum='$datumDB' where export='$ex'";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    
    /**
     *
     * @param type $plan
     * @param type $statnr
     * @param type $dbDatum
     * @param type $beforeDatum if TRUE, get plan for previous Date
     * @return null 
     */
    public function getPlanSollTagMinuten($plan,$statnr,$dbDatum,$beforeDatum=FALSE){
	$nowDB = date('Y-m-d');
	$sql="select minuten from plansolltag where (plan='$plan') and (statnr='$statnr') and (datum='$dbDatum')";
	if($beforeDatum===TRUE)
		$sql="select sum(minuten) as minuten from plansolltag where (plan='$plan') and (statnr='$statnr') and ((datum<'$dbDatum') and (datum>='$nowDB'))";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return NULL;
	return intval($rows[0]['minuten']);
    }
    
    /**
     *
     * @param type $dbDatum
     * @param type $kd_von
     * @param type $kd_bis 
     */
    public function getPlanSollTagKundeSumme($dbDatum,$kd_von,$kd_bis){
	$sql.=" select sum(minuten) as sum_minuten ";
	$sql.=" from plansolltag ";
	$sql.=" join daufkopf on daufkopf.auftragsnr=plansolltag.plan";
	$sql.=" where ";
	$sql.="     (datum='$dbDatum')";
	$sql.="     and daufkopf.kunde between $kd_von and $kd_bis";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return 0;
	return intval($rows[0]['sum_minuten']);
    }
    
    /**
     * 
     * @param type $dbDatum
     * @param type $kd_von
     * @param type $kd_bis
     * @return int
     */
    public function getPlanSollTagKundeSummeNoEx($dbDatum,$kd_von,$kd_bis){
	$sql.=" select sum(minuten) as sum_minuten ";
	$sql.=" from plansolltag ";
	$sql.=" where ";
	$sql.="     (datum='$dbDatum')";
	$sql.="     and plan between '".$kd_von."NOEX' and '".$kd_bis."NOEX'";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return 0;
	return intval($rows[0]['sum_minuten']);
    }
    
    /**
     * 
     * @param type $dbDatum
     * @param type $statnr
     * @param type $kd_von
     * @param type $kd_bis
     * @return int
     */
    public function getPlanSollStatnrSummeNoEx($dbDatum, $statnr, $kd_von, $kd_bis) {
	$sql.=" select sum(minuten) as sum_minuten ";
	$sql.=" from plansolltag ";
	$sql.=" where ";
	$sql.="     (datum='$dbDatum') and (statnr='$statnr')";
	$sql.="     and plan between '".$kd_von."NOEX' and '".$kd_bis."NOEX'";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return 0;
	return intval($rows[0]['sum_minuten']);
    }

    /**
     *
     * @param type $dbDatum
     * @param type $statnr
     * @param type $kd_von
     * @param type $kd_bis 
     */
    public function getPlanSollStatnrSumme($dbDatum, $statnr, $kd_von, $kd_bis) {
	$sql.=" select sum(minuten) as sum_minuten ";
	$sql.=" from plansolltag ";
	$sql.=" join daufkopf on daufkopf.auftragsnr=plansolltag.plan";
	$sql.=" where ";
	$sql.="     (datum='$dbDatum') and (statnr='$statnr')";
	$sql.="     and daufkopf.kunde between $kd_von and $kd_bis";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return 0;
	return intval($rows[0]['sum_minuten']);
    }

    /**
     *
     * @param type $dbDatum 
     */
    public function getPlanSollTagAll($dbDatum){
	$sql = "select sum(minuten) as sum_minuten from plansolltag where (datum='$dbDatum')";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return 0;
	return intval($rows[0]['sum_minuten']);
    }
    /**
     *
     * @param type $plan
     * @param type $dbDatum 
     */
    public function getPlanSollTagSumme($plan,$dbDatum,$beforeDatum=FALSE){
	$nowDatumDb = date('Y-m-d');
	$sql = "select sum(minuten) as sum_minuten from plansolltag where (plan='$plan') and (datum='$dbDatum')";
	if($beforeDatum===TRUE)
	    $sql = "select sum(minuten) as sum_minuten from plansolltag where (plan='$plan') and ((datum>='$nowDatumDb') and (datum<'$dbDatum'))";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return 0;
	return intval($rows[0]['sum_minuten']);
    }
    /**
     *
     * @param type $plan
     * @param type $statnr
     * @param type $dbDatum 
     */
    public function updatePlanSollTag($plan,$statnr,$dbDatum,$minuten){
	$min = $this->getPlanSollTagMinuten($plan,$statnr,$dbDatum);
	if($min===NULL){
	    //insert
	    $sql = "insert into plansolltag (plan,statnr,datum,minuten) values('$plan','$statnr','$dbDatum','$minuten')";
	    $this->query($sql);
	}
	else{
	    //update
   	    $sql = "update plansolltag set minuten='$minuten' where (plan='$plan') and (statnr='$statnr') and (datum='$dbDatum') limit 1";
	    $this->query($sql);
	}
	return $sql;
    }
    /**
     *
     * @param type $im
     * @param type $ex
     * @param type $datumDB
     * @return type 
     */
    public function updateBehBewDatum($im, $ex, $datumDB) {
        if ($im > 0)
            $sql = "update dbehbew set datum='$datumDB' where import='$im'";
        if ($ex > 0)
            $sql = "update dbehbew set datum='$datumDB' where export='$ex'";
        mysql_query($sql);
        echo $sql;
        return mysql_affected_rows();
    }

    
    /**
     * 
     */
    public function saveGPalBemerkung($gid,$val){
	$sql = "update dauftr set bemerkung='$val' where id_dauftr=$gid";
	mysql_query($sql);
	return mysql_affected_rows();
    }
    
    
    /**
     * 
     * @param type $jahr
     * @param type $monat
     */
    public function getSvatekCount($jahr,$monat){
	$ps=0;
	$von = $jahr . "-" . $monat . "-01";
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;
	$sql.=" select ";
	$sql.=" count(calendar.datum) as pocet";
	$sql.=" from calendar";
	$sql.=" where";
	$sql.=" (svatek<>0)";
	$sql.=" and (cislodne<>7)";
	$sql.=" and (datum between '$von' and '$bis')";
	$r=$this->getQueryRows($sql);
	if($r!==NULL){
	    $ps = intval($r[0]['pocet']);
	}
	return $ps;
    }
    /**
     *
     * @param type $dauftrId 
     */
    public function getPalBemerkung($dauftrId){
	$gTatId = $this->getDauftrIdForGPal($dauftrId);
//	return $gTatId;
	if($gTatId===NULL) 
	    return "";
	else{
	    return $this->getPalBemerkungGPal($gTatId);
	}
    }
    
    public function getPalBemerkungIMPal($im,$pal){
	$sql = "select bemerkung from dauftr where auftragsnr=$im and `pos-pal-nr`=$pal and kzgut='G'";
	$res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
	    return $row['bemerkung'];
        } else {
            return "";
        }
    }
    /**
     *
     * @param type $gTatId 
     */
    public function getPalBemerkungGPal($gTatId){
	$sql = "select bemerkung from dauftr where id_dauftr=$gTatId";
	$res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
	    return $row['bemerkung'];
        } else {
            return "";
        }
    }
    /**
     *
     * @param type $dauftrId 
     */
    public function getDauftrIdForGPal($dauftrId){
	$auftragPalRow = $this->getDauftrRow($dauftrId);
//	return $auftragPalRow;
	if($auftragPalRow===NULL)
	    return NULL;
	else{
	    $auftrag = $auftragPalRow['auftragsnr'];
	    $pal = $auftragPalRow['pal'];
	    $id = $this->getDauftrIdGPal1($auftrag,$pal);
	    return $id;
	}
    }
    
    public function getDauftrIdGPal1($auftrag,$pal){
	$sql = "select id_dauftr as id from dauftr where auftragsnr='$auftrag' and `pos-pal-nr`='$pal' and kzgut='G'";
	$res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
	    return $row['id'];
        } else {
            return NULL;
        }
    }
    
    public function getWerkstoffPreis($kunde,$werkstoffId){
	$sql = "select werkstoffpreise.preis_pro_kg from werkstoffpreise where werkstoff_id='$werkstoffId' and kunde='$kunde'";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return floatval($r[0]['preis_pro_kg']);
	}
	else{
	    return 0;
	}
    }
    /**
     *
     * @param type $auftragsnr
     * @param type $pal 
     */
    public function getDrueckTatArray($auftragsnr, $pal){
	$sql.=" select";
	$sql.=" dauftr.abgnr,";
	$sql.=" sum(dauftr.`Stück`) as import_stk,";
	$sql.=" sum(if(drueck.`Stück` is null,0,drueck.`Stück`)) as drueck_stk,";
	$sql.=" sum(if(drueck.auss_typ=2,drueck.`Auss-Stück`,0)) as drueck_auss_2,";
	$sql.=" sum(if(drueck.auss_typ=4,drueck.`Auss-Stück`,0)) as drueck_auss_4,";
	$sql.=" sum(if(drueck.auss_typ=6,drueck.`Auss-Stück`,0)) as drueck_auss_6";
	$sql.=" from";
	$sql.=" dauftr";
	$sql.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
	$sql.=" where";
	$sql.=" dauftr.auftragsnr='$auftragsnr'";
	$sql.=" and";
	$sql.=" dauftr.`pos-pal-nr`='$pal'";
	$sql.=" group by";
	$sql.=" dauftr.abgnr";
	return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $auftrag
     * @param type $pal
     * @param type $lager
     * @return type 
     */
    public function getLagerAussAuftragPalette($auftrag, $pal, $lager) {
	$in = 0;
	$out = 0;

	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(auss_stk) as nach from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_nach='$lager'))";
	$res = mysql_query($sql);
	if (mysql_affected_rows() > 0) {
	    $row = mysql_fetch_array($res);
	    $in = $row['nach'];
	}

	// pocek kusu ze skladu odebranych
	$sql = "select sum(auss_stk) as ven from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_von='$lager'))";
	$res = mysql_query($sql);
	if (mysql_affected_rows() > 0) {
	    $row = mysql_fetch_array($res);
	    $out = $row['ven'];
	}

	return $in - $out;
    }

    /**
     *
     * @param type $auftrag
     * @param type $pal
     * @param type $lager
     * @return type 
     */
    public function getLagerGutAuftragPalette($auftrag, $pal, $lager) {
	$in = 0;
	$out = 0;

	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(gut_stk) as nach from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_nach='$lager'))";
	$res = mysql_query($sql);
	if (mysql_affected_rows() > 0) {
	    $row = mysql_fetch_array($res);
	    $in = $row['nach'];
	}

	// pocek kusu ze skladu odebranych
	$sql = "select sum(gut_stk) as ven from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_von='$lager'))";
	$res = mysql_query($sql);
	if (mysql_affected_rows() > 0) {
	    $row = mysql_fetch_array($res);
	    $out = $row['ven'];
	}

	return $in - $out;
    }

    /**
     *
     * @param type $auftragsnr
     * @param type $pal
     * @param type $dil
     * @param type $ident 
     */
    public function stornoLastDlagerBewExport($auftragsnr, $pal,$dil,$ident) {
	// pokud mam nenulovy dobrych pocet kusu ve skladu 8X, budu stornovat posledni exportni pozici
	$lVon = '8E';
	$lNach = '8X';
	$pocetKusu = $this->getLagerGutAuftragPalette($auftragsnr, $pal, $lNach);

//	if ($pocetKusu != 0) {
	    // vystornuju posledni exportni zaznam
	    $sql = "select gut_stk from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (lager_von='$lVon') and (lager_nach='$lNach')) order by date_stamp desc limit 1";
	    $res = mysql_query($sql);

	    if (mysql_affected_rows() > 0) {
		$row = mysql_fetch_array($res);
		$stornoStk = $row['gut_stk'];
		$this->insertDlagerBew($dil,$auftragsnr,$pal,$stornoStk,0,$lNach,$lVon,$ident);
	    }
//	}
	    // XX se bude stornovat opravdu jen pro neulovy pocet kusu
	    

	// to same pro sklad XX
	$pocetKusu = $this->getLagerGutAuftragPalette($auftragsnr, $pal, "XX");

	if ($pocetKusu != 0) {
	    // vystornuju posledni exportni zaznam
	    $sql = "select lager_von,gut_stk from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (lager_nach='XX')) order by date_stamp desc limit 1";
	    $res = mysql_query($sql);

	    if (mysql_affected_rows() > 0) {
		$row = mysql_fetch_array($res);
		$stornoStk = $row['gut_stk'];
		$lVon = $row['lager_von'];
		$this->insertDlagerBew($dil, $auftragsnr, $pal, $stornoStk, 0, "XX", $lVon, $ident);
	    }
	}

	// a jeste jednou pro vyexportovane ausschussy (B2,B4,B6)
	$aussTypenB = array("B2", "B4", "B6");
	$aussTypenA = array("A2", "A4", "A6");
	$poradiAussTyp = 0;
	foreach ($aussTypenB as $aussTyp) {
	    if ($this->getLagerAussAuftragPalette($auftragsnr, $pal, $aussTyp) != 0) {
		$lVon = $aussTypenA[$poradiAussTyp];
		$lNach = $aussTyp;
		$sql = "select lager_nach,lager_von,auss_stk from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (lager_von='$lVon') and (lager_nach='$lNach')) order by date_stamp desc";
		$res = mysql_query($sql);
		if (mysql_affected_rows() > 0) {
		    $row = mysql_fetch_array($res);
		    $lNach = $row['lager_nach'];
		    $lVon = $row['lager_von'];
		    $aussStk = $row['auss_stk'];
		    $this->insertDlagerBew($dil, $auftragsnr, $pal, 0, $aussStk, $lNach, $lVon, $ident);
		}
	    }
	    $poradiAussTyp++;
	}
    }

    /**
     *
     * @param type $teil
     * @param type $auftrag
     * @param type $paleta
     * @return type 
     */
    public function erster_lager($teil, $auftrag, $paleta) {
		// nejdriv si zjistim cislo operace
	$sql = "select abgnr from dauftr where ((teil='$teil') and (auftragsnr='$auftrag') and (`pos-pal-nr`='$paleta') and (abgnr>3)) order by abgnr";
	$res = mysql_query($sql);
	$abgnr=0;
	$ela='0D';
	
	if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($res);
		$abgnr = $row['abgnr'];
	}
	
	// pro zjistinene abgnr si zjistim z tabulky dpos jmeno skladu
	// pokud pro dane abgnr nic nenajdu vratim 0D
	//$sql = "select lager_von from dpos where ((teil='$teil') and (lager_von is not null) and (lager_von<>'0D') and (`taetnr-aby`='$abgnr'))";
	// zmena 2014-05-29, prvni lager se hleda podle vzestupne setridenych lager_von
	$sql = "select dpos.lager_von from dpos ";
	$sql.= " join dauftr on dauftr.teil=dpos.teil and dauftr.abgnr=dpos.`taetnr-aby`";
	$sql.= " where ((dpos.teil='$teil') and (dpos.lager_von is not null)";
	$sql.= " and (dauftr.auftragsnr='$auftrag') and (dauftr.`pos-pal-nr`='$paleta')";
	$sql.= " and (dpos.lager_von<>'0D')";
	$sql.= " and (length(trim(dpos.lager_von))>0)";
	$sql.= ")";
	$sql.=" order by dpos.lager_von";
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

	//dbConnect();
	// nejdriv si zjistim cislo operace
//	$sql = "select abgnr from dauftr where ((teil='$teil') and (auftragsnr='$auftrag') and (`pos-pal-nr`='$paleta') and (abgnr>3)) order by abgnr";
//	$res = mysql_query($sql);
//	$abgnr = 0;
//	$ela = 'OD';
//
//	if (mysql_affected_rows() > 0) {
//	    $row = mysql_fetch_array($res);
//	    $abgnr = $row['abgnr'];
//	}
//
//	// pro zjistinene abgnr si zjistim z tabulky dpos jmeno skladu
//	// pokud pro dane abgnr nic nenajdu vratim 0D
//	$sql = "select lager_von from dpos where ((teil='$teil') and (lager_von is not null) and (lager_von<>'0D') and (`taetnr-aby`='$abgnr'))";
//	$res = mysql_query($sql);
//	if (mysql_affected_rows() > 0) {
//	    $row = mysql_fetch_array($res);
//	    $lVon = $row['lager_von'];
//	    if (strlen($lVon) > 0)
//		$ela = $lVon;
//	}
//	else {
//	    // jeste posledni moznost, jestli nemam jmeno skladu u zaskrtnute G operace
//	    $sql = "select lager_von from dpos where ((teil='$teil') and (kzgut='G') and (`kz-druck`<>0))";
//	    $res = mysql_query($sql);
//	    if (mysql_affected_rows() > 0) {
//		$row = mysql_fetch_array($res);
//		$lVon = $row['lager_von'];
//		if (strlen($lVon) > 0)
//		    $ela = $lVon;
//	    }
//	}
//	return $ela;
    }

    public function getLagerGutIn($auftrag, $pal, $lager) {
	$in = 0;

	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(gut_stk) as nach from dlagerbew where ((auftrag_import=$auftrag) and (pal_import=$pal) and (lager_nach='$lager'))";
	//echo "sql=$sql";
	$res = mysql_query($sql);
	if (mysql_affected_rows() > 0) {
	    $row = mysql_fetch_array($res);
	    $in = $row['nach'];
	}
	return $in;
    }

    /**
     *
     * @param type $auftrag
     * @param type $pal
     * @param type $lager
     * @return type 
     */
    public function getLagerGesamtOut($auftrag, $pal, $lager) {
	$in = 0;

	// zjistim pocet kusu do skladu vlozenych
	$sql = "select sum(gut_stk+auss_stk) as ven from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (lager_von='$lager'))";
	//echo "sql=$sql<br>";
	$res = mysql_query($sql);
	if (mysql_affected_rows() > 0) {
	    $row = mysql_fetch_array($res);
	    $in = $row['ven'];
	}
	return $in;
    }

    
    /**
     * 
     * @param type $teil
     * @param type $tat
     */
    public function getDposInfo($teil, $tat = NULL,$no3=FALSE) {
	if ($tat === NULL) {
	    $sql.=" select ";
	    $sql.=" dpos.`TaetNr-Aby` as abgnr,";
	    $sql.=" `dtaetkz-abg`.dtaetkz as tat,";
	    $sql.=" dpos.KzGut as kzgut,";
	    $sql.=" dpos.`TaetBez-Aby-D` as tat_bez_d,";
	    $sql.=" dpos.`TaetBez-Aby-T` as tat_bez_cz,";
	    $sql.=" dpos.`VZ-min-aby` as vzaby,";
	    $sql.=" dpos.`VZ-min-kunde` as vzkd,";
	    $sql.=" dpos.`kz-druck` as kz_druck,";
	    $sql.=" dpos.lager_von,";
	    $sql.=" dpos.bedarf_typ,";
	    $sql.=" dpos.lager_nach";
	    $sql.=" from dpos";
	    $sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby`";
	    $sql.=" where ";
	    $sql.=" teil='$teil'";
	    if($no3===TRUE){
		$sql.=" and dpos.`TaetNr-Aby`<>3";
	    }
	    $sql.=" order by";
	    $sql.=" dpos.`TaetNr-Aby`";
	} else {
	    $sql = "select dpos.dpos_id as id,dpos.`TaetNr-Aby` as abgnr,dpos.`VZ-min-kunde` as vzkd,dpos.`vz-min-aby` as vzaby from dpos where (teil='$teil') and (dpos.`TaetNr-Aby`=$tat)";
	}

	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $ex
     */
    public function getAussArtenArrayForExport($ex){
	$sql.=" select";
	$sql.=" drueck.Teil as teil,";
	$sql.=" dauftr.auftragsnr as import,";
	$sql.=" dauftr.`mehrarb-kz` as tatkz,";
	$sql.=" dauftr.fremdauftr as bestnr,";
	$sql.=" dauftr.fremdpos as pos,";
	$sql.=" drueck.auss_typ,";
	$sql.=" drueck.`auss-art` as auss_art,";
	$sql.=" sum(drueck.`Auss-Stück`) as auss_stk";
	$sql.=" from drueck";
	$sql.=" join dauftr on dauftr.auftragsnr=drueck.AuftragsNr and dauftr.teil=drueck.Teil and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.abgnr=drueck.TaetNr";
	$sql.=" where";
	$sql.=" dauftr.`auftragsnr-exp`='$ex'";
	$sql.=" and";
	$sql.=" drueck.auss_typ<>0";
	$sql.=" group by";
	$sql.=" drueck.Teil,";
	$sql.=" dauftr.auftragsnr,";
	$sql.=" dauftr.`mehrarb-kz`,";
	$sql.=" drueck.auss_typ,";
	$sql.=" drueck.`auss-art`";
	$sql.=" having ";
	$sql.=" auss_stk<>0;";
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param type $auftrag
     * @param type $pal
     * @param type $ausstyp
     * @return type 
     */
    public function getAussFromDrueckAuftragPalTyp($auftrag, $pal, $ausstyp) {
	$aussCount = 0;
	mysql_query('set names utf8');
	$sql = "select sum(`Auss-Stück`) as auss from drueck where ((auftragsnr='$auftrag') and (`pos-pal-nr`='$pal') and (auss_typ='$ausstyp'))";
	//echo "sql=$sql<br>";
	$res = mysql_query($sql);
	if (mysql_affected_rows() > 0) {
	    $row = mysql_fetch_array($res);
	    $aussCount = $row['auss'];
	}

	return $aussCount;
    }

    /**
     * 
     */
    public function getFilesForPath($path,$filter=NULL,$inclusivFolders=FALSE) {
	$docsArray = array();
	$fileArray = array();
	
	try{
	if($filter==NULL) 
	    $iterator = new DirectoryIterator($path);
	else{
	    //$iterator = new GlobIterator($path, FilesystemIterator::CURRENT_AS_FILEINFO|FilesystemIterator::SKIP_DOTS);
	    $iterator = new FilesystemIterator($path, FilesystemIterator::CURRENT_AS_FILEINFO|FilesystemIterator::SKIP_DOTS);
	    $iterator = new RegexIterator($iterator, $filter);
	}
	    
	
	//if($iterator->count()==0) return NULL;
	foreach ( $iterator as $file) {
	    // if the file is not this file, and does not start with a '.' or '..',
	    // then store it for later display
	    //if ((!$file->isDot()) && ($file->getFilename() != basename($_SERVER['PHP_SELF']))) {
	    if (($file->getFilename() != basename($_SERVER['PHP_SELF']))) {
		// if the element is a directory add to the file name "(Dir)"
		//echo ($file->isDir()) ? "(Dir) ".$file->getFilename() : $file->getFilename()."<br>";
		    $fileArray['filename'] = utf8_encode($file->getFilename());
		    $fileArray['mtime'] = $file->getMTime();
		    $fileArray['size'] = $file->getSize();
		    $fileArray['type'] = $file->getType();
		    $fileName = $file->getFilename();
		    $ext = substr($fileName, strrpos($fileName, '.')+1);
		    $fileArray['ext'] = strtoupper($ext);
		    $fileArray['url'] = "/gdat" . substr($file->getPath(), 13) . "/" . $fileArray['filename'];
		    if(
			    ($fileArray['type']=='file')
			    ||(($fileArray['type']=='dir')&&($inclusivFolders))
		    ){
			//vynechat odkaz na aktualni slozku '.'
			if($fileArray['filename']!='.')
			    array_push ($docsArray, $fileArray);
		    }
			
	    }
	}
	}
	catch(Exception $e){
	    return NULL;
	}
	
	if(count($docsArray)==0) return NULL;
	return $docsArray;
    }

    /**
     *
     * @param type $import
     * @param type $dil
     * @param type $pal
     * @param type $auss
     * @param type $von
     * @param type $nach 
     */
    public function moveAussLagerFromA2B($import, $dil, $pal, $auss, $von, $nach) {
	$ident = get_user_pc();

	$sql = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
	$sql.= "values ('$dil','$import','$pal',0,'$auss','$von','$nach','$ident')";
	mysql_query($sql);
    }

    /**
     *
     * @param type $auftragsnr
     * @param type $pal
     * @param type $dil
     * @param type $ident 
     */
    public function moveAussLagerA2B($auftragsnr, $pal, $dil, $ident) {
	$auss2 = $this->getAussFromDrueckAuftragPalTyp($auftragsnr, $pal, 2);
	$auss4 = $this->getAussFromDrueckAuftragPalTyp($auftragsnr, $pal, 4);
	$auss6 = $this->getAussFromDrueckAuftragPalTyp($auftragsnr, $pal, 6);
	if ($auss2 != 0)
	    $this->moveAussLagerFromA2B($auftragsnr, $dil, $pal, $auss2, "A2", "B2");
	if ($auss4 != 0)
	    $this->moveAussLagerFromA2B($auftragsnr, $dil, $pal, $auss4, "A4", "B4");
	if ($auss6 != 0)
	    $this->moveAussLagerFromA2B($auftragsnr, $dil, $pal, $auss6, "A6", "B6");
    }

    /**
     *
     * @param type $dil
     * @param type $auftragsnr
     * @param type $pal
     * @param type $ident 
     */
    public function insertDlagerBewXXDummy($dil, $auftragsnr, $pal, $ident) {
	// jmeno prvniho skladu

	$eL = $this->erster_lager($dil, $auftragsnr, $pal);
	// kolik kusu zbyva v prvnim skladu
	$pocetKusuVlozenych = $this->getLagerGutIn($auftragsnr, $pal, $eL);
	$pocetKusuOdebranych = $this->getLagerGesamtOut($auftragsnr, $pal, $eL);
	$zbyvaKusu = $pocetKusuVlozenych - $pocetKusuOdebranych;

	if ($zbyvaKusu != 0)
	    $this->insertDlagerBew($dil, $auftragsnr, $pal, $zbyvaKusu, 0, $eL, "XX", $ident);
    }

    public function deleteRundlaufImEx($id){
	$sql = "delete from drundlaufimex where rundlauf_id='$id'";
	return $this->query($sql);
    }
    /**
     * 
     * @param type $id
     */
    public function deleteRundlauf($id){
	$sql = "delete from drundlauf where id='$id'";
	return $this->query($sql);
    }
    
    /**
     * 
     */
    public function updateRundlaufField($field,$value,$id){
	$sql = "update drundlauf set `$field`='$value' where id='$id' limit 1";
	return $this->query($sql);
    }
    /**
     * 
     * @param type $teil
     * @param type $t
     * @return string
     */
    public function lager_nach($teil, $t)
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

    /**
     * 
     * @param type $teil
     * @param type $t
     * @return string
     */
    public function lager_von($teil, $t)
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
    /**
     *
     * @param type $dil
     * @param type $auftragsnr
     * @param type $gut
     * @param type $auss
     * @param type $von
     * @param type $nach
     * @param type $ident 
     */
    public function insertDlagerBew($dil,$auftragsnr,$pal,$gut,$auss,$von,$nach,$ident,$abgnr=NULL,$prog_module=NULL){
	
	if($abgnr!=NULL){
	    $von = $this->lager_von($dil, $abgnr);
	    $nach = $this->lager_nach($dil, $abgnr);
	}
	
	if(!$prog_module){
	    $sql_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
	    $sql_insert.= "values ('$dil','$auftragsnr','$pal','$gut','$auss','$von','$nach','$ident')";
	}
	else{
    	    $sql_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser,prog_module,abgnr) ";
	    $sql_insert.= "values ('$dil','$auftragsnr','$pal','$gut','$auss','$von','$nach','$ident','$prog_module','$abgnr')";
	}
	
	mysql_query($sql_insert);
	return mysql_insert_id();
    }

    
    /**
     * 
     */
    public function updateDlagerImportStkForDauftrId($dauftrId,$stk,$prog_module=NULL){
	
	$dauftrRow = $this->getDauftrRow($dauftrId);
	$auftragsnr=$dauftrRow['auftragsnr'];
	$pal=$dauftrRow['pal'];
	$teil=$dauftrRow['teil'];

	$sql_select = "select sum(gut_stk) as gut_stk,max(lager_nach) as lager_nach from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (teil='$teil') and (lager_von='0'))";
	$res = mysql_query($sql_select);
	$row = mysql_fetch_array($res);
	$gut_stk = $row['gut_stk'];
	$storno_stk = $gut_stk * (-1);
	$lager_nach = $row['lager_nach'];
	$user = $this->get_user_pc();

	// pripravim storno zaznam
	if($prog_module!==NULL){
	    $sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser,prog_module)";
	    $sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$storno_stk','0','$lager_nach','$user','$prog_module')";
	}
	else{
	    $sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser)";
	    $sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$storno_stk','0','$lager_nach','$user')";
	}
	// pokud je co stornovat, provedu prikaz
	if($storno_stk!=0)
        mysql_query($sql_insert_storno);

	// pripravim novy zaznam
	if($prog_module!==NULL){
	    $sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser,prog_module)";
	    $sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$stk','0','$lager_nach','$user','$prog_module')";
	}
	else{
	    $sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser)";
	    $sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$stk','0','$lager_nach','$user')";
	}
	mysql_query($sql_insert_storno);
    }

    
    /**
     * 
     * @param type $import
     * @param type $pal
     * @param type $abgnr
     * @return type
     */
    public function getDauftrRowsForImportPalAbgnr($import, $pal,$abgnr) {
	$sql.=" select";
	$sql.=" dauftr.id_dauftr as id,";
	$sql.=" dauftr.auftragsnr,";
	$sql.=" dauftr.teil,";
	$sql.=" dauftr.`pos-pal-nr` as pal,";
	$sql.=" dauftr.termin,";
	$sql.=" dauftr.preis,";
	$sql.=" dauftr.`stück` as stk,";
	$sql.=" dauftr.`mehrarb-kz` as tatkz,";
	$sql.=" dauftr.fremdauftr,";
	$sql.=" dauftr.fremdpos,";
	$sql.=" dauftr.KzGut as kzgut,";
	$sql.=" dauftr.abgnr,";
	$sql.=" dauftr.`auftragsnr-exp` as ex,";
	$sql.=" dauftr.VzKd as vzkd,";
	$sql.=" dauftr.VzAby as vzaby";
	$sql.=" from";
	$sql.=" dauftr";
	$sql.=" where";
	$sql.=" dauftr.auftragsnr='$import'";
	$sql.=" and";
	$sql.=" dauftr.`pos-pal-nr`='$pal'";
	$sql.=" and";
	$sql.=" dauftr.`abgnr`='$abgnr'";
	$sql.=" order by";
	$sql.=" abgnr";

	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $import
     * @param type $pal
     * @return type
     */
    public function getDauftrRowsForImportPal($import, $pal) {
	$sql.=" select";
	$sql.=" dauftr.id_dauftr as id,";
	$sql.=" dauftr.auftragsnr,";
	$sql.=" dauftr.teil,";
	$sql.=" dauftr.`pos-pal-nr` as pal,";
	$sql.=" dauftr.termin,";
	$sql.=" dauftr.giesstag,";
	$sql.=" dauftr.preis,";
	$sql.=" dauftr.`stück` as stk,";
	$sql.=" dauftr.`mehrarb-kz` as tatkz,";
	$sql.=" dauftr.fremdauftr,";
	$sql.=" dauftr.fremdpos,";
	$sql.=" dauftr.KzGut as kzgut,";
	$sql.=" dauftr.abgnr,";
	$sql.=" dauftr.`auftragsnr-exp` as ex,";
	$sql.=" dauftr.VzKd as vzkd,";
	$sql.=" dauftr.VzAby as vzaby";
	$sql.=" from";
	$sql.=" dauftr";
	$sql.=" where";
	$sql.=" dauftr.auftragsnr='$import'";
	$sql.=" and";
	$sql.=" dauftr.`pos-pal-nr`='$pal'";
	$sql.=" order by";
	$sql.=" abgnr";

	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $import
     * @param type $pal
     * @param type $teil
     * @param type $tat
     */
    public function getDauftrPosForImPalTeilTat($import,$pal,$teil,$tat){
	$sql.=" select id_dauftr as id,auftragsnr,";
	$sql.=" `pos-pal-nr` as pal,";
	$sql.=" teil,";
	$sql.=" abgnr,";
	$sql.=" VzAby as vzaby,";
	$sql.=" VzKd as vzkd";
	$sql.=" from dauftr";
	$sql.=" where";
	$sql.=" (auftragsnr='$import')";
	$sql.=" and";
	$sql.=" (`pos-pal-nr`=$pal)";
	$sql.=" and";
	$sql.=" (teil='$teil')";
	$sql.=" and";
	$sql.=" (abgnr=$tat)";
	return $this->getQueryRows($sql);
    }
    
    
    /**
     * 
     * @param type $import
     */
    public function getDauftrRowsForImport($import){
	$sql = "select *";
	$sql.=" ,`pos-pal-nr` as imp_pal";
	$sql.=" ,`stück` as imp_stk";
	$sql.=" ,`mehrarb-kz` as tatkz";
	$sql.=" ,`auftragsnr-exp` as ex";
	$sql.=" ,`pal-nr-exp` as palex";
	$sql.=" ,`stk-exp` as stkex";
	$sql.=" from dauftr";
	$sql.=" where auftragsnr='$import'";
	$sql.=" order by `pos-pal-nr`,abgnr";
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $import
     * @param type $teil
     */
    public function getDauftrRows($import,$teil,$plan,$nurOhneEx=TRUE){
	
	//nepovolit kombinaci obou hvezdicek
	if(($import=='*')&&(strlen($teil)<3))
	    return NULL;
	
	$sql=" select ";
	$sql.=" auftragsnr as im";
	$sql.=" ,id_dauftr as id";
	$sql.=" ,teil";
	$sql.=" ,`pos-pal-nr` as pal";
	$sql.=" ,`stück` as im_stk";
	$sql.=" ,bemerkung";
	$sql.=" ,fremdpos";
	$sql.=" ,giesstag";
	$sql.=" ,fremdauftr";
	$sql.=" ,`auftragsnr-exp` as ex";
	$sql.=" ,termin as plan";
	$sql.=" from dauftr";
	$sql.=" where (1)";
	if($nurOhneEx===TRUE)
	    $sql.=" and (`auftragsnr-exp` is null)";
	if($import!='*')
	    $sql.=" and (auftragsnr=$import)";
	if($plan!='*'){
	    $plan='P'.$plan;
	    $sql.=" and (termin like '$plan')";
	}
	    

	$sql.=" and (KzGut='G')";
	if(strlen($teil)>=3){
	    $sql.=" and (teil like '$teil')";
	}
	$sql.=" order by auftragsnr,teil,`pos-pal-nr`";
	
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param type $dauftrId 
     */
    public function getDauftrRow($dauftrId){
	$sql = "select im_stk,id_dauftr as id,termin,fremdauftr,fremdpos,auftragsnr,`pos-pal-nr` as pal,teil,`stück` as stk,abgnr,`auftragsnr-exp` as ex,`stk-exp` as ex_stk,giesstag from dauftr where id_dauftr='$dauftrId'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
	    return $row;
        } else {
            return NULL;
        }
    }
    
    /**
     *
     * @param <type> $im
     * @param <type> $ex
     * @param <type> $behaelternr
     * @param <type> $zustand_id
     * @param <type> $datumDB
     * @param <type> $stk 
     */
    public function insertBehaelterBewegungAfterDelete($im, $ex, $behaelternr, $von, $nach, $zustand_id, $datumDB, $stk, $user) {
        if ($im > 0)
            $sql = "delete from dbehaelterbew where import=$im and behaelternr='$behaelternr' and zustand_id=$zustand_id";
        if ($ex > 0)
            $sql = "delete from dbehaelterbew where export=$ex and behaelternr='$behaelternr' and zustand_id=$zustand_id";
//        echo "sqldelete:$sql";
        mysql_query($sql);
        if ($im > 0)
            $sql = "insert into dbehaelterbew (behaelternr,datum,von,nach,stk,import,zustand_id,user) values ('$behaelternr','$datumDB',$von,$nach,$stk,$im,$zustand_id,'$user')";
        if ($ex > 0)
            $sql = "insert into dbehaelterbew (behaelternr,datum,von,nach,stk,export,zustand_id,user) values ('$behaelternr','$datumDB',$von,$nach,$stk,$ex,$zustand_id,'$user')";
//        echo "sqlinsert:$sql";
        mysql_query($sql);
        return mysql_insert_id();
    }

    /**
     *
     * @param <type> $reparaturID
     * @param <type> $artnr
     */
    public function updateReparaturPosAnzahl($reparaturID, $artnr, $anzahl, $user) {
        // mam uz pozici v databazi ?
        $sql = "select id from dreparaturpos where reparatur_id='$reparaturID' and artnr='$artnr'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            // provedu update
            $row = mysql_fetch_assoc($res);
            $id = $row['id'];
            $sql = "update dreparaturpos set anzahl='$anzahl',user='$user' where id='$id' limit 1";
            mysql_query($sql);
            return mysql_affected_rows();
        } else {
            //provedu insert
            $sql = "insert into dreparaturpos (reparatur_id,artnr,anzahl,user,bemerkung)";
            $sql.= "values('$reparaturID','$artnr','$anzahl','$user','')";
            mysql_query($sql);
            return mysql_affected_rows();
        }
    }

    /**
     * 
     * @param type $reparaturID
     * @param type $artnr
     * @param type $anzahl
     * @param type $user
     * @return type
     */
    public function updateReparaturPosInvnummer($reparaturID, $artnr, $invnummer, $user) {
        // mam uz pozici v databazi ?
        $sql = "select id from dreparaturpos where reparatur_id='$reparaturID' and artnr='$artnr'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            // provedu update
            $row = mysql_fetch_assoc($res);
            $id = $row['id'];
            $sql = "update dreparaturpos set et_invnummer='$invnummer',user='$user' where id='$id' limit 1";
            mysql_query($sql);
            return mysql_affected_rows();
        } else {
            //provedu insert
            $sql = "insert into dreparaturpos (reparatur_id,artnr,anzahl,user,bemerkung,et_invnummer)";
            $sql.= "values('$reparaturID','$artnr',0,'$user','','$invnummer')";
            mysql_query($sql);
            return mysql_affected_rows();
        }
    }

        public function updateReparaturPosBemerkung($reparaturID, $artnr, $bemerkung, $user) {
        // mam uz pozici v databazi ?
        $sql = "select id from dreparaturpos where reparatur_id='$reparaturID' and artnr='$artnr'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            // provedu update
            $row = mysql_fetch_assoc($res);
            $id = $row['id'];
            $sql = "update dreparaturpos set bemerkung='$bemerkung',user='$user' where id='$id' limit 1";
            mysql_query($sql);
            return mysql_affected_rows();
        } else {
            //provedu insert
            $sql = "insert into dreparaturpos (reparatur_id,artnr,anzahl,user,bemerkung)";
            $sql.= "values('$reparaturID','$artnr',0,'$user','$bemerkung')";
            mysql_query($sql);
            return mysql_affected_rows();
        }
    }

    /**
     * 
     * @param type $auftragsnr
     */
    public function getFrachtForExport($auftragsnr){
	$sql.=" select ";
	$sql.=" daufkopf.waehr_kz,";
	$sql.=" sum(drech.`Stück`*drech.DM) as betrag";
	$sql.=" from drech";
	$sql.=" join daufkopf on daufkopf.auftragsnr=drech.AuftragsNr";
	$sql.=" where drech.AuftragsNr='$auftragsnr'";
	$sql.=" and `Taet-kz`='F'";
	$sql.=" group by daufkopf.waehr_kz";

	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return floatval($r[0]['betrag']);
	}
	return 0;
    }
    /**
     * 
     * @param type $auftragsnr
     * @return type
     */
    public function getBehaelterInExport($auftragsnr){
	$sql.=" select ";
	$sql.=" dbehaelterbew.behaelternr,";
	$sql.=" `eink-artikel`.`art-name1` as behname,";
	$sql.=" dbehaelterbew.zustand_id,";
	$sql.=" dbehaelterzustand.zustand_text,";
	$sql.=" sum(dbehaelterbew.stk) as sum_stk";
	$sql.=" from dbehaelterbew";
	$sql.=" join dbehaelterzustand on dbehaelterzustand.zustand_id=dbehaelterbew.zustand_id";
	$sql.=" join `eink-artikel` on `eink-artikel`.`art-nr`=dbehaelterbew.behaelternr";
	$sql.=" where";
	$sql.=" dbehaelterbew.export='$auftragsnr'";
	$sql.=" group by";
	$sql.=" dbehaelterbew.behaelternr,";
	$sql.=" dbehaelterbew.zustand_id";
	$sql.=" having";
	$sql.=" sum_stk>0";
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $username
     */
    public function getUserInfoArray($username){
	$sql="select * from dbenutzer where name='$username'";
	$rows = $this->getQueryRows($sql);
	if($rows!=NULL)
	    return $rows[0];
	else
	    return NULL;
    }
    
    public function getUsersForRoleId($roleId){
	$sql = "select benutzername from dbenutzerroles where role_id=$roleId";
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $user
     * @return type
     */
    public function getUserRolesArray($user){
	$sql.=" select dbenutzerroles.benutzername,dbenutzerroles.role_id,roles.`name` as rolename";
	$sql.=" from dbenutzerroles";
	$sql.=" join roles on roles.id=dbenutzerroles.role_id";
	$sql.=" where dbenutzerroles.benutzername='$user'";
	$sql.=" order by roles.`name`";
	
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param <type> $reparaturID
     * @param <type> $artnr
     * @param <type> $anzahl
     * @param <type> $user
     * @return <type> 
     */
    public function updateReparaturPosAlt($reparaturID, $artnr, $et_alt, $user) {
        // mam uz pozici v databazi ?
        $sql = "select id from dreparaturpos where reparatur_id='$reparaturID' and artnr='$artnr'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            // provedu update
            $row = mysql_fetch_assoc($res);
            $id = $row['id'];
            $sql = "update dreparaturpos set et_alt='$et_alt',user='$user' where id='$id' limit 1";
            mysql_query($sql);
            return mysql_affected_rows();
        } else {
            //provedu insert
            $sql = "insert into dreparaturpos (reparatur_id,artnr,et_alt,user,bemerkung)";
            $sql.= "values('$reparaturID','$artnr','$et_alt','$user','')";
            mysql_query($sql);
            return mysql_affected_rows();
        }
    }

    /**
     *
     * @param <type> $behaelterNr
     * @param <type> $kunde
     * @param <type> $stk
     * @param <type> $zustand_id
     * @param <type> $platz_id
     */
    public function updateBehaelterInventurStk($behaelterNr, $kunde, $stk, $zustand_id, $platz_id, $datumDB = NULL) {
        //zkusim, zda uz mam v databazi pozici, kam to muzu ulozit
        $sql = "select stk from dbehaelterinventur where kunde='$kunde' and behaelternr='$behaelterNr' and platz_id='$platz_id' and zustand_id=$zustand_id and datum='$datumDB'";
        mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            // provedu update
            $sql = "update dbehaelterinventur set stk=$stk where kunde='$kunde' and behaelternr='$behaelterNr' and platz_id='$platz_id' and zustand_id=$zustand_id  and datum='$datumDB'";
            mysql_query($sql);
            return mysql_affected_rows();
        } else {
            //provedu insert
            $sql = "insert into dbehaelterinventur (behaelternr,kunde,zustand_id,platz_id,stk,datum) values('$behaelterNr',$kunde,$zustand_id,'$platz_id',$stk,'$datumDB')";
            mysql_query($sql);
            return mysql_affected_rows();
        }
    }

    /**
     *
     * @param <type> $behaelterNr
     * @param <type> $kunde
     * @param <type> $kundeKontoStk
     */
    public function updateBehaelterKundeKontoStk($behaelterNr, $kunde, $kundeKontoStk, $datumDB = NULL) {
        //zkusim, zda uz mam v databazi pozici, kam to muzu ulozit
        $sql = "select stk from dbehaelterinventur where kunde='$kunde' and behaelternr='$behaelterNr' and platz_id='KDKONTO' and zustand_id=9999 and datum='$datumDB'";
        mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            // provedu update
            $sql = "update dbehaelterinventur set stk=$kundeKontoStk where kunde='$kunde' and behaelternr='$behaelterNr' and platz_id='KDKONTO' and zustand_id=9999 and datum='$datumDB'";
            mysql_query($sql);
            return mysql_affected_rows();
        } else {
            //provedu insert
            $sql = "insert into dbehaelterinventur (behaelternr,kunde,zustand_id,platz_id,stk,datum) values('$behaelterNr',$kunde,9999,'KDKONTO',$kundeKontoStk,'$datumDB')";
            mysql_query($sql);
            return mysql_affected_rows();
        }
    }

    /**
     *
     * @return null 
     */
    public function getLastParsedEdataFile() {
        $sql = "";
        $sql.=" select filename,size,stamp from edatalogs order by stamp desc limit 1";
        $rows = $this->getQueryRows($sql);
        if ($rows === NULL)
            return NULL;
        else
            return $rows[0];
    }

    /**
     *
     * @param type $kunde
     * @param type $zeitpunktDB
     * @return null 
     */
    public function getLastKDKontoInvDatumDBArray($kunde, $zeitpunktDB) {
        $behDatum = array();
        $sql = " select";
        $sql.= " dbehinventur.behaelternr,";
        $sql.= " DATE_FORMAT(max(dbehinventur.datum),'%Y-%m-%d') as last_inventur";
        $sql.= " from dbehinventur";
        $sql.= " where";
        $sql.= " dbehinventur.kunde=$kunde";
        $sql.= " and dbehinventur.platz_id='KDKONTO'";
        $sql.= " and dbehinventur.datum<='$zeitpunktDB'";
        $sql.= " group by";
        $sql.= " dbehinventur.behaelternr";

        $rows = $this->getQueryRows($sql);
        if ($rows === NULL)
            return NULL;
        else {
            foreach ($rows as $row) {
                $behDatum[$row['behaelternr']] = $row['last_inventur'];
            }
            return $behDatum;
        }
    }

    /**
     *
     * @param <type> $behnr
     * @param <type> $kundevon
     * @param <type> $bewvonDB
     * @param <type> $bewbisDB
     */
    public function getLastKDKontoInvDatumDB($behnr, $kundevon, $bewvonDB, $bewbisDB) {
        $sql = "select datum from dbehaelterinventur where behaelternr='$behnr' and kunde=$kundevon and datum<'$bewbisDB' and zustand_id=9999 and platz_id='KDKONTO' order by datum desc limit 1";
//        echo "$sql";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['datum'];
        }
        else
            return NULL;
    }

    /**
     *
     * @param <type> $behnr
     * @param <type> $kundenr
     * @param <type> $lastInvDatum
     * @param <type> $platz
     * @param boolean $auserPlatz - pokud je TRUE tak vrati pocet kusu pro vsechny platz_id krome $platz
     * @return <type>
     */
    public function getBehInventurArrayFuerKundeDatum($behnr, $kundenr, $lastInvDatum, $platz, $auserPlatz = FALSE) {
        if ($auserPlatz === FALSE) {
            $sql = " select";
            $sql.= " dbehinventur.behaelternr,";
            $sql.= " dbehinventur.zustand_id,";
            $sql.= " dbehinventur.inhalt_id,";
            $sql.= " sum(dbehinventur.stk) as suminvstk";
            $sql.= " from";
            $sql.= " dbehinventur";
            $sql.= " where";
            $sql.= " (dbehinventur.kunde=$kundenr)";
            $sql.= " and";
            $sql.= " dbehinventur.datum='$lastInvDatum'";
            $sql.= " and dbehinventur.behaelternr='$behnr'";
            $sql.= " and dbehinventur.platz_id='$platz'";
            $sql.= " group by";
            $sql.= " dbehinventur.behaelternr,";
            $sql.= " dbehinventur.zustand_id,";
            $sql.= " dbehinventur.inhalt_id";
        } else {
            $sql = " select";
            $sql.= " dbehinventur.behaelternr,";
            $sql.= " dbehinventur.zustand_id,";
            $sql.= " dbehinventur.inhalt_id,";
            $sql.= " sum(dbehinventur.stk) as suminvstk";
            $sql.= " from";
            $sql.= " dbehinventur";
            $sql.= " where";
            $sql.= " (dbehinventur.kunde=$kundenr)";
            $sql.= " and";
            $sql.= " dbehinventur.datum='$lastInvDatum'";
            $sql.= " and dbehinventur.behaelternr='$behnr'";
            $sql.= " and dbehinventur.platz_id<>'$platz'";
            $sql.= " group by";
            $sql.= " dbehinventur.behaelternr,";
            $sql.= " dbehinventur.zustand_id,";
            $sql.= " dbehinventur.inhalt_id";
        }
        $rows = $this->getQueryRows($sql);
        return $rows;
    }

    
    public function getRegelKZForAbgNr($abgnr){
        $sql = "select regel from `dtaetkz-abg` where `abg-nr`=$abgnr";
        $rows = $this->getQueryRows($sql);
        if($rows!==NULL){
            return $rows[0]['regel'];
        }
        else
            return "So";
    }
    
    /**
     * 
     * @param type $im
     * @param type $ex
     * @param type $teilnr
     */
    public function getGTArray($im,$ex,$teilnr){
	$sql.=" select";
	$sql.=" dauftr.giesstag";
	$sql.=" from";
	$sql.=" dauftr";
	$sql.=" where";
	$sql.=" `auftragsnr-exp`='$ex'";
	$sql.=" and auftragsnr='$im'";
	$sql.=" and teil='$teilnr'";
	$sql.=" group by";
	$sql.=" dauftr.giesstag";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    $gtA = array();
	    foreach ($rows as $row){
		if(strlen(trim($row['giesstag']))>0){
		    array_push($gtA,$row['giesstag']);
		}
	    }
	    return $gtA;
	}
	else{
	    return NULL;
	}
    }
    /**
     *
     * @param type $teil
     * @param type $abgnr 
     */
    public function getVzKdProTeilAbgnr($teil,$abgnr){
        $sql = "select dpos.`VZ-min-kunde` as vzkd from dpos where teil='$teil' and `TaetNr-Aby`=$abgnr";
        $rows = $this->getQueryRows($sql);
        if($rows===NULL)
            return 0;
        else
            return $rows[0]['vzkd'];
    }

    /**
     * 
     * @param type $im
     */
    public function getMinPreisProImport($im){
	$minpreis = 0;
	$sql = "select daufkopf.minpreis from daufkopf where auftragsnr=$im";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    $minpreis = floatval($r[0]['minpreis']);
	}
	return $minpreis;
    }
    /**
     *
     * @param type $kunde 
     */
    public function getMinPreisProKunde($kunde){
      $sql = "select dksd.preismin from dksd where Kunde=$kunde";
      $rows = $this->getQueryRows($sql);
      if($rows===NULL)
          return 0;
      else
          return $rows[0]['preismin'];
    }
    
    public function getVzAbyProTeilAbgNr($teil, $abgnr){
        $sql = "select dpos.`VZ-min-aby` as vzaby from dpos where teil='$teil' and `TaetNr-Aby`=$abgnr";
        $rows = $this->getQueryRows($sql);
        if($rows===NULL)
            return 0;
        else
            return $rows[0]['vzaby'];
    }
    /**
     *
     * @param type $teil
     * @param type $tatkz
     * @return int 
     */
    public function getPreisProTeilTatKZ($teil,$tatkz){
        // zjistit cenu za minutu
        $kunde = $this->getKundeFromTeil($teil);
        $minpreis = $this->getMinPreisProKunde($kunde);
        
        $sql = " select";
        $sql.= " sum(dpos.`VZ-min-kunde`) as vzkd";
        $sql.= " from";
        $sql.= " dpos";
        $sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dpos.`TaetNr-Aby`";
        $sql.= " where ";
        $sql.= " dpos.Teil='$teil'";
        $sql.= " and";
        $sql.= " `dtaetkz-abg`.dtaetkz='$tatkz'";
                
        $rows = $this->getQueryRows($sql);
        if($rows===NULL)
            return 0;
        else
            return $minpreis*$rows[0]['vzkd'];
    }

    /**
     * 
     */
    public function getrechkzAbgnrArray() {

        $sql = "";
        $sql.=" select `dtaetkz-abg`.dtaetkz as tatkz,`dtaetkz-abg`.`abg-nr` as abgnr";
        $sql.=" from `dtaetkz-abg`";
        $sql.=" group by `dtaetkz-abg`.dtaetkz,`dtaetkz-abg`.`abg-nr`";

        $rechkzArray = array();
        $rows = $this->getQueryRows($sql);
        foreach ($rows as $row) {
            $tatkz = $row['tatkz'];
            $abgnr = $row['abgnr'];
            $rechkzArray[$tatkz].= "$abgnr,";
        }
        if (count($rechkzArray) > 0)
            return $rechkzArray;
        else
            return NULL;
    }

    /**
     *
     * @param <type> $behnr
     * @param <type> $kundenr
     * @param <type> $lastInvDatum
     * @param <type> $zeitpunktDB
     * @return <type>
     */
    public function getBehBewArrayFuerKundeDatumBereich($behnr, $kundenr, $lastInvDatum, $zeitpunktDB) {
        $sql = " select";
        $sql.= " dbehbew.behaelternr,";
        $sql.= " dbehbew.zustand_id,";
        $sql.= " dbehbew.inhalt_id,";
        $sql.= " sum(if(dbehbew.von=$kundenr,dbehbew.stk,0)) as bewplus,";
        $sql.= " sum(if(dbehbew.nach=$kundenr,dbehbew.stk,0)) as bewminus,";
        $sql.= " sum(dbehbew.stk) as sumbewstk";
        $sql.= " from";
        $sql.= " dbehbew";
        $sql.= " where";
        $sql.= " (dbehbew.nach=$kundenr";
        $sql.= " or dbehbew.von=$kundenr)";
        $sql.= " and";
        $sql.= " dbehbew.datum>='$lastInvDatum'";
        $sql.= " and dbehbew.datum<='$zeitpunktDB'";
        $sql.= " and dbehbew.behaelternr='$behnr'";
        $sql.= " group by";
        $sql.= " dbehbew.behaelternr,";
        $sql.= " dbehbew.zustand_id,";
        $sql.= " dbehbew.inhalt_id";
//        echo $sql;
        $rows = $this->getQueryRows($sql);
        return $rows;
    }

    /**
     *
     * @param <type> $behaelternr
     * @param <type> $kundenr
     * @param <type> $invdatumDB
     * @return <type>
     */
    public function getBehaelterBewegungungPlus($behaelternr, $kundenr, $invdatumDB, $bisDatumDB = '2100-01-01') {
        //$sql = "select sum(stk) as stk from dbehaelterbew where datum>='$invdatumDB' and von=$kundenr and nach=100 and behaelternr='$behaelternr' and datum<='$bisDatumDB'";
	//zmena 2014-08-01
	$sql = "select sum(stk) as stk from dbehaelterbew where datum>'$invdatumDB' and von=$kundenr and nach=100 and behaelternr='$behaelternr' and datum<='$bisDatumDB'";
//        echo "$sql";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['stk'];
        }
        else
            return 0;
    }

    /**
     *
     * @param type $behaelternr
     * @param type $kundenr
     * @param type $invdatumDB
     * @param type $bisDatumDB
     * @return int 
     */
    public function getBehBewPlus($behaelternr, $kundenr, $invdatumDB, $bisDatumDB = '2100-01-01') {
        $sql = "select sum(stk) as stk from dbehbew where datum>='$invdatumDB' and von=$kundenr and nach=100 and behaelternr='$behaelternr' and datum<='$bisDatumDB'";
//        echo "$sql";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['stk'];
        }
        else
            return 0;
    }

    /**
     *
     * @param type $behaelternr
     * @param type $kundenr
     * @param type $invdatumDB
     * @param type $bisDatumDB
     * @return int 
     */
    public function getBehaelterBewegungungMinus($behaelternr, $kundenr, $invdatumDB, $bisDatumDB = '2100-01-01') {
        //$sql = "select sum(stk) as stk from dbehaelterbew where datum>='$invdatumDB' and von=100 and nach=$kundenr and behaelternr='$behaelternr' and datum<='$bisDatumDB'";
	// zmena 2014-08-01
	$sql = "select sum(stk) as stk from dbehaelterbew where datum>'$invdatumDB' and von=100 and nach=$kundenr and behaelternr='$behaelternr' and datum<='$bisDatumDB'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['stk'];
        }
        else
            return 0;
    }

    /**
     *
     * @param type $behaelternr
     * @param type $kundenr
     * @param type $invdatumDB
     * @param type $bisDatumDB
     * @return int 
     */
    public function getBehBewMinus($behaelternr, $kundenr, $invdatumDB, $bisDatumDB = '2100-01-01') {
        $sql = "select sum(stk) as stk from dbehbew where datum>='$invdatumDB' and von=100 and nach=$kundenr and behaelternr='$behaelternr' and datum<='$bisDatumDB'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['stk'];
        }
        else
            return 0;
    }

    /**
     *
     * @param type $behnr
     * @param type $kunde
     * @param type $invDatumDB
     * @return null 
     */
    public function getBehelterKDKontoStand($behnr, $kunde, $invDatumDB = NULL) {
        $sql = "select behaelternr,kunde,zustand_id,platz_id,stk from dbehaelterinventur where kunde='$kunde' and behaelternr='$behnr' and datum='$invDatumDB'  and platz_id='KDKONTO' and zustand_id=9999";
        $rows = $this->getQueryRows($sql);
        if ($rows === NULL)
            return NULL;
        else
            return $rows[0]['stk'];
    }

    /**
     *
     * @param <type> $behaelterNr
     * @param <type> $kunde 
     */
    public function getBehaelterInventurStArray($behaelterNr, $kunde, $datumDB = NULL) {
        // pro dane parametry najdu posledni datum
        if ($datumDB === NULL) {
            $sql = "select max(datum) as datum from dbehaelterinventur where kunde='$kunde' and behaelternr='$behaelterNr'";
            $res = mysql_query($sql);
            if (mysql_affected_rows() > 0) {
                $row = mysql_fetch_assoc($res);
                $datumDB = $row['datum'];
            }
            else
                return NULL;
        }
        $sql = "select DATE_FORMAT(datum,'%d.%m.%Y') as datum,behaelternr,kunde,zustand_id,platz_id,stk from dbehaelterinventur where kunde='$kunde' and behaelternr='$behaelterNr' and datum='$datumDB'";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $behnr
     * @param type $kundenr
     * @param type $zustandId
     * @param type $inhaltId
     * @param type $lastInvDatum
     * @param type $platz
     * @return null 
     */
    public function getBehInventurRow($behnr, $kundenr, $zustandId, $inhaltId, $lastInvDatum, $platz) {
        $sql = " select";
        $sql.= " sum(dbehinventur.stk) as stk";
        $sql.= " from dbehinventur";
        $sql.= " where";
        $sql.= " dbehinventur.behaelternr=$behnr";
        $sql.= " and";
        $sql.= " dbehinventur.kunde=$kundenr";
        $sql.= " and";
        $sql.= " dbehinventur.zustand_id=$zustandId";
        $sql.= " and";
        $sql.= " dbehinventur.inhalt_id=$inhaltId";
        $sql.= " and";
        $sql.= " dbehinventur.datum='$lastInvDatum'";
        $sql.= " and";
        $sql.= " dbehinventur.platz_id='$platz'";
        $sql.=" having sum(dbehinventur.stk) is not null";
        $row = $this->getQueryRows($sql);
        if ($row === NULL)
            return NULL;
//        echo $sql;
        return array('stk' => $row[0]['stk'], 'datum' => $lastInvDatum);
    }

    /**
     *
     * @param type $kunde 
     */
    public function getKundeGdatPath($kunde){
	  $sql = "select dksd.gdat_path from dksd where kunde=$kunde";
	  $rows = $this->getQueryRows($sql);
	  if($rows!==NULL){
	      $row = $rows[0];
	      $path = trim($row['gdat_path']);
	      if(strlen($path)>0) return $path;
	  }
	  return NULL;
    }
    /**
     *
     * @param <type> $behaelterNr
     * @param <type> $kunde
     * @return <type> 
     */
    public function getBehaelterInventurRows($behaelterNr, $kunde) {
        // pro dane parametry najdu posledni datum
        $sql = "select dbehinventur.id,DATE_FORMAT(datum,'%d.%m.%Y') as datumF,dbehinventur.zustand_id,dbehinventur.inhalt_id";
        $sql.=" ,dbehinventur.platz_id,stk";
        $sql.=" from dbehinventur";
        $sql.=" where kunde='$kunde' and behaelternr='$behaelterNr'";
        $sql.=" order by datum desc, dbehinventur.platz_id desc";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $class
     * @param type $idevent
     * @param type $time
     * @param type $datetime
     * @param type $type
     * @param type $address
     * @param type $badgenumber
     * @param type $reason 
     */
    public function insertEdataEvent($class, $idevent, $time, $datetime, $type, $address, $badgenumber, $reason, $persnr) {
        $sql = "";
        $sql.=" insert into edata_access_events";
        $sql.=" (class,idevent,time,dt,type,address,badgenumber,persnr,reason)";
        $sql.=" values";
        $sql.=" ('$class','$idevent',$time,'$datetime','$type','$address','$badgenumber',$persnr,'$reason')";
        mysql_query($sql);
        $ar = mysql_insert_id();
        if ($ar > 0)
            return $ar;
        else
            return mysql_error();
    }

    /**
     *
     * @param type $datumDB
     * @param type $persnr 
     */
    public function getAnwesenheitFromEdata($datumDB,$persnr){
        $sql="";
        $sql.=" select ";
        $sql.=" edata_access_events.persnr,";
        $sql.=" min(edata_access_events.dt) as von,";
        $sql.=" max(edata_access_events.dt) as bis";
        $sql.=" from edata_access_events";
        $sql.=" where";
        $sql.=" DATE_FORMAT(edata_access_events.dt,'%Y-%m-%d')='$datumDB'";
        $sql.=" and persnr<>0";
        $sql.=" and persnr=$persnr";
        $sql.=" group by";
        $sql.=" edata_access_events.persnr";
        $sql.=" order by";
        $sql.=" edata_access_events.persnr,";
        $sql.=" edata_access_events.dt";
        
        return $this->getQueryRows($sql);
    }
    /**
     *
     * @param type $latest_filename 
     */
    public function insertLastEdataFile($latest_filename, $size) {
        $sql = "insert into edatalogs (filename,size) values('$latest_filename',$size);";
        mysql_query($sql);
        $ar = mysql_insert_id();
        if ($ar > 0)
            return $ar;
        else
            return mysql_error();
    }

    /**
     * 
     * @param type $artikel
     * @param type $ks
     * @param type $bemerk
     * @param type $abdatum
     */
    public function insertEinkaufAnforderung($artikel,$ks,$bemerk,$abdatum,$user,$anftyp,$prio){
	$sql = "insert into einkauf_anforderungen";
	$sql.=" (artikel,anzahl,user,bemerkung,abdatum,anftyp,prio)";
	$sql.=" values('$artikel',$ks,'$user','$bemerk','$abdatum','$anftyp','$prio')";
	mysql_query($sql);
	return mysql_insert_id();
    }
    
    /**
     * 
     * @param type $im
     * @param type $ex
     * @param type $ab_aby_ist_datetime
     * @param type $ab_aby_soll_datetime
     * @param type $an_aby_ist_datetime
     * @param type $an_aby_soll_datetime
     * @param type $an_kunde_ist_datetime
     * @param type $an_kunde_soll_datetime
     * @param type $proforma
     * @param type $spediteur_id
     * @param type $fahrername
     * @param type $lkw_kz
     * @param type $an_kunde_ort
     * @param type $an_aby_nutzlast
     * @param type $preis
     * @param type $rabatt
     * @param type $betrag
     * @param type $rechnung
     * @param type $bemerkung
     * @return string
     */
    public function insertDRundlauf(
	$im,
	$ex,
	$ab_aby_ist_datetime,
	$ab_aby_soll_datetime,
	$an_aby_ist_datetime,
	$an_aby_soll_datetime,
	$an_kunde_ist_datetime,
	$an_kunde_soll_datetime,
	$proforma,
	$spediteur_id,
	$fahrername,
	$lkw_kz,
	$an_kunde_ort,
	$an_aby_nutzlast,
	$preis,
	$rabatt,
	$betrag,
	$rechnung,
	$bemerkung
	){
	
	$sql="insert into drundlauf (";
	$sql.=" im,";
	$sql.=" ex,";
	$sql.=" ab_aby_ist_datetime,";
	$sql.=" ab_aby_soll_datetime,";
	$sql.=" an_aby_ist_datetime,";
	$sql.=" an_aby_soll_datetime,";
	$sql.=" an_kunde_ist_datetime,";
	$sql.=" an_kunde_soll_datetime,";
	$sql.=" proforma,";
	$sql.=" spediteur_id,";
	$sql.=" fahrername,";
	$sql.=" lkw_kz,";
	$sql.=" an_kunde_ort,";
	$sql.=" an_aby_nutzlast,";
	$sql.=" preis,";
	$sql.=" rabatt,";
	$sql.=" betrag,";
	$sql.=" rechnung,";
	$sql.=" bemerkung";
	$sql.=" )";
	$sql.=" values (";
	$sql.=" $im,";
	$sql.=" $ex,";
	$sql.= $ab_aby_ist_datetime==NULL?"null,":"'".$ab_aby_ist_datetime."',";
	$sql.= $ab_aby_soll_datetime==NULL?"null,":"'".$ab_aby_soll_datetime."',";
	$sql.= $an_aby_ist_datetime==NULL?"null,":"'".$an_aby_ist_datetime."',";
	$sql.= $an_aby_soll_datetime==NULL?"null,":"'".$an_aby_soll_datetime."',";
	$sql.= $an_kunde_ist_datetime==NULL?"null,":"'".$an_kunde_ist_datetime."',";
	$sql.= $an_kunde_soll_datetime==NULL?"null,":"'".$an_kunde_soll_datetime."',";
	$sql.=" '$proforma',";
	$sql.=" $spediteur_id,";
	$sql.=" '$fahrername',";
	$sql.=" '$lkw_kz',";
	$sql.=" '$an_kunde_ort',";
	$sql.=" $an_aby_nutzlast,";
	$sql.=" '$preis',";
	$sql.=" '$rabatt',";
	$sql.=" '$betrag',";
	$sql.=" '$rechnung',";
	$sql.=" '$bemerkung'";
	
	$sql.=" )";
	
	mysql_query($sql);
        $ar = mysql_affected_rows();
        if ($ar > 0)
            return $ar;
        else
            return $sql;
    }
    /**
     *
     * @param <type> $behaelternr
     * @param <type> $kunde
     * @param <type> $datum
     * @param <type> $stk
     * @param <type> $bein
     * @param <type> $bezu
     * @param <type> $lagplatz
     * @param <type> $ident
     * @return <type> 
     */
    public function insertBehInv($behaelternr, $kunde, $datum, $stk, $bein, $bezu, $lagplatz, $ident) {
        $sql = "insert into dbehinventur(behaelternr,kunde,zustand_id,inhalt_id,platz_id,stk,datum,user)";
        $sql.=" values('$behaelternr','$kunde','$bezu','$bein','$lagplatz','$stk','$datum','$ident')";
        mysql_query($sql);
        $ar = mysql_affected_rows();
        if ($ar > 0)
            return $ar;
        else
            return $sql;
    }

    /**
     *
     * @param <type> $behaelternr
     * @param <type> $im
     * @param <type> $ex
     * @param <type> $kundevon
     * @param <type> $kundenach
     * @param <type> $datum
     * @param <type> $stk
     * @param <type> $bein
     * @param <type> $bezu
     * @param <type> $user
     * @return <type> 
     */
    public function insertBehBew($behaelternr, $im, $ex, $kundevon, $kundenach, $datum, $stk, $bein, $bezu, $user) {
        if ($im === NULL)
            $importValue = 'NULL';
        else
            $importValue = $im;
        if ($ex === NULL)
            $exportValue = 'NULL';
        else
            $exportValue = $ex;


        $sql = "insert into dbehbew (behaelternr,datum,von,nach,stk,import,export,zustand_id,inhalt_id,user)";
        $sql.=" values ($behaelternr,'$datum',$kundevon,$kundenach,$stk,$importValue,$exportValue,$bezu,$bein,'$user')";
        mysql_query($sql);
        $ar = mysql_affected_rows();
        if ($ar > 0)
            return $ar;
        else
            return $sql;
    }

    /**
     *
     * @param type $behaelternr
     * @param type $im
     * @param type $ex
     * @param type $kundevon
     * @param type $kundenach
     * @param type $datum
     * @param type $stk
     * @param type $zustand
     * @param type $user
     * @return type 
     */
    public function insertBehaelterBewegung($behaelternr, $im, $ex, $kundevon, $kundenach, $datum, $stk, $zustand, $user) {
        if ($im === NULL)
            $importValue = 'NULL';
        else
            $importValue = $im;
        if ($ex === NULL)
            $exportValue = 'NULL';
        else
            $exportValue = $ex;


        $sql = "insert into dbehaelterbew (behaelternr,datum,von,nach,stk,import,export,zustand_id,user)";
        $sql.=" values ($behaelternr,'$datum',$kundevon,$kundenach,$stk,$importValue,$exportValue,$zustand,'$user')";
        mysql_query($sql);
        $ar = mysql_affected_rows();
        if ($ar > 0)
            return $ar;
        else
            return $sql;
    }

    /**
     * 
     * @param type $teilnr
     */
    public function getTeilInfoArray($teilnr) {
	$sql = "select ";
	$sql.=" `Teil`,";
	$sql.=" `Kunde`,";
	$sql.=" `Teilbez`,";
	$sql.=" `Wst`,";
	$sql.=" `Gew`,";
	$sql.=" `BrGew`,";
	$sql.=" `FA`,";
	$sql.=" `JB`,";
	$sql.=" `Reklamation`,";
	$sql.=" `Letzte-Reklamation`,";
	$sql.=" `Muster-vom`,";
	$sql.=" `Muster-Platz`,";
	$sql.=" `Muster-vorher-vom`,";
	$sql.=" `Muster-Freigabe-1`,";
	$sql.=" `Muster-Freigabe-1-vom`,";
	$sql.=" `Muster-Freigabe-2`,";
	$sql.=" `Muster-Freigabe-2-vom`,";
	$sql.=" `bemerk`,";
	$sql.=" `komplex`,";
	$sql.=" `teillang`,";
	$sql.=" `Art Guseisen`,";
	$sql.=" `fraese_wettkampf_factor`,";
	$sql.=" `inwettkampf_flag`,";
	$sql.=" `dummy_flag`,";
	$sql.=" `status`,";
	$sql.=" `preis_stk_gut`,";
	$sql.=" `preis_stk_auss`,";
	$sql.=" `kosten_stk_auss`,";
	$sql.=" `jahr_bedarf_stk_2011`,";
	$sql.=" `jahr_bedarf_stk_2012`,";
	$sql.=" `jahr_bedarf_stk_2013`,";
	$sql.=" `jb_lfd_2`,";
	$sql.=" `jb_lfd_1`,";
	$sql.=" `jb_lfd_j`,";
	$sql.=" `jb_lfd_plus_1`,";
	$sql.=" `gut_lfd_1`,";
	$sql.=" `stk_g_ist_2012`,";
	$sql.=" `stk_g_ist_2013`,";
	$sql.=" `stk_g_ist_2014`,";
	$sql.=" `fremdauftr_dkopf`,";
	$sql.=" `schwierigkeitsgrad_S11`,";
	$sql.=" `schwierigkeitsgrad_S51`,";
	$sql.=" `schwierigkeitsgrad_SO`,";
	$sql.=" `comp_user_accessuser`,";
	$sql.=" `stamp`,";
	$sql.=" `verpackungmenge`,";
	$sql.=" `restmengen_verw`,";
	$sql.=" `stk_pro_gehaenge`";
	$sql.=" from dkopf";
	$sql.=" where teil='$teilnr'";
	
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL)
	    return $rows[0];
	else
	    return NULL;
    }


    public function getKundePreisRundenStellen($kunde) {
        $sql = "select preis_runden from `dksd` where `kunde`='$kunde'";
	$r=$this->getQueryRows($sql);
	if($r!==NULL){
	    return intval($r[0]['preis_runden']);
	}
	return 4;
    }

    /**
     * 
     * @param type $e
     */
    public function getKundeArrayMatch($e){
	$sql = "select dksd.Kunde as kunde,dksd.Name1,dksd.Name2 from dksd";
	$sql.=" where Kunde like '$e%' or Name1 like '%$e%' or Name2 like '%$e%'";
	$sql.=" order by Kunde";
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param <type> $kunde
     */
    public function getKundeInfoArray($kunde) {
        $sql = "select bearbeitung_tage,kunde,name1,preismin,`waehr-kz` as waehrkz from `dksd` where `kunde`='$kunde'";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $value
     * @param int $pole - 0 vrati konkretni polozku identifikovanou hodnotou value,1 vrati vsechny polozky jejichz cislo zacina na value
     */
    public function getEinkArtikelArray($value, $pole = 0) {
        if ($pole == 0)
            $sql = "select `art-nr` as artnr,`art-name1` as name from `eink-artikel` where `art-nr`='$value'";
        if ($pole == 1)
            $sql = "select `art-nr` as artnr,`art-name1` as name from `eink-artikel` where CONVERT(`art-nr`,CHAR) like '$value%' limit 20";
        return $this->getQueryRows($sql);
    }

    
    public function getPrivilegeSecFull($form_id,$element_id,$puser,$privilege='lesen'){
	$sql = "select id from resources where form_id='$form_id' and element_id='$element_id'";
	$rowsResources = $this->getQueryRows($sql);
	if($rowsResources===NULL){
	    // nic jsem k elementu nenasel, privilegium povolim
//	    echo "$element_id nenalezen<br>";
	    return TRUE;
	}
	else{
	    $eId = $rowsResources[0]['id'];
	}
	
//	echo "eId = $eId<br>";
	$sql = "select role_id,allowed from acl join privileges on privileges.id=acl.privilege_id where resource_id='$eId' and privileges.name='$privilege'";
	$rowsPrivilege = $this->getQueryRows($sql);
	if($rowsPrivilege!==NULL){
//	    AplDB::varDump($rowsPrivilege);
//	    echo "mam privilegia testuji dal<br>";
	    //mam nejaka privilegia testuji dal
	    //uzivatelovi role
	    $sql="select role_id from dbenutzerroles where benutzername='$puser'";
	    $rowsUserRoles = $this->getQueryRows($sql);
	    if($rowsUserRoles===NULL){
		//uzivatel nema zadnou roli, zadne privilegium nebude
//		echo "$puser nema zadnou roli<br>";
		return FALSE;
	    }
	    else{
		//uzivatel ma role
//		AplDB::varDump($rowsUserRoles);
		//projedu role s Y pro dane rousource a privilegium
		foreach ($rowsPrivilege as $pr){
		    if($pr['allowed']=='Y'){
			// projedu vsechny role uzivatele
			foreach($rowsUserRoles as $ur){
			    if($ur['role_id']==$pr['role_id']){
//				echo "nasel jsem shodu allowed=Y role_id=".$ur['role_id']."<br>";
				return TRUE;
			    }
			}
		    }
		}
		//projedu role s N pro dane rousource a privilegium
		foreach ($rowsPrivilege as $pr){
		    if($pr['allowed']=='N'){
			// projedu vsechny role uzivatele
			foreach($rowsUserRoles as $ur){
			    if($ur['role_id']==$pr['role_id']){
//				echo "nasel jsem shodu allowed=N role_id=".$ur['role_id']."<br>";
				return FALSE;
			    }
			}
		    }
		}
		return FALSE;
	    }
	}
	else{
	    //nic jsem pro privilegium a resource_id nenasel, => privilegium beru jako allowed
//	    echo "privilegium $privilege v acl nenalezeno<br>";
	    return TRUE;
	}
    }
    
    /**
     * pomoci privilegii budu jen zakazovat, pokud nezakazu je privilegium 
     * povolene
     * 
     * @param type $form_id
     * @param type $element_id
     * @param type $puser
     * @param type $privilege 
     */
    public function getPrivilegeSec($form_id,$element_id,$puser,$privilege='lesen'){
	$sql = " select dbenutzerroles.benutzername,dbenutzerroles.role_id,`privileges`.`name`,acl.allowed";
	$sql.= " from acl";
	$sql.= " join resources on resources.id=acl.resource_id";
	$sql.= " join dbenutzerroles on dbenutzerroles.role_id=acl.role_id";
	$sql.= " left join privileges on privileges.id=acl.privilege_id";
	$sql.= " where";
	$sql.= " resources.form_id='$form_id'";
	$sql.= " and resources.element_id='$element_id'";
	$sql.= " and dbenutzerroles.benutzername='$puser'";
	//$sql.= " and privileges.`name`='$privilege'";
	$sql.= " order by dbenutzerroles.role_id";

	$rows = $this->getQueryRows($sql);
	
//	echo "<pre>";var_dump($rows);echo "</pre>";
	
	
        if ($rows === NULL)
            return TRUE;
        else{
	    $rPA = array();
	    foreach ($rows as $row){
		$roleId = $row['role_id'];
		$allowed = $row['allowed'];
		$rPA[$roleId][$allowed] = $row['name'];
		$ra[$roleId] = TRUE;
	    }
	    
//	    echo "<pre>";var_dump($rPA);echo "</pre>";
	    
	    foreach ($rPA as $role=>$rp){
		foreach ($rp as $p=>$r){
		    if(($p=='N')&&($r==$privilege)) $ra[$role] = FALSE;
		}
	    }
	    
//	    echo "<pre>";var_dump($ra);echo "</pre>";
	    foreach ($ra as $r){
		if($r==TRUE) return TRUE;
	    }
	    return FALSE;
//	    // pro kazde role_id 
//	    if($rows[0]['allowed']=='N')
//		return FALSE;
//	    else
//		return TRUE;
	}

    }
    
    /**
     *
     * @param type $form_id
     * @param type $element_id
     * @param type $puser 
     */
    public function getDisplaySec($form_id,$element_id,$puser){
	
	$sql = " select dbenutzerroles.benutzername,acl.allowed";
	$sql.= " from acl";
	$sql.= " join resources on resources.id=acl.resource_id";
	$sql.= " join dbenutzerroles on dbenutzerroles.role_id=acl.role_id";
	$sql.= " where";
	$sql.= " resources.form_id='$form_id'";
	$sql.= " and resources.element_id='$element_id'";
	$sql.= " and dbenutzerroles.benutzername='$puser'";

	$rows = $this->getQueryRows($sql);
        if ($rows === NULL)
            return FALSE;
        else{
	    // musim projet vsechny radky, pokud narazim na allowed=Y vracim TRUE a koncim
	    foreach ($rows as $row){
		if($row['allowed']=='Y') return TRUE;
	    }
	    return FALSE;
	}
    }
    
    /**
     *
     * @param <type> $user
     * @param <type> $role
     * @return <type> 
     */
    public function userHasRole($user, $role) {
        $sql = " select";
        $sql.= " dbenutzerroles.benutzername";
        $sql.= " from dbenutzerroles";
        $sql.= " join roles on roles.id=dbenutzerroles.role_id";
        $sql.= " where";
        $sql.= " dbenutzerroles.benutzername='$user'";
        $sql.= " and roles.`name`='$role'";
        $rows = $this->getQueryRows($sql);
        if ($rows === NULL)
            return FALSE;
        else
            return TRUE;
    }

    
    /**
     * 
     * @param type $monat
     * @param type $jahr
     * @param type $persvon
     * @param type $persbis
     * @param type $stammOE  - bere i vzorek napr. G*11
     * @return array asoc. pole array("persnr"=>array("apremie"=>hodnota,"apremie_flag"=>"[V!]"))
     */
    public function getPersnrApremieArray($monat,$jahr,$persvon,$persbis,$stammOE,$calculateIfFlagNotTrue=TRUE) {
// vytahnu paramety z _GET ( z getparameters.php )
	$stammOE = strtoupper(strtr(trim($stammOE), '*', '%'));
	$persVon = $persvon;
	$persBis = $persbis;

	$a = $this;

	$von = $jahr . "-" . $monat . "-01";
	$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
	$bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;
	$datumVon = $von;
	$datumBis = $bis;

	$monthsArrayAll = array();
// vytvorim si pole mesico podle zadaneho rozsahu von a bis
	$start = strtotime($datumVon);
	$end = strtotime($datumBis);
	$increment = 60 * 60 * 24; // 1 den v sekundach
	while ($start <= $end) {
	    $year = date('y', $start);
	    $month = date('m', $start);
	    $yearMonth = "$year-$month";
	    $monthsArrayAll[$yearMonth]+=1;
	    $start+=$increment;
	}

	$monthsArray = array_keys($monthsArrayAll);
	sort($monthsArray);
	$mj = $monthsArray[0];

	$sql = "select dpers.PersNr as persnr from dpers";
	$sql.=" where (PersNr between '$persVon' and '$persBis') and (austritt is null or austritt<eintritt or datediff(now(),austritt)<=60) and (dpersstatus='MA' or dpersstatus='BEENDET')";
	$sql.=" and (kor=0)";

	if ((strlen($stammOE) > 0) && ($stammOE != '%')) {
	    $sql.=" and dpers.regeloe like '%$stammOE%'";
	}
	$sql.=" order by dpers.persnr";
	$persnrArray = $a->getQueryRows($sql);

	if ($persnrArray !== NULL) {
	    foreach ($persnrArray as $p) {
		$persnr = $p['persnr'];

		//loajalita ----------------------------------------------------------------
		$persInfoA = $a->getPersInfoArray($persnr);
		$zeilen[$persnr]['name'] = $persInfoA[0]['Name'] . ' ' . $persInfoA[0]['Vorname'];


		$zeilen[$persnr]['apremie_flag'] = $persInfoA[0]['a_praemie'] != 0 ? $persInfoA[0]['a_praemie_st'] != 0 ? '!' : 'V' : '';
		if($calculateIfFlagNotTrue===FALSE && $zeilen[$persnr]['apremie_flag']==''){
		    continue;
		}
		$regeloe = $persInfoA[0]['regeloe'];
		$zeilen[$persnr]['regeloe'] = $persInfoA[0]['regeloe'];

		$eintritt = $a->getEintrittsDatumDB($persnr);
		$zeilen[$persnr]['loajalita']['eintritt']['sum'] = date('y-m-d', strtotime($eintritt));
		$zeilen[$persnr]['loajalita']['austritt']['sum'] = strlen(trim($persInfoA[0]['austritt'])) == 0 ? '' : date('y-m-d', strtotime($persInfoA[0]['austritt']));
		$aTageFond = $a->getArbTageBetweenDatums($datumVon, $datumBis);
		$zeilen[$persnr]['loajalita']['von_bis_fond_days'] = $aTageFond;
		$zeilen[$persnr]['loajalita']['von_bis_fond_hours'] = $aTageFond * 8;


		// nacharbeit ---------------------------------------------------------------

		$sql = " select";
		$sql.=" drueck.PersNr as persnr";
		$sql.=" ,drueck.Datum as datum";
		//2016-07-08
		//$sql.=" ,sum(if(TaetNr>=6500 and TaetNr<=6599,if(auss_typ=4,abs(drueck.`Stück`+`Auss-Stück`)*`VZ-IST`,abs(drueck.`Stück`)*`VZ-IST`),0)) as vzaby_65xx";
		$sql.=" ,sum(if(TaetNr>=6500 and TaetNr<=6599,if(auss_typ=4,(drueck.`Stück`+`Auss-Stück`)*`VZ-IST`,(drueck.`Stück`)*`VZ-IST`),0)) as vzaby_65xx";
		$sql.=" ,sum(if(auss_typ=4,(drueck.`Stück`+`Auss-Stück`)*`VZ-SOLL`,(drueck.`Stück`)*`VZ-SOLL`)) as vzkd";
		$sql.=" from";
		$sql.=" drueck";
		$sql.=" where";
		$sql.=" PersNr='$persnr'";
		$sql.=" and Datum between '$datumVon' and '$datumBis'";
		$sql.=" group by";
		$sql.=" PersNr,";
		$sql.=" drueck.Datum";


		$persRows = $a->getQueryRows($sql);


		$monthsArray = array();
		if ($persRows !== NULL) {
		    foreach ($persRows as $pr) {
			//$persnr = $pr['persnr'];
			$datum = $pr['datum'];
			$month = date('m', strtotime($datum));
			$yearMonth = date('y-m', strtotime($datum));
			$monthsArray[$yearMonth]+=1;
			$vzaby_65xx = abs(floatval($pr['vzaby_65xx']));
			$vzkd = floatval($pr['vzkd']);
			$zeilen[$persnr]['nacharbeit']['vzaby_65xx'][$yearMonth]+=$vzaby_65xx;
			$zeilen[$persnr]['nacharbeit']['vzkd'][$yearMonth]+=$vzkd;
		    }

		    $monthsArray = array_keys($monthsArray);
		    sort($monthsArray);
		    foreach ($monthsArray as $yearMonth) {
			$vzaby_65xx = floatval($zeilen[$persnr]['nacharbeit']['vzaby_65xx'][$yearMonth]);
			$vzkd = floatval($zeilen[$persnr]['nacharbeit']['vzkd'][$yearMonth]);

			if (($vzkd != 0)) {
			    $zeilen[$persnr]['nacharbeit']['faktor'][$yearMonth] = ($vzaby_65xx / $vzkd) * 100;
			} else {
			    $zeilen[$persnr]['nacharbeit']['faktor'][$yearMonth] = '';
			}
		    }

		    //bewertung czk
		    $value = $zeilen[$persnr]['nacharbeit']['faktor'][$mj];
		    //echo "value: $value<br>";
		    $bew = $a->getBewertungKriteriumArray(100, 'q_nacharbeit', $value, 'bis', $mj, 1, $regeloe);
		    //AplDB::varDump($bew);
		    if ($bew == NULL) {
			$zeilen[$persnr]['nacharbeit']['faktor']['czk'] = '';
		    } else {
			$zeilen[$persnr]['nacharbeit']['faktor']['czk'] = $bew['betrag'];
		    }
		}
		//--------------------------------------------------------------------------
		// Ausschuss ---------------------------------------------------------------
		$sql = " select";
		$sql.="     drueck.PersNr as persnr,";
		$sql.="     drueck.Teil,";
		$sql.="     drueck.insert_stamp,";
		$sql.="     drueck.`Stück` as stk,";
		$sql.="     drueck.Datum as datum,";
		$sql.="     dkopf.Gew as teil_gew,";
		$sql.="     count(TaetNr) as tat_count,";
		$sql.="     sum(`Auss-Stück`) as stk_auss_sum";
		$sql.=" from";
		$sql.="     drueck";
		$sql.=" join dkopf on dkopf.Teil=drueck.Teil";
		$sql.=" where";
		$sql.="     PersNr='$persnr'";
		$sql.="     and Datum between '$datumVon' and '$datumBis'";
		$sql.="     and (DATE_FORMAT(`verb-von`,'%H:%i:%s')!='00:00:00')";
		$sql.=" group by";
		$sql.="     PersNr,";
		$sql.="     drueck.Teil,";
		$sql.="     drueck.insert_stamp,";
		$sql.="     drueck.`Stück`";

		$persRows = $a->getQueryRows($sql);

		$monthsArray = array();
		if ($persRows !== NULL) {
		    foreach ($persRows as $pr) {
			//$persnr = $pr['persnr'];
			$datum = $pr['datum'];
			$month = date('m', strtotime($datum));
			$yearMonth = date('y-m', strtotime($datum));
			$monthsArray[$yearMonth]+=1;
			$stkGut = intval($pr['stk']);
			$stkAuss = intval($pr['stk_auss_sum']);
			$gew = floatval($pr['teil_gew']);
			$zeilen[$persnr]['A6']['sum_gew'][$yearMonth]+=($stkGut + $stkAuss) * $gew;
		    }

		    $monthsArray = array_keys($monthsArray);
		    sort($monthsArray);
		    foreach ($monthsArray as $yearMonth) {
			$year = 2000 + intval(substr($yearMonth, 0, 2));
			$month = intval(substr($yearMonth, 3));
			$a6Gew = $a->getGewAussTypYearMonthPersnr(6, $year, $month, $persnr);
			$sumGew = floatval($zeilen[$persnr]['A6']['sum_gew'][$yearMonth]);
			$zeilen[$persnr]['A6']['a6_gew'][$yearMonth] = $a6Gew;

			if (($sumGew != 0)) {
			    $zeilen[$persnr]['A6']['a6_prozent'][$yearMonth] = ($a6Gew / $sumGew) * 100;
			} else {
			    $zeilen[$persnr]['A6']['a6_prozent'][$yearMonth] = '';
			}

			//vyhodnoceni pomoci kriterii
			//$value = $zeilen[$persnr]['A6']['a6_prozent'][$yearMonth];
			//$bew = $a->getBewertungKriterium(100,'q_auss',$value,'bis',$yearMonth,1);
		    }
		    //bewertung czk
		    $value = $zeilen[$persnr]['A6']['a6_prozent'][$mj];
		    //echo "value: $value<br>";
		    $bew = $a->getBewertungKriteriumArray(100, 'q_auss', $value, 'bis', $mj, 1, $regeloe);
		    //AplDB::varDump($bew);
		    if ($bew == NULL) {
			$zeilen[$persnr]['A6']['a6_prozent']['czk'] = '';
		    } else {
			$zeilen[$persnr]['A6']['a6_prozent']['czk'] = $bew['betrag'];
		    }
		}
		//--------------------------------------------------------------------------
		// reklamace ---------------------------------------------------------------
		$sql = " select";
		$sql.= " dpersschulung.persnr,";
		$sql.= " dreklamation.rekl_nr,";
		$sql.= "     dreklamation.rekl_datum,";
		$sql.= " dreklamation.interne_bewertung";
		$sql.= " from";
		$sql.= " dreklamation";
		$sql.= " join dpersschulung on dpersschulung.rekl_id=dreklamation.id";
		$sql.= " where";
		$sql.= " dreklamation.rekl_datum between '$datumVon' and '$datumBis'";
		$sql.= " and dpersschulung.persnr='$persnr'";
		$sql.= " and dpersschulung.rekl_verursacher<>0";
		$sql.= " group by";
		$sql.= " dpersschulung.persnr,";
		$sql.= " dreklamation.rekl_nr";

		$monthsArray = array();
		$persRows = $a->getQueryRows($sql);
		if ($persRows !== NULL) {
		    foreach ($persRows as $pr) {
			$datum = $pr['rekl_datum'];
			$month = date('m', strtotime($datum));
			$yearMonth = date('y-m', strtotime($datum));
			$monthsArray[$yearMonth]+=1;

			$ie = strtoupper(substr($pr['rekl_nr'], 0, 1));
			if ($ie == 'I') {
			    $zeilen[$persnr]['rekl']['sum_bewertung_I'][$yearMonth]+=$pr['interne_bewertung'];
			    //$zeilen[$persnr]['rekl']['bewertung_I'][$yearMonth] = 0;
			}
			if ($ie == 'E') {
			    $zeilen[$persnr]['rekl']['sum_bewertung_E'][$yearMonth]+=$pr['interne_bewertung'];
			    //$zeilen[$persnr]['rekl']['bewertung_E'][$yearMonth] = 0;
			}
		    }
		    //projit vsechny mesice pro vyhodnoceni kriterii
		    $monthsArray = array_keys($monthsArrayAll);
		    sort($monthsArray);
		    foreach ($monthsArray as $yearMonth) {
			//vyhodnoceni pomoci kriterii I
			$value = $zeilen[$persnr]['rekl']['sum_bewertung_I'][$yearMonth];
			if (intval($value) == 0) {
			    $zeilen[$persnr]['rekl']['sum_bewertung_I'][$yearMonth] = 0;
			}
			//$bew = $a->getBewertungKriterium(100, 'q_reklamationen', $value, 'bis', $yearMonth, 1);
			//$zeilen[$persnr]['rekl']['bewertung_I'][$yearMonth] = $bew;
			//vyhodnoceni pomoci kriterii E
			$value = $zeilen[$persnr]['rekl']['sum_bewertung_E'][$yearMonth];
			if (intval($value) == 0) {
			    $zeilen[$persnr]['rekl']['sum_bewertung_E'][$yearMonth] = 0;
			}
			//$bew = $a->getBewertungKriterium(100, 'q_reklamationen', $value, 'bis', $yearMonth, 1);
			//$zeilen[$persnr]['rekl']['bewertung_E'][$yearMonth] = $bew;
		    }
		}

		//bewertung czk
		$value = $zeilen[$persnr]['rekl']['sum_bewertung_I'][$mj];
		$bew = $a->getBewertungKriteriumArray(100, 'q_reklamationen_I', $value, 'bis', $mj, 1, $regeloe);
		//AplDB::varDump($bew);
		if ($bew == NULL) {
		    $zeilen[$persnr]['rekl']['sum_bewertung_I']['czk'] = '';
		} else {
		    $zeilen[$persnr]['rekl']['sum_bewertung_I']['czk'] = $bew['betrag'];
		}

		$value = $zeilen[$persnr]['rekl']['sum_bewertung_E'][$mj];
		$bew = $a->getBewertungKriteriumArray(100, 'q_reklamationen_E', $value, 'bis', $mj, 1, $regeloe);
		if ($bew == NULL) {
		    $zeilen[$persnr]['rekl']['sum_bewertung_E']['czk'] = '';
		} else {
		    $zeilen[$persnr]['rekl']['sum_bewertung_E']['czk'] = $bew['betrag'];
		}

		//dochazka -----------------------------------------------------------------
		$sql = " select";
		$sql.= " dzeit.PersNr as persnr,";
		$sql.= " dzeit.tat,";
		$sql.= " dtattypen.oestatus,";
		$sql.= " dzeit.Datum as datum,";
		$sql.=" sum(if(dtattypen.oestatus='a',dzeit.stunden,0)) as sum_stundena";
		$sql.= " from";
		$sql.= " dzeit";
		$sql.= " join dtattypen on dtattypen.tat=dzeit.tat";
		$sql.= " where";
		$sql.= " dzeit.persnr='$persnr'";
		$sql.= " and dzeit.datum between '$datumVon' and '$datumBis'";
		$sql.= " group by";
		$sql.= " dzeit.persnr,";
		$sql.= " dzeit.tat,";
		$sql.= " dzeit.Datum";

		$monthsArray = array();
		$persRows = $a->getQueryRows($sql);
		if ($persRows !== NULL) {
		    foreach ($persRows as $pr) {
			$datum = $pr['datum'];
			$month = date('m', strtotime($datum));
			$yearMonth = date('y-m', strtotime($datum));
			$monthsArray[$yearMonth]+=1;

			$zeilen[$persnr]['dzeit']['anwstd'][$yearMonth] += $pr['sum_stundena'];

			if ($pr['tat'] == 'd' || $pr['tat'] == 'n' || $pr['tat'] == 'np' || $pr['tat'] == 'nu' || $pr['tat'] == 'nv' || $pr['tat'] == 'nw' || $pr['tat'] == 'p' || $pr['tat'] == 'u' || $pr['tat'] == 'z' || $pr['tat'] == '?') {
			    // nacitat jen ty, ktere me zajimaji
			    $zeilen[$persnr]['dzeit'][$pr['tat']][$yearMonth]+=1;
			}
		    }
		}
		foreach ($monthsArrayAll as $yearMonth => $dayCount) {
		    $year = 2000 + intval(substr($yearMonth, 0, 2));
		    $month = intval(substr($yearMonth, 3));
		    $von = "$year-$month-01";
		    $bis = "$year-$month-$dayCount";
		    $arbTageProMonat = $a->getArbTageBetweenDatums($von, $bis);
		    $zeilen[$persnr]['dzeit']['astunden_fond'][$yearMonth] = $arbTageProMonat * 8;
		    $zeilen[$persnr]['dzeit']['anw_prozent'][$yearMonth] = $zeilen[$persnr]['dzeit']['astunden_fond'][$yearMonth] != 0 ? $zeilen[$persnr]['dzeit']['anwstd'][$yearMonth] / $zeilen[$persnr]['dzeit']['astunden_fond'][$yearMonth] * 100 : 0;
		}

		// leistung ----------------------------------------------------------------
		foreach ($monthsArrayAll as $yearMonth => $dayCount) {
		    $year = 2000 + intval(substr($yearMonth, 0, 2));
		    $month = intval(substr($yearMonth, 3));
		    $von = "$year-$month-01";
		    $bis = "$year-$month-$dayCount";
		    $arbTageProMonat = $a->getArbTageBetweenDatums($von, $bis);
		    $eintritt = $a->getEintrittsDatumDB($persnr);
		    if (strtotime($eintritt) > strtotime($von)) {
			$vonPers = $eintritt;
		    } else {
			$vonPers = $von;
		    }
		    $arbTagePersMonat = $a->getArbTageBetweenDatums($vonPers, $bis);
		    $dTage = $a->getTatTageBetweenDatums('d', $vonPers, $bis, $persnr);
		    $nwTage = $a->getTatTageBetweenDatums('nw', $vonPers, $bis, $persnr);
		    $monatNormMinuten = ($arbTagePersMonat - $dTage - $nwTage) * 8 * 60;
		    $ganzMonatNormMinuten = $arbTageProMonat * 8 * 60;
		    $leistungArray = $a->getPersLeistungArray($persnr, $von, $bis);
		    $persInfoA = $a->getPersInfoArray($persnr);
		    $leistFaktor = $persInfoA[0]['leistfaktor'];

		    if ($leistungArray !== NULL) {
			$vzaby = $leistungArray['vzaby'];
			$vzaby_akkord = $leistungArray['vzaby_akkord'];
			$vzaby_zeit = ($vzaby - $vzaby_akkord);
			//$vzaby_zeit = ($vzaby-$vzaby_akkord)*$leistFaktor;
		    } else {
			$vzaby = 0;
			$vzaby_akkord = 0;
			$vzaby_zeit = 0;
		    }
		    $zeilen[$persnr]['leistung']['leistGrad'][$yearMonth] = $zeilen[$persnr]['dzeit']['anwstd'][$yearMonth] != 0 ? ($vzaby_akkord + $vzaby_zeit) / ($zeilen[$persnr]['dzeit']['anwstd'][$yearMonth] * 60) * 100 : 0;
		}

		//ko_kriteria
		$koKriteriaArray[$persnr] = array();
		$value = $zeilen[$persnr]['dzeit']['z'][$mj];
		$bew = $a->getBewertungKriteriumArray(100, 'ko_dzeit_z', $value, 'bis', $mj, 1, 'abcd');
		if ($bew === NULL) {
		    $koKriteriaArray[$persnr]['ko_dzeit_z']['multi'] = 0;
		} else {
		    $koKriteriaArray[$persnr]['ko_dzeit_z']['multi'] = $bew['betrag'];
		}

		$value = $zeilen[$persnr]['dzeit']['anw_prozent'][$mj];
		//AplDB::varDump($value);
		$bew = $a->getBewertungKriteriumArray(100, 'ko_dzeit_anw_prozent', $value, 'von', $mj, 1, 'abcd');
		if ($bew === NULL) {
		    $koKriteriaArray[$persnr]['ko_dzeit_anw_prozent']['multi'] = 0;
		} else {
		    $koKriteriaArray[$persnr]['ko_dzeit_anw_prozent']['multi'] = $bew['betrag'];
		}

		//ko_a50
		$value = $zeilen[$persnr]['A6']['a6_prozent'][$mj];
		//AplDB::varDump($value);
		$bew = $a->getBewertungKriteriumArray(100, 'ko_a50', $value, 'bis', $mj, 1, 'abcd');
		if ($bew === NULL) {
		    $koKriteriaArray[$persnr]['ko_a50']['multi'] = 0;
		} else {
		    $koKriteriaArray[$persnr]['ko_a50']['multi'] = $bew['betrag'];
		}

		//sum_bewertung_E
		$value = $zeilen[$persnr]['rekl']['sum_bewertung_E'][$mj];
		//AplDB::varDump($value);
		$bew = $a->getBewertungKriteriumArray(100, 'ko_rekl_E', $value, 'bis', $mj, 1, 'abcd');
		if ($bew === NULL) {
		    $koKriteriaArray[$persnr]['ko_rekl_E']['multi'] = 0;
		} else {
		    $koKriteriaArray[$persnr]['ko_rekl_E']['multi'] = $bew['betrag'];
		}
	    }
	}

//AplDB::varDump($zeilen);
//exit();

	$gesammtSummePremie = 0;
	$persApremieArray = array();
	foreach ($zeilen as $persnr => $persZeile) {
	    $sumPremie = 0;
	    $sumPremie += floatval($persZeile['A6']['a6_prozent']['czk']);
	    $sumPremie += floatval($persZeile['nacharbeit']['faktor']['czk']);
	    $sumPremie += floatval($persZeile['rekl']['sum_bewertung_I']['czk']);
	    $sumPremie += floatval($persZeile['rekl']['sum_bewertung_E']['czk']);
	    $sumPremie = round(floatval($persZeile['leistung']['leistGrad'][$mj] / 100), 2) * $sumPremie;
	    $sumPremie *= floatval($koKriteriaArray[$persnr]['ko_dzeit_z']['multi']);
	    $sumPremie *= floatval($koKriteriaArray[$persnr]['ko_dzeit_anw_prozent']['multi']);
	    $sumPremie *= floatval($koKriteriaArray[$persnr]['ko_a50']['multi']);
	    $sumPremie *= floatval($koKriteriaArray[$persnr]['ko_rekl_E']['multi']);

	    if ($persZeile['apremie_flag'] == '') {
		$sumPremie = 0;
	    }
	    $gesammtSummePremie += round($sumPremie);
	    $persApremieArray[$persnr]['apremie'] = round($sumPremie);
	    $persApremieArray[$persnr]['apremie_flag'] = $persZeile['apremie_flag'];
	}
	return $persApremieArray;
    }

    /**
     * 
     * @param type $persnr
     * @return type
     */
    public function getPersInfoArray($persnr){
	$sql = "select * from dpers where persnr=$persnr";
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $persnr
     * @return type
     */
    public function getPersDetailInfoArray($persnr){
	$sql = "select * from dpersdetail1 where persnr=$persnr";
	return $this->getQueryRows($sql);
    }
    
    
    /**
     * 
     * @param type $value
     * @param type $ohneAustritt
     * @param type $pole
     * @return type
     */
    public function getPersonalArrayMatch($value, $ohneAustritt = TRUE, $pole = 0) {

        $sql = "select persnr,name,vorname,austritt from dpers where 1";
        if ($ohneAustritt === TRUE)
            $sql.= " and (austritt is null or austritt<eintritt)";
        if ($pole == 0)
            $sql.= " and (persnr=$value)";
        if ($pole == 1) {
            $sql.= " and (CONVERT(persnr,CHAR) like '$value%' or LOWER(name) like LOWER('%$value%') or LOWER(vorname) like LOWER('%$value%') )";
        }
        $sql.= " order by persnr";
        if ($pole == 1)
            $sql.= " limit 30";
        return array('sql'=>$sql,'rows'=>$this->getQueryRows($sql));
    }
    
    /**
     *
     * @param <type> $value
     * @param <type> $ohneAustritt
     * @param <type> $pole
     * @return <type>
     */
    public function getPersonalArray($value, $ohneAustritt = TRUE, $pole = 0) {

        $sql = "select persnr,name,vorname from dpers where 1";
	if ($ohneAustritt === TRUE){
	    $sql.= " and (austritt is null or austritt<eintritt)";
	    $sql.= " and (dpersstatus='MA')";
	}
            
        if ($pole == 0)
            $sql.= " and persnr=$value";
        if ($pole == 1) {
            $sql.= " and CONVERT(persnr,CHAR) like '$value%'";
        }
        $sql.= " order by persnr";
        if ($pole == 1)
            $sql.= " limit 30";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $repid
     * @param type $dbfield
     * @param type $value
     * @return type 
     */
    public function updateReparaturKopf($repid, $dbfield, $value) {
        $sql = "update dreparaturkopf set $dbfield='$value' where id='$repid' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param <type> $invnummer
     * @param <type> $datumDB
     * @param <type> $persnr_ma
     * @param <type> $persnr_reparatur 
     */
    public function insertReparaturKopf($invnummer, $datumDB, $persnr_ma, $persnr_reparatur, $user, $repzeit = 0, $bemerkung = '') {
        $sql = "insert into dreparaturkopf (invnummer,persnr_ma,persnr_reparatur,datum,repzeit,bemerkung,user)";
        $sql.=" values('$invnummer','$persnr_ma','$persnr_reparatur','$datumDB','$repzeit','$bemerkung','$user')";
        mysql_query($sql);
        return mysql_insert_id();
    }

    /**
     *
     * @param <type> $reparaturID
     */
    public function getReparaturPositionenArray($reparaturID) {
        $sql = " select";
        $sql.= " convert(dreparatur_et.artnr,CHAR) as artnr";
        $sql.= " ,`eink-artikel`.`art-name1` as name1";
        $sql.= " ,`eink-artikel`.`art-name2` as name2";
        $sql.= " ,dreparatur_ersatzteiltypen.typ as et_typ";
        $sql.= " ,if(dreparaturpos.anzahl is null,0,dreparaturpos.anzahl) as anzahl";
        $sql.= " ,if(dreparaturpos.et_alt is null,0,dreparaturpos.et_alt) as et_alt";
	$sql.= " ,if(dreparaturpos.et_invnummer is null,'',dreparaturpos.et_invnummer) as et_invnummer";
	$sql.= " ,if(dreparaturpos.bemerkung is null,'',dreparaturpos.bemerkung) as bemerkung";
        $sql.= " from dreparatur_et";
        $sql.= " join `eink-artikel` on convert(dreparatur_et.artnr,CHAR)=convert(`eink-artikel`.`art-nr`,CHAR)";
        $sql.= " join dreparatur_geraete on dreparatur_geraete.anlage_id=dreparatur_et.anlage_id";
        $sql.= " join dreparatur_ersatzteiltypen on dreparatur_ersatzteiltypen.ersatzteiltyp_id=dreparatur_et.et_typ_id";
        $sql.= " join dreparaturkopf on dreparaturkopf.invnummer=dreparatur_geraete.invnummer";
        $sql.= " left join dreparaturpos on dreparaturpos.reparatur_id=dreparaturkopf.id  and convert(dreparaturpos.artnr,CHAR)=convert(dreparatur_et.artnr,CHAR)";
        $sql.= " where";
        $sql.= " dreparaturkopf.id='$reparaturID'";
        $sql.= " order by dreparatur_ersatzteiltypen.typ,dreparatur_et.artnr";
        return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $reparaturID
     */
    public function getReparaturKopfArrayFromID($reparaturID){
	$sql = "select * from dreparaturkopf where id='$reparaturID'";
	$rows = $this->getQueryRows($sql); 
	return $rows[0];
    }
    /**
     *
     * @param <type> $invnummer
     * @param <type> $datumDB
     * @param <type> $persnr_ma
     * @param <type> $persnr_reparatur 
     */
    public function getReparaturKopfArray($invnummer, $datumDB, $persnr_ma, $persnr_reparatur) {
        $sql = "select id,repzeit,bemerkung from dreparaturkopf where invnummer='$invnummer' and persnr_ma='$persnr_ma' and persnr_reparatur='$persnr_reparatur' and datum='$datumDB' limit 1";
        return $this->getQueryRows($sql);
    }

    
    /**
     * 
     */
    public function getEinkaufAnforderungenArray($u,$all=TRUE,$mitFertigen=TRUE){
	$sql = "select";
	$sql.=" id,stamp,SUBSTRING(user,LOCATE('/',user)+1) as login,anftyp,artikel,anzahl,user,bemerkung,abdatum,prio,status,lieferdatum,erledigt";
	$sql.=" from einkauf_anforderungen";
	$sql.=" where (1)";
	
	if($all!==TRUE){
	    $sql.=" and (user like '%$u')";
	}
	if($mitFertigen===FALSE){
	    $sql.=" and (status not like 'z%' or status is null)";
	}
	$sql.=" order by id desc";
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param <type> $value
     * @param <type> $pole 
     */
    public function getBehaelterArray($value, $pole = 0) {
        if ($pole == 0) {
            $sql = "select";
            $sql.= " convert(`eink-artikel`.`art-nr`,char) as artnr,";
            $sql.= " `eink-artikel`.`art-name1` as name1";
            $sql.= " from `eink-artikel`";
            $sql.= " where";
            $sql.=" `eink-artikel`.`art-grp-nr`=1170";
            $sql.=" and convert(`eink-artikel`.`art-nr`,CHAR)='$value'";
            $sql.= " order by";
            $sql.= " `eink-artikel`.`art-nr`";
        }
        if ($pole == 1) {
            $sql = "select";
            $sql.= " convert(`eink-artikel`.`art-nr`,char) as artnr,";
            $sql.= " `eink-artikel`.`art-name1` as name1";
            $sql.= " from `eink-artikel`";
            $sql.= " where";
            $sql.=" `eink-artikel`.`art-grp-nr`=1170";
            $sql.=" and convert(`eink-artikel`.`art-nr`,CHAR) like '$value%'";
            $sql.= " order by";
            $sql.= " `eink-artikel`.`art-nr`";
        }
//        echo $sql;
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $value
     * @return type 
     */ 
    public function getFreigabeVom($value){
	$sql = "select id,name from mustervom where ucase(name) like ucase('%$value%') order by name";
	return $this->getQueryRows($sql);
    }
    
    public function getZielortName($id){
	$sql = "select id,zielort from zielorte where id='$id'";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return $r[0]['zielort'];
	}
	else{
	    return '';
	}
    }
    
    /**
     * 
     * @param type $kunde
     * @param type $standard
     * @return type
     */
    public function getZielortStandardInfoArray($kunde,$standard=10){
	$sql = "select * from zielorte where kunde='$kunde' and standard='$standard'";
	return $this->getQueryRows($sql);
    }
    /**
     * 
     * @param type $zielortid
     */
    public function getZielortInfoArray($zielortid){
	$sql = "select * from zielorte where id='$zielortid'";
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param type $kd
     * @param type $value
     * @return type 
     */
    public function getZielorteArray($kd,$value=NULL){
	if($value===NULL){
	    $sql = "select id,zielort from zielorte where kunde=$kd order by zielort";
	}
	else{
	    $sql = "select id,zielort from zielorte where kunde=$kd and zielort like '%$value%' order by zielort";
	}
	return $this->getQueryRows($sql);
    }

    public function updateDMAField($dmaid,$field,$value){
	$sql = "update dma set `$field`='$value' where id='$dmaid' limit 1";
	return $this->query($sql);
    }
    /**
     * 
     */
    public function getLastEMANr($kunde=NULL){
	$sql = "select emanr from dma order by emanr desc";
	if($kunde!==NULL)
	    $sql = "select emanr from dma where emanr like 'EMA_".$kunde."_%'order by emanr desc";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return 0;
	$row = $rows[0];
	$emanr = $row['emanr'];
	$cislo = intval(substr($emanr, strrpos($emanr, '_')+1));
	return $cislo;
    }
    /**
     *
     * @param type $imaid 
     */
    public function getIMAInfoArray($imaid=NULL,$emanr=NULL){
	$sql = "select * from dma where id='$imaid'";
	if($imaid===NULL){
	    if($emanr!==NULL)
		$sql = "select * from dma where emanr='$emanr'";
	}
	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $imanr
     * @return type
     */
    public function getIMAInfoArrayFromImaNr($imanr){
	$sql = "select * from dma where imanr='$imanr'";
	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $value
     * @return type
     */
    public function getArbMittelArray($value=NULL){
	if($value===NULL){
	    $sql = "select id,nazev,poznamka from dmittel order by nazev";
	}
	else{
	    $sql = "select id,nazev,poznamka from dmittel where nazev like '%$value%' order by nazev";
	}
	
	return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $value
     * @return type 
     */
    public function getVPMArray($value=NULL){
	if($value===NULL){
	    $sql = "select `art-nr` as verp,`art-name1` as name1,`art-name2` as name2,`art-name3` as name3 from `eink-artikel` where (`art-grp-nr`=1170 or `art-grp-nr`=1175) order by `art-nr`";
	}
	else{
	    $sql = "select `art-nr` as verp,`art-name1` as name1,`art-name2` as name2,`art-name3` as name3 from `eink-artikel` where (`art-grp-nr`=1170 or `art-grp-nr`=1175) and (`art-nr` like '$value%') order by `art-nr`";
	}
	
	return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $value
     * @return type 
     */
    public function getDokuTypArray($value=NULL){
	if($value===NULL){
	    $sql = "select doku_nr,doku_beschreibung from dokumenttyp order by doku_nr";
	}
	else{
	    $sql = "select doku_nr,doku_beschreibung from dokumenttyp where doku_nr like '$value%' order by doku_nr";
	}
	
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param <type> $value
     * @param <type> $pole
     * @return <type>
     */
    public function getReparaturGeraeteArray($value, $pole = 0) {
        if ($pole == 0) {
            $sql = "    select dreparatur_geraete.invnummer,dreparatur_anlagen.anlage_beschreibung as anlage";
            $sql.= " from `dreparatur_geraete`";
            $sql.= " join dreparatur_anlagen on dreparatur_anlagen.anlage_id=dreparatur_geraete.anlage_id";
            $sql.= " where invnummer='$value'";
            $sql.= " order by invnummer";
        }
        if ($pole == 1) {
            $sql = "    select dreparatur_geraete.invnummer,dreparatur_anlagen.anlage_beschreibung as anlage";
            $sql.= " from `dreparatur_geraete`";
            $sql.= " join dreparatur_anlagen on dreparatur_anlagen.anlage_id=dreparatur_geraete.anlage_id";
            $sql.= " where CONVERT(invnummer,CHAR) like '$value%'";
            $sql.= " order by invnummer";
            $sql.= " limit 30";
        }
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $kunde 
     */
    public function getBehaelterKundeMitInventur($kunde) {
        $sql = " select behaelternr,DATE_FORMAT(max(datum),'%d.%m.%Y') as maxinvdatum,`eink-artikel`.`art-name1` as name";
        $sql.= " from dbehaelterinventur";
        $sql.= " join `eink-artikel` on `eink-artikel`.`art-nr`=dbehaelterinventur.behaelternr";
        $sql.= " where";
        $sql.= " kunde='$kunde'";
        $sql.= " and zustand_id=9999";
        $sql.= " and platz_id='KDKONTO'";
        $sql.= " group by behaelternr";
        $rows = $this->getQueryRows($sql);
        return $rows;
    }

    /**
     *
     * @param <type> $auftrag
     * @param <type> $imex 0 = auftrag je import,1=auftrag je export
     * @return <type>
     */
    public function getBehBewFuerImEx($auftrag, $imex = 0) {
        if ($imex == 0) {
            $sql = "select id,behaelternr,DATE_FORMAT(dbehbew.datum,'%Y-%m-%d') as datum,dbehbew.zustand_id,inhalt_id,dbz1.zustand_text as zustandtext,dbz2.zustand_text as inhalttext,stk";
            $sql.=" ,`eink-artikel`.`art-name1` as behtext ";
            $sql.=" from dbehbew";
            $sql.=" join `eink-artikel` on convert(`eink-artikel`.`art-nr`,char)=convert(dbehbew.behaelternr,char)";
            $sql.=" left join dbehzustand dbz1 on dbz1.zustand_id=dbehbew.zustand_id left join dbehzustand dbz2 on dbz2.zustand_id=dbehbew.inhalt_id where import=$auftrag";
            $sql.=" order by dbehbew.stamp desc";
        }
        if ($imex == 1) {
            $sql = "select id,behaelternr,DATE_FORMAT(dbehbew.datum,'%Y-%m-%d') as datum,dbehbew.zustand_id,inhalt_id,dbz1.zustand_text as zustandtext,dbz2.zustand_text as inhalttext,stk";
            $sql.=" ,`eink-artikel`.`art-name1` as behtext ";
            $sql.=" from dbehbew";
            $sql.=" join `eink-artikel` on convert(`eink-artikel`.`art-nr`,char)=convert(dbehbew.behaelternr,char)";
            $sql.=" left join dbehzustand dbz1 on dbz1.zustand_id=dbehbew.zustand_id left join dbehzustand dbz2 on dbz2.zustand_id=dbehbew.inhalt_id where export=$auftrag";
            $sql.=" order by dbehbew.stamp desc";
        }
//        echo $sql;
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $auftrag
     * @param int $imex - 0 = auftrag je import, 1 = auftraf je export
     */
    public function getBehaelterBewegungenFuerImEx($auftrag, $imex = 0) {
        if ($imex == 0)
            $sql = "select behaelternr,zustand_id,sum(stk) as stk from dbehaelterbew where import=$auftrag group by behaelternr,zustand_id";
        if ($imex == 1)
            $sql = "select behaelternr,zustand_id,sum(stk) as stk from dbehaelterbew where export=$auftrag group by behaelternr,zustand_id";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $termin 
     */
    public function getZielortAuftrag($termin){
	$sql.=" select if(zielorte.zielort is null,'',zielorte.zielort) as zielort";
	$sql.=" from daufkopf";
	$sql.=" left join zielorte on zielorte.id=daufkopf.zielort_id";
	$sql.=" where";
	$sql.=" auftragsnr=$termin";
	$r = $this->getQueryRows($sql);
	if($r===NULL) return '';
	return $r[0]['zielort'];
    }
    
    /**
     * 
     * @param type $export
     */
    public function getLieferscheinRows($export,$maxrows){
	$sql.=" select";
	$sql.=" dauftr.teil,";
	$sql.=" dauftr.fremdauftr,";
	$sql.=" dauftr.fremdpos,";
	$sql.=" dauftr.`mehrarb-kz` as tatkz,";
	$sql.=" dtaetkz.Rechtext,";
	$sql.=" sum(dauftr.`stk-exp`) as gut_stk_ex,";
	$sql.=" sum(if(dauftr.KzGut='G',dauftr.`stk-exp`,0)) as stk_geliefert_G,";
	$sql.=" sum(dauftr.auss2_stk_exp) as auss2,";
	$sql.=" sum(dauftr.auss4_stk_exp) as auss4,";
	$sql.=" sum(dauftr.auss6_stk_exp) as auss6";
	$sql.=" from dauftr";
	$sql.=" join dtaetkz on dtaetkz.Dtaetkz=dauftr.`mehrarb-kz`";
	$sql.=" where";
	$sql.=" dauftr.`auftragsnr-exp`='$export'";
	$sql.=" group by";
	$sql.=" dauftr.teil,";
	$sql.=" dauftr.fremdauftr,";
	$sql.=" dauftr.fremdpos,";
	$sql.=" dauftr.`mehrarb-kz`";
	$sql.=" limit $maxrows";
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param <type> $auftrag
     */
    public function getAuftragInfoArray($auftrag,$kunde=NULL,$match=FALSE) {
	if($kunde===NULL)
	    $sql = "select  waehr_kz,zielort_id,im_stk_gespeichert,if(fertig='2100-01-01','0','1') hatrechnung,DATE_FORMAT(fertig,'%Y-%m-%d') as fertigdat1,DATE_FORMAT(ausliefer_datum,'%H:%i') as auslieferuhr1,DATE_FORMAT(ausliefer_datum,'%Y-%m-%d') as auslieferdat1,DATE_FORMAT(ex_datum_soll,'%H:%i') as exsolluhr1,DATE_FORMAT(ex_datum_soll,'%Y-%m-%d') as exsolldat1,DATE_FORMAT(im_datum_soll,'%H:%i') as imsolluhr1,DATE_FORMAT(im_datum_soll,'%Y-%m-%d') as imsolldat1,DATE_FORMAT(aufdat,'%H:%i') as aufuhr1,DATE_FORMAT(aufdat,'%Y-%m-%d') as aufdat1,fertig as fertig_raw,ausliefer_datum as ausliefer_raw,aufdat as aufdat_raw,im_datum_soll as im_soll_datetime,ex_datum_soll as ex_soll_datetime,bestellnr,zielort_id,auftragsnr,bemerkung,kunde,minpreis,DATE_FORMAT(aufdat,'%d.%m.%Y') as aufdat,DATE_FORMAT(ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(ex_datum_soll,'%d.%m.%Y') as ex_soll_datum,DATE_FORMAT(ex_datum_soll,'%H:%i') as ex_soll_uhrzeit from daufkopf where auftragsnr=$auftrag";
	else
	    $sql = "select  fertig as fertig_raw,ausliefer_datum as ausliefer_raw,aufdat as aufdat_raw,im_datum_soll as im_soll_datetime,ex_datum_soll as ex_soll_datetime,bestellnr,zielort_id,auftragsnr,bemerkung,kunde,minpreis,DATE_FORMAT(aufdat,'%d.%m.%Y') as aufdat,DATE_FORMAT(ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(ex_datum_soll,'%d.%m.%Y') as ex_soll_datum,DATE_FORMAT(ex_datum_soll,'%H:%i') as ex_soll_uhrzeit from daufkopf where auftragsnr=$auftrag and kunde='$kunde'";
	
	if($match===TRUE){
	    $sql = "select  fertig as fertig_raw,ausliefer_datum as ausliefer_raw,aufdat as aufdat_raw,im_datum_soll as im_soll_datetime,ex_datum_soll as ex_soll_datetime,bestellnr,zielort_id,auftragsnr,bemerkung,kunde,minpreis,DATE_FORMAT(aufdat,'%Y-%m-%d') as aufdat1,DATE_FORMAT(aufdat,'%d.%m.%Y') as aufdat,DATE_FORMAT(ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(ex_datum_soll,'%d.%m.%Y') as ex_soll_datum,DATE_FORMAT(ex_datum_soll,'%H:%i') as ex_soll_uhrzeit from daufkopf where auftragsnr like '$auftrag%' limit 100";	    
	}
        //echo $sql;
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $ex
     * @param type $im
     * @param type $teil 
     */
    public function getExpStkExImTeil($ex,$im,$teil){
	$sql.=" select sum(dauftr.`stk-exp`) as sumexp";
	$sql.=" from";
	$sql.=" dauftr";
	$sql.=" where";
	$sql.=" dauftr.`auftragsnr-exp`=$ex";
	$sql.=" and dauftr.auftragsnr=$im";
	$sql.=" and teil='$teil'";
	$sql.=" and KzGut='G'";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return 0;
	return intval($rows[0]['sumexp']);
    }
    
    /**
     * 
     */
    public function getBehaelterLagerplatzArray($mitKdKonto = 0, $platz = NULL) {
        if ($platz === NULL) {
            if ($mitKdKonto == 0)
                $sql = "select platz_id from dbehaelterlagerplatz where platz_id<>'KDKONTO' order by platz_id";
            else
                $sql = "select platz_id from dbehaelterlagerplatz order by platz_id";
        }
        else {
            if ($mitKdKonto == 0)
                $sql = "select platz_id from dbehaelterlagerplatz where platz_id<>'KDKONTO' and platz_id like '$platz%' order by platz_id";
            else
                $sql = "select platz_id from dbehaelterlagerplatz where platz_id like '$platz%' order by platz_id";
        }

        return $this->getQueryRows($sql);
    }

    /**
     * 
     */
    public function getBehaelterZustandD710Array() {
        $sql = "select zustand_id,zustand_text from dbehaelterzustand where D710_order>0 order by D710_order";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $mitKdKonto
     * @param type $nurEingabe
     * @param type $zustandTyp
     * @return type 
     */
    public function getBehaelterStandArray($mitKdKonto = 0, $nurEingabe = TRUE, $zustandTyp = NULL) {

        $eingabeWhere = '';
        $zustandTypWhere = '';
        $order = 'order by zustand_id';

        if ($nurEingabe === TRUE) {
            $eingabeWhere = " and eingabe_order>0";
            $order = 'order by eingabe_order,zustand_id';
        }

        if ($zustandTyp !== NULL) {
            $zustandTypWhere = " and zustand_typ='$zustandTyp'";
        }
        if ($mitKdKonto == 0)
            $sql = "select zustand_id,zustand_text from dbehaelterzustand where zustand_id<9999 $eingabeWhere $zustandTypWhere $order";
        else
            $sql = "select zustand_id,zustand_text from dbehaelterzustand where 1 $eingabeWhere $zustandTypWhere $order";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $term
     * @param type $zustandTyp
     * @return type 
     */
    public function getBehZustandArray($term, $zustandTyp) {

        $sql = "select zustand_id,zustand_text from dbehzustand where zustand_typ='$zustandTyp' and convert(zustand_id,char) like '$term%' order by zustand_id";
//       echo $sql;
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $termin 
     */
    public function getAuftragZeilenProTermin($termin) {
        $query = "select dauftr.stk_laut_waage,dauftr.auss_stk_laut_waage,dauftr.auss_abywaage_kg_stk10,dauftr.auss_abywaage_behaelter_ist,dauftr.auss_abywaage_brutto,dauftr.auss_behaelter_id,dauftr.auss2_stk_exp as auss2,dauftr.auss4_stk_exp as auss4,dauftr.auss6_stk_exp as auss6,dbehaelter.typ as behtyp,dkopf.teillang as platte,dauftr.id_dauftr as id,dauftr.auftragsnr as import,dauftr.teil,dauftr.`pos-pal-nr` as pal,dauftr.exportbehaelter,dauftr.kunde_behaelter_bestellung_netto,dauftr.`stück` as stkimport,dauftr.`stk-exp` as stkexp,dauftr.kg_stk_bestellung,dauftr.abywaage_kg_stk10,dauftr.abywaage_behaelter_ist,dauftr.abywaage_brutto,dauftr.behaelter_id,dauftr.aussbehaelter from dauftr ";
        $query .=" join dkopf on dkopf.teil=dauftr.teil";
        $query .=" left join dbehaelter on dbehaelter.id=dauftr.behaelter_id";
        $query .=" where dauftr.termin='$termin' and dauftr.`KzGut`='G' order by dauftr.auftragsnr,dauftr.teil,dauftr.`pos-pal-nr`";
        $res = mysql_query($query);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($res))
                array_push($rows, $row);
            return $rows;
        }
    }

    public function updateIntoTableText($itid,$fieldName,$value){
        $sql = "update dinfotable set $fieldName='$value' where id='$itid' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }
    
    /**
     *
     * @param type $kunde 
     */
    public function getTermineRowsArray($kunde){
	$sql.=" select ";
	$sql.=" dauftr.termin,";
	$sql.=" da1.ausliefer_datum,";
	$sql.=" da1.ex_datum_soll,";
	$sql.=" da1.bemerkung,";
	$sql.=" da1.zielort_id,";
	$sql.=" zielorte.zielort";
	$sql.=" from dauftr";
	$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
	$sql.=" left join daufkopf da1 on da1.auftragsnr=substring(dauftr.termin,2)";
	$sql.=" left join zielorte on da1.zielort_id=zielorte.id";
	$sql.=" where";
	$sql.=" daufkopf.kunde='$kunde'";
	$sql.=" and (dauftr.`auftragsnr-exp` is null and dauftr.`pal-nr-exp` is null)";
	$sql.=" group by";
	$sql.=" dauftr.termin";
	$sql.=" having (termin is not null and length(termin)>0)";

	return $this->getQueryRows($sql);
    }
    
    /**
     *
     * @param type $ipKlienta
     * @return type 
     */
    public function getInfoTabloTextArray($ipKlienta=NULL,$table_id=NULL) {
        if ($ipKlienta !== NULL) {
            $sql = "";
            $sql.=" select";
            $sql.=" dinfotable.text1,";
            $sql.=" dinfotable.text2,";
            $sql.=" dinfotable.text3,";
	    $sql.=" dinfotable.text4,";
	    $sql.=" dinfotable.text5";
            $sql.=" from";
            $sql.=" dinfotable";
            $sql.=" join dinfopanel on dinfopanel.dinfotable_id=dinfotable.id";
            $sql.=" where";
            $sql.=" dinfopanel.ip='$ipKlienta'";
        } else {
	    if($table_id===NULL){
		$sql.=" select";
		$sql.=" dinfopanel.idpanel,";
		$sql.=" dinfopanel.dinfotable_id as itid,";
		$sql.=" dinfotable.text1,";
		$sql.=" dinfotable.text2,";
		$sql.=" dinfotable.text3,";
		$sql.=" dinfotable.text4,";
		$sql.=" dinfotable.text5";
		$sql.=" from dinfopanel";
		$sql.=" left join dinfotable on dinfotable.id=dinfopanel.dinfotable_id";
		$sql.=" order by dinfopanel.idpanel";
	    }
	    else{
		$sql.=" select";
		$sql.=" dinfotable.id,";
		$sql.=" dinfotable.text1,";
		$sql.=" dinfotable.text2,";
		$sql.=" dinfotable.text3,";
		$sql.=" dinfotable.text4,";
		$sql.=" dinfotable.text5";
		$sql.=" from dinfotable";
		$sql.=" where dinfotable.id=$table_id";
	    }
        }
        return $this->getQueryRows($sql);
    }

    /**
     * return array of abgnr smaller then $abgnr, sorted descendend, abgnr must be active
     * @param <type> $teil
     * @param <type> $abgnr
     */
    public function getAbgNrArrayForTeilKleinerAls($teil, $abgnr) {
        $query = "select dpos.`TaetNr-Aby` as abgnr from dpos where dpos.`Teil`='$teil' and dpos.`kz-druck`<>0 and dpos.`TaetNr-Aby`<'$abgnr' order by dpos.`TaetNr-Aby` desc";
        $res = mysql_query($query);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($res))
                array_push($rows, $row);
            return $rows;
        }
    }

    /**
     *
     * @param <type> $datumDB
     * @param <type> $persnr
     * @return <type>
     */
    public function getTransportArrayDatumPersnr($datumDB, $persnr) {
        $sql = "select";
        $sql.=" dperstransport.id,";
        $sql.=" dperstransport.persnr,";
        $sql.=" dperstransport.preis,";
        $sql.=" dperstransport.kfz";
        $sql.=" from";
        $sql.=" dperstransport";
        $sql.=" where";
        $sql.=" dperstransport.persnr='$persnr' and dperstransport.datum='$datumDB'";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $teilNr
     * @return <type>
     */
    public function getGAbgNrForTeil($teilNr) {
        $query = "select dpos.`TaetNr-Aby` as abgnr from dpos where dpos.`Teil`='$teilNr' and dpos.`kz-druck`<>0 and dpos.`KzGut`='G' order by dpos.`TaetNr-Aby` limit 1";
        $res = mysql_query($query);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $row = mysql_fetch_assoc($res);
            $abgnr = $row['abgnr'];
            return $abgnr;
        }
    }

    /**
     *
     * @param type $von
     * @param type $bis 
     */
    public function getTatInfoArray($von,$bis){
	$sql = "select `dtaetkz-abg`.`abg-nr` as tatnr,`dtaetkz-abg`.`Name` as tatbez from `dtaetkz-abg` where `dtaetkz-abg`.`abg-nr` between '$von' and '$bis' order by `dtaetkz-abg`.`abg-nr`";
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param type $teil
     * @param type $im
     * @param type $term 
     */
    public function getPaletteMitAuftragTeil($term, $im = NULL, $teil = NULL,$ohneEx=FALSE) {
	if (($im === NULL) || ($teil === NULL))
	    return NULL;
	$sql = "select distinct `pos-pal-nr` as pal from dauftr where (auftragsnr='$im') and (teil='$teil') and (`pos-pal-nr' like '$term%')";
	if ($term == ''){
	    $sql = "select id_dauftr as id,`pos-pal-nr` as pal,`stück` as stk,fremdpos from dauftr where (auftragsnr='$im') and (teil='$teil') and (kzgut='G')";
	    if($ohneEx===TRUE)
		$sql = "select id_dauftr as id, `pos-pal-nr` as pal,auftragsnr from dauftr where (auftragsnr='$im') and (teil='$teil') and (kzgut='G') and (`auftragsnr-exp` is null) and (`pal-nr-exp` is null)";
		//return $sql;
	}
	    

	$res = mysql_query($sql);
	if (mysql_affected_rows() == 0 || $res === FALSE)
	    return NULL;
	else {
	    $rows = array();
	    while ($row = mysql_fetch_assoc($res))
		array_push($rows, $row);
	    return $rows;
	}
    }

    /**
     *
     * @param type $teil
     * @param type $term 
     */
    public function getImporteMitTeil($teil=NULL,$term=NULL,$ohneEx = FALSE){
	$sql = "select distinct auftragsnr from dauftr where (auftragsnr like '$term%') and (teil='$teil') order by auftragsnr";
	if($term==''){
	    $sql = "select distinct auftragsnr from dauftr where (teil='$teil') order by auftragsnr";
	    if($ohneEx===TRUE)
		$sql = "select distinct auftragsnr from dauftr where (teil='$teil') and (`auftragsnr-exp` is null) order by auftragsnr";
	}
	if($teil===NULL){
	    $sql = "select auftragsnr from daufkopf where (auftragsnr like '$term%') order by auftragsnr";
	}
	
	$res = mysql_query($sql);
        if (mysql_affected_rows() == 0 || $res===FALSE)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($res))
                array_push($rows, $row);
            return $rows;
        }
    }
    
    /**
     * 
     * @param type $t
     * @return type
     */
    public function getBehDataArray($t) {
	$sql.=" select ";
	$sql.=" dauftr.teil,";
	$sql.="     dauftr.auftragsnr as import,";
	$sql.=" dauftr.`pos-pal-nr` as impal,";
	$sql.=" dauftr.`stück` as imstk,";
	$sql.=" dauftr.termin";
	$sql.=" from dauftr";
	$sql.=" where";
	$sql.=" (dauftr.KzGut='G')";
	$sql.=" and (dauftr.teil='$t')";
	$sql.=" and (dauftr.`auftragsnr-exp` is null)";
	$sql.=" order by";
	$sql.=" dauftr.teil,";
	$sql.=" dauftr.`pos-pal-nr`";
	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $k
     * @param type $e
     */
    public function getTeilArrayForKundeMatch($k,$e){
	$sql.=" select dkopf.teil,";
	$sql.=" dkopf.teillang,";
	$sql.=" dkopf.gew,";
	$sql.=" dkopf.Teilbez as teilbez,";
	$sql.=" dkopf.kunde,";
	$sql.=" dkopf.status";
	$sql.=" from dkopf";
	$sql.=" where";
	$sql.=" (dkopf.Kunde='$k')";
	$sql.=" and (dkopf.teil like '%$e%' or dkopf.teilbez like '%$e%'  or dkopf.teillang like '%$e%')";
	$sql.=" order by";
	$sql.=" dkopf.Kunde,";
	$sql.=" dkopf.Teil";

	return $this->getQueryRows($sql);
	
    }
    /**
     * 
     * @param type $kunde
     * @param type $teil
     * @param type $ohneEx
     */
    public function getTeileArrayForKundeMatch($kunde, $teil, $ohneEx = TRUE) {
	$sql.=" select distinct";
	$sql.=" dkopf.teil,";
	$sql.=" dkopf.teillang,";
	$sql.=" dkopf.Teilbez as teilbez,";
	$sql.=" dkopf.kunde,";
	$sql.=" dkopf.verpackungmenge";
	$sql.=" from dkopf";
	$sql.=" join dauftr on dauftr.teil=dkopf.Teil";
	$sql.=" where";
	$sql.=" (dkopf.Kunde=$kunde)";
	if ($ohneEx === TRUE) {
	    $sql.=" and (dauftr.`auftragsnr-exp` is null)";
	}
	$sql.=" and (dkopf.teil like '%$teil%' or dkopf.teilbez like '%$teil%')";
	$sql.=" order by";
	$sql.=" dkopf.Kunde,";
	$sql.=" dkopf.Teil";

	return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $kunde
     * @return array 
     */
    public function getTeileNrArrayForKunde($kunde=NULL,$teil=NULL) {
        $query = "select dkopf.`Teil` as teil,teillang from dkopf where dkopf.`Kunde`='$kunde' order by dkopf.`Teil`";
	if($kunde===NULL)
	    $query = "select dkopf.`Teil` as teil,teillang,kunde,teilbez,verpackungmenge from dkopf where (teil like '%$teil%') order by dkopf.kunde,dkopf.`Teil`";
        $res = mysql_query($query);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($res))
                array_push($rows, $row);
            return $rows;
        }
    }

    /**
     *
     * @param type $val 
     */
    public function validateZeit($val){
	$returnValue = "00:00";
	// test na prazdnou hodnotu
	if(strlen((trim($val)))==0) return $returnValue;
	// zkusim najit oddelovac hodin a minut
	if(($pozice=strpos($val,":"))!==FALSE){
	    //nasel jsem oddelovac, oddelim hodiny a minuty
	    list($hodiny,$minuty) = explode(":", $val);
	    $hodinyVal = intval($hodiny);
	    $minutyVal = intval($minuty);
	}
	else{
	    // nemam oddelovac, delka musi byt alespon 3 znaky
	    if(strlen(trim($val))>=3){
		// budu nacitat odzadu, tj. nejdriv minuty 2 znaky, co zbyde budou hodiny
		$val = trim($val);
		$minuty = substr($val, -2);
		$hodiny = substr($val, 0,strlen($val)-2);
		$hodinyVal = intval($hodiny);
		$minutyVal = intval($minuty);
	    }
	    else{
		$hodinyVal=0;
		$minutyVal=0;
	    }
	}
	
	// ted mam v promennych nejaka cisl, zkontroluju jejich rozsahy
	if(($hodinyVal>=0) && ($hodinyVal<=23) && ($minutyVal<=59) && ($minutyVal>=0)){
	    $returnValue = sprintf("%02d:%02d",$hodinyVal,$minutyVal);
	}
	
	return $returnValue;
    }
    
    /**
     *
     * @param <type> $dbField
     * @param <type> $value 
     */
    public function updateDaufkopfField($dbField, $value, $auftrag) {
	if(($dbField=='ex_datum_soll') && ($value==NULL))
	    $sql = "update daufkopf set $dbField=NULL where auftragsnr=$auftrag limit 1";
	else
	    $sql = "update daufkopf set $dbField='$value' where auftragsnr=$auftrag  limit 1";
	
	if($value===NULL)
	    $sql = "update daufkopf set $dbField=NULL where auftragsnr=$auftrag limit 1";
	
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param type $dbField
     * @param type $value
     * @param type $teil
     * @return type 
     */
    public function updateDkopfField($dbField, $value, $teil) {
        $sql = "update dkopf set $dbField='$value' where teil='$teil' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param type $von
     * @param type $nach 
     */
    public function changePersNr($von, $nach) {
	$tableFieldArray = array(
	    'dabmahnung' => 'persnr',
	    'dambew' => 'persnr',
	    'dpers' => 'persnr',
	    'dpersbewerber' => 'persnr',
	    'dpersdatumzuschlag' => 'persnr',
	    'dpersdetail1' => 'persnr',
	    'dpersfaehigkeit' => 'persnr',
	    'dperspremie' => 'persnr',
	    'dpersschulung' => 'persnr',
	    'dpersstempel' => 'persnr',
	    'dperstransport' => 'persnr',
	    'dpersuntersuchungdatum' => 'persnr',
	    'dpersvertrag' => 'persnr',
	    'dpraemie' => 'persnr',
	    'dreparaturkopf' => 'persnr_ma',
	    'drueck' => 'persnr',
	    'dstddif' => 'persnr',
	    'dunterkunft' => 'persnr',
	    'durlaub1' => 'persnr',
	    'dvertrag' => 'persnr',
	    'dvorschuss' => 'persnr',
	    'dzeit' => 'persnr',
	    'dzeitsoll' => 'persnr',
	    'dzeitsoll2' => 'persnr',
	    'persunfall' => 'persnr',
	    'schutzmittelausgabe' => 'persnr'
	);
	
	foreach ($tableFieldArray as $table=>$field){
	    $sql = "update `$table` set `$field`=$nach where `$field`=$von";
	    mysql_query($sql);
	}
    }

    /**
     *
     * @param type $persnr
     * @param type $field
     * @param type $value 
     */
    public function updateUrlaubField($persnr,$field,$value){
	$sql = "update durlaub1 set $field='$value' where persnr='$persnr' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }
    
    
    public function updateDRechField($dbField, $value, $drechId){
	$sql = "update drech set `$dbField`='$dbValue' where drech_id=$drechId limit 1";
	mysql_query($sql);
	return mysql_affected_rows();
    }
    
    /**
     * 
     * @param type $id_vorschuss
     * @param type $fieldToUpdate
     * @param type $newValue
     */
    public function updateVorschuss($id_vorschuss,$fieldToUpdate,$newValue,$user){
	$sql = "update dvorschuss set `$fieldToUpdate`='$newValue',`user`='$user' where id_vorschuss=$id_vorschuss limit 1";
	mysql_query($sql);
	return mysql_affected_rows();
    }
    /**
     * 
     * @param type $drueckId
     * @param type $dbField
     * @param type $dbValue
     */
    public function updateDrueckField($drueckId,$dbField,$dbValue){
	$sql = "update drueck set `$dbField`='$dbValue' where drueck_id=$drueckId limit 1";
	mysql_query($sql);
    }
    /**
     *
     * @param <type> $dauftrId
     * @param <type> $dbField
     * @param <type> $dbValue 
     */
    public function updateDauftrField($dauftrId, $dbField, $dbValue, $removeAndSetAussBehaelterFlag = 0) {
        if ($dbField == NULL)
            return 0;
        if ($removeAndSetAussBehaelterFlag != 0)
            $this->removeDauftrAussBehaelterFlag($dauftrId);

        if ($removeAndSetAussBehaelterFlag == 0) {
            $sql = "update dauftr set $dbField='$dbValue' where id_dauftr='$dauftrId' limit 1";
        }
        else
            $sql = "update dauftr set $dbField='$dbValue',aussbehaelter=1 where id_dauftr='$dauftrId' limit 1";

        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param <type> $dauftrId
     * @param <type> $value
     * @return <type> 
     */
    public function updateDauftrBehaelterTypId($dauftrId, $value) {
        $sql = "update dauftr set behaelter_id='$value' where id_dauftr='$dauftrId' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param <type> $id 
     */
    public function removeDauftrAussBehaelterFlag($id) {
        // zrusit eventuelni priznak u ostatnich pozic, ktere patri k danemu exportu
        $sql = "select dauftr.auftragsnr,dauftr.termin,dauftr.teil from dauftr where id_dauftr='$id'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $sql = "update dauftr set aussbehaelter=0 where auftragsnr=" . $row['auftragsnr'] . " and termin='" . $row['termin'] . "' and teil='" . $row['teil'] . "' and aussbehaelter<>0";
            mysql_query($sql);
        }
    }

    /**
     *
     * @param <type> $dauftrId
     * @param <type> $value
     * @return <type>
     */
    public function updateDauftrAussBehaelterTypId($dauftrId, $value) {
        $this->removeDauftrAussBehaelterFlag($dauftrId);
        $sql = "update dauftr set auss_behaelter_id='$value',aussbehaelter=1 where id_dauftr='$dauftrId' limit 1";
        mysql_query($sql);
        $ar = mysql_affected_rows();
        return $ar;
    }

    /**
     *
     * @param <type> $dzeitId
     * @param <type> $oe
     * @return <type> 
     */
    public function updateDzeitOE($dzeitId, $oe) {
        $sql = "update " . self::TABLE_DZEIT . " set tat='$oe' where id='$dzeitId' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param <type> $dzeitId
     * @param <type> $oe
     * @return <type> 
     */
    public function updateDzeitEssenId($dzeitId, $essenid) {
        $sql = "update " . self::TABLE_DZEIT . " set id_essen='$essenid' where id='$dzeitId' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param <type> $dzeitId
     * @param <type> $essenid
     * @return <type>
     */
    public function updateDzeitEssen($dzeitId, $essen, $essenid) {
        // v pripade , ze essen ==1 dovolim pro jeden datum zadat jen jedno jidlo
        if ($essen != 0) {
            // zjistim persnr a datum
            $sql = "select persnr,datum from " . self::TABLE_DZEIT . " where id='$dzeitId' limit 1";
            $res = mysql_query($sql);
            if (mysql_affected_rows() > 0) {
                $r = mysql_fetch_assoc($res);
                $persnr = $r['persnr'];
                $datum = $r['datum'];
                $sql = "update " . self::TABLE_DZEIT . " set essen=0 where persnr='$persnr' and datum='$datum'";
                mysql_query($sql);
                $sql = "update " . self::TABLE_DZEIT . " set id_essen='$essenid',essen='$essen' where id='$dzeitId' limit 1";
                mysql_query($sql);
            }
        }
        $sql = "update " . self::TABLE_DZEIT . " set id_essen='$essenid',essen='$essen' where id='$dzeitId' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param type $transportId
     * @param type $value
     * @return type 
     */
    public function updateTransportPreis($transportId, $value) {
        $preis = floatval($value);
        $sql = "update " . self::TABLE_TRANSPORT . " set preis='$preis' where id='$transportId' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param <type> $transportId
     * @param <type> $value
     * @return <type>
     */
    public function updateTransportKfz($transportId, $value) {
        // v pripade ze je value = 0 radek smazu
        if ($value == 0)
            $sql = "delete from " . self::TABLE_TRANSPORT . " where id='$transportId' limit 1";
        else
            $sql = "update " . self::TABLE_TRANSPORT . " set kfz='$value' where id='$transportId' limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param <type> $kfzFahrtenId
     * @param <type> $dbfield 
     */
    public function updateKfzFahrtenRow($kfzFahrtenId, $dbfield, $value) {
        $sql = "update dkfzfahrten set $dbfield='$value' where id=$kfzFahrtenId limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param type $behbewid
     * @return type 
     */
    public function delBehBewId($behbewid) {
        $sql = "delete from dbehbew where id=$behbewid";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     * smaze polozku z tabulky s inventurou palet
     * @param <type> $behbewid
     * @return <type> 
     */
    public function delBehInvId($behbewid) {
        $sql = "delete from dbehinventur where id=$behbewid";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param <type> $kfzFahrtenId
     */
    public function deleteKfzFahrtenRow($kfzFahrtenId) {
        $sql = "delete from dkfzfahrten where id=$kfzFahrtenId";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     * 
     * @param type $dauftr_id
     * @param type $ganzePalette
     */
    public function deleteDauftr($dauftr_id,$ganzePalette=FALSE){
	if($ganzePalette===TRUE){
	    $dr = $this->getDauftrRow($dauftr_id);
	    if($dr!==NULL){
		$auftragsnr = $dr['auftragsnr'];
		$teil = $dr['teil'];
		$pal = $dr['pal'];
		$sql = "delete from dauftr where auftragsnr='$auftragsnr' and `pos-pal-nr`='$pal' and teil='$teil' limit 50";
		$sql_lager = "delete from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (teil='$teil') and (lager_von='0'))";
	    }
	}
	else{
	    $sql = "delete from dauftr where id_dauftr='$dauftr_id' limit 1";
	}
	if(strlen($sql)>0){
	    $this->query($sql);
	}
	if(strlen($sql_lager)>0){
	    $this->query($sql_lager);
	}
    }
    
    /**
     *
     * @param integer $id
     * @param boolean $autoleistung
     */
    public function deleteDzeitRow($id, $autoleistung = TRUE) {
        if ($autoleistung == TRUE) {
            // pokud chci mazat i autoleistung
            $sql = "select persnr,`Schicht` as schicht,`Datum` as datum,tat as oe from dzeit where id='$id'";
            $result = mysql_query($sql);
            $row = mysql_fetch_assoc($result);
            if ($row != FALSE) {
                $persnr = $row['persnr'];
                $schicht = $row['schicht'];
                $datum = $row['datum'];
                $oe = $row['oe'];
                // zjistim abgnr, ktere by melo byt zadano pro zadanou smenu
                $sql = "select dschicht.auto_leistung,dschicht.auto_abgnr from dschicht where dschicht.`Schichtnr`='$schicht'";
                $result = mysql_query($sql);
                $row = mysql_fetch_assoc($result);
                if ($row != FALSE) {
                    $schichtAuto = $row['auto_leistung'];
                    $autoAbgnr = $row['auto_abgnr'];
                    // zjistit, zda ma clovek nastaven autoleistung priznak
                    $sql = "select dpers.auto_leistung from dpers where persnr='$persnr'";
                    $result = mysql_query($sql);
                    $row = mysql_fetch_assoc($result);
                    if ($row !== FALSE) {
                        if ($row['auto_leistung'] != 0) {
                            // pro tohoto cloveka se zadava i autoleistung, budu ho tedy chtit i smazat
                            $sql_delete = "delete from drueck where persnr='$persnr' and datum='$datum' and auftragsnr=999999 and teil='9999' and oe='$oe' and drueck.`TaetNr`=$autoAbgnr limit 1";
                            mysql_query($sql_delete);
                        }
                    }
                }
            }
        }
        $sql = "delete from " . self::TABLE_DZEIT . " where id='$id' limit 1";
        mysql_query($sql);
    }

    
    public function getDrueckRowsForImTeilPalTat($im,$teil,$pal,$tatneu){
	$sql.="select * from drueck";
	$sql.=" where";
	$sql.=" (drueck.`AuftragsNr`=$im)";
	$sql.=" and (drueck.`teil`=$teil)";
	$sql.=" and (drueck.`pos-pal-nr`=$pal)";
	$sql.=" and (drueck.`taetnr`=$tatneu)";
	return $this->getQueryRows($sql);
    }
    /**
     * returns rows array for noex paletten mit teil und abgnr
     * @param <type> $teil
     * @param <type> $abgnr 
     */
    public function getDrueckRowsFor($teil, $abgnr) {
        $sql = " select * from drueck ";
        $sql.=" join ";
        $sql.=" dauftr";
        $sql.=" on";
        $sql.=" drueck.`AuftragsNr`=dauftr.auftragsnr";
        $sql.=" and drueck.`Teil`=dauftr.teil";
        $sql.=" and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr`";
        $sql.=" and drueck.`TaetNr`=dauftr.abgnr";
        $sql.=" where ";
        $sql.=" drueck.`Teil`='$teil' and abgnr='$abgnr' and dauftr.`auftragsnr-exp` is null";
        $sql.=" order by";
        $sql.=" drueck.auftragsnr,drueck.`pos-pal-nr`";

        $result = mysql_query($sql);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($result))
                array_push($rows, $row);
            return $rows;
        }
    }

    /**
     *
     * @param type $persnr
     * @param type $von
     * @param type $bis
     * @return null|array 
     */
    public function getAnwesenheitArray($persnr, $von, $bis) {

        $sql = "select dzeit.id,`Datum` as datumorder,DATE_FORMAT(datum,'%d.%m.%Y') as datum,persnr,tat as oe,`Stunden` as stunden,pause1,pause2,id_essen,essen from " . self::TABLE_DZEIT;
        //$sql = "select id,datum as datumorder,DATE_FORMAT(datum,'%d.%m.%Y') as datum,persnr,route_id,preis,kfz as kfz_id from ".self::TABLE_TRANSPORT." ";
        $sql.= " where persnr='$persnr' and datum between '$von' and '$bis' order by datumorder desc";
        $result = mysql_query($sql);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($result))
                array_push($rows, $row);
            return $rows;
        }
    }

    public function getEssenInfoArray() {
        $sql = "select id_essen as id,essen_kz from " . self::TABLE_DESSEN . " order by id_essen";
        return $this->getQueryRows($sql);
    }

    public function getArbMittelArrayForTeil($teil){
	$sql.=" select ";
	$sql.=" dmittelteilabgnr.teil,";
	$sql.=" dmittelteilabgnr.abgnr,";
	$sql.=" dmittel.nazev,";
	$sql.=" dmittel.poznamka";
	$sql.=" from dmittelteilabgnr";
	$sql.=" join dmittel on dmittel.id=dmittelteilabgnr.id_mittel";
	$sql.=" where";
	$sql.=" dmittelteilabgnr.teil='$teil'";
	$sql.=" order by";
	$sql.=" dmittelteilabgnr.abgnr,";
	$sql.=" dmittel.nazev";
	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $teil
     * @param type $abgnr
     * @param type $maxItems
     */
    public function getAMMMList($teil,$abgnr,$maxItems=999){
	$retList = "";
	$sql=" select dmittel.nazev";
	$sql.=" from dmittel";
	$sql.=" join dmittelteilabgnr on dmittelteilabgnr.id_mittel=dmittel.id";
	$sql.=" where";
	$sql.=" (dmittelteilabgnr.teil='$teil')";
	$sql.=" and";
	$sql.=" (dmittelteilabgnr.abgnr=$abgnr)";
	$sql.=" order by dmittel.nazev";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) return $retList;
	$items = 0;$itemsArray = array();
	foreach ($rows as $row){
	    array_push($itemsArray, $row['nazev']);
	    $items++;
	    if($items==$maxItems) break;
	}
	return implode(',', $itemsArray);
    }
    /**
     * get associative array for all OEs, array('tat'=>,'og'=>)
     * 
     * @return array
     */
    public function getOEInfoArray() {
        $sql = "select tat,og from " . self::TABLE_TATTYPEN . " order by tat";
        $result = mysql_query($sql);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($result))
                array_push($rows, $row);
            return $rows;
        }
    }

    /**
     *
     * @return null|array 
     */
    public function getOGInfoArray() {
        $sql = "select og from " . self::TABLE_DOG . " order by og";
        $result = mysql_query($sql);
        if (mysql_affected_rows() == 0)
            return NULL;
        else {
            $rows = array();
            while ($row = mysql_fetch_assoc($result))
                array_push($rows, $row);
            return $rows;
        }
    }

    /**
     *
     * @param type $teilOld
     * @param type $teilNew 
     */
    public function teilNrAendern($teilOld, $teilNew) {


//dauftr
//dkopf
//dkopf_attachment
//dlagerbew
//dlagerstk
//dma
//dmittelteilabgnr
//dpos
//dposbedarflager
//dpos_import
//drech
//drechbew
//drechdeleted
//drechneu
//dreklamation
//drueck
//dteildokument
//dverp (teil_id)

	$sql = "update dkopf set `Teil`='$teilNew' where `Teil`='$teilOld' and `Teil`<>'$teilNew'";
        $res = mysql_query($sql);
        $affectedRows = mysql_affected_rows();
        echo "<br>sql=$sql,affectedRows=$affectedRows";
        if ($affectedRows > 0) {
            $sql = "update dpos set `Teil`='$teilNew' where `Teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
            echo "<br>sql=$sql,affectedRows=$affectedRows";
            $sql = "update dauftr set teil='$teilNew' where teil='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
            echo "<br>sql=$sql,affectedRows=$affectedRows";
            $sql = "update drueck set `Teil`='$teilNew' where `Teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
            echo "<br>sql=$sql,affectedRows=$affectedRows";
            $sql = "update dlagerbew set teil='$teilNew' where teil='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
            echo "<br>sql=$sql,affectedRows=$affectedRows";
            $sql = "update dlagerstk set teil='$teilNew' where teil='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
            echo "<br>sql=$sql,affectedRows=$affectedRows";
            $sql = "update dkopffotos set teil='$teilNew' where teil='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
            echo "<br>sql=$sql,affectedRows=$affectedRows";
            $sql = "update dposbedarflager set `Teil`='$teilNew' where `Teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
            echo "<br>sql=$sql,affectedRows=$affectedRows";
            $sql = "update drech set `Teil`='$teilNew' where `Teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
            echo "<br>sql=$sql,affectedRows=$affectedRows";
            $sql = "update drechneu set `Teil`='$teilNew' where `Teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update dkopf_attachment set `teil`='$teilNew' where `teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update dma set `teil`='$teilNew' where `teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update dmittelteilabgnr set `teil`='$teilNew' where `teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update dpos_import set `Teil`='$teilNew' where `Teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update drechbew set `Teil`='$teilNew' where `Teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update drechdeleted set `Teil`='$teilNew' where `Teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update dreklamation set `teil`='$teilNew' where `teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update dteildokument set `teil`='$teilNew' where `teil`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
	    $sql = "update dverp set `teil_id`='$teilNew' where `teil_id`='$teilOld'";
            $res = mysql_query($sql);
            $affectedRows = mysql_affected_rows();
        }
    }

    /**
     *
     * @param type $persnr
     * @param type $von
     * @param type $bis 
     */
    public function getArbStundenBetweenDatums($persnr,$von,$bis){
    	$sql=" select";
	$sql.=" dzeit.persnr,";
	$sql.=" sum(dzeit.stunden) as sumstunden";
	$sql.=" from dzeit";
	$sql.=" join dtattypen on dtattypen.tat=dzeit.tat";
	$sql.=" where persnr=$persnr";
	$sql.=" and datum between '$von' and '$bis'";
	$sql.=" and oestatus='a'";
	
	$res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $v = $row['sumstunden'];
            if ($v == null)
                return 0;
            else
                return $v;
        }
        else
            return 0;
    }
    /**
     * suma hodin pro oe tat
     * bez ohledu na oestatus
     *
     * crati sumu planovanych hodin pro zadane oe v rozmezi $dbDatumVon a $dbDatumBis
     * @param string $dbDatumVon ve formatu YYYY-MM-DD
     * @param string $dbDatumBis ve formatu YYYY-MM-DD
     * @param int $persnr
     * @param string $oe
     * @return double
     */
    public function getPlanOEStundenBetweenDatums($dbDatumVon, $dbDatumBis, $persnr, $oe) {
        $sql = "select sum(dzeitsoll.stunden) as sumstunden from dzeitsoll where datum>'$dbDatumVon' and datum<='$dbDatumBis' and persnr='$persnr' and oe='$oe'";
//        dbConnect();
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $v = $row['sumstunden'];
            if ($v == null)
                return 0;
            else
                return $v;
        }
        else
            return 0;
    }

    public function getIstOEStundenBetweenDatums($dbDatumVon, $dbDatumBis, $persnr, $oe) {
        $sql = "select sum(dzeit.stunden) as sumstunden from dzeit where datum>'$dbDatumVon' and datum<='$dbDatumBis' and persnr='$persnr' and tat='$oe'";
//    echo "<br>".$sql;
//        dbConnect();
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            $v = $row['sumstunden'];
            if ($v == null)
                return 0;
            else
                return $v;
        }
        else
            return 0;
    }

    /**
     *
     * @param type $datumDB 
     */
    public function getVzKdProDatum($datumDB){
            $sql = " select";
            $sql.= " sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,drueck.`Stück`*drueck.`VZ-SOLL`)) as sumvzkd";
            $sql.= " from drueck";
            $sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
            $sql.= " where";
            $sql.= " drueck.Datum between '$datumDB' and '$datumDB'";
//            $sql.= " and `dtaetkz-abg`.Stat_Nr='$statnr'";
        $rows = $this->getQueryRows($sql);
        if ($rows !== NULL) {
            $row = $rows[0];
            return floatval($row['sumvzkd']);
        }
        else
            return 0;
    }
    
    public function getVzAbyProPersNrDatum($von,$bis,$persnr){
	    $sql = " select";
            $sql.= " sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,drueck.`Stück`*drueck.`VZ-IST`)) as sumvzaby";
            $sql.= " from drueck";
            $sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
            $sql.= " where";
            $sql.= " drueck.Datum between '$von' and '$bis'";
            $sql.= " and drueck.PersNr=$persnr";
//            $sql.= " and `dtaetkz-abg`.Stat_Nr='$statnr'";
//	    $sql.= " and `daufkopf`.kunde<>'$ohneKunde'";
        $rows = $this->getQueryRows($sql);
        if ($rows !== NULL) {
            $row = $rows[0];
            return floatval($row['sumvzaby']);
        }
        else
            return 0;
        
    }
    /**
     *
     * @param <type> $statnr
     * @param <type> $von
     * @param <type> $bis
     * @param <type> $persnr 
     */
    public function getVzKdProStatNrDatumPersnr($statnr, $von, $bis, $persnr = NULL,$persvon=0,$persbis=99999,$ohneKunde=355) {
        if ($persnr !== NULL) {
            $sql = " select";
            $sql.= " sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,drueck.`Stück`*drueck.`VZ-SOLL`)) as sumvzkd";
            $sql.= " from drueck";
            $sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
	    $sql.= " join `daufkopf` on `daufkopf`.`auftragsnr`=drueck.auftragsnr";
            $sql.= " where";
            $sql.= " drueck.Datum between '$von' and '$bis'";
            $sql.= " and drueck.PersNr=$persnr";
            $sql.= " and `dtaetkz-abg`.Stat_Nr='$statnr'";
	    $sql.= " and `daufkopf`.kunde<>'$ohneKunde'";
        } else {
            $sql = " select";
            $sql.= " sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,drueck.`Stück`*drueck.`VZ-SOLL`)) as sumvzkd";
            $sql.= " from drueck";
            $sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
	    $sql.= " join `daufkopf` on `daufkopf`.`auftragsnr`=drueck.auftragsnr";
            $sql.= " where";
            $sql.= " drueck.Datum between '$von' and '$bis'";
            $sql.= " and drueck.persnr between '$persvon' and '$persbis'";
            $sql.= " and `dtaetkz-abg`.Stat_Nr='$statnr'";
	    $sql.= " and `daufkopf`.kunde<>'$ohneKunde'";
        }
        $rows = $this->getQueryRows($sql);
        if ($rows !== NULL) {
            $row = $rows[0];
            return floatval($row['sumvzkd']);
        }
        else
            return 0;
    }

    /**
     * ohne nw stunden
     * @param <type> $datvon
     * @param <type> $datbis
     * @param <type> $persnr
     * @return <type>
     */
    public function getIstAnwesenheitStundenBetweenDatums($datvon, $datbis, $persnr, $mitVon = 0) {
        $sql = "select persnr,sum(stunden) as sumstunden from dzeit";
        $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
        if ($mitVon == 0)
            $sql.= " where dzeit.persnr='$persnr' and datum>'$datvon' and datum<='$datbis' and dtattypen.oestatus='a' group by persnr";
        else
            $sql.= " where dzeit.persnr='$persnr' and datum>='$datvon' and datum<='$datbis' and dtattypen.oestatus='a' group by persnr";
//    echo "sql=$sql<br>";
//        dbConnect();
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['sumstunden'];
        } else {
            return 0;
        }
    }

    /**
     *
     * @param <type> $dbDatumVon
     * @param <type> $dbDatumBis
     * @param <type> $persnr
     * @param <type> $oestatus
     * @return <type>
     */
    public function getIstAnwesenheitStundenBetweenDatumsForOEStatus($datvon, $datbis, $persnr, $oestatus) {
        $sql = "select persnr,sum(stunden) as sumstunden from dzeit";
        $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
        $sql.= " where dzeit.persnr='$persnr' and datum>'$datvon' and datum<='$datbis' and dtattypen.oestatus='n' group by persnr";
//    echo $sql;
//        dbConnect();
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['sumstunden'];
        } else {
            return 0;
        }
    }

    /**
     *
     * @param <type> $datvon
     * @param <type> $datbis
     * @param <type> $persnr
     * @return <type>
     */
    public function getLastDZeitDatum($datvon, $datbis, $persnr) {
        $sql = "select persnr,max(datum) as letzte_datum from dzeit";
        $sql.= " where dzeit.persnr='$persnr' and datum>'$datvon' and datum<='$datbis' and datum<=NOW() group by persnr";
//    echo "<br>sql=$sql";
//        dbConnect();
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return substr($row['letzte_datum'], 0, 10);
        } else {
            return null;
        }
    }

    /**
     * 
     */
    public function getVerbPersNrDatum($persnr,$datumDB){
        $sql.=" select sum(drueck.`Verb-Zeit`) as verb";
        $sql.=" from drueck";
        $sql.=" where";
        $sql.=" PersNr=$persnr";
        $sql.=" and datum='$datumDB'";
        $rows = $this->getQueryRows($sql);
        if($rows===NULL) return 0;
        return floatval($rows[0]['verb']);
    }
    /**
     *
     * @param type $persnr 
     */
    public function getCopyVzAbyToVerbFlag($persnr){
      $sql = "select leist_vzaby_to_verb from dpers where persnr=$persnr";
      $rows = $this->getQueryRows($sql);
      if($rows===NULL) return FALSE;
      if(intval($rows[0]['leist_vzaby_to_verb'])!=0)
          return TRUE;
      else
          return FALSE;
    }
    /**
     * vypocita prescasove hodiny, jako vychozi bere hodnotu z tabulky stddiff
     * @param <type> $monat
     * @param <type> $jahr
     * @param <type> $persnr
     * @return <type>
     */
    public function getPlusMinusStunden($monat, $jahr, $persnr, $datumVonDB = NULL) {
        $returnArray = array();
        $plusMinusStunden = 0;

//    echo "<br>jahr=$jahr,monat=$monat<hr>";
        $stddifA = $this->getStdDiff($monat, $jahr, $persnr);

//    echo "<br>persnr=$persnr<hr>";
//    echo "<pre>";
//    print_r($stddifA);
//    echo "</pre>";

        if ($stddifA == null) {
            // pro daneho cloveka nemam v tabulce zadanou posledni hodnotu fondu hodin
            // pouiju nasledujici
            // datum od kdy mu budu nascitavat prescasove hodiny bude od datumu nastupu
            // pocet hodin bude mit na hodnote 0
            $stddifStunden = 0;
            $stddifA['datum'] = $this->getEintrittsDatumDB($persnr);
            if ($stddifA['datum'] == '1980-01-01') {
                // persnr nema zadany eintrittsdatum
                echo "<h2>Kontrollieren Sie Eintrittsdatum beim PersNr=$persnr</h2>";
                exit;
            }
            $dbDatumVon = date('Y-m-d', mktime(0, 0, 1, substr($stddifA['datum'], 5, 2), substr($stddifA['datum'], 8, 2) - 1, substr($stddifA['datum'], 0, 4)));
            $stddifA['datum'] = substr($dbDatumVon, 2, 2) . substr($dbDatumVon, 5, 2) . substr($dbDatumVon, 8, 2);
        } else {
	    //jeste musim zkontrolovat jestli nema eintritt>$stddifA['datum']
	    // pokud ano, zachovam se stejne jako v pripade, ze $stddifA==null
	    $eintrittDatumDB = $this->getEintrittsDatumDB($persnr);
	    if ($eintrittDatumDB == '1980-01-01') {
                // persnr nema zadany eintrittsdatum
                echo "<h2>Kontrollieren Sie Eintrittsdatum beim PersNr=$persnr</h2>";
                exit;
            }
//	    echo "eintrittDatumDB=$eintrittDatumDB,stddifA[datumDB]=".$stddifA['datumDB']."<br>";
	    $timeEintritt = strtotime($eintrittDatumDB);
	    $timeStdDif = strtotime($stddifA['datumDB']);
//	    echo "timeEintritt=$timeEintritt,timeStdDif=$timeStdDif<br>";
	    if($timeEintritt>$timeStdDif){
		$stddifStunden = 0;
		$stddifA['datum'] = $eintrittDatumDB;
		$dbDatumVon = date('Y-m-d', mktime(0, 0, 1, substr($stddifA['datum'], 5, 2), substr($stddifA['datum'], 8, 2) - 1, substr($stddifA['datum'], 0, 4)));
		$stddifA['datum'] = substr($dbDatumVon, 2, 2) . substr($dbDatumVon, 5, 2) . substr($dbDatumVon, 8, 2);
	    }
	    else{
		$stddifStunden = $stddifA['stunden'];
		$dbDatumVon = '20' . substr($stddifA['datum'], 0, 2) . '-' . substr($stddifA['datum'], 2, 2) . '-' . substr($stddifA['datum'], 4, 2);
	    }
        }

        if ($datumVonDB !== NULL) {
            $timeExplizit = strtotime($datumVonDB);
            $timeVon = strtotime($dbDatumVon);
            if ($timeExplizit > $timeVon) {
                $dbDatumVon = $datumVonDB;
                $stddifA['datum'] = substr($dbDatumVon, 2, 2) . substr($dbDatumVon, 5, 2) . substr($dbDatumVon, 8, 2);
                $stddifStunden = 0;
//            echo "<br>upravuji datum von na $dbDatumVon a +-Stunden na $stddifStunden";
            }
        }
//    echo "<br>persnr=$persnr,dbDatumVon=$dbDatumVon<br>stddifStunden=$stddifStunden,stddifA[datum]=".$stddifA['datum'];

        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $dbDatumBis = sprintf("%s-%02d-%02d", $jahr, $monat, $pocetDnuVMesici);

        $regelStunden = $this->getRegelarbeitDatum($monat, $jahr, $persnr);
        // v pripade, ze name zaznam v tabulce dstddif tak pouziju hodnotu z dpers
        if ($regelStunden === NULL)
            $regelStunden = $this->getRegelarbzeit($persnr);

        // horni mez pro hledani posledni zadane dochazky si omezim na posledni den aktualniho mesice
        $aktualniRok = date('Y');
        $aktualniMesic = date('m');
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $aktualniMesic, $aktualniRok);
        $dbDatumBisActualMonat = sprintf("%s-%02d-%02d", $aktualniRok, $aktualniMesic, $pocetDnuVMesici);

        $lastDzeitDatum = $this->getLastDZeitDatum($dbDatumVon, $dbDatumBis, $persnr);
//    echo "<br>lastDzeitDatum = $lastDzeitDatum";
        if ($lastDzeitDatum == null)
            return 0;
        // zjistim pocet prac dnu od 1 do lastDzeitDatum
        //echo "<br>persnr=$persnr stddif,datum:".$stddifA['datum'];
        $dbDatumVonArbTage = date('Y-m-d', mktime(0, 0, 1, substr($stddifA['datum'], 2, 2), substr($stddifA['datum'], 4, 2) + 1, '20' . substr($stddifA['datum'], 0, 2)));
        $arbTage = $this->getArbTageBetweenDatums($dbDatumVonArbTage, $lastDzeitDatum);
//        echo "<br>arbTage = $arbTage, zwischen $dbDatumVonArbTage und $lastDzeitDatum";

        $nStunden = $this->getIstAnwesenheitStundenBetweenDatumsForOEStatus($dbDatumVon, $lastDzeitDatum, $persnr, 'n');
//        echo "<br>nStunden=$nStunden, zwischen $dbDatumVon und $lastDzeitDatum";
        // kolik hodin mam do lastDzeitDatum odpracovat
        $sollStundenLastDzeitDatum = $arbTage * $regelStunden - $nStunden;
//        echo "<br>sollStundenLastDzeitDatum=$sollStundenLastDzeitDatum";
        $plusMinusStunden = $sollStundenLastDzeitDatum;

        // pocet odpracovanych hodin mezi datumama a posledni datum prace
        $istStundenA = $this->getIstAnwesenheitStundenBetweenDatums($dbDatumVon, $lastDzeitDatum, $persnr); // - $nStunden;
        $plusMinusStunden = $istStundenA;
//        echo "istStundenA = $istStundenA zwischen $dbDatumVon und $lastDzeitDatum<br>";

        $prescasyVMesici = $istStundenA - $sollStundenLastDzeitDatum;

        $nwStunden = $this->getPlanOEStundenBetweenDatums($lastDzeitDatum, $dbDatumBis, $persnr, "nw");
//        echo "<br>nwStunden=$nwStunden";
        $prescasyVMesici = $prescasyVMesici - $nwStunden;
        $plusMinusStunden = $prescasyVMesici;

        $prescasyCelkem = $prescasyVMesici + $stddifStunden;
        $plusMinusStunden = $prescasyCelkem;

        // plusminusstunden prubezne prepisuju po poslednim prepisu v nem mam spravnou hodnotu
        // zapisu to tam
        return $plusMinusStunden;
//    }
    }

    /**
     * vypocita prescasove hodiny, jako vychozi bere hodnotu z tabulky stddiff, vraci podrobnejsi informace
     * @param <type> $monat
     * @param <type> $jahr
     * @param <type> $persnr
     * @return <type>
     */
    public function getPlusMinusStundenVerbose($monat, $jahr, $persnr) {
        $returnArray = array();
        $plusMinusStunden = 0;
        $anfangMonatDatum = sprintf("%04d-%02d-%02d", $jahr, $monat, 1);

//    echo "<br>jahr=$jahr,monat=$monat<hr>";
        $stddifA = $this->getStdDiff($monat, $jahr, $persnr);

//    echo "<br>persnr=$persnr<hr>";
//    echo "<pre>";
//    print_r($stddifA);
//    echo "</pre>";

        if ($stddifA == null) {
            // pro daneho cloveka nemam v tabulce zadanou posledni hodnotu fondu hodin
            // pouiju nasledujici
            // datum od kdy mu budu nascitavat prescasove hodiny bude od datumu nastupu
            // pocet hodin bude mit na hodnote 0
            $stddifStunden = 0;
            $stddifA['datum'] = $this->getEintrittsDatumDB($persnr);
            $dbDatumVon = date('Y-m-d', mktime(0, 0, 1, substr($stddifA['datum'], 5, 2), substr($stddifA['datum'], 8, 2) - 1, substr($stddifA['datum'], 0, 4)));
            $stddifA['datum'] = substr($dbDatumVon, 2, 2) . substr($dbDatumVon, 5, 2) . substr($dbDatumVon, 8, 2);
        } else {
            $stddifStunden = $stddifA['stunden'];
            $dbDatumVon = '20' . substr($stddifA['datum'], 0, 2) . '-' . substr($stddifA['datum'], 2, 2) . '-' . substr($stddifA['datum'], 4, 2);
        }

//    echo "<br>persnr=$persnr,dbDatumVon=$dbDatumVon<br>stddifStunden=$stddifStunden,stddifA[datum]=".$stddifA['datum'];

        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $dbDatumBis = sprintf("%s-%02d-%02d", $jahr, $monat, $pocetDnuVMesici);


        $regelStunden = $this->getRegelarbeitDatum($monat, $jahr, $persnr);
        // v pripade, ze name zaznam v tabulce dstddif tak pouziju hodnotu z dpers
        if ($regelStunden === NULL)
            $regelStunden = $this->getRegelarbzeit($persnr);

        $returnArray['regelStd'] = $regelStunden;
        $returnArray['datumVon'] = $dbDatumVon;
        $returnArray['VonStd'] = $stddifStunden;

        // horni mez pro hledani posledni zadane dochazky si omezim na posledni den aktualniho mesice
        $aktualniRok = date('Y');
        $aktualniMesic = date('m');
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $aktualniMesic, $aktualniRok);
        $dbDatumBisActualMonat = sprintf("%s-%02d-%02d", $aktualniRok, $aktualniMesic, $pocetDnuVMesici);

        $lastDzeitDatum = $this->getLastDZeitDatum($dbDatumVon, $dbDatumBis, $persnr);
        $returnArray['lastAnwDat'] = $lastDzeitDatum;
//    echo "<br>lastDzeitDatum = $lastDzeitDatum";
        if ($lastDzeitDatum == null)
            return 0;
        // zjistim pocet prac dnu od 1 do lastDzeitDatum
        $dbDatumVonArbTage = date('Y-m-d', mktime(0, 0, 1, substr($stddifA['datum'], 2, 2), substr($stddifA['datum'], 4, 2) + 1, '20' . substr($stddifA['datum'], 0, 2)));
        $arbTage = $this->getArbTageBetweenDatums($dbDatumVonArbTage, $lastDzeitDatum);
        $arbTageMonat = $this->getArbTageBetweenDatums($anfangMonatDatum, $lastDzeitDatum);
//        echo "<br>arbTage = $arbTage, zwischen $dbDatumVonArbTage und $lastDzeitDatum";
        $returnArray['ATageMonat'] = $arbTageMonat;
        $returnArray['ATage'] = $arbTage;

        $nStunden = $this->getIstAnwesenheitStundenBetweenDatumsForOEStatus($dbDatumVon, $lastDzeitDatum, $persnr, 'n');
//        echo "<br>nStunden=$nStunden, zwischen $dbDatumVon und $lastDzeitDatum";
        $returnArray['nStd'] = $nStunden;
        // kolik hodin mam do lastDzeitDatum odpracovat
        $sollStundenLastDzeitDatum = $arbTage * $regelStunden - $nStunden;
        $returnArray['sollStdLastAnwDat'] = $sollStundenLastDzeitDatum;
//        echo "<br>sollStundenLastDzeitDatum=$sollStundenLastDzeitDatum";
        $plusMinusStunden = $sollStundenLastDzeitDatum;

        // pocet odpracovanych hodin mezi datumama a posledni datum prace
        $istStundenA = $this->getIstAnwesenheitStundenBetweenDatums($dbDatumVon, $lastDzeitDatum, $persnr); // - $nStunden;
        $istStundenAMonat = $this->getIstAnwesenheitStundenBetweenDatums($anfangMonatDatum, $lastDzeitDatum, $persnr, 1); // - $nStunden;
        $returnArray['istStdAMonat'] = $istStundenAMonat;
        $returnArray['istStdAGesamt'] = $istStundenA;

        $plusMinusStunden = $istStundenA;
//        echo "istStundenA = $istStundenA zwischen $dbDatumVon und $lastDzeitDatum<br>";

        $prescasyVMesici = $istStundenA - $sollStundenLastDzeitDatum;

//        $nwStunden = $this->getPlanOEStundenBetweenDatums($anfangMonatDatum,$dbDatumBis,$persnr,"nw");
        $nwStunden = $this->getIstOEStundenBetweenDatums($anfangMonatDatum, $dbDatumBis, $persnr, "nw");
//        echo "<br>nwStunden=$nwStunden";
        $returnArray['nwStd'] = $nwStunden;

        $prescasyVMesici = $prescasyVMesici; // - $nwStunden;
        $plusMinusStunden = $prescasyVMesici;
//        $returnArray['MehrStundenMonat'] = $plusMinusStunden;

        $prescasyCelkem = $prescasyVMesici + $stddifStunden;
        $plusMinusStunden = $prescasyCelkem;
        $returnArray['MehrStundenGesamt'] = $plusMinusStunden;

        // plusminusstunden prubezne prepisuju po poslednim prepisu v nem mam spravnou hodnotu
        // zapisu to tam
        return $returnArray;
//    }
    }

    
    /**
     * 
     * @param type $persnr
     * @return boolean
     */
    public function isUniversalista($persnr){
	$sql.= " select * from dpersstempel";
	$sql.= " where";
	$sql.= " oe like 'G%11'";
	$sql.= " and persnr=$persnr";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL)    
	    return FALSE;
	else
	    return TRUE;
    }
    
    /**
     * 
     * @param type $datumDB
     * @param type $persnr
     */
    public function getMAStundenDatum($datumDB,$persnr){
	$stunden = 0;
	$sql = "select stunden from dstddif where (persnr=$persnr) and (datum='$datumDB')";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    $r = $rows[0];
	    $stunden = floatval($r['stunden']);
	}
	return $stunden;
    }
    
    /**
     *
     * @param <type> $monat
     * @param <type> $jahr
     * @param <type> $persnr
     * @return array array("datum"=>$row['datum'],"stunden"=>$row['stunden']);
     */
    public function getStdDiff($monat, $jahr, $persnr) {
        if ($monat == 1) {
            $vormonat = 12;
            $vorjahr = $jahr - 1;
        } else {
            $vormonat = $monat - 1;
            $vorjahr = $jahr;
        }

        $pocetdnu = cal_days_in_month(CAL_GREGORIAN, $vormonat, $vorjahr);
        $bisDatum = sprintf("%04d-%02d-%02d", $vorjahr, $vormonat, $pocetdnu);
        $sql = "select DATE_FORMAT(datum,'%y%m%d') as datum,DATE_FORMAT(datum,'%Y-%m-%d') as datumDB,stunden from dstddif join dpers on dpers.persnr=dstddif.persnr where dpers.persnr='$persnr' and dstddif.datum<='$bisDatum' order by datum desc";
        $result = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            return array("datum" => $row['datum'], "datumDB" => $row['datumDB'],"stunden" => $row['stunden']);
        }
        else
            return null;
    }

    public function getMusterVomRow($val){
	$sql = "select id from mustervom where name='$val'";
	return $this->getQueryRows($sql);
    }

    public function getTeilSchwierigkeiten($teil){
	$sql = "select schwierigkeitsgrad_S11 as S11,schwierigkeitsgrad_S51 as S51,schwierigkeitsgrad_SO as SO from dkopf where teil='$teil'";
	$rows=$this->getQueryRows($sql);
	if($rows===NULL) return NULL;
	$row = $rows[0];
	return $row;
    }
    
    /**
     *
     * @param type $teil
     * @return type 
     * {}
     */
    public function getLetzteReklamation($teil,$limit=5,$reklMatch=NULL){
	if($reklMatch===NULL){
	    $sql = "select id,rekl_datum as rd,rekl_nr,DATE_FORMAT(rekl_datum,'%d.%m.%Y') as rekl_datum,beschr_abweichung,interne_bewertung,giesstag from dreklamation where teil='$teil' order by rd desc limit $limit";
	}
	else{
	    $sql = "select id,rekl_datum as rd,rekl_nr,DATE_FORMAT(rekl_datum,'%d.%m.%Y') as rekl_datum,beschr_abweichung,interne_bewertung,giesstag from dreklamation where (teil='$teil') and (rekl_nr like '$reklMatch') order by rd desc limit $limit";
	}
	
	return $this->getQueryRows($sql);
    }
	
    /**
     *
     * @param type $teil
     * @param type $anzahl
     * @return null 
     */
    public function getLetzteReklamationString($teil,$anzahl){
	$reklamationenRows = $this->getLetzteReklamation($teil, $anzahl);
	if($reklamationenRows===NULL) return NULL;
	$str="";
	foreach($reklamationenRows as $reklRow){
	    $str .= $reklRow['rekl_nr']." (".$reklRow['rekl_datum'].")"."/";
	}
	if(strlen($str)>0) $str = substr ($str, 0, strlen($str)-1);
	return $str;
    }
    
    /**
     *
     * @param type $teil
     * @param type $dokunr
     * @param type $newest 
     */
    public function getTeilDokument($teil,$dokunr,$newest=TRUE){
	if($newest===TRUE)
	    // nejnovejsi zaznam zvoleneho typu
	    $sql = "select doku_beschreibung,musterplatz,einlag_datum as ed,DATE_FORMAT(einlag_datum,'%d.%m.%Y') as einlag_datum,dokumenttyp.doku_nr,if(freigabe_am is not null,DATE_FORMAT(freigabe_am,'%d.%m.%Y'),'') as freigabe_am,freigabe_vom from dteildokument join dokumenttyp on dokumenttyp.doku_nr=dteildokument.doku_nr where teil='$teil' and dteildokument.doku_nr=$dokunr order by ed desc limit 1";
	else
	    // nejstarsi zaznam bez ohledu na typ dokumentu
	    $sql = "select doku_beschreibung,musterplatz,einlag_datum as ed,DATE_FORMAT(einlag_datum,'%d.%m.%Y') as einlag_datum,dokumenttyp.doku_nr,if(freigabe_am is not null,DATE_FORMAT(freigabe_am,'%d.%m.%Y'),'') as freigabe_am,freigabe_vom from dteildokument join dokumenttyp on dokumenttyp.doku_nr=dteildokument.doku_nr where teil='$teil' order by ed asc limit 1";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) 
	    return NULL;
	else{
	    $row = $rows[0];
	    return $row;
	}
    }
    
    
    /**
     *
     * @param type $dokuId
     * @param type $fieldName
     * @param type $val
     * @return type 
     */
    public function updateVPMField($dokuId,$fieldName,$val){
	if($val===NULL)
	    $sql = "update dverp set `$fieldName`=null where id=$dokuId limit 1";
	else
	    $sql = "update dverp set `$fieldName`='$val' where id=$dokuId limit 1";
	mysql_query($sql);
        return mysql_affected_rows();
    }
    
    /**
     *
     * @param type $dokuId
     * @param type $fieldName
     * @param type $val 
     */
    public function updateTeilDokuField($dokuId,$fieldName,$val){
	if($val===NULL)
	    $sql = "update dteildokument set `$fieldName`=null where id=$dokuId limit 1";
	else
	    $sql = "update dteildokument set `$fieldName`='$val' where id=$dokuId limit 1";
	mysql_query($sql);
        return mysql_affected_rows();
    }
    
    /**
     *
     * @param string $name
     * @param type $value
     * @param type $adressId
     * @return int 
     */
    public function updateAdresyField($name,$value,$adressId){
	if($name=="geboren1"){
	    if(strlen(trim($value))>0){
		$value = $this->make_DB_datum($value);
		$name="geboren";
	    }
	    else return 0;
	}
	$sql = "update adresy set `$name`='$value' where adresy_id=$adressId limit 1";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param type $report
     * @param type $user
     * @param type $param
     * @param type $likeArray, TRUE = return rowsarray where param is used in like $param%
     * @return null 
     */
    public function getReportPrintParam($report,$user,$param,$likeArray=FALSE){
	if($likeArray===TRUE){
	    $sql = "select param,value from reportprintparams where ((report='$report') and (user='$user') and (param like '$param%'))";
	    $rows = $this->getQueryRows($sql);
	    return $rows;
	}
	$sql = "select value from reportprintparams where ((report='$report') and (user='$user') and (param='$param'))";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL) 
	    return NULL;
	else
	    return $rows[0]['value'];
    }
    
    public function updateReportPrintParams($report,$user,$param,$value){
	// have row -> update
	$sql = "select id from reportprintparams where ((report='$report') and (user='$user') and (param='$param'))";
	mysql_query($sql);
	if(mysql_affected_rows()>0){
	    //update
	    $sql = "update reportprintparams set value='$value' where ((report='$report') and (user='$user') and (param='$param')) limit 1";
	}
	else{
	    //insert
	    $sql = "insert into reportprintparams (report,user,param,value) values('$report','$user','$param','$value')";
	}
	mysql_query($sql);
        return mysql_affected_rows();
    }
    

    public function deleteGenehmigtPositionen($imaid,$matyp){
	if($matyp=='ima'){
	    $this->updateIMAField('ima_auftragsnrarray_genehmigt', '', $imaid);
	    $this->updateIMAField('ima_palarray_genehmigt', '', $imaid);
	    $this->updateIMAField('ima_tatundzeitarray_genehmigt', '', $imaid);
	    $this->updateIMAField('ima_dauftrid_array_genehmigt', '', $imaid);
	}
	else{
	    $this->updateIMAField('ema_auftragsarray_genehmigt', '', $imaid);
	    $this->updateIMAField('ema_palarray_genehmigt', '', $imaid);
	    $this->updateIMAField('ema_tatundzeitarray_genehmigt', '', $imaid);
	    $this->updateIMAField('ema_dauftrid_array_genehmigt', '', $imaid);
	}
    }
    
    /**
     * 
     * @param type $field
     * @param type $value
     * @param type $imaid
     * @return type
     */
    public function updateIMAField($field,$value,$imaid){
	if($value===NULL)
	    $sql = "update dma set `$field`=null where id='$imaid'";
	else
	    $sql = "update dma set `$field`='$value' where id='$imaid'";
	$this->query($sql);
	return mysql_affected_rows();
    }


        public function getIMAStkGenehmigtForIMANrNew($imanr){
	$stk = 0;
	$sql = "select ima_dauftrid_array_genehmigt from dma where imanr='$imanr'";
	$imaRows = $this->getQueryRows($sql);
	if($imaRows!==NULL){
	    $imaRow = $imaRows[0];
	    $dauftrIdArrayStr = $imaRow['ima_dauftrid_array_genehmigt'];
	    if((strlen($dauftrIdArrayStr)>0)){
		$idArray = explode(';',$dauftrIdArrayStr);
		if(is_array($idArray)){
		    foreach ($idArray as $id){
			$dauftrRow = $this->getDauftrRow($id);
			if($dauftrRow!==NULL){
			    $stk+=intval($dauftrRow['stk']);
			}
		    }
		}
	    }
	}
	return $stk;
    }

    public function getIMAStkGenehmigtForIMANr($imanr){
	$stk = 0;
	$sql = "select dma.id,teil,imanr,emanr,ima_auftragsnrarray_genehmigt,ima_palarray_genehmigt,tatundzeitarray,bemerkung,imavon,stamp from dma where imanr='$imanr'";
	$imaRows = $this->getQueryRows($sql);
	if($imaRows!==NULL){
	    $imaRow = $imaRows[0];
	    $imarray = $imaRow['ima_auftragsnrarray_genehmigt'];
	    $palarray = $imaRow['ima_palarray_genehmigt'];
	    if((strlen($imarray)>0)&&(strlen($palarray)>0)){
		$imarray1 = strtr($imarray, ';', ',');
		$palarray1 = strtr($palarray, ';', ',');
    		$sql=" select ";
		$sql.=" sum(dauftr.`stück`) as sum_stk";
		$sql.=" from dauftr";
		$sql.=" where";
		$sql.=" (dauftr.auftragsnr in ($imarray1))";
		$sql.=" and ";
		$sql.=" (dauftr.`pos-pal-nr` in ($palarray1))";
		$sql.=" and";
		$sql.=" (dauftr.KzGut='G')";
		$sql.=" and";
		$sql.=" (dauftr.teil='".$imaRow['teil']."')";

		$stkRows = $this->getQueryRows($sql);
		if($stkRows!==NULL){
		    $stkRow = $stkRows[0];
		    $stk = intval($stkRow['sum_stk']);
		}
	    }
	}
	return $stk;
	
    }
    
    public function getIMAStkForIMANr($imanr){
	$stk = 0;
	$sql = "select dma.id,teil,imanr,emanr,auftragsnrarray,palarray,tatundzeitarray,bemerkung,imavon,stamp from dma where imanr='$imanr'";
	$imaRows = $this->getQueryRows($sql);
	if($imaRows!==NULL){
	    $imaRow = $imaRows[0];
	    $imarray = $imaRow['auftragsnrarray'];
	    $palarray = $imaRow['palarray'];
	    if((strlen($imarray)>0)&&(strlen($palarray)>0)){
		$imarray1 = strtr($imarray, ';', ',');
		$palarray1 = strtr($palarray, ';', ',');
    		$sql=" select ";
		$sql.=" sum(dauftr.`stück`) as sum_stk";
		$sql.=" from dauftr";
		$sql.=" where";
		$sql.=" (dauftr.auftragsnr in ($imarray1))";
		$sql.=" and ";
		$sql.=" (dauftr.`pos-pal-nr` in ($palarray1))";
		$sql.=" and";
		$sql.=" (dauftr.KzGut='G')";
		$sql.=" and";
		$sql.=" (dauftr.teil='".$imaRow['teil']."')";

		$stkRows = $this->getQueryRows($sql);
		if($stkRows!==NULL){
		    $stkRow = $stkRows[0];
		    $stk = intval($stkRow['sum_stk']);
		}
	    }
	}
	return $stk;
    }
    
    public function getIMAStkForIMANrNew($imanr){
	$stk = 0;
	$sql = "select dma.id,teil,imanr,emanr,auftragsnrarray,palarray,tatundzeitarray,ima_dauftrid_array,bemerkung,imavon,stamp from dma where imanr='$imanr'";
	$imaRows = $this->getQueryRows($sql);
	if($imaRows!==NULL){
	    $imaRow = $imaRows[0];
	    $dauftrIdArrayStr = $imaRow['ima_dauftrid_array'];
	    if((strlen($dauftrIdArrayStr)>0)){
		$idArray = explode(';',$dauftrIdArrayStr);
		if(is_array($idArray)){
		    foreach ($idArray as $id){
			$dauftrRow = $this->getDauftrRow($id);
			if($dauftrRow!==NULL){
			    $stk+=intval($dauftrRow['stk']);
			}
		    }
		}
	    }
	}
	return $stk;
    }

    /**
     *
     * @param type $teil
     * @return type 
     */
    public function getTeilIMAArray($teil){
	$sql = "select ema_genehmigt,ima_genehmigt,dma.id,teil,imanr,emanr,auftragsnrarray,palarray,tatundzeitarray,bemerkung,imavon,stamp from dma where teil='$teil' order by imanr desc";
	return $this->getQueryRows($sql);
    }

    
    public function getArtikelBezeichnung($artnr){
	$sql = "select `art-name1` as name1,`art-name2` as name2,`art-name3` as name3 from `eink-artikel` where `art-nr`='$artnr'";
	return $this->getQueryRows($sql);
    }
    
    /**
     * 
     * @param type $teil
     */
    public function getTeilMittelArray($teil){
	$sql.=" select ";
	$sql.=" dmittelteilabgnr.id,";
	$sql.=" dmittel.nazev,";
	$sql.=" dmittel.poznamka,";
	$sql.=" dmittelteilabgnr.abgnr";
	$sql.=" from";
	$sql.=" dmittel";
	$sql.=" join dmittelteilabgnr on dmittelteilabgnr.id_mittel=dmittel.id";
	$sql.=" where";
	$sql.=" dmittelteilabgnr.teil='$teil'";
	$sql.=" order by";
	$sql.=" dmittelteilabgnr.abgnr,";
	$sql.=" dmittel.nazev";
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param type $teil
     * @return type 
     */
    public function getTeilVPMArray($teil){
	$sql = "select dverp.id,teil_id as teil,verp_id as verp,verp_stk,bemerkung from dverp where teil_id='$teil' order by verp_id";
	return $this->getQueryRows($sql);
    }

    public function getTeilDokuDistinctDokuArray($teil,$beachteArbPapireDruckFlag=FALSE){
	$sql=" select td1.id,td1.doku_nr,td1.teil,if(einlag_datum is null,'',DATE_FORMAT(einlag_datum,'%d.%m.%Y')) as einlag_datum,doku_beschreibung,if(freigabe_am is null,'',DATE_FORMAT(freigabe_am,'%d.%m.%Y')) as freigabe_am,freigabe_vom,musterplatz";
	$sql.=" from dteildokument td1";
	$sql.=" join dokumenttyp on dokumenttyp.doku_nr=td1.doku_nr";
	$sql.=" where";
	$sql.=" (einlag_datum=(select max(einlag_datum) from dteildokument td2 where (td1.doku_nr=td2.doku_nr) and (td2.teil='$teil')))";
	$sql.=" and";
	$sql.=" (td1.teil='$teil')";
	$sql.=" order by";
	$sql.=" td1.doku_nr";
	if($beachteArbPapireDruckFlag===TRUE){
	    $sql=" select td1.id,td1.doku_nr,td1.teil,if(einlag_datum is null,'',DATE_FORMAT(einlag_datum,'%d.%m.%Y')) as einlag_datum,doku_beschreibung,if(freigabe_am is null,'',DATE_FORMAT(freigabe_am,'%d.%m.%Y')) as freigabe_am,freigabe_vom,musterplatz";
	    $sql.=" from dteildokument td1";
	    $sql.=" join dokumenttyp on dokumenttyp.doku_nr=td1.doku_nr";
	    $sql.=" where";
	    $sql.=" (einlag_datum=(select max(einlag_datum) from dteildokument td2 where (td1.doku_nr=td2.doku_nr) and (td2.teil='$teil')))";
	    $sql.=" and";
	    $sql.=" (td1.teil='$teil')";
	    $sql.=" and";
	    $sql.=" (dokumenttyp.druck_arbpapier=1)";
	    $sql.=" order by";
	    $sql.=" td1.doku_nr";
	}
	//$sql = "select dteildokument.id,doku_nr,teil,if(einlag_datum is null,'',DATE_FORMAT(einlag_datum,'%d.%m.%Y')) as einlag_datum,if(freigabe_am is null,'',DATE_FORMAT(freigabe_am,'%d.%m.%Y')) as freigabe_am,freigabe_vom,musterplatz from dteildokument where teil='$teil' order by einlag_datum desc,doku_nr asc";
	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $teil
     */
    public function getTeilTatArray($teil,$nurAktiveTat=FALSE){
	$sql=" select dpos.`TaetNr-Aby` as abgnr from dpos where teil='$teil'";
	if($nurAktiveTat===TRUE){
	    $sql=" select dpos.`vz-min-aby` as vzaby,dpos.KzGut as kzgut,dpos.`TaetNr-Aby` as abgnr,`vz-min-kunde` as vzkd from dpos where (teil='$teil') and (`kz-druck`<>0)";
	}
	return $this->getQueryRows($sql);
    }
    /**
     *
     * @param type $teil 
     */
    public function getTeilDokuArray($teil){
//	$sql=" select td1.id,td1.doku_nr,td1.teil,if(einlag_datum is null,'',DATE_FORMAT(einlag_datum,'%d.%m.%Y')) as einlag_datum,doku_beschreibung,if(freigabe_am is null,'',DATE_FORMAT(freigabe_am,'%d.%m.%Y')) as freigabe_am,freigabe_vom,musterplatz";
//	$sql.=" from dteildokument td1";
//	$sql.=" join dokumenttyp on dokumenttyp.doku_nr=td1.doku_nr";
//	$sql.=" where";
//	$sql.=" (einlag_datum=(select max(einlag_datum) from dteildokument td2 where (td1.doku_nr=td2.doku_nr) and (td2.teil='$teil')))";
//	$sql.=" and";
//	$sql.=" (td1.teil='$teil')";
//	$sql.=" order by";
//	$sql.=" td1.doku_nr";
		
	$sql = "select dokumenttyp.doku_beschreibung,dteildokument.id,dteildokument.doku_nr,teil,if(einlag_datum is null,'',DATE_FORMAT(einlag_datum,'%d.%m.%Y')) as einlag_datum,if(freigabe_am is null,'',DATE_FORMAT(freigabe_am,'%d.%m.%Y')) as freigabe_am,freigabe_vom,musterplatz from dteildokument";
	$sql.=" join dokumenttyp on dokumenttyp.doku_nr=dteildokument.doku_nr";
	$sql.=" where teil='$teil' order by einlag_datum desc,doku_nr asc";
	return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $dokuId
     * @return type
     */
    public function delTeilMittel($dokuId){
	$sql = "delete from dmittelteilabgnr where id='$dokuId' limit 1";
	mysql_query($sql);
        return mysql_affected_rows();
    }
    /**
     *
     * @param type $dokuId
     * @return type 
     */
    public function delVPM($dokuId){
	$sql = "delete from dverp where id='$dokuId' limit 1";
	mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     *
     * @param type $dokuId 
     */
    public function delTeilDokument($dokuId){
	$sql = "delete from dteildokument where id='$dokuId' limit 1";
	mysql_query($sql);
        return mysql_affected_rows();
    }

    /**
     * 
     */
    public function addTeilIMA($teil,$imanr,$bemerkung,$auftragsarray,$palarray,$dauftridarray,$tatarray,$user){
	$sql = "insert into dma";
	$sql.=" (imanr,teil,auftragsnrarray,palarray,ima_dauftrid_array,tatundzeitarray,bemerkung,imavon)";
	$sql.=" values('$imanr','$teil','$auftragsarray','$palarray','$dauftridarray','$tatarray','$bemerkung','$user')";
        mysql_query($sql);
        return mysql_insert_id();
    }
    
    /**
     * 
     * @param type $teil
     * @param type $n_mittel_nr
     * @param type $n_abgnr
     * @param type $user
     * @return int
     */
    public function addTeilMittel($teil,$n_mittel_nr,$n_abgnr,$user){
	$sql = "select id from dmittel where nazev='$n_mittel_nr'";
	$rows = $this->getQueryRows($sql);
	if($rows!==NULL){
	    $r = $rows[0];
	    $idmittel = $r['id'];
	    $sql = "insert into dmittelteilabgnr (id_mittel,teil,abgnr,user) values($idmittel,'$teil','$n_abgnr','$user')";
	    mysql_query($sql);
	    return mysql_affected_rows();
	}
	return 0;
    }
    /**
     *
     * @param type $teil
     * @param type $n_vpm_nr
     * @param type $n_anzahl
     * @param type $n_bemerkung
     * @param type $user
     * @return type 
     */
    public function addTeilVPM($teil,$n_vpm_nr,$n_anzahl,$n_bemerkung,$user){
	$sql = "insert into dverp (teil_id,verp_id,verp_stk,bemerkung,user) values('$teil','$n_vpm_nr','$n_anzahl','$n_bemerkung','$user')";
        mysql_query($sql);
        return mysql_affected_rows();
    }
    /**
     *
     * @param type $teil
     * @param type $n_dokumenttyp_id
     * @param type $n_einlag_datum
     * @param type $n_freigabe_am
     * @param type $n_freigabe_vom
     * @param type $n_musterplatz 
     */
    public function addTeilDokument($teil,$n_doku_nr,$n_einlag_datum,$n_freigabe_am,$n_freigabe_vom,$n_musterplatz){
	// zjistit dokumenttyp_id protoze predavam doku_nr
//	$sql = "select id from dokumenttyp where doku_nr=$n_dokumenttyp_id";
//	$rows = $this->getQueryRows($sql);
//	if($rows===NULL) return -1;
//	$n_dokumenttyp_id = $rows[0]['id'];
	if($n_freigabe_am=="")
	    $sql = "insert into dteildokument (teil,doku_nr,einlag_datum,freigabe_am,freigabe_vom,musterplatz) values('$teil','$n_doku_nr','$n_einlag_datum',null,'$n_freigabe_vom','$n_musterplatz')";
	else
	    $sql = "insert into dteildokument (teil,doku_nr,einlag_datum,freigabe_am,freigabe_vom,musterplatz) values('$teil','$n_doku_nr','$n_einlag_datum','$n_freigabe_am','$n_freigabe_vom','$n_musterplatz')";
        mysql_query($sql);
        return mysql_affected_rows();
    }
    
    public function addAdresyInKategorie($adressId,$katId){
	$sql="insert into adresyinkategorie (adresy_id,adresy_kategorie_id) values('$adressId','$katId') ";
        mysql_query($sql);
        return mysql_affected_rows();
    }
    
    public function deleteAdresyInKategorie($adressId,$kId){
	$sql="delete from adresyinkategorie where adresy_id='$adressId' and adresy_kategorie_id='$kId'";
        mysql_query($sql);
        return mysql_affected_rows();
    }
    
    
    
    public function setAdressDeleted($adressId){
        $sql = "update adresy set deleted=1 where adresy_id=$adressId";
        mysql_query($sql);
        return mysql_affected_rows();
    }

    public function getAdresyInKategorien($adressId){
	$sql.=" select";
	$sql.=" adresyinkategorie.adresy_id,";
	$sql.=" adresy_kategorie.kategorie,";
	$sql.=" adresyinkategorie.adresy_kategorie_id";
	$sql.=" from ";
	$sql.=" adresyinkategorie";
	$sql.=" join adresy_kategorie on adresy_kategorie.id=adresyinkategorie.adresy_kategorie_id";
	$sql.=" where adresy_id='$adressId'";
	$sql.=" order by adresy_kategorie.sort,adresy_kategorie.id";
	return $this->getQueryRows($sql);
    }
    
    public function getPlanIstFertig($termin,$datum,$rmDateTime=NULL){
	    $sql.=" select ";
	    $sql.=" dauftr.termin,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0011,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0041',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0041,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0051,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0061',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0061,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0081',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0081,";
	    $sql.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as sum_vzkd";
	    $sql.=" from ";
	    $sql.=" drueck";
	    $sql.=" join dauftr on dauftr.auftragsnr=drueck.AuftragsNr and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.abgnr=drueck.TaetNr";
	    $sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
	    $sql.=" where";
	    $sql.=" (dauftr.termin='$termin')";
	    $sql.=" and (drueck.Datum<='$datum')";
	    if($rmDateTime!==NULL)
	    $sql.=" and (drueck.insert_stamp<='$rmDateTime')";
	    $sql.=" and (dauftr.`auftragsnr-exp` is null)";
	    $sql.=" group by";
	    $sql.=" dauftr.termin";
	    return $this->getQueryRows($sql);
    }

    /**
     * 
     * @param type $termin
     * @param type $datum
     * @param type $rmDateTime
     * @return type
     */
    public function getIstFertigKunde($kunde_von,$kunde_bis,$datum,$rmDateTime=NULL,$getSql=FALSE){
	    $sql.=" select ";
	    $sql.=" daufkopf.kunde,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0011,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0041',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0041,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0051,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0061',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0061,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0081',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0081,";
	    $sql.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as sum_vzkd";
	    $sql.=" from ";
	    $sql.=" drueck";
	    $sql.=" join dauftr on dauftr.auftragsnr=drueck.AuftragsNr and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.abgnr=drueck.TaetNr";
	    $sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
	    $sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
	    $sql.=" where";
	    $sql.=" daufkopf.kunde between '$kunde_von' and '$kunde_bis'";
	    $sql.=" and (drueck.Datum='$datum')";
	    if($rmDateTime!==NULL)
	    $sql.=" and (drueck.insert_stamp<='$rmDateTime')";
	    $sql.=" and (dauftr.`auftragsnr-exp` is null)";
	    $sql.=" group by";
	    $sql.=" daufkopf.kunde";
	    if($getSql===TRUE){
		return $sql;
	    }
	    $r=$this->getQueryRows($sql);
	    if($r!==NULL){
		$a = array();
		foreach ($r as $row){
		    $a[$row['kunde']]=$row;
		}
		return $a;
	    }
	    else {
		return NULL;
	    }
    }

    
    public function getIstFertigKundeS610($kunde_von,$kunde_bis,$datum,$rmDateTime=NULL,$getSql=FALSE){
	    $sql.=" select ";
	    $sql.=" daufkopf.kunde,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0011,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0041',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0041,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0051,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0061',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0061,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0081',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0081,";
	    $sql.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as sum_vzkd";
	    $sql.=" from ";
	    $sql.=" drueck";
	    $sql.=" join dauftr on dauftr.auftragsnr=drueck.AuftragsNr and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.abgnr=drueck.TaetNr";
	    $sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
	    $sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
	    $sql.=" where";
	    $sql.=" daufkopf.kunde between '$kunde_von' and '$kunde_bis'";
	    $sql.=" and (drueck.Datum='$datum')";
	    if($rmDateTime!==NULL)
	    $sql.=" and (drueck.insert_stamp<='$rmDateTime')";
	    //$sql.=" and (dauftr.`auftragsnr-exp` is null)";
	    $sql.=" group by";
	    $sql.=" daufkopf.kunde";
	    if($getSql===TRUE){
		return $sql;
	    }
	    $r=$this->getQueryRows($sql);
	    if($r!==NULL){
		$a = array();
		foreach ($r as $row){
		    $a[$row['kunde']]=$row;
		}
		return $a;
	    }
	    else {
		return NULL;
	    }
    }

    public function getIstFertig($termin,$datum,$rmDateTime=NULL){
	    $sql.=" select ";
	    $sql.=" dauftr.termin,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0011,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0041',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0041,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0051,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0061',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0061,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0081',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0081,";
	    $sql.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as sum_vzkd";
	    $sql.=" from ";
	    $sql.=" drueck";
	    $sql.=" join dauftr on dauftr.auftragsnr=drueck.AuftragsNr and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.abgnr=drueck.TaetNr and drueck.teil=dauftr.teil";
	    $sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
	    $sql.=" where";
	    $sql.=" dauftr.termin='$termin'";
	    $sql.=" and (drueck.Datum='$datum')";
	    if($rmDateTime!==NULL)
	    $sql.=" and (drueck.insert_stamp<='$rmDateTime')";
	    $sql.=" and (dauftr.`auftragsnr-exp` is null)";
	    $sql.=" group by";
	    $sql.=" dauftr.termin";
	    return $this->getQueryRows($sql);
    }

    
    public function getPlanIstFertigNoEx($kunde,$datum,$rmDateTime=NULL){
	    $datum = date('Y-m-d',$datum);
	    $sql.=" select ";
	    $sql.=" '$kunde"."NOEX' as termin,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0011,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0041',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0041,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0051,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0061',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0061,";
	    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0081',if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as sum_vzkd_S0081,";
	    $sql.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as sum_vzkd";
	    $sql.=" from ";
	    $sql.=" drueck";
	    $sql.=" join dauftr on dauftr.auftragsnr=drueck.AuftragsNr and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.abgnr=drueck.TaetNr and dauftr.teil=drueck.teil";
	    $sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
	    $sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
	    $sql.=" where";
	    $sql.=" (daufkopf.kunde='$kunde')";
	    $sql.=" and (drueck.Datum<='$datum')";
	    if($rmDateTime!==NULL)
	    $sql.=" and (drueck.insert_stamp<='$rmDateTime')";
	    $sql.=" and (dauftr.`auftragsnr-exp` is null)";
	    $sql.=" group by";
	    $sql.=" dauftr.`auftragsnr-exp`";
//	    var_dump($sql);
	    return $this->getQueryRows($sql);
    }

	/**
	 * 
	 * @param type $kunde
	 * @param type $time
	 * @return type
	 */
        public function getPlanVzKdNoEx($kunde,$time=NULL){
	if($time!==NULL){
	$datumDB = date('Y-m-d',$time);
	$sql.=" select ";
	$sql.=" '$kunde"."NOEX' as termin,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0011,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0041',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0041,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0051,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0061',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0061,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0081',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0081,";
	$sql.=" sum(dauftr.`stück`*dauftr.VzKd) as sum_vzkd";
	$sql.=" from ";
	$sql.=" dauftr";
	$sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
	$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
	$sql.=" where";
	$sql.=" (daufkopf.kunde='$kunde')";
	$sql.=" and (dauftr.`auftragsnr-exp` is null)";
	$sql.=" and (DATE_FORMAT(daufkopf.aufdat,'%Y-%m-%d')<='$datumDB')";
	$sql.=" group by";
	$sql.=" dauftr.`auftragsnr-exp`";
	}
	return $this->getQueryRows($sql);
    }

    /**
     *
     * @param type $terminAktual 
     * @param int  timestamp aktualniho datumu radky
     */
    public function getPlanVzKd($terminAktual,$time=NULL){
	if($time!==NULL){
	$datumDB = date('Y-m-d',$time);
	$sql.=" select ";
	$sql.=" dauftr.termin,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0011,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0041',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0041,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0051,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0061',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0061,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0081',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0081,";
	$sql.=" sum(dauftr.`stück`*dauftr.VzKd) as sum_vzkd";
	$sql.=" from ";
	$sql.=" dauftr";
	$sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
	$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
	$sql.=" where";
	$sql.=" (dauftr.termin='$terminAktual')";
	$sql.=" and (dauftr.`auftragsnr-exp` is null)";
	$sql.=" and (DATE_FORMAT(daufkopf.aufdat,'%Y-%m-%d')<='$datumDB')";
	$sql.=" group by";
	$sql.=" dauftr.termin";
	}
	else{
	$sql.=" select ";
	$sql.=" dauftr.termin,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0011,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0041',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0041,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0051,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0061',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0061,";
	$sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0081',dauftr.`stück`*dauftr.VzKd,0)) as sum_vzkd_S0081,";
	$sql.=" sum(dauftr.`stück`*dauftr.VzKd) as sum_vzkd";
	$sql.=" from ";
	$sql.=" dauftr";
	$sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
	$sql.=" where";
	$sql.=" (dauftr.termin='$terminAktual')";
	$sql.=" and (dauftr.`auftragsnr-exp` is null)";
	$sql.=" group by";
	$sql.=" dauftr.termin";
	}
	return $this->getQueryRows($sql);
    }
    
    /**
     *
     * @param int $von kunde von
     * @param int $bis kunde bis
     * @param timestamp $time 
     */
    public function getPlanSollProTagArray($von,$bis,$time=NULL,$getsql=FALSE){
	$statnrArray = array("S0011","S0041","S0051","S0061","S0081");
	if($time===NULL){
	    $sql.=" select ";
	    $sql.=" dstat.stat_nr as statnr, ";
	    $sql.=" sum(dispostatnrkunde.vzkd) as sumvzkd";
	    $sql.=" from dstat";
	    $sql.=" left join dispostatnrkunde on dispostatnrkunde.statnr=dstat.Stat_Nr";
	    $sql.=" where";
	    $sql.=" dispostatnrkunde.kunde between $von and $bis";
	    $sql.=" group by";
	    $sql.=" dstat.Stat_Nr";
	    if($getsql===TRUE) return $sql;
	    $arr = $this->getQueryRows($sql);
	    if($arr!==NULL){
		$rA = array();
		foreach ($arr as $a){
		    $rA[$a['statnr']] = $a['sumvzkd'];
		}
		return $rA;
	    }
	}
	else{
	    $datumDB = date('Y-m-d',$time);
	    $sql="select ";
	    $sql.=" dstat.Stat_Nr as statnr,";
	    $sql.=" sum(dispostatnrvzkd.vzkd) as sumvzkd";
	    $sql.=" from";
	    $sql.=" dstat";
	    $sql.=" left join dispostatnrvzkd on dispostatnrvzkd.statnr=dstat.Stat_Nr";
	    $sql.=" where";
	    $sql.=" (dispostatnrvzkd.kunde between $von and $bis)";
	    $sql.=" and";
	    $sql.=" (dispostatnrvzkd.datum='$datumDB')";
	    $sql.=" group by";
	    $sql.=" dstat.Stat_Nr";
	    if($getsql===TRUE) return $sql;
	    $arr = $this->getQueryRows($sql);
//	    if($arr!==NULL){
		$rA1 = array();
		
		if($arr!==NULL){
		    foreach ($arr as $a){
		    $rA1[$a['statnr']] = $a['sumvzkd'];
		}
		}
		foreach ($statnrArray as $statnr){
		    if(array_key_exists($statnr, $rA1))
			$value = $rA1[$statnr];
		    else
			$value = 0;
		    $rA[$statnr] = $value;
		}
		return $rA;
//	    }
	}
	return NULL;
    }

    /**
     * 
     * @param type $terminAktual
     * @param type $statnr
     * @param type $time
     * @param type $rmDateTime
     * @return type
     */
    public function getPlanZuBearbeitenNoEx($terminAktual,$statnr,$time,$rmDateTime=NULL){
	$kunde = substr($terminAktual, 0, 3);
	
	$zubearbeiten = 0;
	
	// vzkdplan
	$vzkdPlanArray = $this->getPlanVzKdNoEx($kunde,$time);
	if($vzkdPlanArray===NULL)
	    $vzkdPlan = 0;
	else{
	    $index = 'sum_vzkd_'.$statnr;
	    if($statnr=='sum')
		$index = 'sum_vzkd';
	    $vzkdPlan = floatval($vzkdPlanArray[0][$index]);
	}
	
	

	// fertig
	$fertigPlanArray = $this->getPlanIstFertigNoEx($kunde, $time,$rmDateTime);
	if($fertigPlanArray===NULL)
	    $fertigPlan = 0;
	else{
	    $index = 'sum_vzkd_'.$statnr;
	    if($statnr=='sum')
		$index = 'sum_vzkd';
	    $fertigPlan = floatval($fertigPlanArray[0][$index]);
	}
	
	
	// solltag
	if($statnr=='sum')
	    $beforeMins = floatval($this->getPlanSollTagSumme($terminAktual, date('Y-m-d',$time), TRUE));
	else
	    $beforeMins = floatval($this->getPlanSollTagMinuten($terminAktual, $statnr, date('Y-m-d',$time), TRUE));

	$zubearbeiten = $vzkdPlan-$fertigPlan-$beforeMins;
	
	return $zubearbeiten;
    }
    /**
     *
     * @param type $terminAktual
     * @param type $statnr
     * @param type $time 
     */
    public function getPlanZuBearbeiten($terminAktual,$statnr,$time,$rmDateTime=NULL){
	$zubearbeiten = 0;
	
	// vzkdplan
	$vzkdPlanArray = $this->getPlanVzKd($terminAktual);
	if($vzkdPlanArray===NULL)
	    $vzkdPlan = 0;
	else{
	    $index = 'sum_vzkd_'.$statnr;
	    if($statnr=='sum')
		$index = 'sum_vzkd';
	    $vzkdPlan = floatval($vzkdPlanArray[0][$index]);
	}
	
	

	// fertig
	$fertigPlanArray = $this->getPlanIstFertig($terminAktual, date('Y-m-d',$time),$rmDateTime);
	if($fertigPlanArray===NULL)
	    $fertigPlan = 0;
	else{
	    $index = 'sum_vzkd_'.$statnr;
	    if($statnr=='sum')
		$index = 'sum_vzkd';
	    $fertigPlan = floatval($fertigPlanArray[0][$index]);
	}
	
	
	// solltag
	if($statnr=='sum')
	    $beforeMins = floatval($this->getPlanSollTagSumme(substr($terminAktual,1), date('Y-m-d',$time), TRUE));
	else
	    $beforeMins = floatval($this->getPlanSollTagMinuten(substr($terminAktual,1), $statnr, date('Y-m-d',$time), TRUE));
	
	$zubearbeiten = $vzkdPlan-$fertigPlan-$beforeMins;
	
	return $zubearbeiten;
    }
    
    /**
     *
     * @param type $terminAktual
     * @param type $von
     * @param type $bis
     * @param type $time 
     */
    public function getPlanInfoArray($terminAktual,$von,$bis,$time,$rmDateTime=NULL){
	$pIA = array();
	// pokud uz ma termin po exportu vratim NULL
	$exSoll = $this->getExDatumSoll(substr($terminAktual,1));
	
	if(strlen(trim($exSoll['ex_datum_soll']))==0) 
	    $exTime = strtotime ('2099-01-01');
	else
	    $exTime = strtotime($this->make_DB_datum($exSoll['ex_datum_soll']));
	
	if($time>$exTime) return NULL;
	
	if($rmDateTime===NULL) $rmDateTime = date('Y-m-d H:i:s');
	
	//1. vzkdplan
	$vzkdPlanArray = $this->getPlanVzKd($terminAktual,$time);
	$columnIndex = "vzkdplan";
	if($vzkdPlanArray!==NULL){
	    $r = $vzkdPlanArray[0];
	    $pIA["S0011"][$columnIndex] = $r['sum_vzkd_S0011'];
	    $pIA["S0041"][$columnIndex] = $r['sum_vzkd_S0041'];
	    $pIA["S0051"][$columnIndex] = $r['sum_vzkd_S0051'];
	    $pIA["S0061"][$columnIndex] = $r['sum_vzkd_S0061'];
	    $pIA["S0081"][$columnIndex] = $r['sum_vzkd_S0081'];
	    $pIA["sum"][$columnIndex] = $r['sum_vzkd'];
	}
	else{
	    $pIA["S0011"][$columnIndex] = NULL;
	    $pIA["S0041"][$columnIndex] = NULL;
	    $pIA["S0051"][$columnIndex] = NULL;
	    $pIA["S0061"][$columnIndex] = NULL;
	    $pIA["S0081"][$columnIndex] = NULL;
	    $pIA["sum"][$columnIndex] = NULL;
	}
	//2. fertig
	$fertigPlanArray = $this->getPlanIstFertig($terminAktual, date('Y-m-d',$time),$rmDateTime);
	$columnIndex = "fertig";
	if($fertigPlanArray!==NULL){
	    $r = $fertigPlanArray[0];
	    $pIA["S0011"][$columnIndex] = $r['sum_vzkd_S0011'];
	    $pIA["S0041"][$columnIndex] = $r['sum_vzkd_S0041'];
	    $pIA["S0051"][$columnIndex] = $r['sum_vzkd_S0051'];
	    $pIA["S0061"][$columnIndex] = $r['sum_vzkd_S0061'];
	    $pIA["S0081"][$columnIndex] = $r['sum_vzkd_S0081'];
	    $pIA["sum"][$columnIndex] = $r['sum_vzkd'];
	}
	else{
	    $pIA["S0011"][$columnIndex] = NULL;
	    $pIA["S0041"][$columnIndex] = NULL;
	    $pIA["S0051"][$columnIndex] = NULL;
	    $pIA["S0061"][$columnIndex] = NULL;
	    $pIA["S0081"][$columnIndex] = NULL;
	    $pIA["sum"][$columnIndex] = NULL;
	}
	//3.soll/tag
	$columnIndex = "solltag";
	$plan = substr($terminAktual,1);
	$pIA["S0011"][$columnIndex] = intval($this->getPlanSollTagMinuten($plan, "S0011", date('Y-m-d',$time)));
	$pIA["S0041"][$columnIndex] = intval($this->getPlanSollTagMinuten($plan, "S0041", date('Y-m-d',$time)));
	$pIA["S0051"][$columnIndex] = intval($this->getPlanSollTagMinuten($plan, "S0051", date('Y-m-d',$time)));
	$pIA["S0061"][$columnIndex] = intval($this->getPlanSollTagMinuten($plan, "S0061", date('Y-m-d',$time)));
	$pIA["S0081"][$columnIndex] = intval($this->getPlanSollTagMinuten($plan, "S0081", date('Y-m-d',$time)));
	$pIA["sum"][$columnIndex] = intval($this->getPlanSollTagSumme($plan, date('Y-m-d',$time)));
	

	//4.ist 
	$istArray = $this->getIstFertig($terminAktual, date('Y-m-d',$time),$rmDateTime);
	$columnIndex = "ist";
	if($istArray!==NULL){
	    $r = $istArray[0];
	    $pIA["S0011"][$columnIndex] = $r['sum_vzkd_S0011'];
	    $pIA["S0041"][$columnIndex] = $r['sum_vzkd_S0041'];
	    $pIA["S0051"][$columnIndex] = $r['sum_vzkd_S0051'];
	    $pIA["S0061"][$columnIndex] = $r['sum_vzkd_S0061'];
	    $pIA["S0081"][$columnIndex] = $r['sum_vzkd_S0081'];
	    $pIA["sum"][$columnIndex] = $r['sum_vzkd'];
	}
	else{
	    $pIA["S0011"][$columnIndex] = NULL;
	    $pIA["S0041"][$columnIndex] = NULL;
	    $pIA["S0051"][$columnIndex] = NULL;
	    $pIA["S0061"][$columnIndex] = NULL;
	    $pIA["S0081"][$columnIndex] = NULL;
	    $pIA["sum"][$columnIndex] = NULL;
	}

	return $pIA;
    }
    
    /**
     * 
     * @param type $var
     */
    static public function varDump($var){
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
    }
    /**
     * 
     * @param type $kd_von
     * @param type $kd_bis
     */
    public function getKundenMitGeplantenMinuten($kd_von,$kd_bis){
	$sql.=" select";
	$sql.=" daufkopf.kunde,";
	$sql.=" sum(dauftr.`stück`*dauftr.VzKd) as vzkd_gepl";
	$sql.=" from daufkopf";
	$sql.=" join dauftr on dauftr.auftragsnr=daufkopf.auftragsnr";
	$sql.=" where";
	$sql.=" daufkopf.kunde between '$kd_von' and '$kd_bis'";
	$sql.=" and (dauftr.`auftragsnr-exp` is null)";
	$sql.=" and (dauftr.`pal-nr-exp` is null)";
	$sql.=" group by";
	$sql.=" daufkopf.kunde";
	$sql.=" having vzkd_gepl<>0";
	return $this->getQueryRows($sql);	
    }
    /**
     *
     * @param type $kd_von
     * @param type $kd_bis 
     */
    public function getPlaene($kd_von,$kd_bis,$timeVon,$timeBis,$nurOffene = TRUE,$nurMitMinuten=TRUE){
	
	$vonDB = date('Y-m-d',$timeVon);
	$bisDB = date('Y-m-d',$timeBis);
	$timeVonMinusMonat = $timeVon - 60*60*24*30;
	$vonMinusMonatDB = date('Y-m-d',$timeVonMinusMonat);
	
	if($nurOffene===TRUE){
	    $sql.=" select auftragsnr,ex_datum_soll,zielorte.zielort";
	    $sql.=" from daufkopf";
	    $sql.=" left join zielorte on zielorte.id=daufkopf.zielort_id";
	    $sql.=" where";
	    $sql.=" (daufkopf.ausliefer_datum is null)";
	    $sql.=" and (daufkopf.kunde between $kd_von and $kd_bis)";
	    //$sql.=" and ex_datum_soll is not null";
	    $sql.=" and ((ex_datum_soll>='$vonDB') or (ex_datum_soll is null))";
	    $sql.=" and ((aufdat>'$vonMinusMonatDB') or (ex_datum_soll>='$vonDB'))";
	    $sql.=" order by auftragsnr";
	}
	else{
	    $sql.=" select auftragsnr,ex_datum_soll";
	    $sql.=" from daufkopf";
	    $sql.=" where";
	    $sql.=" daufkopf.kunde between $kd_von and $kd_bis";
	    $sql.=" and ex_datum_soll is not null";
	    $sql.=" and ex_datum_soll>='$vonDB'";
//	    $sql.=" and ex_datum_soll<=$bisDB";
	    $sql.=" order by auftragsnr";
	}
	
	$expRows = $this->getQueryRows($sql);
	// otestovat zda maji vzbrane exporty nejake naplanovane minuty, podle
	// parametru $nurMitMinuten je eventuelne vynecham
	// TODO
	if($nurMitMinuten===TRUE){
	    //$expRows = $this->getQueryRows($sql);
	    if($expRows!==NULL){
		$expRows1 = array();
		foreach ($expRows as $exp){
		    $export = $exp['auftragsnr'];
		    //ma export naplanovane nejake minuty
		    $p = $this->getPlanVzKd('P'.$export);
		    if($p!==NULL){
			$minuten = floatval($p[0]['sum_vzkd']);
			if($minuten>0){
			    array_push($expRows1, $exp);
			}
		    }
		}
		if(count($expRows1)>0)
		    $expRows = $expRows1;
		else
		    $expRows = NULL;
	    }
	}
	return $expRows;
    }
    
    public function getAdresyKategorien(){
	$sql.=" select";
	$sql.=" adresy_kategorie.id,";
	$sql.=" adresy_kategorie.kategorie";
	$sql.=" from ";
	$sql.=" adresy_kategorie";
	$sql.=" order by";
	$sql.=" adresy_kategorie.sort,adresy_kategorie.id";
	return $this->getQueryRows($sql);
    }
    
    public function getAdressArray($adressId){
	$sql="select * from adresy where adresy_id=$adressId";
	$sql="select if(geboren is not null,DATE_FORMAT(geboren,'%d.%m.%Y'),'') AS geboren1,adresy.* from adresy where adresy_id=$adressId";
	$rows = $this->getQueryRows($sql);
	if($rows===NULL)
	    return NULL;
	else
	    return $rows[0];
    }
    
    /**
     * 
     */
    public function getAdressen($search) {
        $sql = "select adresy_id,firma,ansprechpartner,CONCAT(name,' ',vorname) as name,telefon,telefonprivat,fax,handy,ort,strasse,plz,email,sonstiges";
        $sql.=" from adresy";
        $sql.=" where (ucase(firma) like ucase('%$search%')";
        $sql.=" or ucase(ansprechpartner) like ucase('%$search%')";
        $sql.=" or ucase(name) like ucase('%$search%')";
        $sql.=" or ucase(vorname) like ucase('%$search%')";
        $sql.=" or ucase(suchbegriff) like ucase('%$search%')";
        $sql.=" or ucase(email) like ucase('%$search%')";
        $sql.=" or ucase(sonstiges) like ucase('%$search%'))";
	$sql.=" and (deleted=0)";
        $sql.=" order by firma,ansprechpartner";
//        echo $sql;
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param string $oe
     * @return array array($anw_ist,$anw_soll)
     */
    public function getAnwArrayForOE($oe) {
        $sql = "select dtattypen.anw_ist,anw_soll from dtattypen where tat='$oe'";
        $res = mysql_query($sql, $this->con) or die(mysql_error());
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_array($res);
            $anw_ist = $row['anw_ist'] == null ? 0 : strtoupper($row['anw_ist']);
            $anw_soll = $row['anw_soll'] == null ? 0 : strtoupper($row['anw_soll']);
            return array('anw_ist' => $anw_ist, 'anw_soll' => $anw_soll);
        } else {
            return array('anw_ist' => 0, 'anw_soll' => 0);
        }
    }

    public function getZuschlagTageCount($persnr, $eintrittDB, $aktualDatumDB) {
        $sql = "select";
        $sql.= " DATE_FORMAT(drueck.Datum,'%Y-%m-%d') as datum,";
        $sql.= " `dtaetkz-abg`.Stat_Nr as statnr,";
        $sql.= " sum(if(drueck.taetnr<5000 or drueck.taetnr>7999,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby";
        $sql.= " from";
        $sql.= " drueck";
        $sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
        $sql.= " where";
        $sql.= " drueck.PersNr='$persnr'";
        $sql.= " and drueck.Datum between '$eintrittDB' and '$aktualDatumDB'";
        $sql.= " group by";
        $sql.= " drueck.Datum,`dtaetkz-abg`.stat_nr";

//        echo $sql;
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $tageArray = array();
            $statnrArray = array();
            while ($row = mysql_fetch_assoc($res)) {
                $statnrArray[$row['statnr']] += 1;
                $tageArray[$row['datum']][$row['statnr']]['poradi'] = $statnrArray[$row['statnr']];
                $tageArray[$row['datum']][$row['statnr']]['vzaby'] = round($row['vzaby']);
            }
            return $tageArray;
        } else {
            return NULL;
        }
    }

    public function getAnwesenheitArrayForPersNrDatum($persnr, $dbDatum) {
        $anwArray = array();
        $dbDatum = substr($dbDatum, 0, 10);
        $query = "select DATE_FORMAT(dzeit.anw_von,'%H:%i') as anw_von,DATE_FORMAT(dzeit.anw_bis,'%H:%i') as anw_bis,dzeit.tat,dzeit.stunden,dzeit.pause1,dzeit.pause2 from dzeit where persnr='$persnr' and datum='$dbDatum' order by anw_von,id";
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            array_push($anwArray, $row);
        }
        return $anwArray;
    }

    public function setOEForDrueckID($drueck_id, $oe) {
        $query = "update drueck set drueck.oe='$oe' where drueck_id='$drueck_id'";
        mysql_query($query);
    }

    /**
     * suggest OE for auftrag,abgnr,persnr,von
     * 
     * @param <type> $auftragsnr
     * @param <type> $abgnr
     * @param <type> $persnr
     * @param <type> $von 
     */
    public function getSuggestedOE($auftragsnr, $abgnr, $persnr, $von) {

        $suggestedOE = "?GF";
        $oeArrayAbgnr = array();


        $oeStringForAbgnr = $this->getOEForAbgnr($abgnr);
//        echo "oeStringForAbgnr = $oeStringForAbgnr";

        $oeArray = split(';', $oeStringForAbgnr);
        if (count($oeArray) > 1) {
            // procistit od mezer
            foreach ($oeArray as $oe) {
                array_push($oeArrayAbgnr, trim($oe));
            }
        } else {
            // mam jen jednu moznost tak ji vratim
            return array('OE' => trim($oeStringForAbgnr));
        }

        // pokracuju jen pokud mam vice moznosti OE
        // zkusim omezit na zaklade PG podle zakazky
        $pg = $this->getPGFromAuftragsnr($auftragsnr);
        $oesPG = $this->getOESForPG($pg);
        // udelam prunik poli
        $oeArrayPG1 = array_intersect($oeArrayAbgnr, $oesPG);
        $oeArrayPG = array();
        foreach ($oeArrayPG1 as $oepg) {
            array_push($oeArrayPG, $oepg);
        }

        if (count($oeArrayPG) == 1) {
            // vysla mi jen jedna moznost vratim ji
            return array('PG' => $pg, 'OEPG' => join(';', $oeArrayPG), 'OE' => $oeArrayPG[0]);
//            return $oeArrayPG[0];
        } elseif (count($oeArrayPG) == 0) {
            // zadny prunik ?, tak to je nekde chyba, vratim defaultni hodnotu ?GF
//            return array('PG'=>$pg,'OEPG'=>join(';', $oeArrayPG),'OE'=>$suggestedOE);
            // nebo se vratim zpet k oeArrayAbgnr
            $oeArrayPG = $oeArrayAbgnr;
//            return $suggestedOE;
        }

        // pokracuju jen pokud mam v oeArrayPG vice moznosti
        // zkusim omezit na zaklade persnr ale jen u PG=9
        if ($pg == 9) {
            $regelOE = $this->getRegelOE($persnr);
            if (in_array($regelOE, $oeArrayPG)) {
                return array('PG' => $pg, 'OEPG' => join(';', $oeArrayPG), 'OE' => $regelOE);
//                return $regelOE;
            } else {
                return array('PG' => $pg, 'OEPG' => join(';', $oeArrayPG), 'OE' => $suggestedOE);
//                return $suggestedOE;
            }
        }

        // sem se dostanu jen pogud je pg!=9 a mam vice moznosti v oeArrayPG
        $schicht = $this->getSchichtFromVon($von);
        $oeArraySchicht = $this->getOEForFrSp($schicht);
        // prunik poli
        $oePrunikFrSpArray = array_intersect($oeArrayPG, $oeArraySchicht);
        return array('PG' => $pg, 'OEPG' => join(';', $oeArrayPG), 'OE' => join(';', $oePrunikFrSpArray));
    }

    /**
     * vrati pocet dnu ktere nejsou pracovni, ale clovek byl v praci
     * @param <type> $dbDatumVon
     * @param <type> $dbDatumBis
     * @param <type> $persnr
     * @return <type>
     */
    public function getNotInATageInArbeitCountBetweenDatums($dbDatumVon, $dbDatumBis, $persnr) {
        $sql = "select persnr,dtattypen.oestatus,dzeit.datum";
        $sql.=" from dzeit ";
        $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
        $sql.=" join calendar on calendar.datum=dzeit.datum";
        $sql.=" where dzeit.datum between '$dbDatumVon' and '$dbDatumBis' and persnr='$persnr' and dtattypen.oestatus='a' and (calendar.svatek<>0 or calendar.cislodne>5)";
        $sql.=" group by persnr,dtattypen.oestatus,dzeit.datum";
//    echo 'sql='.$sql;
        $res = mysql_query($sql);
        return mysql_num_rows($res);
    }

    public function getQTLLeistungProPersNr($jahr, $qtl, $persnr) {
        $leistungGesamt = 0;
        $kcGesamt = 0;
        $monateVonBisProQTL = array(
            1 => array('von' => 1, 'bis' => 3),
            2 => array('von' => 4, 'bis' => 6),
            3 => array('von' => 7, 'bis' => 9),
            4 => array('von' => 10, 'bis' => 12),
        );
        if ($qtl > 0 && $qtl < 5) {

            $dbDatumVon = sprintf("%04d-%02d-01", $jahr, $monateVonBisProQTL[$qtl]['von']);
            $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monateVonBisProQTL[$qtl]['bis'], $jahr);
            $dbDatumBis = sprintf("%04d-%02d-%02d", $jahr, $monateVonBisProQTL[$qtl]['bis'], $pocetDnuVMesici);
            $eintrittsDatumDB = $this->getEintrittsDatumDB($persnr);

            $query = "select";
            $query.="    drueck.persnr,";
            $query.=" month(drueck.datum) as month,";
            $query .= "  sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
            $query .= "  sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_akkord,";
            $query .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_zeit,";
            // prepocet na kc podle faktoru u OE v tabulce dtattypen
            $query .= "  sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor)) as vzaby_kc,";
            $query .= "  sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor),0)) as vzaby_akkord_kc,";
            $query .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dpers.lohnfaktor/60,(drueck.`Stück`)*drueck.`VZ-IST`*dpers.lohnfaktor/60),0)) as vzaby_zeit_kc,";
            $query .= "  sum(if(dtattypen.akkord=0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dpers.leistfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dpers.leistfaktor),0)) as vzaby_zeit_leistung";

            $query.=" from drueck";
            $query.=" join dtattypen on drueck.oe=dtattypen.tat";
            $query.=" join dpers on dpers.persnr=drueck.persnr";
            $query.=" where";
            $query.=" (";
            $query.="    (dpers.austritt is null or dpers.austritt>='$eintrittsDatumDB' or dpers.eintritt>dpers.austritt)";
            $query.="    and (drueck.`Datum` between '$dbDatumVon' and '$dbDatumBis')";
            $query.="    and (drueck.persnr='$persnr')";
            $query.=" )";
            $query.=" group by drueck.persnr,month(drueck.datum)";

            $res = mysql_query($query);
            while ($row = mysql_fetch_assoc($res)) {
//            echo "<br>month=".$row['month']."vzaby=".$row['vzaby']." vzaby_akkord=".$row['vzaby_akkord']." vzaby_zeit=".$row['vzaby_zeit']." vzaby_zeit_kc=".$row['vzaby_zeit_kc']." vzaby_akkord_kc=".$row['vzaby_akkord_kc'];
                $leistungGesamt += intval($row['vzaby_akkord']) + intval($row['vzaby_zeit_leistung']);
                $kcGesamt += intval($row['vzaby_akkord_kc']) + intval($row['vzaby_zeit_kc']);
            }
        }
        return array('leistung_min' => $leistungGesamt, 'leistung_kc' => $kcGesamt);
    }

    /**
     * vrati pocet planovanych dnu, ktere ma persnr odpracovat v danem kvartalu, vyjma d a nw
     * 
     * @param integer $jahr
     * @param integer $qtl 1 az 4
     * @param integer $persnr
     * @return integer 
     */
    public function sollTageQTLProPersNr($jahr, $qtl, $persnr) {
        $sollTage = 0;
        $monateVonBisProQTL = array(
            1 => array('von' => 1, 'bis' => 3),
            2 => array('von' => 4, 'bis' => 6),
            3 => array('von' => 7, 'bis' => 9),
            4 => array('von' => 10, 'bis' => 12),
        );
        if ($qtl > 0 && $qtl < 5) {
            $dbDatumVon = sprintf("%04d-%02d-01", $jahr, $monateVonBisProQTL[$qtl]['von']);
            $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monateVonBisProQTL[$qtl]['bis'], $jahr);
            $dbDatumBis = sprintf("%04d-%02d-%02d", $jahr, $monateVonBisProQTL[$qtl]['bis'], $pocetDnuVMesici);
            $eintrittsDatumDB = $this->getEintrittsDatumDB($persnr);

            $sql = "select count(calendar.datum) as atage from calendar where datum>='$eintrittsDatumDB' and datum between '$dbDatumVon' and '$dbDatumBis' and svatek=0 and cislodne<6 ";
            $res = mysql_query($sql);
            if (mysql_affected_rows() > 0) {
                $row = mysql_fetch_assoc($res);
                $sollTagen = intval($row['atage']);
            }
            else
                $sollTagen = 0;

//        echo "<br>arbtagen = $sollTagen";
            // odectu dny s oe = d nebo oe = nw
            $sql = "select sum(if(tat='d',1,0)) as d_tage,sum(if(tat='nw',1,0)) as nw_tage from dzeit where datum between '$dbDatumVon' and '$dbDatumBis' and persnr='$persnr'";
//        echo "<br>sql=$sql";
            $res = mysql_query($sql);
            if (mysql_affected_rows() > 0) {
                $row = mysql_fetch_assoc($res);
//            echo "<br>d_tage=".$row['d_tage']." nw_tage=".$row['nw_tage'];
                $sollTagen -= intval($row['d_tage']) + intval($row['nw_tage']);
            }
            $sollTage = $sollTagen;
        }
        return $sollTage;
    }

    /**
     *
     * @param <type> $dbDatumVon
     * @param <type> $dbDatumBis
     * @param <type> $marke
     * @return <type> 
     */
    public function getSollMAFahrtenBetweenDatums($dbDatumVon, $dbDatumBis, $marke) {

        $sql = " select sum(dkfzfahrten.anzahl_fahrten) as anzahl,sum(dkfzfahrten.anzahl_fahrten*dkfz.sitzen) as ma_soll";
        $sql.= " from dkfzfahrten";
        $sql.= " join dkfz on dkfz.id=dkfzfahrten.kfz_id";
        $sql.= " where";
        $sql.= "     dkfz.marke='$marke'";
        $sql.= "     and dkfzfahrten.datum between '$dbDatumVon' and '$dbDatumBis'";

//        echo "<br>$sql";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $datumDB
     * @return <type> 
     */
    public function getSollMAFahrtDatum($datumDB) {
        $sql = " select sum(dkfzfahrten.anzahl_fahrten*dkfz.sitzen) as sollma";
        $sql.= " from dkfzfahrten";
        $sql.= " join dkfz on dkfz.id=dkfzfahrten.kfz_id";
        $sql.= " where";
        $sql.= " datum = '$datumDB'";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return intval($row['sollma']);
        }
        else
            return 0;
    }

    /**
     *
     * @param <type> $datumDB
     * @param <type> $kfz_id
     * @param <type> $fahrten
     */
    public function addKfzFahrtenRow($datumDB, $kfz_id, $fahrten) {
        $sql = "insert into dkfzfahrten (datum,kfz_id,anzahl_fahrten) values ('$datumDB',$kfz_id,$fahrten)";
        mysql_query($sql);
        return mysql_insert_id();
    }

    /**
     *
     * @param <type> $kfzId
     */
    public function getKfzFahrtenArray($kfzId = NULL) {
        if ($kfzId === NULL)
            $sql = "select id,kfz_id,DATE_FORMAT(datum,'%d.%m.%Y') as datumF,anzahl_fahrten from dkfzfahrten order by datum desc";
        else
            $sql = "select id,kfz_id,DATE_FORMAT(datum,'%d.%m.%Y') as datumF,anzahl_fahrten from dkfzfahrten where kfz_id=$kfzId order by datum desc";
        return $this->getQueryRows($sql);
    }

    /**
     *
     * @param <type> $dienstwagen
     * @return <type>
     */
    public function getKfzInfoArray($dienstwagen = 1) {

        if ($dienstwagen == 1)
            $sql = "select id,CONCAT(rz,' ',marke) as fahrzeug from " . self::TABLE_KFZ . " where aby_dienstwagen<>0 order by rz";
        else
            $sql = "select id,CONCAT(rz,' ',marke) as fahrzeug from " . self::TABLE_KFZ . " order by rz";
        //echo "$sql";
        return $this->getQueryRows($sql);
    }

    public function getRegelTransportPreis($persnr) {
        $sql = "select " . self::TABLE_DPERSDETAIL . ".regeltrans from " . self::TABLE_DPERSDETAIL . " where persnr='$persnr'";
        $result = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            return $row['regeltrans'];
        }
        else
            return 0;
    }

    /**
     * vrati denni premii pri splneni urciteho faktoru
     * @param float $leistfaktor = vzaby/norma ( hodnota 0 az XX )
     */
    public function getLeistungsPraemieBetragProLeistungsFaktor($leistfaktor) {

        // abych pracoval s procentama
//    echo "<br>$leistfaktor=$leistfaktor";
        $leistfaktor = intval(round($leistfaktor * 100));
// zmena 2014-01-23
// hranice zustavaji, castky se zmeni 150->200, 100->150, 50->50
//    echo "<br>$leistfaktor=$leistfaktor";
        if ($leistfaktor >= 115)
            $betrag = 200;
        else if ($leistfaktor >= 100)
            $betrag = 150;
        else if ($leistfaktor >= 85)
            $betrag = 50;
        else
            $betrag = 0;

        return $betrag;
    }

    public function getPlanedOEForDatumPersNr($persnr, $datum) {
        $sql = "select dzeitsoll.oe from dzeitsoll where persnr='$persnr' and datum='$datum'";
        $result = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            return $row['oe'];
        }
        else
            return NULL;
    }

    /**
     *
     * @param <type> $persnr
     * @param <type> $datum
     * @return <type> 
     */
    public function getOEForPersNrUndDatum($persnr, $datum) {
        // zjistit sudy nebo lichy tyden
        $datumVonStamp = strtotime($datum);
        $weekOfYear = date('W', $datumVonStamp);
        $lichy = $weekOfYear % 2 != 0 ? TRUE : FALSE;
        if ($lichy)
            return $this->getRegelOE($persnr);
        else
            return $this->getAlternativOE($persnr);
    }

    /**
     * vrati pocet dnu kdy persnr pracoval (oestatus=a) mezi zadanyma datumama
     *
     * @param int $persnr osobni cislo
     * @param string $dbDatumVondatum od ve formatu YYYY-MM-DD
     * @param string $dbDatumBis
     * @param int $nurArbeitsTage vyjmout svatky a soboty a nedele, default je 1, tj, nebere dny , tere jsou svatek , sobota, nedele
     * @return int pocet odpracovanych dnu mezi datumama
     */
    public function getATageProPersnrBetweenDatums($persnr, $dbDatumVon, $dbDatumBis, $nurArbeitsTage = 1) {
        $atage = 0;
        $sql = "select";
        $sql.=" dzeit.`PersNr`,";
        $sql.=" dzeit.`Datum`";
        $sql.=" from";
        $sql.=" dzeit";
        $sql.=" join";
        $sql.=" dtattypen on dzeit.tat=dtattypen.tat";
        $sql.=" join";
        $sql.=" calendar on dzeit.`Datum`=calendar.datum";
        $sql.=" where";
        $sql.=" dzeit.`Datum` between '$dbDatumVon' and '$dbDatumBis'";
        $sql.=" and";
        $sql.=" dzeit.`PersNr`='$persnr'";
        $sql.=" and ";
        $sql.=" dtattypen.oestatus='a'";
        if ($nurArbeitsTage == 1) {
            $sql.=" and";
            $sql.=" calendar.cislodne between 1 and 5";
            $sql.=" and";
            $sql.=" calendar.svatek=0";
        }
        $sql.=" group by";
        $sql.=" dzeit.`PersNr`,";
        $sql.=" dzeit.datum";

        $result = mysql_query($sql);
        $atage = mysql_num_rows($result);
        if ($atage == FALSE)
            $atage = 0;
        return $atage;
    }

    /**
     *
     * @param <type> $dbDatumVon
     * @param <type> $dbDatumBis
     * @return integer
     */
    public function getArbTageBetweenDatums($dbDatumVon, $dbDatumBis) {
        $sql = "select count(calendar.datum) as worktage from calendar where svatek=0 and datum between '$dbDatumVon' and '$dbDatumBis' and cislodne<>6 and cislodne<>7";
//    echo $sql;
        $result = mysql_query($sql);
        $row = mysql_fetch_assoc($result);
        return $row['worktage'];
    }

    /**
     *
     * @param integer $persnr osobni cislo
     * @return string vrati datum nastupu ve tvaru YYYY-MM-DD, pokud zadny nema, tak vrati hodnotu 1980-01-01
     */
    public function getEintrittsDatumDB($persnr) {
        $datum = '1980-01-01';
        $sql = "select dpers.eintritt from dpers where persnr='$persnr' limit 1";
        $result = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            $eintritt = $row['eintritt'];
            if ($eintritt != null && strlen($eintritt) > 0)
                $datum = $eintritt;
        }

        return $datum;
    }

    public function getNameVorname($persnr) {
        $sql = "select dpers.`Name` as name,dpers.`Vorname` as vorname from dpers where dpers.`PersNr`=$persnr";
        mysql_query('set names utf8');
        $result = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            return array('name' => $row['name'], 'vorname' => $row['vorname']);
        }
        return NULL;
    }

    /**
     * pocet naplanovanych dnu dovolene mezi datumy
     * @param integer $persnr
     * @param string $vonDatum ( YYYY-MM-DD )
     * @param string $bisDatum ( YYYY-MM-DD )
     * @return integer
     */
    public function getUrlaubtageGenommenBisSoll($persnr, $vonDatum, $bisDatum) {
        $eintrittDatum = $this->getEintrittsDatumDB($persnr);
        // 1. January
        $datvon = substr($bisdatum, 0, 4) . "-" . "01-01";
        $sql = "select count(datum) as hd from dzeitsoll where dzeitsoll.`Datum` between '$vonDatum' and '$bisDatum' and persnr='$persnr' and dzeitsoll.oe='d' and dzeitsoll.`Datum`>='$eintrittDatum'";
//        echo 'sql='.$sql;
        $result = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            $genommenBis = $row['hd'];
        }
        else
            $genommenBis = 0;

        return $genommenBis;
    }

    /**
     *
     * @param <type> $persnr
     * @return <type>
     */
    public function getProbezeitAktual($persnr) {
        $sql = " select if(dpersvertrag.probezeit is not null,DATE_FORMAT(dpersvertrag.probezeit,'%Y-%m-%d'),'') as probezeit";
        $sql.= " from dpersvertrag";
        $sql.= " where";
        $sql.= " dpersvertrag.persnr=$persnr";
        $sql.= " order by";
        $sql.= " dpersvertrag.eintritt desc";
        $rows = $this->getQueryRows($sql);
        if ($rows !== NULL) {
            $row = $rows[0];
            return $row['probezeit'];
        }
        else
            return '';
    }

    /**
     *
     * @param <type> $persnr
     * @return <type> 
     */
    public function getBefristetAktual($persnr) {
        $sql = " select if(dpersvertrag.befristet is not null,DATE_FORMAT(dpersvertrag.befristet,'%Y-%m-%d'),'') as befristet";
        $sql.= " from dpersvertrag";
        $sql.= " where";
        $sql.= " dpersvertrag.persnr=$persnr";
        $sql.= " order by";
        $sql.= " dpersvertrag.eintritt desc";
        $rows = $this->getQueryRows($sql);
        if ($rows !== NULL) {
            $row = $rows[0];
            return $row['befristet'];
        }
        else
            return '';
    }

    /**
 * gives info about holiday for persnr
 * @param integer $persnr
 * @param string $bisDatum in form of YYYY-MM-DD
 *
 * @return array('rest'=>$rest,'anspruch'=>$anspruch,'alt'=>$alt,'gekrzt'=>$gekrzt,'genommen'=>$genommenBis)
 */
public function getUrlaubBisDatum($persnr,$bisDatum) {
    $sql = "select durlaub1.jahranspruch,durlaub1.rest,durlaub1.gekrzt from durlaub1 where `PersNr`='$persnr'";
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
    // should be only 1 row
        $row = mysql_fetch_assoc($result);
        $anspruch = $row['jahranspruch'];
        $alt = $row['rest'];
        $gekrzt = $row['gekrzt'];
    }
    else {
        $anspruch = 0;
        $rest = 0;
        $alt = 0;
        $gekrzt = 0;
    }

    // holiday day from begin of years to $bisDatum
    // 1. January
    $datvon = substr($bisDatum, 0, 4)."-"."01-01";
    $sql = "select count(datum) as hd from dzeit where dzeit.`Datum` between '$datvon' and '$bisDatum' and persnr='$persnr' and dzeit.tat='d'";
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($result);
        $genommenBis = $row['hd'];
    }
    else
        $genommenBis = 0;

    $rest = $anspruch + $alt + $gekrzt - $genommenBis;

    return array('rest'=>$rest,'anspruch'=>$anspruch,'alt'=>$alt,'gekrzt'=>$gekrzt,'genommen'=>$genommenBis);
}

public function getUrlaubTageInMonatSoll($persnr,$monat,$jahr,$vonAktual=FALSE) {
    $datvon = $jahr."-".$monat."-01";
    // get number of days in month
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $datbis = $jahr."-".$monat."-".$pocetDnuVMesici;
    $aktualDatum = date('Y-m-d');
    
    if($vonAktual===TRUE){
	$sql = "select count(dzeitsoll.datum) as urlaubtage from dzeitsoll where persnr='$persnr' and datum between '$aktualDatum' and '$datbis' and oe='d'";
    }
    else{
	$sql = "select count(dzeitsoll.datum) as urlaubtage from dzeitsoll where persnr='$persnr' and datum between '$datvon' and '$datbis' and oe='d'";
    }
    
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($result);
        $urlaubDays = $row['urlaubtage'];
    }
    else
        $urlaubDays = 0;

    return $urlaubDays;

}

public function getUrlaubTageInMonatIst($persnr,$monat,$jahr) {
    $datvon = $jahr."-".$monat."-01";
    // get number of days in month
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $datbis = $jahr."-".$monat."-".$pocetDnuVMesici;

    $aktualDatum = date('Y-m-d');
    $aktualJahr = date('Y');
    $aktualTag = date('d');
    $aktualMonat = date('m');
    if($monat==$aktualMonat && $jahr==$aktualJahr){
        // zajime me aktualni mesic, tj. vratim skutecne vybrane dny do aktualniho dne
        $sql = "select count(dzeit.datum) as urlaubtage from dzeit where persnr='$persnr' and datum>='$datvon' and datum<'$aktualDatum' and tat='d'";
    }
    else if(($jahr*12+$monat)<($aktualJahr*12+$aktualMonat)){
        // zajima me predchozi mesic muzi vzit od prvniho dne az do datbis ( tj. konec mesice )
        $sql = "select count(dzeit.datum) as urlaubtage from dzeit where persnr='$persnr' and datum between '$datvon' and '$datbis' and tat='d'";
    }
    else{
        // zajima me nasleduji mesic, v budoucnu si jeste nemohl skutecne nic vybrat, takze rovnou vratim 0
        // POZOR - kdyby nekdo napred zadal dovolenou do tabulky dzeit, tak stejne vratim nulu !!!!
        return 0;
    }
//    $sql = "select count(dzeit.datum) as urlaubtage from dzeit where persnr='$persnr' and datum between '$datvon' and '$datbis' and tat='d'";
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($result);
        $urlaubDays = $row['urlaubtage'];
    }
    else
        $urlaubDays = 0;

    return $urlaubDays;

}

    /**
     * pocet vybranych dnu dovolene
     * @param integer $persnr
     * @param string $bisdatum datum do ktereho nascitam vybrane dny dovolene od zacatku roku
     */
    public function getUrlaubtageGenommenBis($persnr, $bisdatum) {
        // holiday day from begin of years to $bisDatum
        // 2009-11-09 - vzit v uvahu datum nastupu, protoze clovek mohl nastoupit v lednu a vzit si dovolenou
        // v zari mu skoncila smlouva a od rijna znovu nastoupi, budu mu brat vybranou dovolenou jen od doby nastupu
        $eintrittDatum = $this->getEintrittsDatumDB($persnr);

        // porovnam eintrittdatum a bisdatum
        $eintrittStamp = strtotime($eintrittDatum);
        $bisStamp = strtotime($bisdatum);
        // 1. January
        $datvon = substr($bisdatum, 0, 4) . "-" . "01-01";
        if ($bisStamp < $eintrittStamp)
            $sql = "select count(datum) as hd from dzeit where dzeit.`Datum` between '$datvon' and '$bisdatum' and persnr='$persnr' and dzeit.tat='d'";
        else
            $sql = "select count(datum) as hd from dzeit where dzeit.`Datum` between '$datvon' and '$bisdatum' and persnr='$persnr' and dzeit.tat='d' and dzeit.`Datum`>='$eintrittDatum'";
        //echo 'sql='.$sql;
        $result = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            $genommenBis = $row['hd'];
        }
        else
            $genommenBis = 0;

        return $genommenBis;
    }

    /**
     * vraci F pokud cas von patri do ranni smeny, jinak vraci S default rozsah pro ranni smenu je od 02:01 do 14:00
     * @param string $von cas od ve tvaru HHMM
     * @param $vh = start ranni smehy hodiny
     * @param $vm = start ranni smehy minuty
     * @param $bh = konec ranni smehy hodiny
     * @param $bm = start ranni smehy minuty
     */
    public function getSchichtFromVon($von, $vh = 2, $vm = 1, $bh = 14, $bm = 0) {
        $od = mktime($vh, $vm);
        $do = mktime($bh, $bm);
        $vonHod = substr($von, 0, 2);
        $vonMin = substr($von, 3, 2);
        $testovanyCas = mktime(intval($vonHod), intval($vonMin));

        if ($testovanyCas >= $od && $testovanyCas < $do)
            $schicht = 'F';
        else
            $schicht = 'S';
        return $schicht;
    }

    /**
     * vraci retez s moznymi OE pro dane abgnr
     * 
     * @param int $abgnr
     * @return string
     */
    public function getOEForAbgnr($abgnr) {
        $oes = "";
        $query = "select `dtaetkz-abg`.`OE` from `dtaetkz-abg` where `dtaetkz-abg`.`abg-nr`='$abgnr'";
        $result = mysql_query($query);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            $oe = $row['OE'];
            if ($oe == null)
                $oes = "";
            else
                $oes = $oe;
        }
        return $oes;
    }

    function getRundlaufLieferant($rundlaufId){

	$sql = "select * from drundlauf where id='$rundlaufId'";
	$res = mysql_query($sql);
    }
    
    function getKundeFromAuftransnr($auftrag) {
        $sql = "select kunde from daufkopf where (auftragsnr='$auftrag')";
        $res = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_array($res);
        $kunde = trim($row['kunde']);
        if (strlen($kunde) == 3)
            return $row['kunde'];
        return substr($auftrag, 0, 3);
    }

    public function getOEForFrSp($schicht) {
        $oearray = array();
        $query = "select dtattypen.tat as oe from dtattypen where dtattypen.fr_sp='$schicht'";
        $result = mysql_query($query);
        if (mysql_affected_rows() > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                array_push($oearray, $row['oe']);
            }
        }
        return $oearray;
    }

    public function getPGFromAuftragsnr($auftragsnr) {
        $kunde = $this->getKundeFromAuftransnr($auftragsnr);
        $query = "select dksd.`Kunden_Stat_Nr` as pg from dksd where dksd.`Kunde`='$kunde'";
        $result = mysql_query($query);
        $pg = 9;
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($result);
            $pg = $row['pg'];
        }
        return $pg;
    }

    public function getOESForPG($pg) {
        $oes = array();
        $query = "select dtattypen.tat from dtattypen where dtattypen.`PG`='$pg'";
        $result = mysql_query($query);
        if (mysql_affected_rows() > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                array_push($oes, $row['tat']);
            }
        }
        return $oes;
    }

    /**
     * vrati pole oe, ktere maji oestaus = $oestatus
     * @param string $oestatus
     * @param boolean $gleich = TRUE (default) vrati pole s OE jejich status je roven oestatus, pri $gleich = FALSE vrati pole oe jejichz status neni roven oestatus
     * @return array
     */
    public function getOESForOEStatus($oestatus, $gleich = TRUE) {
        if ($gleich == TRUE)
            $query = "select dtattypen.tat from dtattypen where oestatus='$oestatus'";
        else
            $query = "select dtattypen.tat from dtattypen where oestatus<>'$oestatus'";

        //echo $query;
        $result = mysql_query($query);
        $oeArray = array();
        while ($row = mysql_fetch_assoc($result)) {
            array_push($oeArray, $row['tat']);
        }
        return $oeArray;
    }

    /**
     * vyhleda cas prichodu do prace, ktery byl zadan bez casu odchodu
     * @param <type> $persnr
     * @param <type> $datum
     * @param boolean $delete FALSE NEmaze nalezeny zaznam, TRUE smaze nalezeny zaznam, tudiz muzu vlozit novy e nemusim delat update puvodniho
     */
    public function getAnwesenheitVonFuerAbgang($persnr, $datum, $delete = FALSE) {
        $sql = "select dzeit.id,DATE_FORMAT(dzeit.anw_von,'%H:%i') as von from dzeit where persnr='$persnr' and datum='$datum'";
        $res = mysql_query($sql, $this->con) or die(mysql_error());
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            // smazu nalezeny radek
            if ($delete == TRUE)
                mysql_query("delete from dzeit where id='" . $row['id'] . "' limit 1");
            return $row['von'];
        }
        else {
            return NULL;
        }
    }

    /**
     * vrati oestatus pro zadany oe
     * oe status bere z tabulky dtattypen
     * @param string $oe 
     */
    public function getOEStatusForOE($oe) {
        $sql = "select dtattypen.oestatus from dtattypen where tat='$oe'";
        $res = mysql_query($sql, $this->con) or die(mysql_error());
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_array($res);
            return $row['oestatus'];
        } else {
            return "";
        }
    }

    public function getRegelOE($persnr) {
        $sql = "select dpers.regeloe from dpers where persnr='$persnr'";
//        dbConnect();
        $res = mysql_query($sql);
        $row = mysql_fetch_assoc($res);
        return $row['regeloe'];
    }

    public function getAlternativOE($persnr) {
        $sql = "select alteroe as regeloe from dpers where persnr='$persnr'";
//        dbConnect();
        $res = mysql_query($sql);
        $row = mysql_fetch_assoc($res);
        return $row['regeloe'];
    }

    public function getRegelarbeitDatum($monat, $jahr, $persnr) {
        $bisDatum = sprintf("%04d-%02d-%02d", $jahr, $monat, 1);
        $sql = "select dstddif.regelstunden_weiter as regelstunden from dstddif where dstddif.datum<'$bisDatum' and dstddif.persnr=$persnr order by datum desc limit 1";
        $res = mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_assoc($res);
            return $row['regelstunden'];
        } else {
            return NULL;
        }
    }

    /**
     *
     * @param <type> $persnr
     * @return <type> 
     */
    public function getRegelarbzeit($persnr) {
        $sql = "select dpers.regelarbzeit from dpers where persnr='$persnr'";
//        dbConnect();
        $res = mysql_query($sql);
        $row = mysql_fetch_assoc($res);
        return $row['regelarbzeit'] == null ? 0 : $row['regelarbzeit'];
    }

    /**
     *
     * @param string $datum ve formatu YYYY-MM-DD
     * @return boolean true pokud je datum sobota, nedele nebo svatek
     *                  false jinak nebo v pripade, ze datum nenajdu v kalendari
     */
    public function isDatumVikendSvatek($datum) {
        $sql = "select calendar.cislodne,calendar.svatek from calendar where datum = '$datum'";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_assoc($res);
            if ($row['cislodne'] == 6 || $row['cislodne'] == 7 || $row['svatek'] != 0)
                return true;
            else
                return false;
        }
        else
            return false;
    }

    /**
     *  vytvori zaznam pro dochazku + automaticky vlozi zaznam do druecku
     * @param <type> $persnr
     * @param <type> $schicht
     * @param <type> $datum
     * @param <type> $von
     * @param <type> $bis
     * @param <type> $pause1
     * @param <type> $pause2
     * @param <type> $stunden
     * @param <type> $tatigkeit
     * @return array vystup['verb'] - pocet minut vlozenych automaticky do druecku, vystup['noaduplicita'] = 1 v pripade, ze chci
     * zadat cinnost jinou nez 'a' a ta uz pro dany persnr,datum v tabulce dzeit existuje
     */
    public function insertAnwesenheit($persnr, $schicht, $datum, $von, $bis, $pause1, $pause2, $stunden, $tatigkeit) {

        $vystup = array(
            "verb" => 0,
            "noaduplicita" => 0
        );

        $user = $this->get_user_pc();

        $cVA = $this->getCopyVzAbyToVerbFlag($persnr);
        $drueckVerb = 0;
        if($cVA)
            $drueckVerb = $this->getVerbPersNrDatum ($persnr, $this->make_DB_datum ($datum));
        
        $datumRoz = explode(".", $datum); // Roz�e�eme datum na jednotliv� �daje
        $datumOriginal = $datum;
        $datum = $datumRoz[2] . "-" . $datumRoz[1] . "-" . $datumRoz[0]; // Op�t ho spoj�me
        //test jestli zadavam jen prichod, poznam to podle toho, ze cas odchodu bude 00:00
        if (($bis == '00:00') && ($von != '00:00')) {
            // zadal jsem je prichod, docasne vyrobim zaznam s anw_bis = null

            $vonHod = substr($von, 0, 2); // roz�e�eme p��chod na �daje
            $vonMin = substr($von, 3, 2); // roz�e�eme p��chod na �daje

            $vonOriginal = $von;
            $von = date("y-m-d H:i:s", mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2])); // sestav�me nov� p��chod i s datumem

            $stundenNetto = 0;
            $pause1 = 0;
            $pause2 = 0;

            $sql = "insert into dzeit";
            $sql.=" (Persnr, Datum, Stunden, Schicht, tat, anw_von, anw_bis, pause1, pause2,comp_user_accessuser)";
            $sql.=" values ('$persnr', '$datum', '$stundenNetto', '$schicht', '$tatigkeit', '$von', null, $pause1, $pause2,'$user')";
            $res = mysql_query($sql, $this->con) or die(mysql_error());
            return $vystup;
        } else if (($von == '00:00') && ($bis != '00:00')) {
            // zadal jen odchod, najdu si k nemu cas prichodu a pote postupuju standartne jako pri zadani obou casu
            // samotny prichod najdu jako radek s casem bis = null
            $von = $this->getAnwesenheitVonFuerAbgang($persnr, $datum, TRUE);
        }

        // pro tatigkeit si zjistim oestatus
        $oestatus = $this->getOEStatusForOE($tatigkeit);
        // zjistim si obsah anw_ist a anw_soll
        $anwArray = $this->getAnwArrayForOE($tatigkeit);
//        echo "insertanwesenheit :   oestatus=$oestatus";
//        print_r($anwArray);
        // zjistim i regelstunden pro persnr
        $regelstunden = $this->getRegelarbzeit($persnr);


        if ($pause2 == "")
            $pause2 = 0;

        if ($oestatus != "a") {
            $stunden = 0;
        } // Pokud neni �innost nastavena na "a" pak nastav�me po�et hodin na '0'!
        // podle hodnoty $anwArray['anw_ist'] upravim stunden
        // pokud je hodnota = 'R' nastavim stunden na regelarbstunden

        $bPrepocitat = TRUE;
        if (!strcmp($anwArray['anw_ist'], 'R')) {
            $stunden = $regelstunden;
            $bPrepocitat = FALSE;
        }


//        echo "za podminkou> regelstunden = $regelstunden, stunden = $stunden";
        //---------------------------------------------------------------------------------------------------------------------
        // u vybranych oe , ktere se zadavaji i pres vikend a svatky nastavim stunden = 0 pokud jde o svatek nebo vikend
        $oeNulove = $this->getOESForOEStatus('n');
        //var_dump($oeNulove);
//        echo "inArray ".in_array($tatigkeit, $oeNulove);
//        echo "datum ".$datum;
//        echo "datumOriginal ".$datumOriginal;
//        echo "isvikendsvatek ".$this->isDatumVikendSvatek($this->make_DB_datum($datum));

        if (in_array($tatigkeit, $oeNulove) && $this->isDatumVikendSvatek($this->make_DB_datum($datumOriginal)))
            $stunden = 0;
        //---------------------------------------------------------------------------------------------------------------------
//        $datumRoz = explode(".",$datum); // Roz�e�eme datum na jednotliv� �daje
//        $datumOriginal = $datum;
//        $datum = $datumRoz[2]."-".$datumRoz[1]."-".$datumRoz[0]; // Op�t ho spoj�me

        $vonHod = substr($von, 0, 2); // roz�e�eme p��chod na �daje
        $vonMin = substr($von, 3, 2); // roz�e�eme p��chod na �daje

        $vonOriginal = $von;
        $bisOriginal = $bis;

        $bisHod = substr($bis, 0, 2); // roz�e�eme odchod na �daje
        $bisMin = substr($bis, 3, 2); // roz�e�eme odchod na �daje
//        $vonStamp = mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2],0);
        $vonStamp = mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2]);
        // kvuli prechodu na stredoevropsky cas
//        $vonStamp1 = mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2],1);
        $vonStamp1 = mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2]);
//        $bisStamp = mktime($bisHod, $bisMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2],1);
        $bisStamp = mktime($bisHod, $bisMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2]);
        $von = date("y-m-d H:i:s", $vonStamp1); // sestav�me nov� p��chod i s datumem
        $bis = date("y-m-d H:i:s", $bisStamp); // sestav�me nov� odchod i s datumem

        if ($stunden != 0 && $bPrepocitat) {
            // jeste jednou si prepocitam stunden
            $hodin = ($bisStamp - $vonStamp1) / 60 / 60;
            $stunden = round($hodin, 2);
        }

        $stundenNetto = $stunden - $pause1 - $pause2;

        if($cVA) $stundenNetto = round($drueckVerb/60,2);
        
        $sql = "insert into dzeit";
        $sql.=" (Persnr, Datum, Stunden, Schicht, tat, anw_von, anw_bis, pause1, pause2,comp_user_accessuser)";
        $sql.=" values ('$persnr', '$datum', '$stundenNetto', '$schicht', '$tatigkeit', '$von', '$bis', $pause1, $pause2,'$user')";
//        echo "<br>sql = $sql";
        $res = mysql_query($sql, $this->con) or die(mysql_error());
        //return $sql;
        // pokus uzivatel patri do smeny s automatickym zadavanim vykonu do druecku, tam mu vytvorim i vykon v druecku
        if ($oestatus == 'a') {
            $vystup['verb'] = $this->insertAutoLeistungUnproduktiv($persnr, $schicht, $datumOriginal, $vonOriginal, $bisOriginal, $pause1, $pause2, $oestatus, $tatigkeit);
        }
        return $vystup;
    }

    /**
     * vrati cislo dilu podle id v dauftr
     * @param <type> $id
     * @return <type> 
     */
    function getTeilFromDauftrId($id) {
        $teil = "";
        $id = mysql_real_escape_string($id);
        $sql = "select dauftr.teil from dauftr where dauftr.id_dauftr='$id'";
//        echo $sql;
        $result = mysql_query($sql, $this->con) or die(mysql_errno());
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result);
            $teil = $row['teil'];
        }
        return $teil;
    }

    /**
     * vrati radek z dauftr podle id v dauftr
     * @param <type> $id
     * @return <type>
     */
    function getRowFromDauftrId($id) {
        $teil = null;
        $id = mysql_real_escape_string($id);
        $sql = "select * from dauftr where dauftr.id_dauftr='$id'";
        $result = mysql_query($sql, $this->con) or die(mysql_errno());
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result);
            $teil = $row;
        }
        return $teil;
    }

    /**
     *
     * @param <type> $teil cislo dilu, pro ktery chci vratit obsahy jednotlivych skladu
     */
    function getLagerBestandForTeil($teil, $stampbis,$von=NULL) {

        // zjistim si datum inventury
        if($von===NULL) 
	    $datumVon = $this->getInventurDatumForTeil($teil);
	else
	    $datumVon = $von;

        $sql = "select";
        $sql.=" dlagerbew.teil,";
        $sql.=" sum(if(dlagerbew.lager_nach='0D',dlagerbew.gut_stk,0)) as plus_0D,";
        $sql.=" sum(if(dlagerbew.lager_von='0D',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_0D,";
        $sql.=" sum(if(dlagerbew.lager_nach='0S',dlagerbew.gut_stk,0)) as plus_0S,";
        $sql.=" sum(if(dlagerbew.lager_von='0S',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_0S,";
        $sql.=" sum(if(dlagerbew.lager_nach='1R',dlagerbew.gut_stk,0)) as plus_1R,";
        $sql.=" sum(if(dlagerbew.lager_von='1R',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_1R,";
        $sql.=" sum(if(dlagerbew.lager_nach='2T',dlagerbew.gut_stk,0)) as plus_2T,";
        $sql.=" sum(if(dlagerbew.lager_von='2T',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_2T,";
        $sql.=" sum(if(dlagerbew.lager_nach='3P',dlagerbew.gut_stk,0)) as plus_3P,";
        $sql.=" sum(if(dlagerbew.lager_von='3P',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_3P,";
        $sql.=" sum(if(dlagerbew.lager_nach='4R',dlagerbew.gut_stk,0)) as plus_4R,";
        $sql.=" sum(if(dlagerbew.lager_von='4R',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_4R,";
        $sql.=" sum(if(dlagerbew.lager_nach='5K',dlagerbew.gut_stk,0)) as plus_5K,";
        $sql.=" sum(if(dlagerbew.lager_von='5K',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_5K,";
        $sql.=" sum(if(dlagerbew.lager_nach='5Q',dlagerbew.gut_stk,0)) as plus_5Q,";
        $sql.=" sum(if(dlagerbew.lager_von='5Q',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_5Q,";
        $sql.=" sum(if(dlagerbew.lager_nach='6F',dlagerbew.gut_stk,0)) as plus_6F,";
        $sql.=" sum(if(dlagerbew.lager_von='6F',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_6F,";
        $sql.=" sum(if(dlagerbew.lager_nach='8E',dlagerbew.gut_stk,0)) as plus_8E,";
        $sql.=" sum(if(dlagerbew.lager_von='8E',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_8E,";
        $sql.=" sum(if(dlagerbew.lager_nach='8X',dlagerbew.gut_stk,0)) as plus_8X,";
        $sql.=" sum(if(dlagerbew.lager_von='8X',dlagerbew.gut_stk,0)) as minus_8X,";
        $sql.=" sum(if(dlagerbew.lager_nach='XX',dlagerbew.gut_stk,0)) as plus_XX,";
        $sql.=" sum(if(dlagerbew.lager_von='XX',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_XX,";
        $sql.=" sum(if(dlagerbew.lager_nach='XY',dlagerbew.gut_stk,0)) as plus_XY,";
        $sql.=" sum(if(dlagerbew.lager_von='XY',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_XY,";
        $sql.=" sum(if(dlagerbew.lager_nach='8V',dlagerbew.gut_stk,0)) as plus_8V,";
        $sql.=" sum(if(dlagerbew.lager_von='8V',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_8V,";
        $sql.=" sum(if(dlagerbew.lager_nach='9V',dlagerbew.gut_stk,0)) as plus_9V,";
        $sql.=" sum(if(dlagerbew.lager_von='9V',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_9V,";
        $sql.=" sum(if(dlagerbew.lager_nach='9R',dlagerbew.gut_stk,0)) as plus_9R,";
        $sql.=" sum(if(dlagerbew.lager_von='9R',dlagerbew.gut_stk+dlagerbew.auss_stk,0)) as minus_9R,";
        $sql.=" sum(if(dlagerbew.lager_nach='A2',dlagerbew.auss_stk,0)) as plus_A2,";
        $sql.=" sum(if(dlagerbew.lager_von='A2',dlagerbew.auss_stk,0)) as minus_A2,";
        $sql.=" sum(if(dlagerbew.lager_nach='A4',dlagerbew.auss_stk,0)) as plus_A4,";
        $sql.=" sum(if(dlagerbew.lager_von='A4',dlagerbew.auss_stk,0)) as minus_A4,";
        $sql.=" sum(if(dlagerbew.lager_nach='A6',dlagerbew.auss_stk,0)) as plus_A6,";
        $sql.=" sum(if(dlagerbew.lager_von='A6',dlagerbew.auss_stk,0)) as minus_A6,";
        $sql.=" sum(if(dlagerbew.lager_nach='B2',dlagerbew.auss_stk,0)) as plus_B2,";
        $sql.=" sum(if(dlagerbew.lager_von='B2',dlagerbew.auss_stk,0)) as minus_B2,";
        $sql.=" sum(if(dlagerbew.lager_nach='B4',dlagerbew.auss_stk,0)) as plus_B4,";
        $sql.=" sum(if(dlagerbew.lager_von='B4',dlagerbew.auss_stk,0)) as minus_B4,";
        $sql.=" sum(if(dlagerbew.lager_nach='B6',dlagerbew.auss_stk,0)) as plus_B6,";
        $sql.=" sum(if(dlagerbew.lager_von='B6',dlagerbew.auss_stk,0)) as minus_B6";
        $sql.=" from dlagerbew";
        $sql.=" where";
        $sql.=" (";
        $sql.=" (dlagerbew.teil = '$teil')";
        $sql.=" and (dlagerbew.date_stamp between '$datumVon' and '$stampbis')";
        $sql.=" )";
        $sql.=" group by teil";

//    echo "<br>".$sql;
        $rowLagerBestand = "NO_DLAGERBEW";

        $result = mysql_query($sql, $this->con) or die(mysql_errno());
        if (mysql_num_rows($result) > 0) {
            $rowLagerBestand = mysql_fetch_array($result);
//            $rowInventur = $this->getInventurStandForTeil($teil);
//            if ($rowInventur == null)
//                return "NO_INVENTUR";
//            else
//                $rowLagerBestand['inventur'] = $rowInventur;
        }
        return $rowLagerBestand;
    }

    /**
     * vrati seznam aktalnich dilu pro daneho zakaznika
     * seznam bere z tabulky dkopf
     * aktualnost dilu je dana stampem v tabulce dauftr, ktery nesmi byt starsi nez days
     * 
     * @param <type> $kunde
     * @param integer $days pocet dnu stari stamp v dauftr pro dany dil
     * @return array
     */
    function getActiveTeilArrayForKunde($kunde, $days) {

        $kunde = mysql_real_escape_string($kunde);
        $sql = "select dkopf.teil,MIN(DATEDIFF(NOW(),dauftr.stamp1)) as alt from dkopf join dauftr on dkopf.teil=dauftr.teil where ((kunde='$kunde') and (DATEDIFF(NOW(),dauftr.stamp1)<'$days'))  group by teil ";
        $result = mysql_query($sql, $this->con) or die(mysql_errno());

        $row = null;
        $poleDilu = null;

        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_array($result)) {
                $poleDilu[$row['teil']] = $row['teil'] . "  ( " . $row['alt'] . " )";
            }
        }
        return $poleDilu;
    }

    /**
     * vrati pole se seznamem skladu a stavem kusu s inventurou pro dany dil
     * @param <type> $teil
     * @return array
     */
    function getInventurStandForTeil($teil) {
        $teil = mysql_real_escape_string($teil);
        $sql = "select * from dlagerstk where teil='$teil'";
        $result = mysql_query($sql, $this->con) or die(mysql_errno());

        $row = null;
        $invArray = null;

        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_array($result)) {
                $invArray[$row['lager']] = $row['stk'];
                $invDatum = $row['datum_inventur'];
            }
            $invArray['inventur_datum'] = $invDatum;
        }
        return $invArray;
    }

    /**
     *
     * @param type $von
     * @param type $bis
     * @param type $persnr 
     */
    public function getSollStundenLautDzeitSoll($von,$bis,$persnr){
	$sql.=" select ";
	$sql.=" dzeitsoll.persnr,";
	$sql.=" sum(dzeitsoll.stunden) as sumstunden";
	$sql.=" from dzeitsoll";
	$sql.=" where ";
	$sql.=" dzeitsoll.datum between '$von' and '$bis' and dzeitsoll.persnr=$persnr";
	$sql.=" group by";
	$sql.=" dzeitsoll.persnr";
	
	$result = mysql_query($sql);
	if(mysql_affected_rows()>0){
	    $row = mysql_fetch_assoc($result);
	    return floatval ($row['sumstunden']);
	}
	else
	    return 0;
	    
    }
    
    public function getSollStundenLautCalendar($von,$bis,$stundenProTag) {
    $jahr = $rok;
    $monat = $mesic;
    $datvon = $von;
    $datbis = $bis;

    $sql = "select count(datum) as workdays from calendar where calendar.cislodne<6 and calendar.svatek=0 and datum between '$datvon' and '$datbis'";
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);
    $workDays = $row['workdays'];
    $sollStunden = $workDays * $stundenProTag;
    return array("arbtage"=>$workDays,"sollstunden"=>$sollStunden);
    }

    /**
     * vrati seznam skladu z tabulky dlager 
     */
    function getLagerArray($lager=NULL) {
	$sql = "select dlager.`Lager` as kz,dlager.`LagerBeschreibung` as beschreibung from dlager where dlager.lager like '$lager%' order by dlager.`Lager`";
	if($lager===NULL)
	    $sql = "select dlager.`Lager` as kz,dlager.`LagerBeschreibung` as beschreibung from dlager order by dlager.`Lager`";
        $result = mysql_query($sql, $this->con) or die(mysql_errno());
        $lagerArray = null;
        while ($row = mysql_fetch_array($result)) {
            $lagerArray[$row['kz']] = $row['beschreibung'];
        }
        return $lagerArray;
    }


    /**
     * 
     * @param type $field
     * @param type $value
     * @param type $id
     * @return type
     */
    public function updateEinkaufAufforderungFieldFromId($field, $value, $id,$user) {
	$sql = "update einkauf_anforderungen set `$field`='$value',edit_stamp=NOW(),last_editor='$user'";
	$sql.=" where (id=$id)";
	mysql_query($sql);
	return mysql_affected_rows();
    }
    
    /**
     * 
     * @param type $field
     * @param type $value
     * @param type $dauftr_id
     * @return type
     */
    public function updateDauftrFieldAllePositionenProPalFromId($field, $value, $dauftr_id) {
	$dauftrRow = $this->getDauftrRow($dauftr_id);
	$auftragsnr = $dauftrRow['auftragsnr'];
	$pal = $dauftrRow['pal'];
	$teil = $dauftrRow['teil'];
	
	$sql = "update dauftr set `$field`='$value'";
	$sql.=" where ((auftragsnr='$auftragsnr') and (teil='$teil') and (`pos-pal-nr`='$pal'))";
	mysql_query($sql);
	//return $sql;
	return mysql_affected_rows();
    }
    
    
//    function updateDauftr_Termin_AuftragsnrExp_PalExp_fremdauftr_fremdpos($stk,$termin,$auftragsnr_exp,$pos_pal_nr_exp,$fremdauftr,$fremdpos,$dauftr_id)
//{
//	$dauftrRow = getDauftrRowFromId($dauftr_id);
//	
//	$pos_pal_nr_exp=chop($_GET['pos_pal_nr_exp']);
//	if(strlen($pos_pal_nr_exp)==0)
//		$pos_pal_nr_exp='NULL';
//		
//	$auftragsnr=$dauftrRow['auftragsnr'];
//	$pal=$dauftrRow['pos-pal-nr'];
//	$teil=$dauftrRow['teil'];
//	$sql = "update dauftr set `stück`='$stk',termin='$termin',`auftragsnr-exp`=$auftragsnr_exp,`pal-nr-exp`=$pos_pal_nr_exp,fremdauftr='$fremdauftr',fremdpos='$fremdpos'";
//	$sql.=" where ((auftragsnr='$auftragsnr') and (teil='$teil') and (`pos-pal-nr`='$pal')) limit 20";
//	mysql_query('set names utf8');
//	mysql_query($sql);
//	$mysql_error=mysql_error();
//	
//	// musim vzhledem ke zmene poctu importnich kusu udelat i zmenu v dlagerbew
//	// najdu di odpovidajici import_auftrag,teil,import_pal
//	// mam z predesla
//
//	// zmena nemuzu udelat jednoduchy update, protoze v pripade, ze mam udelanou inventuru, tak se mi posune
//	// pri updatu i timestamp a ten nemuzu natvrdo zapsat.
//
//	// musim udelat storno zaznam a vytvorit novy
//	// nejdriv si vytahnu stary zaznam
//	// musim vystornovat sumu vsech kusu daneho dilu
//	// TODO: nemusi spravne fungovat pokud se v prubehu zmeni prvni sklad
//	$sql_select = "select sum(gut_stk) as gut_stk,max(lager_nach) as lager_nach from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (teil='$teil') and (lager_von='0'))";
//	$res = mysql_query($sql_select);
//	$row = mysql_fetch_array($res);
//	$gut_stk = $row['gut_stk'];
//	$storno_stk = $gut_stk * (-1);
//	$lager_nach = $row['lager_nach'];
//	$user = get_user_pc();
//
//	// pripravim storno zaznam
//	$sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser)";
//	$sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$storno_stk','0','$lager_nach','$user')";
//	// pokud je co stornovat, provedu prikaz
//	if($storno_stk!=0)
//        mysql_query($sql_insert_storno);
//
//	// pripravim novy zaznam
//	$sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser)";
//	$sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$stk','0','$lager_nach','$user')";
//	mysql_query($sql_insert_storno);
//
//	$archiv = $potvrzeni - (45 * $kolem);
//	// pri uprave poctu kusu u dilu, ktery uz ma inventuru zobrazim hlaseni
//	// co mam vratit za hodnotu a jak ji vyhodnotit ?
//	// 1. zjistim si datum inventury dilu
//
//	//
//	//	$sql_update = "update dlagerbew set gut_stk='$stk' where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (teil='$teil') and (lager_von='0')) limit 1";
//	//	mysql_query($sql_update);
//	
//	return $mysql_error;
//}

    /**
     *
     * @param type $stk
     * @param type $termin
     * @param type $auftragsnr_exp
     * @param type $pos_pal_nr_exp
     * @param type $fremdauftr
     * @param type $fremdpos
     * @param type $dauftr_id
     * @return type 
     */
    public function updateDauftr_Termin_AuftragsnrExp_PalExp_fremdauftr_fremdpos($stk, $termin, $auftragsnr_exp, $pos_pal_nr_exp, $fremdauftr, $fremdpos, $dauftr_id,$gt,$user,$bemerkung) {

	$dauftrRow = $this->getDauftrRow($dauftr_id);

	$auftragsnr = $dauftrRow['auftragsnr'];
	$pal = $dauftrRow['pal'];
	$teil = $dauftrRow['teil'];
	
	$sql = "update dauftr set bemerkung='$bemerkung',giesstag='$gt',`stück`='$stk',termin='$termin',`auftragsnr-exp`=$auftragsnr_exp,`pal-nr-exp`=$pos_pal_nr_exp,fremdauftr='$fremdauftr',fremdpos='$fremdpos'";
	$sql.=" where ((auftragsnr='$auftragsnr') and (teil='$teil') and (`pos-pal-nr`='$pal')) limit 20";
	$this->query($sql);

	// musim vzhledem ke zmene poctu importnich kusu udelat i zmenu v dlagerbew
	// najdu di odpovidajici import_auftrag,teil,import_pal
	// mam z predesla
	// zmena nemuzu udelat jednoduchy update, protoze v pripade, ze mam udelanou inventuru, tak se mi posune
	// pri updatu i timestamp a ten nemuzu natvrdo zapsat.
	// musim udelat storno zaznam a vytvorit novy
	// nejdriv si vytahnu stary zaznam
	// musim vystornovat sumu vsech kusu daneho dilu
	// TODO: nemusi spravne fungovat pokud se v prubehu zmeni prvni sklad
	// to cele provedu jen v pripade ze se zmenil pocet importnich kusu

	$sql_select = "select sum(gut_stk) as gut_stk,max(lager_nach) as lager_nach from dlagerbew where ((auftrag_import='$auftragsnr') and (pal_import='$pal') and (teil='$teil') and (lager_von='0'))";
	$rows = $this->getQueryRows($sql_select);
	$row = $rows[0];
	$gut_stk = $row['gut_stk'];
	$storno_stk = $gut_stk * (-1);
	$lager_nach = $row['lager_nach'];
	
	// pripravim storno zaznam
	$sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser)";
	$sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$storno_stk','0','$lager_nach','$user')";
	
	// pokud je co stornovat, provedu prikaz
	if ($storno_stk != 0){
	    $this->query($sql_insert_storno);
	}
	// pripravim novy zaznam
	$sql_insert_storno = "insert into dlagerbew (auftrag_import,teil,pal_import,gut_stk,lager_von,lager_nach,comp_user_accessuser)";
	$sql_insert_storno.=" values ('$auftragsnr','$teil','$pal','$stk','0','$lager_nach','$user')";
	$this->query($sql_insert_storno);

	return $mysql_error;
    }

    /**
     * vrati datum inventury pro dany dil
     * pokud dil nemam inventuru vratim null
     * 
     * @param <type> $teil 
     * @return String datum inventury ve tvaru YYYY-MM-DD HH:mm:ss
     */
    function getInventurDatumForTeil($teil) {

        $teil = mysql_real_escape_string($teil);
        $inventuraDatum = $teil;
        $sql = "select dlagerstk.datum_inventur from dlagerstk where dlagerstk.teil='$teil' limit 1";
//        echo "<hr>".$sql."<hr>";
        $result = mysql_query($sql, $this->con) or die(mysql_errno());
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result);
            $inventuraDatum = $row['datum_inventur'];
        }
//        echo "<hr>".$inventuraDatum."<hr>";
        return $inventuraDatum;
    }

    /**
     *  zkontroluje zda pro zadane parametry uz existuje v dzeit zaznam
     *
     * @param <type> $persnr
     * @param <type> $tat
     * @param <type> $datum
     * @return <type>
     */
    function hasAnwesenheit($persnr, $tat, $datum) {
        $datumDB = $this->make_DB_datum($datum);
        $sql = "select persnr from dzeit where ((persnr='$persnr') and (tat='$tat') and (datum='$datumDB'))";
        //echo "<br>jsem v has anwesenheit sql = $sql";
        $result = mysql_query($sql, $this->con) or die(mysql_error());
        return mysql_num_rows($result);
    }

    public function insertTransport($preis, $kfz, $datumDB, $persnr) {
        $sql = "insert into " . self::TABLE_TRANSPORT . " (persnr,datum,preis,kfz,route_id) values($persnr,'$datumDB','$preis',$kfz,0)";
        //echo $sql;
        mysql_query($sql);
        return mysql_insert_id();
    }

    /**
     * vlozi automaticky vykon do druecku pro neproduktivni
     * @param <type> $persnr
     * @param <type> $schicht
     * @param <type> $datum
     * @param <type> $von
     * @param <type> $bis
     * @param <type> $pause1
     * @param <type> $tat
     * @return int -1 pokud nic nevlozil, jinak vraci spotrebovany cas bez pauzy v minutach
     */
    public function insertAutoLeistungUnproduktiv($persnr, $schicht, $datum, $von, $bis, $pause1, $pause2, $tat, $tatigkeit) {
        $verb = -1;
	if ($tat == 'a') {
	    //2014-09-03 autoleistung bude zavisle jen na priznaku u cloveka, 
	    //zaroven u nej bude definovano jake abgnr se vlozi do druecku
	    $sql = "select dpers.auto_leistung,dpers.auto_leistung_abgnr from dpers where `PersNr`='$persnr'";
	    $res = mysql_query($sql, $this->con);
	    if (mysql_num_rows($res) > 0) {
		$row = mysql_fetch_array($res);
		$autopers = $row['auto_leistung'];
		$abgnr = $row['auto_leistung_abgnr'];
		if ($autopers != 0) {
		    $auftrag = 999999;
		    $teil = 9999;
		    $insertstamp = 'NOW()';
		    $vonDB = $this->make_DB_datetime($von, $datum);
		    $bisDB = $this->make_DB_datetime($bis, $datum);
		    $datumDB = $this->make_DB_datum($datum);
		    // 
		    // pokud jsem tam nejaky stejny zaznam se stejnym datumem, operaci a persnr, tak ho smazu	    
		    // $sql_delete = "delete from drueck where ((auftragsnr='$auftrag') and (teil='$teil') and (persnr='$persnr') and (datum='$datumDB') and (taetnr='$abgnr')) limit 1";
		    // mysql_query($sql_delete,$this->con) or die ("chyba".mysql_error());

		    $user = $this->get_user_pc();
		    // spozitam spotrebovany cas
		    if ($pause2 == "")
			$pause2 = 0;

		    // 2010-05-31 uprava / pauzu nepocitam , ale vlozim zadanou uzivatelem
		    //$pause1 = round(1/17*$this->getVerbMinuten($von, $bis)) + $pause2*60;
		    $pause1 = round($pause1 * 60 + $pause2 * 60);
		    $verb = $this->getVerbMinuten($von, $bis) - $pause1;
		    $sql = "insert into drueck ";
		    $sql.=" (auftragsnr,teil,taetnr,`verb-zeit`,persnr,datum,`verb-von`,`verb-bis`,`verb-pause`,schicht,oe,comp_user_accessuser,insert_stamp) ";
		    $sql.=" values ('$auftrag','$teil','$abgnr','$verb','$persnr','$datumDB','$vonDB','$bisDB','$pause1','$schicht','$tatigkeit','$user',$insertstamp)";
		    mysql_query($sql, $this->con) or die("chyba" . mysql_error());
		}
	    }

//            // zjistim priznak a abgnr u smeny
//            $sql = "select dschicht.auto_leistung,dschicht.auto_abgnr from dschicht where `Schichtnr`='$schicht'";
//            $res = mysql_query($sql, $this->con);
//            if (mysql_num_rows($res) > 0) {
//                $row = mysql_fetch_array($res);
//                $auto = $row['auto_leistung'];
//                $abgnr = $row['auto_abgnr'];
//                if ($auto != 0) {
	    // zjistim priznak u cloveka
	}
	return $verb;
    }

    /**
     * vrati pocet minut mezi zadanymi casy HH:MM
     * @param <type> $von
     * @param <type> $bis
     */
    public function getVerbMinuten($von, $bis) {
        $vontime = mktime(substr($von, 0, 2), substr($von, 3, 2));
        $bistime = mktime(substr($bis, 0, 2), substr($bis, 3, 2));
        $rozdil = ($bistime - $vontime) / 60;
        return $rozdil;
    }

    /**
     * vrati retezec s ip adresou pocitace a prihlasovacim jmenem uzivatele
     * @return string
     */
    public function get_user_pc() {
        $pocitac = "PHP_" . $_SERVER["REMOTE_ADDR"];
        $ident = $pocitac . "/" . $_SESSION["user"];
        return $ident;
    }

    public function naplnPoleSvatku($von, $bis) {
	$datvon = $von;
	$datbis = $bis;

	$sql = "select calendar.datum from calendar where calendar.svatek<>0 and calendar.datum between '$datvon' and '$datbis'";

	$result = mysql_query($sql);
	$i = 0;
	$pole = array();
	while ($row = mysql_fetch_assoc($result)) {
	    $pole[$i] = trim(substr($row['datum'], 0, 10));
	    $i++;
	}
	return $pole;
    }

    /**
     * vytvori datetime pro databazi ze zadaneho datumu a casu
     * @param <type> $time HH:MM
     * @param <type> $datum ve tvaru DD.MM.RRRR
     * @return <type>
     */
    public function make_DB_datetime($time, $datum) {
        $vonHod = substr($time, 0, 2); // roz�e�eme p��chod na �daje
        $vonMin = substr($time, 3, 2); // roz�e�eme p��chod na �daje
        $datumRoz = explode(".", $datum); // Roz�e�eme datum na jednotliv� �daje

        $von = date("Y-m-d H:i:s", mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2])); // sestav�me nov� p��chod i s datumem

        return $von;
    }

    /**
     *
     * @param type $date
     * @param type $time
     * @return null 
     */
    public function datetimeOrNull($date, $time) {
	if (strlen($date) == 10) {
	    if (strlen(trim($time)) == 0)
		$time = "00:00";
	    // zajistit spravny format casu
	    $timeArray = split(":", $time);
	    $time = sprintf("%02d:%02d", $timeArray[0], $timeArray[1]);
	    return $this->make_DB_datetime($time, $date);
	}
	return NULL;
    }

    /**
     * vytvoru datum vhodne pro databazi z datumu ve tvaru DD.MM.RRRR
     * @param <type> $datum
     * @return <type>
     */
    public function make_DB_datum($d) {
	$datum='';
	if(strlen(trim($d))==0) return $datum;
	if($d==NULL) return $datum;
        $datumRoz = explode(".", $d);
	if(is_array($datumRoz)){
	    if(count($datumRoz)==3){
		$datum = $datumRoz[2] . "-" . $datumRoz[1] . "-" . $datumRoz[0];
	    }
	}
        return $datum;
    }

    /**
     * 
     */
    public function getLastImportSollTime($kunde){
	$imnr = "00:00";
	$sql.=" select";
	$sql.=" DATE_FORMAT(daufkopf.im_datum_soll,'%H:%i') as time";
	$sql.=" from daufkopf";
	$sql.=" where";
	$sql.=" daufkopf.kunde=$kunde";
	$sql.=" order by ";
	$sql.=" DATE_FORMAT(daufkopf.im_datum_soll,'%Y-%m-%d') desc,";
	$sql.=" daufkopf.auftragsnr desc";
	$sql.=" limit 1";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    $imnr = $r[0]['time'];
	}
	return $imnr;
    }
    /**
     * 
     * @param type $kunde
     */
    public function getLastImportNr($kunde){
	$imnr = 0;
	$sql.=" select";
	$sql.=" auftragsnr";
	$sql.=" from daufkopf";
	$sql.=" where";
	$sql.=" daufkopf.kunde=$kunde";
	$sql.=" order by ";
	$sql.=" DATE_FORMAT(daufkopf.Aufdat,'%Y-%m-%d') desc,";
	$sql.=" daufkopf.auftragsnr desc";
	$sql.=" limit 1";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    $imnr = intval($r[0]['auftragsnr']);
	}
	return $imnr;
    }

    /**
     * 
     * @param type $kunde
     * @return string
     */
    public function getBordelTeilProKunde($kunde){
	$sql.=" select dkopf.Teil as teil";
	$sql.=" from dkopf";
	$sql.=" where";
	$teil = sprintf("99%03d",$kunde);
	$sql.=" dkopf.Teil='$teil'";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return $r[0]['teil'];
	}
	else{
	    return '';
	}
    }
    /**
     * 
     * @param type $kunde
     */
    public function getPlanTeilProKunde($kunde){
	$sql.=" select dkopf.Teil as teil";
	$sql.=" from dkopf";
	$sql.=" where";
	$teil = sprintf("99%03dIM",$kunde);
	$sql.=" dkopf.Teil='$teil'";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return $r[0]['teil'];
	}
	else{
	    return '';
	}
    }

    public function createNewImport($imNr,$kunde,$minpreis,$aufdatDateTime,$sollImDateTime,$waehrKz,$bemerkung,$bestellnr,$sollExDateTime,$standard_zielort_id){
	$sql.="insert into daufkopf (auftragsnr,kunde,minpreis,Aufdat,im_datum_soll,waehr_kz,bemerkung,bestellnr,ex_datum_soll,zielort_id)";
	$sql.=" values($imNr,$kunde,'$minpreis','$aufdatDateTime','$sollImDateTime','$waehrKz','$bemerkung','$bestellnr','$sollExDateTime','$standard_zielort_id')";
	mysql_query($sql);
	return mysql_insert_id();
    }

    public function getRechnungKz($abgnr){
	$retval = "";
	$sql.="select `dtaetkz-abg`.dtaetkz from `dtaetkz-abg` where `abg-nr`=$abgnr";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    $retval = $r[0]['dtaetkz'];
	}
	return $retval;
    }
    public function getPlanStkProKunde($kunde){
	$vzkd = 0;
	$sql.=" select dksd.import_plan_vzkd as vzkd from dksd where Kunde=$kunde";
//	$sql.=" select";
//	$sql.=" sum(dispostatnrkunde.vzkd) as vzkd";
//	$sql.=" from  dispostatnrkunde";
//	$sql.=" where";
//	$sql.=" kunde=$kunde";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return intval($r[0]['vzkd']);
	}
	return $vzkd;
    }

    
    public function getStandardZielortId($kunde){
	$sql.="select zielorte.id from zielorte where kunde=$kunde order by standard desc,zielort asc limit 1";
	$r = $this->getQueryRows($sql);
	if($r!==NULL){
	    return $r[0]['id'];
	}
	else {
	    return 0;
	}
    }
    /**
     * 
     * @param type $import
     */
    public function getVzKdSollImport($import){
	$sql.=" select ";
	$sql.=" sum(dauftr.`stück`*VzKd) as sum_vzkd";
	$sql.=" from dauftr";
	$sql.=" where";
	$sql.=" auftragsnr='$import'";
	$r=$this->getQueryRows($sql);
	if($r!==NULL){
	    return intval($r[0]['sum_vzkd']);
	}
	else
	    return 0;
    }
    
    public function getExporteVzkdDatumKunde($kunde,$time){
	$datum = date('Y-m-d',$time);
	$sql.=" select";
	$sql.=" daufkopf.auftragsnr";
	$sql.=" from";
	$sql.=" daufkopf";
	$sql.=" where";
	$sql.=" (daufkopf.kunde=$kunde)";
	$sql.=" and (DATE_FORMAT(daufkopf.ex_datum_soll,'%Y-%m-%d')='$datum')";
	return $this->getQueryRows($sql);
    }
    
    public function getImporteVzkdDatumKunde($kunde,$time){
	$datum = date('Y-m-d',$time);
	$sql.=" select";
	$sql.=" daufkopf.auftragsnr,";
	$sql.=" sum(dauftr.`stück`*dauftr.VzKd) as vzkd";
	$sql.=" from";
	$sql.=" daufkopf";
	$sql.=" join dauftr on dauftr.auftragsnr=daufkopf.auftragsnr";
	$sql.=" where";
	$sql.=" (daufkopf.kunde=$kunde)";
	$sql.=" and (DATE_FORMAT(daufkopf.Aufdat,'%Y-%m-%d')='$datum')";
	$sql.=" and (dauftr.`auftragsnr-exp` is null)";
	$sql.=" and (dauftr.`pal-nr-exp` is null)";
	$sql.=" group by";
	$sql.=" daufkopf.auftragsnr";
	return $this->getQueryRows($sql);
    }
    /**
     * overi platnost zadaneho datumu ve tvaru den mesic [rok]
     * oddelovace muzou byt , . - mezera
     * pokud nelze vyrobit platne datum vratim null
     *
     * @param string $value datum ve tvaru d,m,rok
     * @return string datum ve formatu DD.MM.RRRR nebo null pokud nemuzu vyrobit platne datum
     */
    public function validateDatum($value) {

        // casti datumu povolim oddelovat znaky : ,.- a mezera
        $vymenit = array(",", ".", "-", " ");
        if (strlen($value) >= 3) {
            // datum byl zadan i s rokem
            // sjednotim si oddelovaci znak
            $novy_datum = str_replace($vymenit, "/", $value);
            // rozkouskuju na jednotlivy casti
            $dily = explode("/", $novy_datum);
            $pocetDilu = count($dily);

            // trochu otestuju jednotlivy dily,jestli tam neni uplnej nesmysl
            if (($dily[1] < 13) && ($dily[1] > 0) && ($dily[0] > 0) && ($dily[0] < 32)) {
                if ($pocetDilu == 2) {
                    // nezadal rok
                    $dily[2] = date('Y');
                }
                if (($pocetDilu == 3) && (strlen($dily[2]) == 0)) {
                    // nezadal rok
                    $dily[2] = date('Y');
                }

                $timestamp = mktime(0, 0, 0, $dily[1], $dily[0], $dily[2]);
                $rok = date("Y", $timestamp);
                $mesic = date("m", $timestamp);
                $den = date("d", $timestamp);
                // provedena jen mala kontrola datumu
                return "$den.$mesic.$rok";
            }
            else
                return null;
        }
        else
            return null;
    }

}

?>
