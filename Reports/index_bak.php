<?
session_start();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <meta name="author" content="Orcen; e-mail:orcen@centrum.cz ">    
    <title>
      APL Abydos
    </title>    
    <link rel="stylesheet" href="./styl.css" type="text/css">
<?php

?>
</head>
  <body>
    <div id="celek">
      <span id="tables">
        <span id="headers">
          <h1>
            <span>
              Abydos
            </span></h1>
          <h2>
            <span>
              ABYDOS Auftragsabwicklung / 
              <br />
              Produktionssteuerung
            </span></h2>
        </span>
        <hr style="clear: both; visibility: hidden; width: 50px;">
              <input type="button" value="Lager Zettel/ Vysačky" id="lagrVysacky" class="abyStartButton"  onClick="location.href='./vysacky.php'" /><br /><br />
              <input type="button" value="D605" id="d605" class="abyStartButton"  onClick="location.href='./d605.php?auftrNr='" />
              <input type="button" value="D605 - PHP" id="d605-php" class="abyStartButton"  onClick="location.href='./d605-php.php?auftrNr='" /><br /><br />
              <input type="button" value="D606" id="d606" class="abyStartButton"  onClick="location.href='./d606.php?auftrNr='" /><br /><br />
              <input type="button" value="D607" id="d607" class="abyStartButton"  onClick="location.href='./d607.php?auftrNr='" /><br /><br />
              <input type="button" value="S816" id="s816" class="abyStartButton"  onClick="location.href='./s816.php?auftrVon=&amp;auftrBis=&amp;teil='" /><br /><br />
			  <input type="button" value="S165" id="s165" class="abyStartButton"  onClick="location.href='../get_parameters.php?popisky=Datum&promenne=datevon&values=<?echo date("d.m.Y");?>&report=S165'" /><br /><br />
			  <input type="button" value="S610" id="s610" class="abyStartButton"  onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis&promenne=datevon;datebis&values=<?echo date("d.m.Y");?>;<?echo date("d.m.Y");?>&report=S610 bla bla bla'" /><br /><br />
			  <input type="button" value="S240" id="s240" class="abyStartButton"  onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis;Autrag von;Auftrag bis&promenne=datevon;datebis;auftrvon;auftrbis&values=<?echo date("d.m.Y");?>;<?echo date("d.m.Y");?>;111000;111999&report=S610'" /><br /><br />
			  <input type="button" value="S195" id="s195" class="abyStartButton"  onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis;Teile [mit | getrennt]&promenne=datumvon;datumbis;teile&values=<?echo date("d.m.Y");?>;<?echo date("d.m.Y");?>;06017272|535bv410e&report=S195'" /><br /><br />
			  <input type="button" value="S220" id="s220" class="abyStartButton"  onClick="location.href='../get_parameters.php?popisky=Auftrag;Teil&promenne=auftragsnr;teil&values=;*&report=S220'" /><br /><br />
      
  </body>
</html>
