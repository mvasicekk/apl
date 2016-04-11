<?
include("../Classes/pChart/class/pData.class.php");
include("../Classes/pChart/class/pDraw.class.php");
include("../Classes/pChart/class/pImage.class.php");

$myData = new pData();
$myData->addPoints(array(10,20,30,40),"Serie1");
$myData->setSerieDescription("Serie1","Serie 1");
$myData->setSerieOnAxis("Serie1",0);

$myData->addPoints(array("led","uno","brez","dub"),"Absissa");
$myData->setAbscissa("Absissa");

$myData->setAxisPosition(0,AXIS_POSITION_LEFT);
$myData->setAxisName(0,"ppm");
$myData->setAxisUnit(0,"");

$myPicture = new pImage(700,400,$myData);
$Settings = array("StartR"=>231, "StartG"=>231, "StartB"=>97, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
$myPicture->drawGradientArea(0,0,700,400,DIRECTION_VERTICAL,$Settings);

$myPicture->drawRectangle(0,0,699,399,array("R"=>0,"G"=>0,"B"=>0));

$myPicture->setFontProperties(array("FontName"=>"../Classes/pChart/fonts/Forgotte.ttf","FontSize"=>14));
$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE
, "R"=>42, "G"=>18, "B"=>255);
$myPicture->drawText(350,25,"PPM",$TextSettings);

$myPicture->setGraphArea(50,50,675,360);
$myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"../Classes/pChart/fonts/Bedizen.ttf","FontSize"=>9));

$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
, "Mode"=>SCALE_MODE_FLOATING
, "LabelingMethod"=>LABELING_ALL
, "GridR"=>255, "GridG"=>255, "GridB"=>255, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>0, "CycleBackground"=>1, "DrawXLines"=>0, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>ALL);
$myPicture->drawScale($Settings);

$Config = array("AroundZero"=>1);
$myPicture->drawBarChart($Config);

$Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>"../Classes/pChart/fonts/pf_arma_five.ttf", "FontSize"=>6, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_BOX
, "Mode"=>LEGEND_HORIZONTAL
);
$myPicture->drawLegend(645,16,$Config);

$myPicture->stroke();

//toto taky funguje
//$myPicture->Render("../Reports/graf.png");