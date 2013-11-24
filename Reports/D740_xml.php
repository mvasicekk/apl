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

$views=array("D740_sumteilstk","");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");

$pt=" create view $viewname as ";
$pt.=" select daufkopf.kunde,";
$pt.=" drech.auftragsnr,";
$pt.=" daufkopf.bestellnr,";
$pt.="daufkopf.fertig as rechdatumraw,";
$pt.="DATE_FORMAT(daufkopf.fertig,'%Y-%m-%d') as rechdatum,";
$pt.="DATE_FORMAT(daufkopf.ausliefer_datum,'%Y-%m-%d') as lieferdatum,";
//$pt.=" if(fremdauftr is null,' ',fremdauftr) as fremdauftr,";
//$pt.=" if(fremdpos is null,'',fremdpos) as fremdpos,";
$pt.=" teil,";
$pt.=" teilbez,";
$pt.=" `taet-kz` as tat,";
$pt.=" text1,";
$pt.=" dm,";
$pt.=" abgnr,";
$pt.=" sum(`stück`) as stk,";
$pt.=" sum(ausschuss) as auss";
$pt.=" from drech";
$pt.=" join daufkopf on drech.auftragsnr=daufkopf.auftragsnr";
$pt.=" where (((drech.AuftragsNr)='$export') and (`Taet-kz`<>'I'))";
//$pt.=" GROUP BY drechneu.kunde, drechneu.AuftragsNr, drechneu.vom, drechneu.an, drechneu.fremdauftr, drechneu.fremdpos, drechneu.Teil, drechneu.teilbez, drechneu.`Taet-kz`, drechneu.DM, drechneu.abgnr";
$pt.=" GROUP BY daufkopf.kunde, drech.auftragsnr,drech.teil, drech.teilbez, drech.`Taet-kz`, drech.DM, drech.abgnr";

$db->query($pt);
//echo $pt;

$D740_sumteilstk=$pcip.$views[0];

//$sql = "select * from $D743_sumteilstk";

$sql=" select $D740_sumteilstk.kunde";
$sql.=", auftragsnr";
$sql.=", bestellnr";
$sql.=",rechdatum";
$sql.=",lieferdatum";
$sql.=",DATE_FORMAT(DATE_ADD($D740_sumteilstk.rechdatumraw,INTERVAL dksd.zahnlungziel DAY),'%d.%m.%Y') as zahldatum";
//$sql.=",fusszeile1";
//$sql.=",fusszeile2";
//$sql.=",fusszeile3";
$sql.=",dksd.`waehr-kz` as wahr";
//$sql.=",dkndumrech.mwst";
$sql.=",dksd.zahnlungziel as zahlungsziel";
$sql.=",' ' as rechtext";
//$sql.=",dkndumrech.kontotext";
//$sql.=",dkndumrech.verzweck";
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
//$sql.=", $D74_sumteilstk.origauftrag";
$sql.=", '100' as vom";
//$sql.=", $D743_sumteilstk.an";
//$sql.=", fremdauftr";
//$sql.=", fremdpos";
$sql.=", Teil";
$sql.=", teilbez";
$sql.=", text1";
$sql.=", tat";
$sql.=", stk";
$sql.=", auss";
$sql.=", sum(DM) as preis";
$sql.=", max(abgnr) as reihe";
$sql.=" FROM $D740_sumteilstk";
$sql.=" join dksd on dksd.kunde=$D740_sumteilstk.kunde";
$sql.=" join dkonto on dksd.konto=dkonto.konto";
//$sql.=" join dksd as dksd1 on dksd1.kunde=$D743_sumteilstk.an";
//$sql.=" join dkndumrech on dkndumrech.vom=$D743_sumteilstk.vom and dkndumrech.an=$D743_sumteilstk.an";
//$sql.=" GROUP BY $D743_sumteilstk.kunde, auftragsnr, $D743_sumteilstk.vom, $D743_sumteilstk.an, fremdauftr, fremdpos, Teil, teilbez, tat";
$sql.=" GROUP BY $D740_sumteilstk.kunde, auftragsnr, Teil, teilbez, tat,stk";
//$sql.=" order by fremdauftr,fremdpos,teil,reihe";
$sql.=" order by teil,reihe";

//echo $sql;

$query2xml = XML_Query2XML::factory($db);

function getBetrag($record){
    $value = round(
                    floatval(($record['stk']+$record['auss'])*$record['preis'])
                    ,3);
    return round($value,2);
}

$options = array(
    'encoder' => false,
    'rootTag' => 'D740',
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
        'teile' => array(
            'rootTag' => 'teile',
            'rowTag' => 'teil',
            'idColumn' => 'Teil',
            'elements' => array(
                'teilnr' => 'Teil',
                'teilbez',
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
                                'stk',
                                'auss',
                                'betrag' => '#getBetrag();',
                                'waehrung' => 'wahr'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    )
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
$domxml->save("D740.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
