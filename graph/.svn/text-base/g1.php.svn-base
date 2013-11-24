<?php
include 'Image/Graph.php';     
$Graph =& Image_Graph::factory('graph', array(600, 200)); 
$Plotarea =& $Graph->addNew('plotarea'); 
$Dataset =& Image_Graph::factory('dataset'); 
srand();

$fill =& Image_Graph::factory('Image_Graph_Fill_Array');

for($i=1;$i<=25;$i++)
{
	$Dataset->addPoint($i, rand(1,10),$i); 
	$fill->addColor(array(rand(0,255),rand(0,255),rand(0,255)), $i);
}

$Plot1 =& $Plotarea->addNew('bar', &$Dataset);
$Plot1->setLineColor('red'); 
$Plot1->setFillColor('#0000ff@0.1'); 
$Plot1->setBackgroundColor('green@0.1'); 
$Plot1->setBorderColor(array(0, 0, 0));

$Plot1->setFillStyle($fill);

$AxisY1 =& $Plotarea->getAxis('y');
$AxisY1->showArrow();  

$Graph->done(); 
?>