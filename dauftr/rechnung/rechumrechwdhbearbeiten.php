<?
require_once '../../security.php';
?>
<?
include "../../fns_dotazy.php";
dbConnect();
require("../../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}


	$rechnung=$_GET['rechnung'];


	if(isset($rechnung))
	{
		$sql="select id,auftragsnr,teil,`stück` as stk,ausschuss,dm as preis,";
		$sql.="DATE_FORMAT(datum,'%Y-%m-%d') as datum,text1,`taet-kz` as tat,";
		$sql.="`best-nr` as bestnr,DATE_FORMAT(`datum-auslief`,'%Y-%m-%d') as ausliefdatum,";
		$sql.="`pos-pal-nr` as pal,fremdauftr,fremdpos,vom,an,waehrung,";
		$sql.="origauftrag,kunde,teilbez,abgnr";
		$sql.=" from drechneu";
		$sql.=" where (`auftragsnr`='$rechnung')";
		$sql.=" order by auftragsnr,teil,pal,abgnr";

		mysql_query('set names utf8');
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res))
		{
			$rows[$row['id']]=$row;
			$auslieferdatum=$row['ausliefdatum'];
			$vom=$row['vom'];
			$an=$row['an'];
		}
		$smarty->assign("rows",$rows);
		$smarty->assign('sql',$sql);
		$smarty->assign('rechnung',$rechnung);
			$smarty->assign('auslieferdatum',$auslieferdatum);
		$sql="select letzterechnung_sonst from dkndumrech where ((vom='$vom') and (an='$an'))";

		$res=mysql_query($sql);

		$row=mysql_fetch_array($res);

		$smarty->assign('letzterechnung_sonst',$row['letzterechnung_sonst']);
	}
	$smarty->display('rechumrechwdhbearbeiten.tpl');
?>
