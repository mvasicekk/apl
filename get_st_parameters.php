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

if(strlen(trim($_GET['popisky']))==0){
    //rovnou prejdu k zobrazeni tabulky
    $urlparams = "label=".urlencode($_GET['label'])."&sql=".base64_encode(base64_decode($_GET['sql']))."&filter=".urlencode($_GET['filter']);
    $reporturl="./Reports/showquery1.php?".$urlparams;
    header("Location: ".$reporturl);
}	
// vytvorit pole popisku
if(isset($_GET['popisky']))
{
	$popisky=explode(";",$_GET['popisky']);
	$promenne=explode(";",$_GET['promenne']);
	$values=explode(";",$_GET['values']);
	$paramok=1;
}
else
	$paramok=0;
	
$i=0;

//echo "<pre>";
//var_dump($promenne);
//echo "</pre>";
foreach($popisky as $popis)
{
	// za popiskem jeste muze byt podrobnejsi popis typu policka
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

	$pop[$i]['readonly']='';
	//test na readonly, na zacatku popisku bude #
	if($label[0]=='X'){
	    $label = substr($label, 1);
	    $pop[$i]['readonly']='readonly';
	}
	
	$pop[$i]['typ']=$typPole;
	$pop[$i]['label']=$label;
	// rozsireni pro nove input type
	$promenna = $promenne[$i];
	
	if(strpos($promenna, '!')){
	    $varName = substr($promenna, 0,strpos($promenna, '!'));
	    $varType = substr($promenna, strpos($promenna, '!')+1);
	}
	else{
	    $varName = $promenna;
	    $varType = 'text';
	}
	
	$pop[$i]['var']=$varName;
	if($typPole!="text")
	    $pop[$i]['inputtype']=$typPole;
	else
	    $pop[$i]['inputtype']=$varType;


    // pokud je policko
    // *CB combo box
    // *RA radiobutton
    // tak hodnotu predam jako pole

    if ($typPole == '*CB' || $typPole == '*RA'){
	$pop[$i]['val'] = explode(",", $values[$i]);
	$i++;
    }
    else {
        $pop[$i]['val'] = $values[$i];
        $i++;
    }
}

//echo "<pre>";
//var_dump($pop);
//echo "</pre>";
$smarty->assign("param",$pop);
$smarty->assign("paramok",$paramok);
$smarty->assign("query",$_GET['query']);
$smarty->assign("nadpis",$_GET['query']." parametry");
$smarty->display('get_st_parameters.tpl');

?>

