<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Exporte umplanen
    </title>

<link rel="stylesheet" href="../styl.css" type="text/css">
<link rel="stylesheet" href="../../styldesign.css" type="text/css">
<link rel='stylesheet' href='../print.css' type='text/css' media="print"/>
<link rel='stylesheet' href='./umterminieren.css' type='text/css'/>
<link rel="stylesheet" href="../../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
<script type = "text/javascript" src = "../../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../../js/jquery.ui.datepicker-cs.js"></script>
<script type="text/javascript" src="./umterminieren.js"></script>


</head>


<body>
    
    <div id="header">
        <h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
    </div>
	
	
    <div align="center" id="podheader">
        {if $prihlasen}
	    {$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
        {else}
	    Benutzer nicht angemeldet/neprihlaseny uzivatel
        {/if}
    </div>
	
    <div id="formular_header">
        Umplanung Exporte
    </div>
	
    <div id="formular_telo">
	<div id='kunde_id'>
	    <label for="kunde">Kunde :</label>
	    <input acturl="./gettermine.php" id="kunde" size="3" maxlength="3" value="" />
	    <input acturl='umterminierenSave.php' type='button' id='umterminieren_button' value='umterminieren !' />
	</div>
	
	<div id='termine_table'></div>
    </div>
	
</body>
</html>
