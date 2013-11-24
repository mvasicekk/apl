<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');

// cast pro vytvoreni XML by mela byt v jinaem souboru jmenosestavy_xml.php
$db= &DB::connect('mysql://root:nuredv@localhost/apl');
$db->query("set names utf8");

	
$query2xml = XML_Query2XML::factory($db);
	 
	$sql="select dksd.kunden_stat_nr as pg,dkopf.kunde,auftragsnr,";
	$sql.=" sum(if(stat_nr='S0011',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as T_S0011,";
	$sql.=" sum(if(stat_nr='S0041',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as T_S0041,";
	$sql.=" sum(if(stat_nr='S0051',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as T_S0051,";
	$sql.=" sum(if(stat_nr='S0061',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as T_S0061,";
	$sql.=" sum(if(stat_nr='S0081',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as T_S0081,";
	$sql.=" sum(if(stat_nr='S0091',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as T_S0091,";
	$sql.=" sum(if(stat_nr='X',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as T_X,";
	$sql.=" sum(if(stat_nr='M',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as T_M,";
	$sql.=" sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`)) as celkem";
	$sql.=" from drueck join dkopf using (teil) join dksd using (kunde)";
	$sql.=" join `dtaetkz-abg` on (`dtaetkz-abg`.`abg-nr`=drueck.taetnr)";
	$sql.=" where (drueck.datum between '$datumvon' and '$datumbis')";
	$sql.=" group by pg,dkopf.kunde,auftragsnr";
	
// tady se budou tisknout parametry


$options = array(
		'rootTag'=>'S610',
		'rowTag'=>'produktgruppe',
		'idColumn'=>'pg',
		'elements'=>array(
			'pg',
			'kunde'=>array(
					'rootTag'=>'kunden',
					'rowTag'=>'kunde',
					'idColumn'=>'kunde',
					'elements'=>array(
						'kundenr'=>'kunde',
						'auftragsnr'=>array(
							'rootTag'=>'auftraege',
							'rowTag'=>'auftrag',
							'idColumn'=>'auftragsnr',
							'elements'=>array(
								'auftragsnr',
								'T_S0011',
								'T_S0041',
								'T_S0051',
								'T_S0061',
								'T_S0081',
								'T_S0091',
								'T_X',
								'T_M',
								'celkem'
							)
						)
					)
			)
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

$element=$domxml->createElement("parameters");
$domxml->appendChild($element);
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
$db->disconnect();
//============================================================+
// END OF FILE                                                 
//============================================================+
//$domxml->save("S610.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
