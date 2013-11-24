<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
require_once '../db.php';

// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu

//var_dump($parameters);
//exit;
$teilen = 0;
$auftragsnrTeilen='';
$dt='voll';


if(array_key_exists('hatma', $parameters)){
    $hatma = $parameters['hatma'];
    $teilen = 1;
    $auftragsnrTeilen = $parameters['ma_rechnr'];
    $dt = $parameters['dt'];
}

$parametersPDF = $parameters;

$pcip=get_pc_ip();
$views=array("D742_rechnung","tat_reihenfolge");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" daufkopf.kunde,";
$pt.=" dkonto.konto,";
// a dal
$pt.=" dkonto.`text-konto` as textkonto,";
$pt.=" dkonto.`text-verwzweck` as textverwzweck,";
$pt.=" dksd.name1,";
$pt.=" dksd.name2,";
$pt.=" dksd.`Straße` as strasse,";
$pt.=" dksd.ico,";
$pt.=" dksd.dic,";
$pt.=" dksd.land,";
$pt.=" dksd.plz,";
$pt.=" dksd.ort,";
//$pt.=" dksd.`waehr-kz` as waehrung,";
$pt.=" daufkopf.waehr_kz as waehrung,";
$pt.=" dksd.`rech-anschr` as rechanschr,";
$pt.=" DATE_FORMAT(ADDDATE(daufkopf.fertig,dksd.zahnlungziel),'%d.%m.%Y') as zahlenbis,";
$pt.=" DATE_FORMAT(daufkopf.fertig,'%d.%m.%Y') as fertig,";
$pt.=" DATE_FORMAT(daufkopf.ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,";
$pt.=" daufkopf.auftragsnr,";
$pt.=" daufkopf.bestellnr,";
$pt.=" if(fremdauftr is null or fremdauftr='','---',fremdauftr) as fremdauftr,";
$pt.=" if(fremdpos is null or fremdpos='','---',fremdpos) as fremdpos,";
$pt.=" teil,";
//$pt.=" `pos-pal-nr` as pal,";
$pt.=" teilbez,";
$pt.=" text1,";
$pt.=" `taet-kz` as tatkz,";
$pt.=" dm as preis,";
$pt.=" sum(stück) as stk,";
$pt.=" sum(ausschuss) as auss";
$pt.=" from drech";
$pt.=" join daufkopf using (auftragsnr)";
$pt.=" join dksd on drech.kunde=dksd.kunde";
$pt.=" join dkonto on dksd.konto=dkonto.konto";
$pt.=" where ((auftragsnr='$export') and (`taet-kz`<>'I')";
if($hatma!=0){
    if($parameters['dt']=='ma'){
        $pt.=" and ( drech.rechnr_druck=".$parameters['ma_rechnr']." )";
    }
    elseif($parameters['dt']=='regular'){
        $pt.=" and ( drech.rechnr_druck=".$export." )";
    }
    else{
        $pt.=" and ( 1 )";
    }
}
$pt.=" )";
$pt.=" group by";
$pt.=" fremdauftr,";
$pt.=" fremdpos,";
$pt.=" teil,";
//$pt.=" pal,";
$pt.=" teilbez,";
$pt.=" text1,";
$pt.=" tatkz,";
$pt.=" preis";
//$pt.=" order by";
//$pt.=" teil";


//echo $pt."<br>";
//exit;
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.=" dtaetkz,min(`abg-nr`) as minofabgnr";
$pt.=" from `dtaetkz-abg`";
$pt.=" group by dtaetkz";
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy

$D742_rechnung=$pcip.$views[0];
$tat_reihenfolge = $pcip.$views[1];

$sql="SELECT kunde,konto,textkonto,textverwzweck,name1,";
$sql.=" name2,strasse,ico,dic,land,plz,";
$sql.=" ort,waehrung,rechanschr,zahlenbis,fertig,ausliefer_datum,auftragsnr,bestellnr,";
//$sql.=" fremdauftr,fremdpos,teil,pal,teilbez,text1,tatkz,sum(preis) as preis,stk,sum(preis*(stk+auss)) as betrag,";
$sql.=" fremdauftr,fremdpos,teil,teilbez,text1,tatkz,sum(preis) as preis,stk,sum(preis*(stk+auss)) as betrag,";
$sql.=" auss";
$sql.=" FROM $D742_rechnung join $tat_reihenfolge on $tat_reihenfolge.dtaetkz=$D742_rechnung.tatkz";
$sql.=" group by";
$sql.=" kunde,konto,textkonto,textverwzweck,name1,";
$sql.=" name2,strasse,land,plz,";
$sql.=" ort,waehrung,rechanschr,zahlenbis,fertig,ausliefer_datum,auftragsnr,bestellnr,";
$sql.=" fremdauftr,fremdpos,teil,teilbez,text1,tatkz,stk,auss";
//$sql.=" fremdauftr,fremdpos,teil,pal,teilbez,text1,tatkz,stk,auss";
$sql.=" order by teil,minofabgnr";
//$sql.=" order by teil,pal,minofabgnr";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry





$options = array(
					'encoder'=>false,
					'rootTag'=>'D760',
					'idColumn'=>'auftragsnr',
					'rowTag'=>'rechnung',
					'elements'=>array(
						'auftragsnr',
						'kunde',
						'konto',
						'textkonto',
						'textverwzweck',
						'name1',
						'name2',
						'strasse',
                                                'ico',
						'dic',
						'land',
						'plz',
						'ort',
						'rechanschr',
						'zahlenbis',
						'fertig',
						'ausliefer_datum',
						'bestellnr',
						'waehrung',
						'fremdauftr'=>array(
							'rootTag'=>'fremdauftraege',
							'idColumn'=>'fremdauftr',
							'rowTag'=>'fremdauftr',
							'elements'=>array(
								'fremdauftrnr'=>'fremdauftr',
								'fremdpos'=>array(
									'rootTag'=>'fremdpositionen',
									'rowTag'=>'fremdpos',
									'idColumn'=>'fremdpos',
									'elements'=>array(
										'fremdauftrnr'=>'fremdauftr',
										'fremdposnr'=>'fremdpos',
										'teile'=>array(
											'rootTag'=>'teile',
											'rowTag'=>'teil',
											'idColumn'=>'teil',
											'elements'=>array(
												'teilnr'=>'teil',
												'teilbez',
//												'paletten'=>array(
//													'rootTag'=>'paletten',
//													'rowTag'=>'palette',
//													'idColumn'=>'pal',
//													'elements'=>array(
//														'palnr'=>'pal',
														'taetigkeiten'=>array(
															'rootTag'=>'taetigkeiten',
															'rowTag'=>'taetigkeit',
															'idColumn'=>'tatkz',
															'elements'=>array(
																'teilnr'=>'teil',
//																'palnr'=>'pal',
																'teilbez',
																'tatkz',
																'text1',
																'preis',
																'stk',
																'auss',
																'betrag',
																'waehrung'
															),
														),
//													),
//												),
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
$domxml->save("D760.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
