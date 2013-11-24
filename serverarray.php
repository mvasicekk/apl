<?php
/*
 * Created on 13.12.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
  echo "promenna SERVER<br>";
  foreach($_SERVER as $key=>$polozka)
  {
  	echo "key=$key,polozka=$polozka<br>";
  }
  
  echo "promenna GET<br>";
  foreach($_GET as $key=>$polozka)
  {
  	echo "key=$key,polozka=$polozka<br>";
  }
  
?>
