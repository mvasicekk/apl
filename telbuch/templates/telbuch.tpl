<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>
            Telefonbuch Abydos
        </title>
        <link rel="stylesheet" href="./styl.css" type="text/css">
        <link rel="stylesheet" href="../styldesign.css" type="text/css">
        <!--<link rel='stylesheet' href='./print.css' type='text/css' media="print"/>-->
        <link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
        <script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
        <script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
        <script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
        <script type="text/javascript" src="./telbuch.js"></script>
    </head>


{popup_init src="../js/overlib.js"}


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
        Telefonbuch Abydos
    </div>

    <div id="formular_telo">

        <div id="controlpanel">
            <table style="width: 100%;">
                <tr>
                    <td>Suchen :&nbsp;</td>
                    <td><input class="entermove ui-widget" type="text" size="20" maxlength="20" id="suchen" acturl="./searchAdress.php" value=""/></td>
		    <td style="width: 70%;text-align: right;"><input class="" type="button" id="printbutton" acturl="./showPrintDiv.php" value="Druck / Tisk"/></td>
                </tr>
            </table>
        </div>

        <div id="adressen">
            {$adressenTable}
        </div>

    </div>
</body>
</html>
