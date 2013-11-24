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

// dabmahnungvorschlag si vytvori novou tabulku s navrhem na abmahnung podle reklamace

$sql="";
$sql.=" select ";
$sql.="	    daufkopf.kunde,";
$sql.="     dauftr.auftragsnr,";
$sql.="     CONCAT(dauftr.teil,' ') as teil,";
$sql.="     dkopf.verpackungmenge,";
$sql.="     dauftr.`stück` as stk_import,";
$sql.="     if(dauftr.`stk-exp` is null,0,dauftr.`stk-exp`) as stk_export,";
$sql.="     dauftr.`pos-pal-nr` as pal,";
$sql.="     dauftr.abgnr,";
$sql.="     DATE_FORMAT(dauftr.inserted,'%Y-%m-%d') as inserted,";
$sql.="     DATE_FORMAT(e.ausliefer_datum,'%Y-%m-%d') as ausgeliefert,";
$sql.="     dauftr.`auftragsnr-exp` as export,";
$sql.="     1 as nach_lager,";
$sql.="     if(dauftr.`auftragsnr-exp` is not null and e.ausliefer_datum is not null,1,0) as aus_lager";
$sql.=" from dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" join dkopf on dkopf.Teil=dauftr.teil";
$sql.=" left join daufkopf e on e.auftragsnr=dauftr.`auftragsnr-exp`";
$sql.=" where";
$sql.="     ((inserted between '$von' and '$bis')  or (e.ausliefer_datum between '$von' and '$bis'))";
$sql.="     and ((convert(dauftr.`pos-pal-nr`,CHAR) like '%4') or (convert(dauftr.`pos-pal-nr`,CHAR) like '%7'))";
$sql.="     and daufkopf.kunde between '$kdvon' and '$kdbis'";
$sql.="     and dauftr.kzgut='G'";
$sql.=" order by daufkopf.kunde,dauftr.auftragsnr,dauftr.teil,dauftr.`pos-pal-nr`;";

   
//echo "sql=$sql"."<br>";
//exit;

$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'E320',
		'idColumn'=>'kunde',
		'rowTag'=>'kunde',
		'elements'=>array(
                    'kundenr'=>'kunde',
		    'importe'=>array(
			'rootTag'=>'importe',
			'idColumn'=>'auftragsnr',
			'rowTag'=>'import',
			'elements'=>array(
			    'importnr'=>'auftragsnr',
			    'palety'=>array(
				'rootTag'=>'palety',
				'idColumn'=>'pal',
				'rowTag'=>'pal',
				'elements'=>array(
				    'kundenr'=>'kunde',
				    'importnr'=>'auftragsnr',
				    'palnr'=>'pal',
				    'teil',
				    'verpackungmenge',
				    'stk_import',
				    'stk_export',
				    'abgnr',
				    'inserted',
				    'ausgeliefert',
				    'export',
				    'nach_lager',
				    'aus_lager'
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
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("E320.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
