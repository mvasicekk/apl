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
$views=array("export_gutstk");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     termin,";
$pt.="     dauftr.auftragsnr,";
$pt.="     dkopf.teillang,";
$pt.="     dkopf.teil,";
$pt.="     dauftr.`pos-pal-nr` as pal,";
$pt.="     dauftr.`stück`*dauftr.kg_stk_bestellung as behaelter_gew_bestellung,";
$pt.="     dauftr.`stück` as stkimport,";
$pt.="     if(dauftr.`stk-exp` is null,0,dauftr.`stk-exp`) as stkexport,";
$pt.="     dauftr.kg_stk_bestellung,";
$pt.="     dauftr.stk_laut_waage,";
$pt.="     dauftr.auss_stk_laut_waage,";
$pt.="     dauftr.abywaage_kg_stk10,";
$pt.="     dauftr.abywaage_behaelter_ist,";
$pt.="     dauftr.abywaage_brutto-dauftr.abywaage_behaelter_ist as behaelter_netto_ist,";
//$pt.="     if(dauftr.`stk-exp` is null,0,dauftr.`stk-exp`)*dauftr.abywaage_kg_stk10+dauftr.abywaage_behaelter_ist as soll_gew_brutto,";
$pt.="     dauftr.abywaage_brutto,";
$pt.="     db1.typ as behaeltertyp,";
$pt.="     db1.beschreibung as behaelter_beschreibung,";
$pt.="     db1.aby_id as behaelter_aby_id,";
$pt.="     dauftr.auss2_stk_exp+dauftr.auss4_stk_exp+dauftr.auss6_stk_exp as auss_stk_exp,";
$pt.="     dauftr.auss_abywaage_kg_stk10,";
$pt.="     dauftr.auss_abywaage_brutto,";
$pt.="     dauftr.auss_abywaage_behaelter_ist,";
$pt.="     dauftr.auss_abywaage_brutto-dauftr.auss_abywaage_behaelter_ist as auss_behaelter_netto_ist,";
$pt.="     dauftr.aussbehaelter,";
$pt.="     db2.typ as auss_behaeltertyp,";
$pt.="     db2.beschreibung as auss_behaelter_beschreibung,";
$pt.="     db2.aby_id as auss_behaelter_aby_id";
$pt.=" from dauftr";
$pt.=" join dkopf on dkopf.`Teil`=dauftr.teil";
$pt.=" left join dbehaelter db1 on db1.id=dauftr.behaelter_id";
$pt.=" left join dbehaelter db2 on db2.id=dauftr.auss_behaelter_id";
$pt.=" where dauftr.termin='$termin' and dauftr.`KzGut`='G'";
$pt.=" order by dauftr.termin,dauftr.auftragsnr,dkopf.teil,dauftr.`pos-pal-nr`";

//echo $pt."<br>";
$db->query($pt);


// provedu dotaz nad vytvorenymi pohledy
$export_gut_stk=$pcip.$views[0];

$sql=" SELECT $export_gut_stk.*";
$sql.=" from $export_gut_stk";
//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options = array(
                                        'encoder'=>false,
					'rootTag'=>'S212',
					'idColumn'=>'termin',
					'rowTag'=>'termin',
					'elements'=>array(
						'geplant'=>'termin',
                                                'importe'=>array(
                                                    'rootTag'=>'importe',
                                                    'rowTag'=>'import',
                                                    'idColumn'=>'auftragsnr',
							'elements'=>array(
                                                            'auftragsnr',
                                                            'teile'=>array(
                                                                'rootTag'=>'teile',
                                                                'rowTag'=>'teil',
                                                                'idColumn'=>'teil',
                                                                    'elements'=>array(
                                                                        'teilnr'=>'teil',
                                                                        'platte'=>'teillang',
                                                                        'paletten'=>array(
                                                                            'rootTag'=>'paletten',
                                                                            'rowTag'=>'palette',
                                                                            'idColumn'=>'pal',
                                                                            'elements'=>array(
                                                                                'teilnr'=>'teil',
                                                                                'platte'=>'teillang',
                                                                                'pal',
                                                                                'behaelter_gew_bestellung',
                                                                                'stkimport',
                                                                                'stkexport',
                                                                                'kg_stk_bestellung',
                                                                                'stk_laut_waage',
                                                                                'abywaage_kg_stk10',
                                                                                'abywaage_behaelter_ist',
                                                                                'behaelter_netto_ist',
                                                                                //'soll_gew_brutto',
                                                                                'abywaage_brutto',
                                                                                'behaeltertyp',
                                                                                'behaelter_beschreibung',
                                                                                'behaelter_aby_id',
                                                                                'auss_stk_exp',
                                                                                'auss_stk_laut_waage',
                                                                                'auss_abywaage_kg_stk10',
                                                                                'auss_abywaage_brutto',
                                                                                'auss_abywaage_behaelter_ist',
                                                                                'auss_behaelter_netto_ist',
                                                                                'aussbehaelter',
                                                                                'auss_behaeltertyp',
                                                                                'auss_behaelter_beschreibung',
                                                                                'auss_behaelter_aby_id',
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
$domxml->save("S212.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
