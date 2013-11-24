<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Behaelter Inventur
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<!--<link rel='stylesheet' href='./print.css' type='text/css' media="print"/>-->

<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
    <script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
    <script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
    <script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
<!--<script type="text/javascript" src="../js/jquery.js"></script>-->
<script type="text/javascript" src="./bewinventur.js"></script>


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
        Behaelter Inventur
    </div>

<!--    <div id="helpdiv">

    </div>-->
    <div id="formular_telo">

        <div id="behaelterinfo">
            <table>
                <tr>
                    <td>BehaelterNr</td>
                    <td><input class="entermove ui-widget" type="text" size="10" maxlength="20" id="behaelternr" acturl=""/></td>
                </tr>
                <tr>
                    <td>Kunde</td>
                    <td><input class="entermove" type="text" size="4" maxlength="4" style="text-align: right;" id="kunde" acturl="./kundeUpdate.php"/></td>
                    <td colspan="3" id="kunde_info"></td>
                </tr>

                <!--radky pro zadani pozice do tabulky -->

                <tr>
                    <td>Datum [DD.MM.JJJJ]</td>
                    <td><input title="CTRL+HOME zeigt Kalendar / zobrazi kalendar" class="datepicker entermove" type="text" size="10" maxlength="10"  id="datum" acturl=""/></td>
                    <td>Behaelterzustand</td>
                    <td><input class="entermove ui-widget" type="text" size="3" maxlength="4" id="bezu" acturl="."/></td>
                    <td>Behaelterinhalt</td>
                    <td><input class="entermove ui-widget" type="text" size="3" maxlength="4" id="bein" acturl="."/></td>
                    <td>Lag.Platz</td>
                    <td><input class="entermove ui-widget" type="text" size="8" maxlength="8" id="lagplatz" acturl="."/></td>
                    <td>Stk</td>
                    <td><input class="entermove" type="text" size="6" maxlength="6" style="text-align: right;" id="stk" value="0" acturl=""/></td>
                </tr>

                <tr>
                    <td><input class="entermove" type="button" value="Enter" id="enter" acturl="./enterBehInv.php"</td>
                    <td><input type="button" value="Abbruch" id="abbruch"</td>
                </tr>
                <tr>
                    <td colspan="6" id="enter_info">&nbsp;</td>
                </tr>
                

            </table>
        </div>

        <div id="bewegungendiv">
            
        </div>
    </div>
</body>
</html>
