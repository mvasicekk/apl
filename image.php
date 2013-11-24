<?php
/*
 * Created on 12.12.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 	try
 	{
 		if($animage=imagecreate(500,500))
 		{
 			$red = imagecolorallocate($animage,0,0,255);
 			$white = imagecolorallocate($animage,255,255,255);
 			$black = imagecolorallocate($animage,0,0,0);
 			
 			imagefilledrectangle($animage,0,0,500,500,$red);
 			imagefilledrectangle($animage,5,5,493,493,$black);
 			
 			$slovo="1,2,3,4,\n5,6,7,8,";
 			imagestring($animage,4,500/2-(imagefontwidth(4)*strlen($slovo))/2,50,$slovo,$white);
 			
 			header("Content-type: image/png");
                        imagepng($animage);
 			imagedestroy($animage);
 		}
 		else
 		{
 			throw new exception ("nefunguje GD knihovna !");
 		}
 	}
 	catch(exception $e)
 	{
 		echo $e->getmessage();
 	}
