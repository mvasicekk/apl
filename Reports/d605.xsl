<?xml version="1.0" encoding="windows-1250"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">  

<!-- Úpravy parametrů -->

<!-- Velikost papíru -->
<xsl:param name="paper.type" select="'A4'"/>

<!-- XSLT procesor může používat rozšíření pro callouts apod. -->
<xsl:param name="use.extensions" select="1"/>

<!-- Rozšíření specifická pro daný FO procesor -->
<!-- <xsl:param name="passivetex.extensions" select="1"/> -->

<xsl:param name="xep.extensions" select="1"/>

<!-- Velikost písma textu -->
<xsl:param name="body.font.master">11</xsl:param>

<!-- Velikost okrajů -->
<xsl:param name="page.margin.inner" select="'1in'"/>
<xsl:param name="page.margin.outer" select="'1in'"/>

<!-- Číslování sekcí a kapitol -->
<xsl:param name="section.autolabel" select="1"/>
<xsl:param name="section.label.includes.component.label" select="1"/>
<xsl:param name="chapter.autolabel" select="1"/>
<xsl:param name="appendix.autolabel" select="1"/>
<xsl:param name="part.autolabel" select="1"/>
<xsl:param name="preface.autolabel" select="0"/>

<!-- Nechceme obrázek -->
<xsl:param name="draft.watermark.image" select="''"/>

<!-- Nadpisy jsou zarovnány s textem, jak je zvykem v evropské typografii -->
<xsl:param name="title.margin.left" select="'0pt'"/>

<xsl:template match="/root">
  <html>
  <head>
    <title>D605</title>
    <style type="text/css" media="screen, print">
    /* <![CDATA[ */

    .heading{border:none; vertical-align: bottom; margin-bottom:5px;}
    .heading td{border:none;}
    body{width:280mm;}
    table{width:280mm; border-top:2px solid black; clear:left; margin-top:0px;}
    th{ font-size:2mm; font-family:Arial; font-weight:bold; border:1px solid black;}
    td{font-size:2mm; font-family:Arial; font-weight:normal; text-align:right; border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black;}
    .down{width:8mm; border-bottom:2px solid black;}
    .slim{width:1mm; border-right:none; font-size:1mm; vertical-align: top; text-align:left;}
    h1{margin:0px 50px 10px 20px; float:left;}
    h3{float:left;}
    footer{bottom:0; page-break-after:always;}
    /* ]]> */
    </style>
    
  </head>
    
    <body>
    <table class='heading'>
      <tr>
        <td>
          <span style='font-size:3mm; font-weight:bold;'>
            Abydos s.r.o. 
          </span>
        </td>
        <td>
          <h1>D605 Auftragsuebersicht (IM)</h1>
        </td>
        <td>
          <h3>Import Datum:  
            <xsl:value-of select='row/aufdat'/>
            <span style='margin-left:5mm;'>
              Auftrag: (IM) 
              <xsl:value-of select='row/AuftragsNr'/>
            </span></h3>
        </td>
      </tr>
    </table>
      <table border="0" cellspacing="0">
        <tr>
          <th rowspan='2' style='border-bottom:2px solid black;'>Lief-EXP</th>
          <th rowspan='2' style='border-bottom:2px solid black;'>Teil</th>
          <th colspan='3' style='border-right:2px solid black;'>Pallete</th>
          <th colspan='10'>Stueckzahl</th>
          <th rowspan='2' style='border-bottom:2px solid black; border-right:2px solid black;'>Gew<br /> (to)<br /> IMP</th>
          <th colspan='3' style='border-right:2px solid black;'>S0011 (P)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0011 (T)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0041 (St)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0051 (E)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0061 (F)</th>
          <th rowspan='2' style='border-bottom:2px solid black; border-right:2px solid black;'>GESAMT<br />VzKd</th>
          </tr>
          <tr>
          <th class='down'>IMP</th>
          <th class='down'>EXP</th>
          <th class='down' style='border-right:2px solid black;'>Stk. EXP</th>
          <th class='down'>IM</th>
          <th class='down'>Tr</th>
          <th class='down'>PU</th>
          <th class='down'>St</th>
          <th class='down'>G</th>
          <th class='down'>E</th>
          <th class='down'>(2)</th>
          <th class='down'>(4)</th>
          <th class='down'>(6)</th>
          <th class='down'>G- <br />IMP</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th></tr>
        <xsl:for-each select="row">

          <xsl:if test="datAndPage or end">            
                <tr>
          <td colspan='35' style="border:none;"><br /><xsl:value-of select='datAndPage'/><br /><br /></td>
        </tr>
        <xsl:choose>
        <xsl:when test="end='page'">

        <tr>
          <th rowspan='2' style='border-bottom:2px solid black;'>Lief-EXP</th>
          <th rowspan='2' style='border-bottom:2px solid black;'>Teil</th>
          <th colspan='3' style='border-right:2px solid black;'>Pallete</th>
          <th colspan='10'>Stueckzahl</th>
          <th rowspan='2' style='border-bottom:2px solid black; border-right:2px solid black;'>Gew<br /> (to)<br /> IMP</th>
          <th colspan='3' style='border-right:2px solid black;'>S0011 (P)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0011 (T)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0041 (St)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0051 (E)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0061 (F)</th>
          <th rowspan='2' style='border-bottom:2px solid black; border-right:2px solid black;'>GESAMT<br />VzKd</th>
        </tr>
        <tr>
          <th class='down'>IMP</th>
          <th class='down'>EXP</th>
          <th class='down' style='border-right:2px solid black;'>Stk. EXP</th>
          <th class='down'>IM</th>
          <th class='down'>Tr</th>
          <th class='down'>PU</th>
          <th class='down'>St</th>
          <th class='down'>G</th>
          <th class='down'>E</th>
          <th class='down'>(2)</th>
          <th class='down'>(4)</th>
          <th class='down'>(6)</th>
          <th class='down'>G- <br />IMP</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
        </tr>
    </xsl:when>  
    <xsl:when test="end='konec'">

    </xsl:when>
    </xsl:choose>  
  </xsl:if>        
          <tr>
            <td>
              <xsl:apply-templates select='export_lief'/></td>
            <td>
              <xsl:apply-templates select='Teil'/></td>
            <td>
              <xsl:apply-templates select='import_pal'/></td>
            <td>
              <xsl:apply-templates select='export_pal'/></td>
            <td style='border-right:2px solid black;'>
              <xsl:apply-templates select='export_stk'/></td>
            <td>                           
              <xsl:apply-templates select='import_stk'/></td>
            <td>                           
              <xsl:apply-templates select='sum_stk_T'/></td>
            <td>                           
              <xsl:apply-templates select='sum_stk_P'/></td>
            <td>                           
              <xsl:apply-templates select='sum_stk_St'/></td>
            <td>                           
              <xsl:apply-templates select='sum_stk_G'/></td>
            <td>
              <xsl:apply-templates select='sum_stk_E'/></td>
            <td>                           
              <xsl:apply-templates select='auss2'/></td>
            <td>                           
              <xsl:apply-templates select='auss4'/></td>
            <td>                           
              <xsl:apply-templates select='auss6'/></td>
            <td>
              <xsl:apply-templates select='imp_gew'/></td>
            <td>                           
              <xsl:apply-templates select='GDiff'/></td>
            <td class='slim'>                           
              <xsl:apply-templates select='cnt_S0011P'/></td>
            <td style='border-left:none;'>                           
              <xsl:apply-templates select='S0011P'/></td>
            <td>                           
              <xsl:apply-templates select='sumS0011P'/></td>
            <td class='slim'>                           
              <xsl:apply-templates select='cnt_S0011T'/></td>
            <td style='border-left:none;'>                           
              <xsl:apply-templates select='S0011T'/></td>
            <td>                           
              <xsl:apply-templates select='sumS0011T'/></td>
            <td class='slim'>                           
              <xsl:apply-templates select='cnt_S0041'/></td>
            <td style='border-left:none;'>
              <xsl:apply-templates select='S0041'/></td>
            <td>                           
              <xsl:apply-templates select='sumS0041'/></td>
            <td class='slim'>                           
              <xsl:apply-templates select='cnt_S0051'/></td>
            <td style='border-left:none;'>                           
              <xsl:apply-templates select='S0051'/></td>
            <td>                           
              <xsl:apply-templates select='sumS0051'/></td>
            <td class='slim'>                           
              <xsl:apply-templates select='cnt_S0061'/></td>
            <td style='border-left:none;'>                           
              <xsl:apply-templates select='S0061'/></td>
            <td>                           
              <xsl:apply-templates select='sumS0061'/></td>
            <td>                           
              <xsl:apply-templates select='sumvzkd'/></td>
            </tr>

          </xsl:for-each>              
            </table>

            <h4 style='margin-bottom:0px; margin-left:150px;'> Erstellt: _________________________________________</h4>
              <span style='margin-left:350px;'>Datum/Unterschrift</span>

            </body>
            </html>
              </xsl:template>
</xsl:stylesheet>
