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
<!--<link rel="stylesheet" href="../css/jquery-ui-themes-1.10.4/themes/humanity/jquery-ui.css" type="text/css">-->
<!--<link rel="stylesheet" href="../css/jquery-ui-themes-1.10.4/themes/humanity/jquery.ui.theme.css" type="text/css">-->

<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<!--<script type = "text/javascript" src = "../js/jquery-1.11.0.min.js"></script>-->
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<!--<script type = "text/javascript" src = "../js/jquery-ui-1.10.4/themes/base/jquery.ui.all.css"></script>-->
<!--<script type = "text/javascript" src = "../js/jquery-ui-1.10.4/jquery-1.10.2.js"></script>-->
<!--<script type = "text/javascript" src = "../js/jquery-ui-1.10.4/ui/jquery-ui.js"></script>-->

<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>

<!--<link rel="stylesheet" href="../js/jquery.ui.tinytbl.css" type="text/css">-->
<!--<script type = "text/javascript" src = "../js/jquery.ui.tinytbl.js"></script>-->

<!--<script type="text/javascript" src="../js/jquery.js"></script>-->
<script type="text/javascript" src="./fixed_table_rc.js"></script>
<script type="text/javascript" src="./dispo.js"></script>
<link rel="stylesheet" href="./fixed_table_rc.css" type="text/css">
</head>

<body >
<div id='dispoform'>
    <fieldset>
	<legend>parametry</legend>
	<label for="datum_von">Datum von:</label>
	<input acturl="./getDispoDiv.php" value='{$datevon}' type='text' id='datum_von' class='datepicker' size='10'/>
	<label for="datum_bis">Datum bis:</label>
	<input acturl="./getDispoDiv.php" type='text' id='datum_bis' class='datepicker' size='10'/>
	<label for="kunde_von">Kunde von:</label>
	<input acturl="./getDispoDiv.php" type='text' id='kunde_von' size='3' maxlength="3"/>
	<label for="kunde_bis">Kunde bis:</label>
	<input acturl="./getDispoDiv.php" type='text' id='kunde_bis' size='3' maxlength="3"/>
	<label for="rm_bis">Rueckmeldungen bis:</label>
	<input acturl="./getDispoDiv.php" type='text' id='rm_bis' size='5' maxlength="5" value='{$rm_bis}'/>
	nur mit gepl. Minuten>0&nbsp<input acturl="./getDispoDiv.php" type='checkbox' checked='checked' id='nurMitMin' name='nurMitMin' value='1'/>
	<input acturl="./getDispoDiv.php" type="button" id="disporefresh" value="Refr." />
	<a href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;RM bis (Zeit);Kunde von;Kunde bis&promenne=von;bis;rm_bis;kundevon;kundebis&values={$datevon};;{$rm_bis};0;999&report=S218'>Dispo Report</a>
    </fieldset>
</div>

<div id='plany'></div>
<p id="spinner">zpracovávám dotaz ....</p>
<div id='dispodiv'>
    
</div>

</body>
</html>
