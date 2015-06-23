<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Auftraege pflegen
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../libs/bower_components/angular/angular.min.js"></script>
<script type="text/javascript" src="../libs/bower_components/numeral/numeral.js"></script>
<script type="text/javascript" src="../js/app.js"></script>
<script type="text/javascript" src="../js/controllers.js"></script>
<script type="text/javascript" src="../js/filters.js"></script>

</head>

<body ng-app="aplApp" ng-controller="dauftrController">
    {literal}
	<p>
	    cislo=<span ng-bind="cislo|numeral_procenta"></span>
	</p>
	<input ng-model="cislo" value="{{cislo|currency}}"/>
    {/literal}
</body>
</html>
