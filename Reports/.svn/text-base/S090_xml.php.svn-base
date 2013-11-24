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
$views=array("v_fraeseAusgabe","v_fraeseWettBewerbStk","v_fraeseWettBewerbVzkd","v_fraeseGesamtVzkd");

// vytvoreni views pro spojeni v dotazu....
// jmeno si ulozim do stringu a ten pouziju v SQL dotazu.

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT persnr,";
$pt.=" sum(if(day(datum)=1,ausgabestk*factor,0)) as d1,";
$pt.=" sum(if(day(datum)=2,ausgabestk*factor,0)) as d2,";
$pt.=" sum(if(day(datum)=3,ausgabestk*factor,0)) as d3,";
$pt.=" sum(if(day(datum)=4,ausgabestk*factor,0)) as d4,";
$pt.=" sum(if(day(datum)=5,ausgabestk*factor,0)) as d5,";
$pt.=" sum(if(day(datum)=6,ausgabestk*factor,0)) as d6,";
$pt.=" sum(if(day(datum)=7,ausgabestk*factor,0)) as d7,";
$pt.=" sum(if(day(datum)=8,ausgabestk*factor,0)) as d8,";
$pt.=" sum(if(day(datum)=9,ausgabestk*factor,0)) as d9,";
$pt.=" sum(if(day(datum)=10,ausgabestk*factor,0)) as d10,";
$pt.=" sum(if(day(datum)=11,ausgabestk*factor,0)) as d11,";
$pt.=" sum(if(day(datum)=12,ausgabestk*factor,0)) as d12,";
$pt.=" sum(if(day(datum)=13,ausgabestk*factor,0)) as d13,";
$pt.=" sum(if(day(datum)=14,ausgabestk*factor,0)) as d14,";
$pt.=" sum(if(day(datum)=15,ausgabestk*factor,0)) as d15,";
$pt.=" sum(if(day(datum)=16,ausgabestk*factor,0)) as d16,";
$pt.=" sum(if(day(datum)=17,ausgabestk*factor,0)) as d17,";
$pt.=" sum(if(day(datum)=18,ausgabestk*factor,0)) as d18,";
$pt.=" sum(if(day(datum)=19,ausgabestk*factor,0)) as d19,";
$pt.=" sum(if(day(datum)=20,ausgabestk*factor,0)) as d20,";
$pt.=" sum(if(day(datum)=21,ausgabestk*factor,0)) as d21,";
$pt.=" sum(if(day(datum)=22,ausgabestk*factor,0)) as d22,";
$pt.=" sum(if(day(datum)=23,ausgabestk*factor,0)) as d23,";
$pt.=" sum(if(day(datum)=24,ausgabestk*factor,0)) as d24,";
$pt.=" sum(if(day(datum)=25,ausgabestk*factor,0)) as d25,";
$pt.=" sum(if(day(datum)=26,ausgabestk*factor,0)) as d26,";
$pt.=" sum(if(day(datum)=27,ausgabestk*factor,0)) as d27,";
$pt.=" sum(if(day(datum)=28,ausgabestk*factor,0)) as d28,";
$pt.=" sum(if(day(datum)=29,ausgabestk*factor,0)) as d29,";
$pt.=" sum(if(day(datum)=30,ausgabestk*factor,0)) as d30,";
$pt.=" sum(if(day(datum)=31,ausgabestk*factor,0)) as d31,";
$pt.=" sum(ausgabestk*factor) as fraese_celkem";
$pt.=" from dambew join dfraese on dfraese.amnr=dambew.amnr";
$pt.=" where ((datum between '$datumvon' and '$datumbis')) group by persnr";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT persnr,drueck.teil,dkopf.fraese_wettkampf_factor,";
$pt.=" sum(if(day(datum)=1,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d1_teil_stk,";
$pt.=" sum(if(day(datum)=2,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d2_teil_stk,";
$pt.=" sum(if(day(datum)=3,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d3_teil_stk,";
$pt.=" sum(if(day(datum)=4,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d4_teil_stk,";
$pt.=" sum(if(day(datum)=5,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d5_teil_stk,";
$pt.=" sum(if(day(datum)=6,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d6_teil_stk,";
$pt.=" sum(if(day(datum)=7,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d7_teil_stk,";
$pt.=" sum(if(day(datum)=8,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d8_teil_stk,";
$pt.=" sum(if(day(datum)=9,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d9_teil_stk,";
$pt.=" sum(if(day(datum)=10,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d10_teil_stk,";
$pt.=" sum(if(day(datum)=11,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d11_teil_stk,";
$pt.=" sum(if(day(datum)=12,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d12_teil_stk,";
$pt.=" sum(if(day(datum)=13,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d13_teil_stk,";
$pt.=" sum(if(day(datum)=14,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d14_teil_stk,";
$pt.=" sum(if(day(datum)=15,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d15_teil_stk,";
$pt.=" sum(if(day(datum)=16,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d16_teil_stk,";
$pt.=" sum(if(day(datum)=17,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d17_teil_stk,";
$pt.=" sum(if(day(datum)=18,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d18_teil_stk,";
$pt.=" sum(if(day(datum)=19,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d19_teil_stk,";
$pt.=" sum(if(day(datum)=20,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d20_teil_stk,";
$pt.=" sum(if(day(datum)=21,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d21_teil_stk,";
$pt.=" sum(if(day(datum)=22,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d22_teil_stk,";
$pt.=" sum(if(day(datum)=23,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d23_teil_stk,";
$pt.=" sum(if(day(datum)=24,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d24_teil_stk,";
$pt.=" sum(if(day(datum)=25,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d25_teil_stk,";
$pt.=" sum(if(day(datum)=26,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d26_teil_stk,";
$pt.=" sum(if(day(datum)=27,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d27_teil_stk,";
$pt.=" sum(if(day(datum)=28,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d28_teil_stk,";
$pt.=" sum(if(day(datum)=29,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d29_teil_stk,";
$pt.=" sum(if(day(datum)=30,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d30_teil_stk,";
$pt.=" sum(if(day(datum)=31,if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor),0)) as d31_teil_stk,";
$pt.=" sum(if(auss_typ=4,(`Stück`+`auss-Stück`)*fraese_wettkampf_factor,`Stück`*fraese_wettkampf_factor)) as teil_celkem";
$pt.=" from drueck ";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.taetnr";
$pt.=" join dkopf on drueck.teil=dkopf.teil";
$pt.=" where ((datum between '$datumvon' and '$datumbis') and (inwettkampf_flag<>0) and ((dtaetkz='P') or (`abg-nr`=6930))) group by drueck.persnr,drueck.teil,fraese_wettkampf_factor";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT persnr,";
$pt.=" sum(if(day(datum)=1,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d1_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=2,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d2_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=3,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d3_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=4,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d4_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=5,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d5_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=6,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d6_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=7,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d7_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=8,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d8_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=9,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d9_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=10,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d10_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=11,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d11_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=12,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d12_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=13,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d13_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=14,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d14_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=15,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d15_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=16,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d16_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=17,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d17_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=18,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d18_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=19,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d19_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=20,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d20_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=21,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d21_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=22,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d22_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=23,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d23_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=24,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d24_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=25,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d25_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=26,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d26_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=27,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d27_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=28,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d28_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=29,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d29_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=30,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d30_wettkampf_vzkd,";
$pt.=" sum(if(day(datum)=31,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d31_wettkampf_vzkd,";
$pt.=" sum(if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`)) as wettkampf_celkem";
$pt.=" from drueck ";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.taetnr";
$pt.=" join dkopf on drueck.teil=dkopf.teil";
$pt.=" where ((datum between '$datumvon' and '$datumbis') and (inwettkampf_flag<>0) and (dtaetkz='P')) group by drueck.persnr";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[3];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT persnr,";
$pt.=" sum(if(day(datum)=1,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d1_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=2,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d2_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=3,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d3_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=4,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d4_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=5,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d5_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=6,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d6_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=7,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d7_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=8,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d8_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=9,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d9_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=10,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d10_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=11,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d11_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=12,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d12_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=13,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d13_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=14,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d14_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=15,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d15_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=16,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d16_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=17,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d17_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=18,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d18_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=19,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d19_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=20,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d20_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=21,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d21_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=22,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d22_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=23,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d23_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=24,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d24_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=25,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d25_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=26,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d26_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=27,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d27_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=28,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d28_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=29,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d29_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=30,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d30_nowettkampf_vzkd,";
$pt.=" sum(if(day(datum)=31,if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as d31_nowettkampf_vzkd,";
$pt.=" sum(if(auss_typ=4,(`Stück`+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`)) as nowettkampf_celkem";
$pt.=" from drueck ";
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.taetnr";
$pt.=" join dkopf on drueck.teil=dkopf.teil";
$pt.=" where ((datum between '$datumvon' and '$datumbis') and (dtaetkz='P')) group by drueck.persnr";

//echo $pt."<br>";
$db->query($pt);


// provedu dotaz nad vytvorenymi pohledy
$v_fraeseAusgabe=$pcip.$views[0];
$v_fraeseWettBewerbVzkd=$pcip.$views[2];
$v_fraeseGesamtVzkd=$pcip.$views[3];
$v_fraeseWettBewerbStk=$pcip.$views[1];


$sql=" SELECT $v_fraeseAusgabe.persnr, dpers.Name, dpers.Vorname, $v_fraeseWettBewerbStk.teil,";
$sql.=" $v_fraeseWettBewerbStk.fraese_wettkampf_factor,";
$sql.=" $v_fraeseAusgabe.d1,";
$sql.=" $v_fraeseAusgabe.d2,";
$sql.=" $v_fraeseAusgabe.d3,";
$sql.=" $v_fraeseAusgabe.d4,";
$sql.=" $v_fraeseAusgabe.d5,";
$sql.=" $v_fraeseAusgabe.d6,";
$sql.=" $v_fraeseAusgabe.d7,";
$sql.=" $v_fraeseAusgabe.d8,";
$sql.=" $v_fraeseAusgabe.d9,";
$sql.=" $v_fraeseAusgabe.d10,";
$sql.=" $v_fraeseAusgabe.d11,";
$sql.=" $v_fraeseAusgabe.d12,";
$sql.=" $v_fraeseAusgabe.d13,";
$sql.=" $v_fraeseAusgabe.d14,";
$sql.=" $v_fraeseAusgabe.d15,";
$sql.=" $v_fraeseAusgabe.d16,";
$sql.=" $v_fraeseAusgabe.d17,";
$sql.=" $v_fraeseAusgabe.d18,";
$sql.=" $v_fraeseAusgabe.d19,";
$sql.=" $v_fraeseAusgabe.d20,";
$sql.=" $v_fraeseAusgabe.d21,";
$sql.=" $v_fraeseAusgabe.d22,";
$sql.=" $v_fraeseAusgabe.d23,";
$sql.=" $v_fraeseAusgabe.d24,";
$sql.=" $v_fraeseAusgabe.d25,";
$sql.=" $v_fraeseAusgabe.d26,";
$sql.=" $v_fraeseAusgabe.d27,";
$sql.=" $v_fraeseAusgabe.d28,";
$sql.=" $v_fraeseAusgabe.d29,";
$sql.=" $v_fraeseAusgabe.d30,";
$sql.=" $v_fraeseAusgabe.d31,";
$sql.=" $v_fraeseAusgabe.fraese_celkem,";
$sql.= " $v_fraeseWettBewerbStk.d1_teil_stk, $v_fraeseWettBewerbStk.d2_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d3_teil_stk, $v_fraeseWettBewerbStk.d4_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d5_teil_stk, $v_fraeseWettBewerbStk.d6_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d7_teil_stk, $v_fraeseWettBewerbStk.d8_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d9_teil_stk, $v_fraeseWettBewerbStk.d10_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d11_teil_stk, $v_fraeseWettBewerbStk.d12_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d13_teil_stk, $v_fraeseWettBewerbStk.d14_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d15_teil_stk, $v_fraeseWettBewerbStk.d16_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d17_teil_stk, $v_fraeseWettBewerbStk.d18_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d19_teil_stk, $v_fraeseWettBewerbStk.d20_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d21_teil_stk, $v_fraeseWettBewerbStk.d22_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d23_teil_stk, $v_fraeseWettBewerbStk.d24_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d25_teil_stk, $v_fraeseWettBewerbStk.d26_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d27_teil_stk, $v_fraeseWettBewerbStk.d28_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d29_teil_stk, $v_fraeseWettBewerbStk.d30_teil_stk,";
$sql.=" $v_fraeseWettBewerbStk.d31_teil_stk, $v_fraeseWettBewerbStk.teil_celkem,";
$sql.=" if($v_fraeseWettBewerbVzkd.d1_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d1_wettkampf_vzkd) as d1_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d2_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d2_wettkampf_vzkd) as d2_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d3_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d3_wettkampf_vzkd) as d3_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d4_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d4_wettkampf_vzkd) as d4_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d5_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d5_wettkampf_vzkd) as d5_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d6_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d6_wettkampf_vzkd) as d6_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d7_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d7_wettkampf_vzkd) as d7_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d8_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d8_wettkampf_vzkd) as d8_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d9_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d9_wettkampf_vzkd) as d9_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d10_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d10_wettkampf_vzkd) as d10_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d11_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d11_wettkampf_vzkd) as d11_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d12_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d12_wettkampf_vzkd) as d12_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d13_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d13_wettkampf_vzkd) as d13_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d14_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d14_wettkampf_vzkd) as d14_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d15_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d15_wettkampf_vzkd) as d15_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d16_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d16_wettkampf_vzkd) as d16_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d17_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d17_wettkampf_vzkd) as d17_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d18_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d18_wettkampf_vzkd) as d18_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d19_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d19_wettkampf_vzkd) as d19_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d20_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d20_wettkampf_vzkd) as d20_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d21_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d21_wettkampf_vzkd) as d21_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d22_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d22_wettkampf_vzkd) as d22_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d23_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d23_wettkampf_vzkd) as d23_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d24_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d24_wettkampf_vzkd) as d24_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d25_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d25_wettkampf_vzkd) as d25_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d26_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d26_wettkampf_vzkd) as d26_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d27_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d27_wettkampf_vzkd) as d27_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d28_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d28_wettkampf_vzkd) as d28_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d29_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d29_wettkampf_vzkd) as d29_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d30_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d30_wettkampf_vzkd) as d30_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.d31_wettkampf_vzkd is NULL,0,$v_fraeseWettBewerbVzkd.d31_wettkampf_vzkd) as d31_wettkampf_vzkd,";
$sql.=" if($v_fraeseWettBewerbVzkd.wettkampf_celkem is NULL,0,$v_fraeseWettBewerbVzkd.wettkampf_celkem) as wettkampf_celkem,";
$sql.=" if($v_fraeseGesamtVzkd.d1_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d1_nowettkampf_vzkd) as d1_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d2_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d2_nowettkampf_vzkd) as d2_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d3_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d3_nowettkampf_vzkd) as d3_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d4_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d4_nowettkampf_vzkd) as d4_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d5_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d5_nowettkampf_vzkd) as d5_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d6_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d6_nowettkampf_vzkd) as d6_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d7_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d7_nowettkampf_vzkd) as d7_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d8_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d8_nowettkampf_vzkd) as d8_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d9_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d9_nowettkampf_vzkd) as d9_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d10_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d10_nowettkampf_vzkd) as d10_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d11_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d11_nowettkampf_vzkd) as d11_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d12_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d12_nowettkampf_vzkd) as d12_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d13_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d13_nowettkampf_vzkd) as d13_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d14_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d14_nowettkampf_vzkd) as d14_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d15_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d15_nowettkampf_vzkd) as d15_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d16_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d16_nowettkampf_vzkd) as d16_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d17_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d17_nowettkampf_vzkd) as d17_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d18_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d18_nowettkampf_vzkd) as d18_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d19_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d19_nowettkampf_vzkd) as d19_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d20_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d20_nowettkampf_vzkd) as d20_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d21_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d21_nowettkampf_vzkd) as d21_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d22_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d22_nowettkampf_vzkd) as d22_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d23_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d23_nowettkampf_vzkd) as d23_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d24_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d24_nowettkampf_vzkd) as d24_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d25_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d25_nowettkampf_vzkd) as d25_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d26_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d26_nowettkampf_vzkd) as d26_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d27_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d27_nowettkampf_vzkd) as d27_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d28_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d28_nowettkampf_vzkd) as d28_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d29_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d29_nowettkampf_vzkd) as d29_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d30_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d30_nowettkampf_vzkd) as d30_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.d31_nowettkampf_vzkd is NULL,0,$v_fraeseGesamtVzkd.d31_nowettkampf_vzkd) as d31_nowettkampf_vzkd,";
$sql.=" if($v_fraeseGesamtVzkd.nowettkampf_celkem is NULL,0,$v_fraeseGesamtVzkd.nowettkampf_celkem) as nowettkampf_celkem";
$sql.=" FROM ((($v_fraeseAusgabe LEFT JOIN $v_fraeseWettBewerbStk";
$sql.=" ON $v_fraeseAusgabe.persnr = $v_fraeseWettBewerbStk.persnr)";
$sql.=" INNER JOIN dpers ON $v_fraeseAusgabe.persnr = dpers.PersNr)";
$sql.=" LEFT JOIN $v_fraeseWettBewerbVzkd";
$sql.=" ON $v_fraeseAusgabe.persnr = $v_fraeseWettBewerbVzkd.persnr)";
$sql.=" LEFT JOIN $v_fraeseGesamtVzkd ";
$sql.=" ON $v_fraeseAusgabe.persnr = $v_fraeseGesamtVzkd.persnr";
$sql.=" ORDER BY $v_fraeseAusgabe.persnr, $v_fraeseWettBewerbStk.teil";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function get_kurs($wahr,$ausliefer)
{
	//echo "wahr=$wahr,ausliefer=$ausliefer<br>";
	if($wahr!="EUR")
	{
		// podle auslieferdatumu a meny zjistim kurs
		$res=mysql_query("select kurs from dkurs where ((gilt_von<='".$ausliefer."') and (gilt_bis>='".$ausliefer."'))");
		$row=mysql_fetch_array($res);
		//echo "kurs=".$row['kurs']."<br>";
		return $row['kurs'];
	}
	else
	{
		//echo "kurs=1<br>";
		return 1;
	}
}

function vypocti_fac1($record)
{
	if($record['verb']!=0)
		return $record['vzkd']/$record['verb'];
	else
		return 0;
}

function vzkd_stk($record)
{
	if($record['bezstueck']!=0)
		return $record['vzkd']/$record['bezstueck'];
	else
		return 0;
}

function vzaby_stk($record)
{
	if($record['bezstueck']!=0)
		return $record['vzaby']/$record['bezstueck'];
	else
		return 0;
}

function procent_in_wettbewerb($record)
{
	if($record['nowettkampf_celkem']!=0)
		return $record['wettkampf_celkem']/$record['nowettkampf_celkem'];
	else
		return 0;
}

function fraese_in_wettbewerb($record)
{
	$factor = procent_in_wettbewerb($record);
	return(round($factor*$record['fraese_celkem']));
}


$options = array(
					'encoder'=>false,
					'rootTag'=>'S090',
					'idColumn'=>'persnr',
					'rowTag'=>'personen',
					'elements'=>array(
						'persnr',
						'Name',
						'Vorname',
						'd1',
						'd2',
						'd3',
						'd4',
						'd5',
						'd6',
						'd7',
						'd8',
						'd9',
						'd10',
						'd11',
						'd12',
						'd13',
						'd14',
						'd15',
						'd16',
						'd17',
						'd18',
						'd19',
						'd20',
						'd21',
						'd22',
						'd23',
						'd24',
						'd25',
						'd26',
						'd27',
						'd28',
						'd29',
						'd30',
						'd31',
						'fraese_celkem',
						'd1_wettkampf_vzkd',
						'd2_wettkampf_vzkd',
						'd3_wettkampf_vzkd',
						'd4_wettkampf_vzkd',
						'd5_wettkampf_vzkd',
						'd6_wettkampf_vzkd',
						'd7_wettkampf_vzkd',
						'd8_wettkampf_vzkd',
						'd9_wettkampf_vzkd',
						'd10_wettkampf_vzkd',
						'd11_wettkampf_vzkd',
						'd12_wettkampf_vzkd',
						'd13_wettkampf_vzkd',
						'd14_wettkampf_vzkd',
						'd15_wettkampf_vzkd',
						'd16_wettkampf_vzkd',
						'd17_wettkampf_vzkd',
						'd18_wettkampf_vzkd',
						'd19_wettkampf_vzkd',
						'd20_wettkampf_vzkd',
						'd21_wettkampf_vzkd',
						'd22_wettkampf_vzkd',
						'd23_wettkampf_vzkd',
						'd24_wettkampf_vzkd',
						'd25_wettkampf_vzkd',
						'd26_wettkampf_vzkd',
						'd27_wettkampf_vzkd',
						'd28_wettkampf_vzkd',
						'd29_wettkampf_vzkd',
						'd30_wettkampf_vzkd',
						'd31_wettkampf_vzkd',
						'wettkampf_celkem',
						'd1_nowettkampf_vzkd',
						'd2_nowettkampf_vzkd',
						'd3_nowettkampf_vzkd',
						'd4_nowettkampf_vzkd',
						'd5_nowettkampf_vzkd',
						'd6_nowettkampf_vzkd',
						'd7_nowettkampf_vzkd',
						'd8_nowettkampf_vzkd',
						'd9_nowettkampf_vzkd',
						'd10_nowettkampf_vzkd',
						'd11_nowettkampf_vzkd',
						'd12_nowettkampf_vzkd',
						'd13_nowettkampf_vzkd',
						'd14_nowettkampf_vzkd',
						'd15_nowettkampf_vzkd',
						'd16_nowettkampf_vzkd',
						'd17_nowettkampf_vzkd',
						'd18_nowettkampf_vzkd',
						'd19_nowettkampf_vzkd',
						'd20_nowettkampf_vzkd',
						'd21_nowettkampf_vzkd',
						'd22_nowettkampf_vzkd',
						'd23_nowettkampf_vzkd',
						'd24_nowettkampf_vzkd',
						'd25_nowettkampf_vzkd',
						'd26_nowettkampf_vzkd',
						'd27_nowettkampf_vzkd',
						'd28_nowettkampf_vzkd',
						'd29_nowettkampf_vzkd',
						'd30_nowettkampf_vzkd',
						'd31_nowettkampf_vzkd',
						'nowettkampf_celkem',
						'wettbewerb_procent'=>'#procent_in_wettbewerb();',
						'fraese_in_wettbewerb'=>'#fraese_in_wettbewerb();',
						'teile'=>array(
							'rootTag'=>'teile',
							'rowTag'=>'teil',
							'idColumn'=>'teil',
							'elements'=>array(
								'teilnr'=>'teil',
								'fraese_wettkampf_factor',
								'd1_teil_stk',
								'd2_teil_stk',
								'd3_teil_stk',
								'd4_teil_stk',
								'd5_teil_stk',
								'd6_teil_stk',
								'd7_teil_stk',
								'd8_teil_stk',
								'd9_teil_stk',
								'd10_teil_stk',
								'd11_teil_stk',
								'd12_teil_stk',
								'd13_teil_stk',
								'd14_teil_stk',
								'd15_teil_stk',
								'd16_teil_stk',
								'd17_teil_stk',
								'd18_teil_stk',
								'd19_teil_stk',
								'd20_teil_stk',
								'd21_teil_stk',
								'd22_teil_stk',
								'd23_teil_stk',
								'd24_teil_stk',
								'd25_teil_stk',
								'd26_teil_stk',
								'd27_teil_stk',
								'd28_teil_stk',
								'd29_teil_stk',
								'd30_teil_stk',
								'd31_teil_stk',
								'teil_celkem',
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
//$domxml->save("S090.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
