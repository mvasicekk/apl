<?xml version="1.0" encoding="windows-1250"?>
<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">


  <fo:layout-master-set>
    <!-- All page templates go here -->    
    <fo:simple-page-master master-name="A4"
 page-width="297mm" page-height="210mm"
 margin-top="1cm"   margin-bottom="1cm"
 margin-left="1cm"  margin-right="1cm">
  <fo:region-body   margin="3cm"/>
  <fo:region-before extent="0cm"/>
  <fo:region-after  extent="2cm"/>
  <fo:region-start  extent="0cm"/>
  <fo:region-end    extent="0cm"/>
</fo:simple-page-master>
  </fo:layout-master-set>


<fo:page-sequence master-reference="A4">
          <!-- Page content goes here -->
  <fo:flow flow-name="xsl-region-body">
      <fo:table-and-caption>
<fo:table>
<fo:table-column column-width="25mm"/>
<fo:table-column column-width="25mm"/>

<fo:table-header>
  <fo:table-row>
  <fo:table-cell>Lief-EXP</fo:table-cell>
          <fo:table-cell>Teil</fo:table-cell>
          <fo:table-cell>Pallete</fo:table-cell>
          <fo:table-cell>Stueckzahl</fo:table-cell>
          <fo:table-cell>Gew<br /> (to)<br /> IMP</fo:table-cell>
          <fo:table-cell>S0011 (P)</fo:table-cell>
          <fo:table-cell>S0011 (T)</fo:table-cell>
          <fo:table-cell>S0041 (St)</fo:table-cell>
          <fo:table-cell>S0051 (E)</fo:table-cell>
          <fo:table-cell>S0061 (F)</fo:table-cell>
          <fo:table-cell>GESAMT<br />VzKd</fo:table-cell>

  </fo:table-row>
</fo:table-header>

<fo:table-body>
  <fo:table-row>
    <fo:table-cell>
      <fo:block>Volvo</fo:block>
    </fo:table-cell>
    <fo:table-cell>
      <fo:block>$50000</fo:block>
    </fo:table-cell>
  </fo:table-row>
  <fo:table-row>
    <fo:table-cell>
      <fo:block>SAAB</fo:block>
    </fo:table-cell>
    <fo:table-cell>
      <fo:block>$48000</fo:block>
    </fo:table-cell>
  </fo:table-row>
</fo:table-body>

</fo:table>
</fo:table-and-caption>
  </fo:flow>
</fo:page-sequence>
  
</fo:root>
