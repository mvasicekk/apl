<?
session_start();
require("./libs/Smarty.class.php");
$smarty = new Smarty;

	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}
	else{
	    header("Location: ../index.php");
	}

// vytvorit pole popisku
if(isset($_GET['popisky']))
{
	$popisky=split(";",$_GET['popisky']);
	$promenne=split(";",$_GET['promenne']);
	$values=split(";",$_GET['values']);
	$paramok=1;
}
else
	$paramok=0;
	
$i=0;

foreach($popisky as $popis)
{
	
	// za popisek jeste muze byt podrobnejsi popis typu policka , oddeleno mrizkou #
	if(strpos($popis,","))
	{
		$label=substr($popis,0,strpos($popis,","));
		$typPole = substr($popis,strpos($popis,",")+1);
	}
	else
	{
		$label=$popis;
		$typPole = "text";
	}
		
	$pop[$i]['typ']=$typPole;
	$pop[$i]['label']=$label;
	$pop[$i]['var']=$promenne[$i];

    // pokud je policko
    // *CB combo box
    // *RA radiobutton
    // tak hodnotu predam jako pole

    if ($typPole == '*CB' || $typPole == '*RA')
        $pop[$i]['val'] = split(",", $values[$i]);
    else {
        $pop[$i]['val'] = $values[$i];
        $i++;
    }
}



// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
if(isset($_SESSION['user'])&&isset($_SESSION['level']))
{
	$smarty->assign("user",$_SESSION['user']);
	$smarty->assign("level",$_SESSION['level']);
	$smarty->assign("prihlasen",1);
}

$smarty->assign("param",$pop);
$smarty->assign("paramok",$paramok);
$smarty->assign("report",$_GET['report']);
$smarty->assign("nadpis",$_GET['report']." parametry");


$smarty->display('get_parameters.tpl');

?>

