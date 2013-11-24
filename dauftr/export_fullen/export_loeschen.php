<?
 session_start();
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


	$hatRechnung=has_rechnung($_GET["auftragsnr"]);
		
	$smarty->assign("hatRechnung",$hatRechnung);
	
	if(isset($_GET['auftragsnr']))
	{
		// vytahnout navrh pro vyplneni exportu
		$auftragsnr=$_GET['auftragsnr'];
		$sql="SELECT dauftr.AuftragsNr, dauftr.`pos-pal-nr` as pal, dauftr.Teil, ";
	       	$sql.=" dauftr.id_dauftr as id,`stk-exp` AS gut_stk";
		$sql.=" from dauftr left join drech on drech.auftragsnr=dauftr.auftragsnr and drech.`pos-pal-nr`=dauftr.`pos-pal-nr`";
		$sql.=" where (((dauftr.`auftragsnr-exp`)='$auftragsnr') and (drech.`auftragsnr` is null) and (dauftr.kzgut='G'))";
		$sql.=" GROUP BY dauftr.AuftragsNr, dauftr.`pos-pal-nr`, dauftr.Teil";
		$sql.=" order by dauftr.AuftragsNr,pal,dauftr.Teil";

		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res))
		{
			$rows[$row['id']]=$row;
		}

		$smarty->assign("loeschen",$rows);
		$smarty->assign("sql",$sql);
		
		$smarty->assign("auftragsnr",$_GET['auftragsnr']);
		$smarty->display('export_loeschen.tpl');
		
	}
?>
