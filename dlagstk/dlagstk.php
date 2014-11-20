<?
require_once '../security.php';
include "../fns_dotazy.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1250" />
    
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <title>
      DLagerStk
    </title>
<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>

<style type="text/css">
      <!--
      body{padding-top:0px; background-color: rgb(202,202,202);}
      insert, input{margin: 3px;}
      .teil{width: 150px;}
      .lager{width: 320px;}
      #datum_inventury{background-color: rgb(191,191,191);}
      //-->
    </style>
    
  </head>
  <body>
    <fieldset style="padding-bottom: 0px; padding-top: 0px; width: 980px;">
      <legend>        DLagStk
      </legend>
      <form action="save.php" method="post" name="formDpers" accept-charset="windows-1250">        
        <label>Teil:&nbsp;&nbsp;&nbsp;</label><select name="Teil" id="Teil" class="teil" onchange="stahniData(this.value);">
        
<?  
dbConnect();
  
$res = mysql_query("SELECT `Teil` FROM `dkopf` order by `Teil` asc") or die(mysql_error());
while($teil = mysql_fetch_array($res)){
echo "<option value='".$teil["Teil"]."'>".$teil["Teil"]."</option>";
}
          ?>
        </select>
        <label style='margin-left: 150px;'>Datum:</label><input type='text' name="datum" id='datum' value='<?echo date("Y-m-d H:i:s");?>' ><label style='margin-left: 50px;'>Datum inventury:</label><input type='text' name="datum_inventury" id="datum_inventury" readonly='readonly' >
        <br /><br />
<?  
  
$res = mysql_query("SELECT * FROM `dlager` order by lager") or die(mysql_error());
while($lager = mysql_fetch_array($res)){
echo "<input type='text' class='lager' readonly='readonly' name='Lag".$lager["Lager"]."' value='".$lager["Lager"]." - ".$lager["LagerBeschreibung"]."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stk.&nbsp;&nbsp;<input type='text' name='".$lager["Lager"]."Stk' id='".$lager["Lager"]."Stk'><br />";
}
mysql_close();
        ?>
<!--        // ulozeni zadanyh hodnot-->
        <input type="submit" value="Uloz" id='save'>
        <!-- <input type="button" value="Lager Bestand" id="lagerbestand" style="width: 100px; margin-left: 300px; margin-bottom:3px;" onClick="lagerBestand();" /> -->
        <input type="button" value="Ende/Konec" id="konec" style="width: 100px; margin-left: 20px; margin-bottom:3px;" onClick="location.href='../index.php'">
      </form>
             
      </fieldset>
                 
  </body>
</html>
