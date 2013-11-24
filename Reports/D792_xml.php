<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();

$views=array("D740_sumteilstk","");

// povolit naslouchani na portu

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");

$pt=" create view $viewname as ";
$pt.=" select daufkopf.kunde,";
$pt.=" drech.auftragsnr,";
$pt.=" daufkopf.bestellnr,";
$pt.="daufkopf.fertig as rechdatumraw,";
$pt.="DATE_FORMAT(daufkopf.fertig,'%Y-%m-%d') as rechdatum,";
$pt.="DATE_FORMAT(daufkopf.ausliefer_datum,'%Y-%m-%d') as lieferdatum,";
$pt.=" if(drech.fremdauftr is null,' ',drech.fremdauftr) as fremdauftr,";
$pt.=" if(drech.fremdpos is null,'',drech.fremdpos) as fremdpos,";
$pt.=" drech.teil,";
$pt.=" teilbez,";
$pt.=" `taet-kz` as tat,";
$pt.=" text1,";
$pt.=" dm,";
$pt.=" drech.abgnr,";
$pt.=" sum(drech.`stück`) as stk,";
$pt.=" sum(if(kzgut='G',drech.`stück`,0)) as gut_stk,";
$pt.=" sum(ausschuss) as auss";
$pt.=" from drech";
//$pt.=" join dpos on dpos.teil=drech.teil and dpos.`taetnr-aby`=drech.abgnr";
$pt.=" join dauftr on dauftr.`auftragsnr-exp`=drech.auftragsnr and dauftr.teil=drech.teil and dauftr.`pos-pal-nr`=drech.`pos-pal-nr` and dauftr.abgnr=drech.abgnr";
$pt.=" join daufkopf on drech.auftragsnr=daufkopf.auftragsnr";
$pt.=" where (((drech.AuftragsNr)='$export') and (`Taet-kz`<>'I'))";
$pt.=" GROUP BY daufkopf.kunde, drech.auftragsnr,drech.fremdauftr,drech.fremdpos,drech.teil, drech.teilbez, drech.`Taet-kz`, drech.DM, drech.abgnr";

// kompletni dotaz ...
// 
// spusteni pripraveneho pohledu


$db->query($pt);
//echo "pt=$pt";
$D740_sumteilstk=$pcip.$views[0];

$sql=" select $D740_sumteilstk.kunde";
$sql.=", auftragsnr";
$sql.=", bestellnr";
$sql.=",rechdatum";
$sql.=",lieferdatum";
$sql.=",DATE_FORMAT(DATE_ADD($D740_sumteilstk.rechdatumraw,INTERVAL dksd.zahnlungziel DAY),'%d.%m.%Y') as zahldatum";
$sql.=",dksd.`waehr-kz` as wahr";
$sql.=",dksd.zahnlungziel as zahlungsziel";
$sql.=",' ' as rechtext";
$sql.=" ,dkonto.konto";
$sql.=" ,dkonto.`text-konto` as kontotext";
$sql.=" ,dkonto.`text-verwzweck` as verwzweck";
$sql.=",'Abydos s.r.o.' as vomname";
$sql.=",'Hazlov 247' as vomstrasse";
$sql.=",'35132' as vomplz";
$sql.=",'Hazlov' as vomort";
$sql.=",'CZ' as vomland";
$sql.=",dksd.name1 as anname1";
$sql.=",dksd.name2 as anname2";
$sql.=",dksd.Straße as anstrasse";
$sql.=",dksd.plz as anplz";
$sql.=",dksd.ort as anort";
$sql.=",dksd.land as anland";
$sql.=",dksd.dic as andic";
$sql.=", '100' as vom";
$sql.=", fremdauftr";
$sql.=", fremdpos";
$sql.=", $D740_sumteilstk.Teil";
$sql.=", $D740_sumteilstk.teilbez";
$sql.=", dkopf.preis_stk_gut";
$sql.=", dkopf.preis_stk_auss";
$sql.=", text1";
$sql.=", tat";
$sql.=", stk";
$sql.=", gut_stk";
$sql.=", sum(auss) as auss";
$sql.=", sum(DM) as preis";
$sql.=", max(abgnr) as reihe";
$sql.=" FROM $D740_sumteilstk";
$sql.=" join dksd on dksd.kunde=$D740_sumteilstk.kunde";
$sql.=" join dkonto on dksd.konto=dkonto.konto";
$sql.=" join dkopf on $D740_sumteilstk.Teil=dkopf.teil";
$sql.=" GROUP BY $D740_sumteilstk.kunde, auftragsnr, fremdauftr,fremdpos,Teil, teilbez, tat,stk";
$sql.=" order by fremdauftr,fremdpos,teil,reihe";
//echo "sql=$sql";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function getBetrag($record){
    $value = round(
                    floatval(($record['stk']+$record['auss'])*$record['preis'])
                    ,3);
    return round($value,2);
}

$options = array(
    'encoder' => false,
    'rootTag' => 'D742',
    'idColumn' => 'auftragsnr',
    'rowTag' => 'rechnung',
    'elements' => array(
        'auftragsnr',
        'bestellnr',
        'rechdatum',
        'lieferdatum',
        'zahldatum',
        'kunde',
        'vom',
        'wahr',
        'zahlungsziel',
        'rechtext',
        'kontotext',
        'verwzweck',
        'vomname',
        'vomstrasse',
        'vomplz',
        'vomort',
        'vomland',
        'anname1',
        'anname2',
        'anstrasse',
        'anplz',
        'anort',
        'anland',
        'andic',
        'fremdauftr' => array(
            'rootTag' => 'fremdauftraege',
            'idColumn' => 'fremdauftr',
            'rowTag' => 'fremdauftr',
            'elements' => array(
                'fremdauftrnr' => 'fremdauftr',
                'fremdpos' => array(
                    'rootTag' => 'fremdpositionen',
                    'rowTag' => 'fremdpos',
                    'idColumn' => 'fremdpos',
                    'elements' => array(
                        'fremdauftrnr' => 'fremdauftr',
                        'fremdposnr' => 'fremdpos',
                        'teile' => array(
                            'rootTag' => 'teile',
                            'rowTag' => 'teil',
                            'idColumn' => 'Teil',
                            'elements' => array(
                                'teilnr' => 'Teil',
                                'teilbez',
                                'preis_stk_gut',
                                'preis_stk_auss',
                                'taetigkeiten' => array(
                                    'rootTag' => 'taetigkeiten',
                                    'rowTag' => 'taetigkeit',
                                    'idColumn' => 'tat',
                                    'elements' => array(
                                        'tat',
                                        'kusy' => array(
                                            'rootTag' => 'kusy',
                                            'rowTag' => 'kus',
                                            'idColumn' => 'stk',
                                            'elements' => array(
                                                'teilnr' => 'Teil',
                                                'teilbez',
                                                'tat',
                                                'text1',
                                                'preis',
                                                'reihe',
                                                'stk',
                                                'gut_stk',
                                                'auss',
                                                'betrag' => '#getBetrag();',
                                                'waehrung' => 'wahr'
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
         ),
        ),
);


// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
//$domxml->encoding="windows-1250";

// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru
// TODO doplnit nejakou moznost zformatovani, napr. datum zadane ve formulari by melo nejak vypadat
// mozna by bylo lepsi resit to AJAXem uz pri zadavani do formulare
// ale uz pri generovani formulare budu muset pridat handlery podle planovaneho obsahu formulare

foreach($parameters as $var=>$value)
{
	
	// pokud nazev parametru obsahuje _label, budu hledat hodnotu parametru
	if(strpos($var,"_label"))
	{
		$p[$value]=$last_value;
	}
	$last_value=$value;
	//$promenne.=$var."=".$value."&";
}

// v promenne p bych mel mit seznam parametru, pridam ho do XML jako node do domxml
//

$element=$domxml->createElement("parameters");
$parametry=$domxml->firstChild;
$parametry->appendChild($element);
$i=1;
foreach($p as $var=>$value)
{
	$poradinode=$domxml->createElement("N".$i);
	$labelnode=$domxml->createElement("label",$var);
	$valuenode=$domxml->createElement("value",$value);
	$element->appendChild($poradinode);
	$poradinode->appendChild($labelnode);
	$poradinode->appendChild($valuenode);
	$i++;
}


//header('Content-Type: application/xml');
//echo $proc->transformToXML($domxml);



// smazu pouzite pohledy
for($i=0;$i<sizeof($views);$i++)
{

	// pohledy se smazou podle jejich poctu definovaneho polem views
	
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("D792.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
