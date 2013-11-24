<xsl:stylesheet version = '1.0'
    xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>

<xsl:output method="html" encoding="windows-1250"/>

<xsl:template match="S165">
<html>
<head>
<title>
S165
</title>
<link rel="stylesheet" href="./styl.css" type="text/css"/>
</head>
<body>
<h1>S165</h1>
<table class="S165" cellspacing="0">
<tr class="header1">
<td class="cislo">
Persnr
</td>
<td>
Name
</td>
<td class="cislo">
Stunden
</td>
<td class="cislo">
Schicht
</td>
<td>
tat
</td>
<td>
trans
</td>
<td>
essen
</td>
<td>
von
</td>
<td>
bis
</td>
<td class="cislo">
pause1
</td>
<td class="cislo">
pause2
</td>

</tr>
	<xsl:apply-templates select="row"/>
</table>
</body>
</html>



</xsl:template>

<xsl:template	match="row">
	<tr class="ramecek">
		<xsl:apply-templates select="PersNr|Name|Vorname|Stunden|von|bis|tat|Schicht|transport|essen|pause1|pause2"/>
	</tr>
</xsl:template>

<xsl:template	match="PersNr">
	<td class="cislo_border_right">
		<xsl:apply-templates/>
	</td>
</xsl:template>


<xsl:template	match="Name|Vorname">
	<td>
		<xsl:apply-templates/>
	</td>
</xsl:template>

<xsl:template	match="von|bis">
	<td>
		<xsl:apply-templates/>
	</td>
</xsl:template>


<xsl:template	match="tat">
	<td>
		<xsl:apply-templates/>
	</td>
</xsl:template>

<xsl:template	match="Schicht">
	<td class="cislo_border_right">
		<xsl:apply-templates/>
	</td>
</xsl:template>

<xsl:template	match="transport|essen">
	<td class="cislo">
		<xsl:apply-templates/>
	</td>
</xsl:template>


<xsl:template	match="pause1|pause2|Stunden">
	<td class="cislo_border_right">
		<xsl:apply-templates/>
	</td>
</xsl:template>


</xsl:stylesheet>
