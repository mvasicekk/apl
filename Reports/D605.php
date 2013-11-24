<?php

include '../fns_dotazy.php';

if(!$_GET['auftrNr']){$auftrag='';}
$auftrag=$_GET['auftrNr'];

if($auftrag==''){
echo "
<form action='d605.php' method='get'>
<input type='text' size='7' name='auftrNr' id='auftrNr' value='' />
<input type='submit' name='OK' value='OK' />
</form>
";
}else{
dbConnect();
// Vytvoøí pohled na tabulku Dauftr
$query = "
create view view_d605_dauftr
as
SELECT max(if(kzgut='G',`auftragsnr-exp`,0)) as export_lief, dauftr.AuftragsNr, dauftr.`pos-pal-nr`as import_pal, aufdat,max(if(kzgut='G',`pal-nr-exp`,0)) as export_pal,dauftr.Teil, sum(if(kzgut='G',`Stück`,0)) as import_stk,sum(if(kzgut='G',`stk-exp`,0)) as export_stk, sum(if(`taetkz-nr`='P',vzkd,0)) as S0011P,sum(if(kzgut='G',`Stück`,0))*sum(if(`taetkz-nr`='P',vzkd,0)) as sumS0011P, sum(if(`taetkz-nr`='P',1,0)) as cnt_S0011P,sum(if(`taetkz-nr`='T',vzkd,0)) as S0011T, sum(if(kzgut='G',`Stück`,0))*sum(if(`taetkz-nr`='T',vzkd,0)) as sumS0011T, sum(if(`taetkz-nr`='T',1,0)) as cnt_S0011T,sum(if(Stat_Nr='S0041',vzkd,0)) as S0041, sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0041',vzkd,0)) as sumS0041, sum(if(Stat_Nr='S0041',1,0)) as cnt_S0041, sum(if(Stat_Nr='S0051',vzkd,0)) as S0051, sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0051',vzkd,0)) as sumS0051, sum(if(Stat_Nr='S0051',1,0)) as cnt_S0051, sum(if(Stat_Nr='S0061',vzkd,0)) as S0061, sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0061',vzkd,0)) as sumS0061, sum(if(Stat_Nr='S0061',1,0)) as cnt_S0061, sum(if(kzgut='G',`Stück`,0))*sum(vzkd) as sumvzkd, sum(if(kzgut='G',`Stück`,0))*dkopf.gew as imp_gew FROM DAUFTR JOIN dkopf using (teil) JOIN `dtaetkz-abg` ON dauftr.abgnr = `dtaetkz-abg`.`abg-nr` JOIN daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr where (((DAUFTR.AuftragsNr) = $auftrag)) group BY dauftr.AuftragsNr, import_pal,dauftr.Teil order by dauftr.AuftragsNr, import_pal,dauftr.Teil;
";
mysql_query($query) or die(mysql_error());

// Vytvoøí pohled na tabulku Drueck
$query = "
create view view_d605_drueck
as
 SELECT drueck.AuftragsNr, drueck.`pos-pal-nr`as import_pal,drueck.Teil, sum(if(`taetkz-nr`='T',`Stück`,0))  as sum_stk_T, sum(if(`taetkz-nr`='P',`Stück`,0))  as sum_stk_P, sum(if(`taetkz-nr`='St',`Stück`,0))  as sum_stk_St, sum(if(`taetkz-nr`='G',`Stück`,0))  as sum_stk_G, sum(if(`taetkz-nr`='E',`Stück`,0))  as sum_stk_E, sum(if(auss_typ=2,`auss-Stück`,0)) as auss2, sum(if(auss_typ=4,`auss-Stück`,0)) as auss4, sum(if(auss_typ=6,`auss-Stück`,0)) as auss6 FROM DRUECK JOIN `dtaetkz-abg`  ON drueck.TaetNr=`dtaetkz-abg`.`abg-nr` where (((DRUECK.AuftragsNr) = $auftrag)) group BY drueck.AuftragsNr, import_pal,drueck.Teil order by drueck.AuftragsNr, import_pal,drueck.Teil;
";
mysql_query($query) or die(mysql_error());

// Vytvoøí pohled na tabulku drueck
$query = "
create view view_d605_gesamt
as
SELECT drueck.AuftragsNr, drueck.`pos-pal-nr`as import_pal, drueck.Teil, sum(if(dpos.`kzgut`='G',`Stück`,0))  as sum_stk_Gtat FROM DRUECK JOIN `dpos`  on (drueck.teil=dpos.teil) and (drueck.taetnr=dpos.`taetnr-aby`) where (((DRUECK.AuftragsNr) = 355150)) group BY drueck.AuftragsNr, import_pal,drueck.Teil;
";
mysql_query($query) or die(mysql_error());

// Vykoná dotaz na uvedené pohledy

$table_id = 'row';
$query = "
SELECT `view_d605_dauftr`.`export_lief`, `view_d605_dauftr`.`AuftragsNr`, `view_d605_dauftr`.`import_pal`, `view_d605_dauftr`.`export_pal`, `view_d605_dauftr`.`Teil`, `view_d605_dauftr`.`import_stk`, `view_d605_drueck`.`sum_stk_T`, `view_d605_drueck`.`sum_stk_P`, `view_d605_drueck`.`sum_stk_St`, `view_d605_drueck`.`sum_stk_G`, `view_d605_drueck`.`sum_stk_E`, `view_d605_drueck`.`auss2`, `view_d605_drueck`.`auss4`, `view_d605_drueck`.`auss6`, `view_d605_dauftr`.`S0011P`, `view_d605_dauftr`.`sumS0011P`, `view_d605_dauftr`.`cnt_S0011P`, `view_d605_dauftr`.`S0011T`, `view_d605_dauftr`.`sumS0011T`, `view_d605_dauftr`.`cnt_S0011T`, `view_d605_dauftr`.`S0041`, `view_d605_dauftr`.`sumS0041`, `view_d605_dauftr`.`cnt_S0041`, `view_d605_dauftr`.`S0051`, `view_d605_dauftr`.`sumS0051`, `view_d605_dauftr`.`cnt_S0051`, `view_d605_dauftr`.`S0061`, `view_d605_dauftr`.`sumS0061`, `view_d605_dauftr`.`cnt_S0061`, `view_d605_dauftr`.`imp_gew`, `view_d605_dauftr`.`sumvzkd`, `view_d605_dauftr`.`export_stk`, `view_d605_dauftr`.`aufdat`, `sum_stk_Gtat`-`import_stk` AS `GDiff`
FROM (`view_d605_dauftr` LEFT JOIN `view_d605_drueck` ON (`view_d605_dauftr`.`Teil` = `view_d605_drueck`.`Teil`) AND (`view_d605_dauftr`.`import_pal` = `view_d605_drueck`.`import_pal`) AND (`view_d605_dauftr`.`AuftragsNr` = `view_d605_drueck`.`AuftragsNr`)) LEFT JOIN `view_d605_gesamt` ON (`view_d605_dauftr`.`Teil` = `view_d605_gesamt`.`Teil`) AND (`view_d605_dauftr`.`import_pal` = `view_d605_gesamt`.`import_pal`) AND (`view_d605_dauftr`.`AuftragsNr` = `view_d605_gesamt`.`AuftragsNr`)
";

$dbresult = mysql_query($query) or die(mysql_error());

// Create a new XML Document
$doc = new DomDocument('1.0'); 



// Create root node

$root = $doc->createElement('root');
$root = $doc->appendChild($root);

// process one row at a time
$i=1;
$u=1;
$pagCon =50;
while($row = mysql_fetch_assoc($dbresult)){
  $occ = $doc->createElement($table_id);
  $occ = $root->appendChild($occ);

  // add a child node for each field
  
  if($i==$pagCon){
  $occ = $doc->createElement($table_id);
  $occ = $root->appendChild($occ);
  
  $child = $doc->createElement('datAndPage');
  $child = $occ->appendchild($child);
    

  $value = $doc->createTextNode(date('j.n.Y H:i:s')." Strana:".$u);
  $value = $child->appendchild($value);
  
    $occ = $doc->createElement($table_id);
  $occ = $root->appendChild($occ);
  
  $child = $doc->createElement('end');
  $child = $occ->appendchild($child);
    

  $value = $doc->createTextNode('page');
  $value = $child->appendchild($value);
  
    $i=1;
    $u++;
    $pagCon =$pagCon +50;
  }
  foreach($row as $fieldname=>$fieldvalue){
  
    $child = $doc->createElement($fieldname);
    $child = $occ->appendchild($child);
    
      if($fieldvalue==''){$fieldvalue=0;}
      
      if(is_numeric($fieldvalue)){$fieldvalue= round($fieldvalue, 2);}
      
    $value = $doc->createTextNode($fieldvalue);
    $value = $child->appendchild($value);
    
    }
$i++;
}
  $occ = $doc->createElement($table_id);
  $occ = $root->appendChild($occ);
  
  $child = $doc->createElement('datAndPage');
  $child = $occ->appendchild($child);
    

  $value = $doc->createTextNode(date('j.n.Y H:i:s')." Strana:".$u);
  $value = $child->appendchild($value);

  $occ = $doc->createElement($table_id);
  $occ = $root->appendChild($occ);
  
  $child = $doc->createElement('end');
  $child = $occ->appendchild($child);
    

  $value = $doc->createTextNode('konec');
  $value = $child->appendchild($value);

// Vymaže uvedené dotazy
$query = "DROP VIEW `view_d605_dauftr`, `view_d605_drueck`, `view_d605_gesamt`";
mysql_query($query) or die(mysql_error());

mysql_close();

//echo $doc->saveXML();

$xsl = new DomDocument(); 
$xsl->load("d605.xsl"); 
 

/* create the processor and import the stylesheet */ 

$proc = new XsltProcessor(); 
$xsl = $proc->importStylesheet($xsl); 
$proc->setParameter(null, "titles", "Titles"); 

$newdom = $proc->transformToDoc($doc);
$output = $newdom->saveXML();
//$output = html_entity_decode($output);
//echo $output;
/*$tmpfile = tempnam("/tmp", "dompdf_") or die("Nejde ten soubor!");
file_put_contents($tmpfile, $output); // Replace $smarty->fetch()
                                                // with your HTML string

$url = "dompdf.php?input_file=".rawurlencode($tmpfile). 
       "&paper=letter&output_file=".rawurlencode("My Fancy PDF.pdf");

header("Location: http://" . $_SERVER["HTTP_HOST"] . "/APL_WEB/Reports/$url");*/

//require_once("dompdf_config.inc.php");
$output = substr($output, 21);
echo $output;
/*
$dompdf = new DOMPDF();
$dompdf->load_html($output);
$dompdf->set_paper('A4','landscape');

$dompdf->render();
$dompdf->stream("d605.pdf");*/
}

?>
