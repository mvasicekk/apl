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
$views=array("pt_D362","pt_D362_gutstk");

if($datumtyp=="drueck") 
    $datum = 'drueck.datum';
else
    $datum = 'daufkopf.aufdat';

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
if($reporttyp=='IM'){
    $pt="create view $viewname";
    $pt.=" as select";
    $pt.="     daufkopf.kunde as kunde,";
    $pt.="     drueck.Teil as teil,";
    $pt.="     dkopf.gew as netto_gew,";
    $pt.="     drueck.AuftragsNr as auftragsnr,";
    $pt.="     drueck.`auss-art` as auss_art,";
    $pt.="     drueck.`auss_typ`,";
    $pt.="     sum(drueck.`Auss-Stück`) as auss_stk";
    $pt.=" from drueck";
    $pt.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
    $pt.=" join dkopf on dkopf.teil=drueck.teil";
    $pt.=" where";
    $pt.="     ($datum between '$date_von' and '$date_bis')";
    $pt.="     and (daufkopf.kunde between '$kundevon' and '$kundebis')";
    if($bTeil)
	$pt.="     and (dkopf.teil like '$teil')";
//  $pt.="     and drueck.`auss-art`<>0";
    $pt.=" group by";
    $pt.="     daufkopf.kunde,";
    $pt.="     drueck.Teil,";
    $pt.="     drueck.auftragsnr,";
    $pt.="     drueck.`auss-art`";
}
else{
    $pt="create view $viewname";
    $pt.=" as select";
    $pt.="     daufkopf.kunde as kunde,";
    $pt.="     drueck.Teil as teil,";
    $pt.="     dkopf.gew as netto_gew,";
    $pt.="     dauftr.`auftragsnr-exp` as auftragsnr,";
    $pt.="     drueck.`auss-art` as auss_art,";
    $pt.="     drueck.`auss_typ`,";
    $pt.="     sum(drueck.`Auss-Stück`) as auss_stk";
    $pt.=" from drueck";
    $pt.=" join dauftr on dauftr.auftragsnr=drueck.AuftragsNr and dauftr.teil=drueck.teil and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.abgnr=drueck.taetnr";
    $pt.=" join daufkopf on daufkopf.auftragsnr=dauftr.`auftragsnr-exp`";
    $pt.=" join dkopf on dkopf.teil=drueck.teil";
    $pt.=" where";
    $pt.="     (daufkopf.`ausliefer_datum` between '$date_von' and '$date_bis')";
    $pt.="     and (daufkopf.kunde between '$kundevon' and '$kundebis')";
    if($bTeil)
	$pt.="     and (dkopf.teil like '$teil')";

//    $pt.="     and drueck.`auss-art`<>0";
    $pt.=" group by";
    $pt.="     daufkopf.kunde,";
    $pt.="     drueck.Teil,";
    $pt.="     dauftr.`auftragsnr-exp`,";
    $pt.="     drueck.`auss-art`";

}
//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");

if($reporttyp=='IM'){
    $pt="create view $viewname";
//    $pt.=" as SELECT drueck.teil,drueck.auftragsnr,sum(drueck.`Stück`) as gut_stk";
//    $pt.=" FROM `drueck`";
//    $pt.=" join dauftr on drueck.teil=dauftr.teil and drueck.taetnr=dauftr.abgnr and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.auftragsnr=dauftr.auftragsnr";
//    $pt.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
//    $pt.=" WHERE (($datum between '$date_von' and '$date_bis') and (daufkopf.kunde between '$kundevon' and '$kundebis') and (dauftr.kzgut='G'))";
//    $pt.=" group by drueck.teil,drueck.auftragsnr";
    $pt.=" as SELECT dauftr.teil,dauftr.auftragsnr,sum(dauftr.`Stück`) as gut_stk";
    $pt.=" FROM `dauftr`";
    $pt.=" join daufkopf on daufkopf.auftragsnr=dauftr.AuftragsNr";
    $pt.=" WHERE ((daufkopf.aufdat between '$date_von' and '$date_bis') and (daufkopf.kunde between '$kundevon' and '$kundebis') and (dauftr.kzgut='G')";
    if($bTeil)
	$pt.="     and (dauftr.teil like '$teil')";
    $pt.=" )";
    $pt.=" group by dauftr.teil,dauftr.auftragsnr";
}
else{
    $pt="create view $viewname";
    $pt.=" as SELECT drueck.teil,dauftr.`auftragsnr-exp` as auftragsnr,sum(drueck.`Stück`) as gut_stk";
    $pt.=" FROM `drueck`";
    $pt.=" join dauftr on drueck.teil=dauftr.teil and drueck.taetnr=dauftr.abgnr and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.auftragsnr=dauftr.auftragsnr";
    $pt.=" join daufkopf on daufkopf.auftragsnr=dauftr.`auftragsnr-exp`";
    $pt.=" WHERE ((daufkopf.ausliefer_datum between '$date_von' and '$date_bis') and (daufkopf.kunde between '$kundevon' and '$kundebis') and (dauftr.kzgut='G')";
    if($bTeil)
	$pt.="     and (dauftr.teil like '$teil')";
    $pt.=" )";
    $pt.=" group by drueck.teil,dauftr.`auftragsnr-exp`";
}
//echo $pt."<br>";
$db->query($pt);
// pomocny dotay pro zjisteni souctu dobrych kusu


// provedu dotaz nad vytvorenymi pohledy
$pt_D362=$pcip.$views[0];
$pt_D362_gutstk=$pcip.$views[1];

$sql=" select kunde,teil,netto_gew,auftragsnr,auss_art,auss_typ,auss_stk,";
$sql.=" gut_stk";
$sql.=" from $pt_D362 left join $pt_D362_gutstk using(teil,auftragsnr)";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options = array(
    'encoder' => false,
    'rootTag' => 'S372',
    'idColumn' => 'kunde',
    'rowTag' => 'k',
    'elements' => array(
	'kunde',
	'teile' => array(
	    'rootTag' => 'teile',
	    'idColumn' => 'teil',
	    'rowTag' => 'teil',
	    'elements' => array(
		'teilnr' => 'teil',
		'netto_gew',
		'auftraege' => array(
		    'rootTag' => 'auftraege',
		    'rowTag' => 'auftrag',
		    'idColumn' => 'auftragsnr',
		    'elements' => array(
			//'teilnr'=>'teil',
			'auftragsnr',
			'gut_stk',
			'ausschuss' => array(
			    'rootTag' => 'ausschuss',
			    'rowTag' => 'aussart',
			    'idColumn' => 'auss_art',
			    'elements' => array(
				'auss_art',
				'auss_typ',
				'auss_stk',
				'netto_gew',
			    )
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
$domxml->save("S372.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
