<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Dispotest
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">

<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
<!--<script type="text/javascript" src="../js/jquery.js"></script>-->
<script type="text/javascript" src="./dispo.js"></script>
</head>

<body >
<div id='dispoform'>
    <fieldset>
	<legend>parametry</legend>
	<label for="datum_von">Datum von:</label>
	<input acturl="./getDispoDiv.php" type='text' id='datum_von' class='datepicker' size='10'/>
	<label for="datum_bis">Datum bis:</label>
	<input acturl="./getDispoDiv.php" type='text' id='datum_bis' class='datepicker' size='10'/>
	<label for="kunde_von">Kunde von:</label>
	<input acturl="./getDispoDiv.php" type='text' id='kunde_von' size='3' maxlength="3"/>
	<label for="kunde_bis">Kunde bis:</label>
	<input acturl="./getDispoDiv.php" type='text' id='kunde_bis' size='3' maxlength="3"/>
    </fieldset>
</div>

<div id='plany'></div>
<p id="spinner">zpracovávám dotaz ....</p>
<div id='dispodiv'>
    
</div>

</body>
</html>
