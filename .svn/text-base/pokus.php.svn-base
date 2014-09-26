<?php

  
  $sql="select dksd.kunden_stat_nr as pg, drueck.datum, sum(if(`auss_typ`=4, ((`Auss-Stück`+`Stück`)*`VZ-SOLL`), (`Stück`*`VZ-SOLL`))) as summin FROM `drueck` join dkopf using (teil) join dksd using (kunde) where (month(datum)=month(now()) and year(datum) = year(now())) group by pg, drueck.datum order by drueck.datum desc, pg desc;";
  $res=mysql_query($sql);
  
  $height = 150;
  $width = 800;
  $im = ImageCreate($width,$height);

  $white = imagecolorallocate($im, 255, 255, 255);
  $black = imagecolorallocate($im, 0, 0, 0);
  $red = imagecolorallocate($im, 255, 0, 0);
  $souradnice=array(10,25,34,45,68,15);
  imagepolygon($im, $souradnice, 3, $black);
  imagefill($im, 0, 0, $white);
  imageellipse($im, 50, 50, 100, 80, $black);
  imageline($im, 0, 87, $width, $height, $black);
  imagestring($im,10, 10, 10, "sales", $black);
  Header("content-type: image/png");

  imagepng($im);
?>
