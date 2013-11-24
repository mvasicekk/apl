<?
include 'Image/Graph.php';     
include "../fns_dotazy.php";

dbConnect();

if (isset($_GET['dat']))
{
		$curdat=$_GET['dat'];
}

$sql_leistung="select DATE_FORMAT(drueck.datum,'%d') as datum,sum(if(kunden_stat_nr=1,if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as pg1,sum(if(kunden_stat_nr=3,if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as pg3,sum(if(kunden_stat_nr=4,if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as pg4,sum(if(kunden_stat_nr=9,if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as pg9,sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`)) as celkem from drueck join dkopf using (teil) join dksd using (kunde) where (datum between  subdate('$curdat',day('$curdat')-1) and '$curdat') group by drueck.datum order by drueck.datum asc limit 30";
$res = mysql_query($sql_leistung) or die(mysql_error());
$i=0;

//echo $sql_leistung;

while($row=mysql_fetch_array($res))
{
	$datum=$row['datum'];
	$pole[$i]['datum']=$datum;
	
	$pg1=$row['pg1'];$pole[$i]['pg1']=$pg1;$sum_pg1+=$pg1;
	
	$pg3=$row['pg3'];$pole[$i]['pg3']=$pg3;$sum_pg3+=$pg3;
	
	$pg4=$row['pg4'];$pole[$i]['pg4']=$pg4;$sum_pg4+=$pg4;
	
	$pg9=$row['pg9'];$pole[$i]['pg9']=$pg9;$sum_pg9+=$pg9;
	
	$celkem=$row['celkem'];
	
	$pole[$i]['celkem']=$celkem;$sum_celkem+=$celkem;
	
	$i++;
}




$Graph =& Image_Graph::factory('graph', array(300, 100)); 

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array($curdat, 7)),
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            95
        ),
        8
    )
);

//$Legend->setPlotarea($Plotarea); 

$Graph->setBackground(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'lightsteelblue', 'papayawhip')));
$Font =& $Graph->addNew('font', 'Verdana');
$Font->setSize(6);

$Graph->setFont($Font);

//$Plotarea =& $Graph->addNew('plotarea'); 
$Dataset1 =& Image_Graph::factory('dataset'); 
$Dataset3 =& Image_Graph::factory('dataset'); 
$Dataset4 =& Image_Graph::factory('dataset'); 
//$Dataset9 =& Image_Graph::factory('dataset'); 
$Dataset_celkem =& Image_Graph::factory('dataset'); 

for($j=0;$j<$i;$j++)
{
	$Dataset1->addPoint($pole[$j]['datum'],$pole[$j]['pg1'] ,$j); 
	$Dataset3->addPoint($pole[$j]['datum'],$pole[$j]['pg3'] ,$j); 
	$Dataset4->addPoint($pole[$j]['datum'],$pole[$j]['pg4'] ,$j); 
	//$Dataset9->addPoint($pole[$j]['datum'],$pole[$j]['pg9'] ,$j); 
	$Dataset_celkem->addPoint($pole[$j]['datum'],$pole[$j]['celkem'] ,$j);
	if($pole[$j]['celkem']>$celkem_max)
		$celkem_max=$pole[$j]['celkem']; 
}

//$Plot1 =& $Plotarea->addNew('bar', &$Dataset_celkem);
$Plot2 =& $Plotarea->addNew('smooth_area', &$Dataset_celkem);
$Plot_pg1 =& $Plotarea->addNew('smooth_area', &$Dataset1);

$Plot_pg4 =& $Plotarea->addNew('smooth_area', &$Dataset4);
$Plot_pg3 =& $Plotarea->addNew('smooth_area', &$Dataset3);

//$Datasets=array($Dataset1,$Dataset3,$Dataset4);
//$Plot1 =& $Plotarea->addNew('bar', array($Datasets));
//$Plot2 =& $Plotarea->addNew('bar', &$Dataset3);

$Plot2->setLineColor('blue'); 
$Plot2->setFillColor('#0000ff@0.5'); 

$Plot_pg1->setFillColor('#00ff00@0.5'); 
$Plot_pg3->setFillColor('#ff0000@0.5'); 
$Plot_pg4->setFillColor('#ffff00@0.5'); 
//$Plot2->explode(2);
//$Plot1->setLineColor('red'); 
//$Plot1->setFillColor('#0000ff@0.1'); 
//$Plot1->setBackgroundColor('yellow@0.1'); 
//$Plot1->setBorderColor(array(0, 0, 1));
$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X); 
$AxisX->setLabelInterval(1);
//$AxisX->setTitle("Datum");
$AxisX->showLabel(IMAGE_GRAPH_LABEL_MAXIMUM);

//$Plotarea->addNew('line_grid', array(), IMAGE_GRAPH_AXIS_Y);
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y); 
$AxisY->forceMaximum($celkem_max*1.15);
//$AxisY->setTitle("VzKd");
//$AxisY->setBorderColor("green");
//$AxisY->setFontSize(4);
//$AxisY->setBackgroundColor("#ccccff");
//$AxisY->setFontAngle(180);
//$padding=array("left"=>10,"top"=>20,"right"=>0,"bottom"=>0);
//$AxisY->setPadding($padding);


//$AxisX->setTickOptions(-1, 1, 2); 
//$AxisX->setFontAngle('vertical'); 
$Graph->done(); 
?>
