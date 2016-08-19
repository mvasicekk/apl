<?php /* Smarty version 2.6.14, created on 2016-06-15 14:20:19
         compiled from drueck_new.tpl */ ?>
<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<title></title>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="style_new.css">
</head>
<body>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => '../../templates/headingBS.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="formular_header">
	<?php if ($this->_tpl_vars['stornoid'] > 0): ?>
		Drueck EDIT / editace položky
	<?php else: ?>
		Rückmeldungen / zadání výkonu
	<?php endif; ?>
</div>
<div id="wrapper">

	<form class="form" name="auftragsuchen_formular">

		<div class="col-xs-12 col-md-12">
			<div class="form-group col-md-2">
				<label for="auftragsnr">Auftragsnr</label>
				<input type="text" class="form-control input-sm" id="auftragsnr" placeholder="">
			</div>

			<div class="form-group col-md-2">
				<label for="pal">Palette / paleta</label>
				<input type="text" class="form-control input-sm" id="pal" placeholder="">
			</div>

			<div class="form-group col-md-2">
				<label for="datum">Datum</label>
				<input type="text" class="form-control input-sm" id="datum" placeholder="">
			</div>

			<div class="form-group col-md-5">
				<label for="tatnrarray">Mögliche Tätigkeiten</label>
				<input type="text" class="form-control input-sm" id="tatnrarray" placeholder="" disabled>
			</div>

			<input type="button" class="btn btn-info" VALUE="Info" />


		</div>

		<div class="col-xs-12 col-md-12">
			<div class="form-group col-md-2">
				<label for="auftragsnr">teil</label>
				<input type="text" class="form-control input-sm" id="teil" placeholder="" disabled>
			</div>



		</div>


	</form>
</div>
</body>
</html>