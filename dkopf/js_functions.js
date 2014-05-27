// JavaScript Document

function rebuildpage()
{
	// zjistim formatovaci udaje pro formular
	
	// pouze pri existenci elementu dkopf_form_footer
	if(document.getElementById('dkopf_form_footer'))
	{
		var clientHeight = document.getElementById('dkopf_form_footer').parentNode.clientHeight;
		var footerHeight = document.getElementById('dkopf_form_footer').clientHeight;
		var clientWidth = document.getElementById('dkopf_form_footer').parentNode.clientWidth;
		var souradniceNode = document.getElementById('souradnice');
		var aplNode = document.getElementById('apl_table');
		var offsetTopAplNode = aplNode.offsetTop;
		var scrollAplNode = document.getElementById('scroll_apl');
		// pokud mam tabulku s obrazkama zkratim vysku apl u vysku tabulky s obrazkama + mezeru
		var attachmentNode = document.getElementById('attach_table');
		
		//aplNode.style.height=clientHeight-offsetTopAplNode-footerHeight-20;
		vyskaApl=clientHeight-offsetTopAplNode-footerHeight-20;
		
		if(attachmentNode)
		{
	 		vyskaApl=vyskaApl-document.getElementById('attach_table').clientHeight-10;
		}
		
		scrollAplNode.style.height=vyskaApl;
		
	
		textSouradnice='clientHeight='+clientHeight+'<br>clientWidth='+clientWidth;
		textSouradnice+='<br>aplNode='+aplNode+'<br>offsetTopAplNode='+offsetTopAplNode;
		textSouradnice+='<br>vyskaApl='+vyskaApl;
	
		if(attachmentNode)
		{
			vyskaAttachTable=document.getElementById('attach_table').clientHeight;
			textSouradnice+='<br>attachmentNode='+attachmentNode+'<br>vyskaAttachTable='+vyskaAttachTable;
		}
	}
	//souradniceNode.innerHTML=textSouradnice;
}


// Removes leading whitespaces
function LTrim( value ) {
	
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	
}

// Removes ending whitespaces
function RTrim( value ) {
	
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	
}

// Removes leading and ending whitespaces
function trim( value ) {
	
	return LTrim(RTrim(value));
	
}



function refreshdatum(text)
{
	//alert(text);
	
	// control id je posladni cast textu pred znakem >
	var controlid=text.substring(text.lastIndexOf('>')+1,text.length);
	// hodnota pro zobrazeni v textboxu je od zacatku retezce, po znamenko >
	var hodnota=text.substring(0,text.indexOf('>'));
	// objekt s obsahem ke zmene
	var control = document.getElementById(controlid);
	
	if(hodnota=="ERROR")
	{
		control.style.backgroundColor='red';
		control.value=hodnota;
	}
	else
	{
		control.style.backgroundColor='';
		control.value=hodnota;
	}
}

/*
function validatedatum()
{
	var datum = document.getElementById('Datum');
	//alert(datum.value);	
}
*/

function refreshteil(text)
{
	var teil = document.getElementById('teil');
	var teilbez = document.getElementById('teilbez');
	var kunde = document.getElementById('kunde');
	
	if(text=="NOTEIL")
	{
		teil.value=text;
		teilbez.value="";
		kunde.value="";
	}
	else
	{
		
		//teil.value=text;
		teil.value=text.substring(0,text.indexOf(';'));
		teilbez.value=text.substring(text.indexOf(';')+1,text.lastIndexOf(';'));
		kunde.value=text.substring(text.lastIndexOf(';')+1);
		teil.select();
	}	
}	

function refreshteillang(text)
{
	var teillang = document.getElementById('teillang');
	var teilbez = document.getElementById('teilbez');
	var kunde = document.getElementById('kunde');
	
	if(text=="NOTEIL")
	{
		teillang.value=text;
		teilbez.value="";
		kunde.value="";
	}
	else
	{
		// rozkouskovat retezec na polozky teillang,teilbez,kunde podle stredniku
		teillang.value=text.substring(0,text.indexOf(';'));
		teilbez.value=text.substring(text.indexOf(';')+1,text.lastIndexOf(';'));
		kunde.value=text.substring(text.lastIndexOf(';')+1);
	}	
}	



function pissuggest(xml)
{
	//var foot = document.getElementById('form_footer_tlacitka_reporty');
	var foot = document.getElementById('suggest');
	var scroll = document.getElementById('scroll');
	var kw = document.getElementById('teil');
	var bez = document.getElementById('teilbez');

	var div ="";

	teilArray = xml.getElementsByTagName('teilnr');
	kundeArray =xml.getElementsByTagName('kunde');
	gewArray = xml.getElementsByTagName('gew');
        statusArray = xml.getElementsByTagName('status');
	teillangArray = xml.getElementsByTagName('teillang');
	teilbezArray = xml.getElementsByTagName('bezeichnung');
	
// vytvorim tabulku s vysledkama
	var div="<table id='vysledky'>";
	// pokud mam nejake vysledky, zobrazim hlavicku tabulky
	if(teilArray.length>0)
	{
		div+="<tr class='result_table_header'><td>kunde</td><td>teil</td><td>Bezeichnung</td><td>Teil Original</td><td>Gewicht</td><td>Status</td></tr>";
	}
	for(i=0;i<teilArray.length;i++)
	{
		dil=teilArray.item(i).firstChild.data;

		div+="<tr id='tr"+i+"' onclick='handleOnClickSuggest(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);'>";
		div+="<td>"+kundeArray.item(i).firstChild.data+"</td>";
		//tlustydil="<font color='red'><b>"+dil.substring(0,kw.value.length)+"</b></font>";
		//zbytekdilu=dil.substring(kw.value.length,dil.length);		
		div+="<td id='a"+i+"'>"+teilArray.item(i).firstChild.data+"</td>";
		//div+="<td id='a"+i+"'>"+tlustydil+zbytekdilu+"</td>";
		if(teilbezArray.item(i).hasChildNodes())
			div+="<td>"+teilbezArray.item(i).firstChild.data+"</td>";
		else
			div+="<td></td>";
			
		if(teillangArray.item(i).hasChildNodes())
			div+="<td>"+teillangArray.item(i).firstChild.data+"</td>";
		else
			div+="<td></td>";
		
		div+="<td>"+gewArray.item(i).firstChild.data+"</td>";
                div+="<td>"+statusArray.item(i).firstChild.data+"</td>";
		div+="<tr>";
		//dily+="kunde:"++"teil:"+teilArray.item(i).firstChild.data+"bez:"+teilbezArray.item(i).firstChild.data+"original:"+teillangArray.item(i).firstChild.data+"gew:"+gewArray.item(i).firstChild.data+"<br>";
	}
	div+="</table>";
	
	foot.innerHTML=div;
	
	if(kw.value.length>0)
	{
		// uvolnim tlacitko pro vytvoreni noveho dilu pokud mam alespon jeden znak
		var nBut = document.getElementById('teilneubutton');
		nBut.disabled=false;
	}
	else
	{
		// uvolnim tlacitko pro vytvoreni noveho dilu pokud mam alespon jeden znak
		var nBut = document.getElementById('teilneubutton');
		nBut.disabled=true;
	}
	
	if(teilArray.length>0)
	{
		scroll.style.left=getRealPos(kw,"x")+'px';//kw.style.left;
		scroll.style.top=getRealPos(kw,"y")+'px';//kw.style.top;
		//bez.value=getRealPos(kw,"x")+'px'+";"+getRealPos(kw,"y")+'px';
		scroll.style.visibility = "visible";
		scroll.style.height="200px";
		scroll.scrollTop=0;
		autocompleteKeyword();
	}
	else
	{
		scroll.style.visibility = "hidden";
		scroll.style.height="0";
	}
}

function handleOnClickSuggest(oTr)
{
	
	updateKeywordValue(oTr);
	var oKeyword = document.getElementById("teil");
	
	//alert('handleonclicksuggest,okeyword='+oKeyword.value);
	
	document.location.href='./dkopf.php?teil='+oKeyword.value;
}


function getRealPos(ele,dir)
{
	pos=0;
	(dir=="x") ? pos = ele.offsetLeft : pos = ele.offsetTop;
	tempEle = ele.offsetParent;
	while(tempEle != null)
	{
		pos += (dir=="x") ? tempEle.offsetLeft : tempEle.offsetTop;
		tempEle = tempEle.offsetParent;
	}
	return pos;
}

function handleOnMouseOver(oTr)
{
	//deselectAll();
	oTr.className="highlightrow";
	position = oTr.id.substring(2,oTr.id.length);
}

function handleOnMouseOut(oTr)
{
	oTr.className="";
	position = -1;
}

function refresh_muster_vom(text)
{
	var datum = document.getElementById('muster_vom');
	if(text=="ERROR")
	{
		//alert('Fehler bei Datumeingabe / chyba pri zadani datumu');
		datum.value=text;
		datum.style.backgroundColor="red";
		//datum.focus();
		//datum.select();
	}
	else
	{
		datum.value=text;
		datum.style.backgroundColor="";
	}	
}	

function refreshvon()
{
	var von = document.getElementById('Von');
	var hodiny,minuty;
	if(von.value.length==4)
	{
		hodiny=von.value.substr(0,2);
		minuty=von.value.substr(2,2);
		von.value=hodiny+":"+minuty;
	}	
}

function refreshbis()
{
	var von = document.getElementById('Bis');
	var hodiny,minuty;
	if(von.value.length==4)
	{
		hodiny=von.value.substr(0,2);
		minuty=von.value.substr(2,2);
		von.value=hodiny+":"+minuty;
	}	
}

function pisjmeno(text)
{
	var jmeno = document.getElementById('persName');
	var pers = document.getElementById('PersNr');
	var schicht=document.getElementById('Schicht');

	// test na existenci osobniho cisla
	// pokud ne, vratim se na pole s osobnim cislem
	if((text=="nopersnr"))
	{
		alert('PersNr nichtgefunden');
		pers.focus();
		pers.select();
	}
	else
	{
		jmeno.value=text.substring(0,text.indexOf(';'));
		schicht.value=text.substring(text.indexOf(';')+1);
		schicht.select();
	}
}

function pauza(){

refreshbis();
var von = document.getElementById("Von");
var bis = document.getElementById("Bis");
var vonh= von.value.substr(0,2);
var vonm= von.value.substr(3,2);
var bish= bis.value.substr(0,2);
var bism= bis.value.substr(3,2);
var vonsc = new Date(0,0,0,vonh,vonm,0);
var bissc = new Date(0,0,0,bish,bism,0);
var time = bissc.getTime() - vonsc.getTime();
var cas = Math.round((time / (60*60*1000))*10)/10;
var cas1 = time / (60*60*1000);
var pauza = Math.round((cas1 / 17)*10)/10 ;
void (document.getElementById('pause1').value = pauza);
void (document.getElementById('stunden').value = cas);
}


function zjistiId(element,pole){
for(i=0;i<pole.length; i++){
if(pole[i]== element){return i;}
}
}


function checkCR() {
	
	var oEvent = EventUtil.getEvent();
	
	// ktere policko mi poslalo event
	var element =  oEvent.target.id;
	var policko = oEvent.target;
	
	// pokud to byl teil budu testovat dat
	if((oEvent.keyCode==13)&&(element=='teil'))
	{
		//alert('je to z teil'+element);
		// zjistim kolik znaku uz mam v textovem poli
		hodnota = policko.value;
		if(hodnota.length>1)
		{
			//alert('hodnota v policku='+hodnota);
			// mam v tabulce pod polem jeden radek ?
			// zkusim ziskat tabulku
			var tabulka = document.getElementById('vysledky');
			if(tabulka)
			{
				//alert('mam tabulku:'+tabulka);
				var radek=tabulka.rows[1];
				if(radek)
				{
					//alert('mam radek:'+radek);
					oEvent.preventDefault();
					handleOnClickSuggest(radek);
				}
			}
		}
	}
}
  
EventUtil.addEventHandler(document, "keydown", checkCR);
EventUtil.addEventHandler(window,"load",init);
EventUtil.addEventHandler(window,"resize",rebuildpage);



function beforeSubmit(){
//alert("Pr�v� odes�l�te data!")
}

function init()
{
	// retrieve the input control for the keyword
	//var oKeyword = document.getElementById("keyword");
	// prevent browser from starting the autofill function
	//oKeyword.setAttribute("autocomplete", "off");
	rebuildpage();
}

/* function that hides the layer containing the suggestions */
function hideSuggestions()
{
var oScroll = document.getElementById("scroll");
oScroll.style.visibility = "hidden";
oScroll.style.height="0px";
}

function autocompleteKeyword()
{
	var oKeyword = document.getElementById("teil");
	position = 0;
	deselectAll();
	
	start=oKeyword.value.length;
	document.getElementById("tr0").className="highlightrow";	
	
	// nebudu upravovat obsah textboxu s vyhledavanym textem
	//updateKeywordValue(document.getElementById("tr0"));
	//selectRange(oKeyword,start,oKeyword.value.length);
}

function updateKeywordValue(oTr)
{
	var oKeyword=document.getElementById("teil");
	
	slovo=document.getElementById("a"+oTr.id.substring(2,oTr.id.length)).childNodes[0].data;
	//slovo="a"+oTr.id.substring(2,oTr.id.length);
	oKeyword.value=slovo;
}

/* function that selects a range in the text object passed as parameter */
function selectRange(oText, start, length)
{
// check to see if in IE or FF
if (oText.createTextRange)
{
//IE
var oRange = oText.createTextRange();
oRange.moveStart("character", start);
oRange.moveEnd("character", length - oText.value.length);
oRange.select();
}
else
// FF
if (oText.setSelectionRange)
{
oText.setSelectionRange(start, length);
}
oText.focus();
}

function deselectAll()
{
	
}


function init_dkopf_form(rezim)
{
	if(rezim=='show')
	{
		// pouze rezim pro prohlizeni, pripadne editaci nekterych parametru
		var teil = document.getElementById("teil");
		var kunde = document.getElementById("kunde");
		// teil bude readonly
		teil.disabled=true;
		// nastavim fokus na kunde
		kunde.focus();
		
	}
}

function validate_kunde(xml)
{
	var kdnrarray = xml.getElementsByTagName('kundenr');
	var kunde = document.getElementById("kunde");
	var failed = document.getElementById("kunde_failed");
	
	kdnr=kdnrarray.item(0).firstChild.data;
	
	if(kdnr.substring(0,5)=="ERROR")
	{
		kunde.style.backgroundColor='red';
		failed.className='error';
	}
	else
	{
		kunde.style.backgroundColor='';
		failed.className='hidden';
	}
	
}

function validate_teil(xml)
{
	var teilnrarray = xml.getElementsByTagName('teilnr');
	var teil = document.getElementById("teil");
	var failed = document.getElementById("teil_failed");
	var error_descriptionarray = xml.getElementsByTagName('teilbez');
	
	teilnr=teilnrarray.item(0).firstChild.data;
	error_description = error_descriptionarray.item(0).firstChild.data;
	
	
	if(teilnr.substring(0,5)=="ERROR")
	{
		teil.style.backgroundColor='red';
		failed.className='error';
		failed.value=error_description;
	}
	else
	{
		teil.style.backgroundColor='';
		failed.className='hidden';
	}
	
}


function js_validate_float(control)
{

	var hodnota = control.value
	
	re = /,/
	novahodnota=hodnota.replace(re,".");
	
	floatvalue = parseFloat(novahodnota);
	
	if(!isNaN(floatvalue)&&(floatvalue>=0))
	{
		control.value=floatvalue;
		control.style.backgroundColor='';
	}
	else
	{
		//chyba validace
		control.style.backgroundColor='red';
		control.value='error';
		//failed.className='error';
		//failed.value=error_description;
	}
}

function js_validate_jn(control)
{
	var hodnota=control.value
	if((hodnota.toUpperCase()=='J')||(hodnota.toUpperCase()=='N'))
	{
		control.value=hodnota.toUpperCase();
		control.style.backgroundColor='';
	}
	else
	{
		control.style.backgroundColor='red';
		control.value='';
	}
}


function toggle_kz_druck(text)
{
	
	var id=text.substr(0,text.indexOf(":"));
	var barva=text.substr(text.indexOf(":")+1);
	//alert('druck'+id);
	var control = document.getElementById('druck'+eval(id));
	
	control.style.backgroundColor=barva;
	
}


function validate_taetnr(xml)
{
	var taetnrarray = xml.getElementsByTagName('taetnr');
	var e_taetnr = document.getElementById("taetnr");
	var e_bez_d = document.getElementById("bez_d");
	var e_bez_t = document.getElementById("bez_t");
	var bez_t = xml.getElementsByTagName("bez_t");
	var bez_d = xml.getElementsByTagName("bez_d");
	
	var failed = document.getElementById("kunde_failed");
	
	taetnr=taetnrarray.item(0).firstChild.data;
	
	if(taetnr.substring(0,5)=="ERROR")
	{
		e_taetnr.style.backgroundColor='red';
		//failed.className='error';
	}
	else
	{
		e_taetnr.style.backgroundColor='';
		bez_d_value=bez_d.item(0).firstChild.data;
		bez_t_value=bez_t.item(0).firstChild.data;
		e_taetnr.value=taetnr;
		e_bez_t.value=bez_t_value;
		e_bez_d.value=bez_d_value;
		
		failed.className='hidden';
	}
	
}



function update_dpos(xml)
{
	var sql = xml.getElementsByTagName('sql').item(0).firstChild.data;
	var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	
	
	if(mysqlerrorarray.item(0).hasChildNodes())
	{
		mysqlerror = mysqlerrorarray.item(0).firstChild.data;
		alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
	}
}



function saverefresh(xml)
{
	var sql = xml.getElementsByTagName('sql').item(0).firstChild.data;
	var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	
	
	if(mysqlerrorarray.item(0).hasChildNodes())
	{
		mysqlerror = mysqlerrorarray.item(0).firstChild.data;
		alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
	}
	
	//alert('sql='+sql);
	//nove = window.open('');
	//nove.document.writeln('sql='+sql);
	//nove.document.writeln('affected rows='+affectedrows);
	
	window.location.reload();
	
	//alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
		
}



function encodeControlValue(i)
{
	var control = document.getElementById(i);
	return encodeURIComponent(control.value);
}

function encodeSelectControlValue(i)
{
	var control = document.getElementById(i);
	var optionarray = control.getElementsByTagName('option');
	var	selectedvalue=optionarray.item(control.selectedIndex).getAttribute('value');
	return encodeURIComponent(selectedvalue);
}

function positionneu()
{
	var div = document.getElementById('dposnew');
	div.style.visibility = 'visible';
	
	// vymazu vsechny hodnoty v polickach formulare
	var inputsArray = div.getElementsByTagName('input');
	for(i=0;i<inputsArray.length;i++)
	{
		// text pro textova pole
		if(inputsArray.item(i).getAttribute('type')=='text')
			inputsArray.item(i).value='';
	}
	
	document.getElementById('newvzkd').value=0;
	document.getElementById('newvzaby').value=0;
	
	var newtatnr = document.getElementById('newtatnr');
	newtatnr.value=3;
	newtatnr.focus();
	newtatnr.select();
}

function insertpositionCancel()
{
	var div = document.getElementById('dposnew');
	div.style.visibility = 'hidden';
}

function insertposition()
{
	var parametry = '';
	
	parametry+='tatnr='+encodeControlValue('newtatnr');
	parametry+='&bez_d='+encodeControlValue('newbez_d');
	parametry+='&bez_t='+encodeControlValue('newbez_t');
	parametry+='&mittel='+encodeControlValue('mittel');
	parametry+='&vzkd='+encodeControlValue('newvzkd');
	parametry+='&vzaby='+encodeControlValue('newvzaby');
	parametry+='&kzgut='+encodeControlValue('newkzgut');
	parametry+='&bedarf='+encodeControlValue('newbedarf');
	parametry+='&lagervon='+encodeControlValue('newlagervon');
	parametry+='&lagernach='+encodeControlValue('newlagernach');
	parametry+='&teil='+encodeControlValue('teil');
	
	//alert('parametry = '+parametry);
	
	parametry=encodeURI(parametry);
	
	// pomoci AJAXu vlozim novy radek do tabulky
	getDataReturnXml('./insert_dpos.php?'+parametry, insert_dpos)
	
}

function validate_newtaetnr(xml)
{
	var errorarray = xml.getElementsByTagName('error');
	var newtatnr = document.getElementById("newtatnr");
	var failed = document.getElementById("newtatnr_failed");
	
	if(errorarray.length>0)
	{
		var errorDescription = errorarray.item(0).firstChild.data;
		newtatnr.style.backgroundColor='red';
		failed.className='error';
		failed.value=errorDescription;
	}
	else
	{
		newtatnr.style.backgroundColor='';
		failed.className='hidden';
		var bez_d='';
		var bez_t='';
		
		// vytahnu si hodnoty navrchovanych casu
		var vzkdNode = xml.getElementsByTagName('vzkdvorschlag').item(0);
		var vzabyNode = xml.getElementsByTagName('vzabyvorschlag').item(0);
		
		if(vzkdNode.hasChildNodes())
			document.getElementById('newvzkd').value=vzkdNode.firstChild.data;
		else
			document.getElementById('newvzkd').value=0;
			
		if(vzkdNode.hasChildNodes())
			document.getElementById('newvzaby').value=vzabyNode.firstChild.data;
		else
			document.getElementById('newvzaby').value=0;
		
			
		// vse je ok, vytahnu si texty pro operace a vlozim je do textovych policek
		var bez_dNode = xml.getElementsByTagName('bez_d').item(0);
		var bez_tNode = xml.getElementsByTagName('bez_t').item(0);
		if(bez_dNode.hasChildNodes())
			bez_d=bez_dNode.firstChild.data;
		if(bez_tNode.hasChildNodes())
			bez_t=bez_tNode.firstChild.data;
		
		document.getElementById('newbez_d').value=bez_d;
		document.getElementById('newbez_t').value=bez_t;
		
		document.getElementById('newbez_d').focus();
		document.getElementById('newbez_d').select();
			
	}
}

function insert_dpos(xml)
{
	var sql = xml.getElementsByTagName('sql').item(0).firstChild.data;
	var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	
	
	if(mysqlerrorarray.item(0).hasChildNodes())
	{
		mysqlerror = mysqlerrorarray.item(0).firstChild.data;
		alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
	}
	
	var div = document.getElementById('dposnew');
	div.style.visibility = 'hidden';
	
	document.location.href='./dkopf.php?teil='+document.getElementById('teil').value;
	//alert('sql='+sql);
}

function new_teil(xml)
{
	var teilneu = xml.getElementsByTagName('teilneu').item(0).firstChild.nodeValue;
	var mysqlerror = xml.getElementsByTagName('mysqlerror').item(0);
	var error = xml.getElementsByTagName('error');

	if(mysqlerror.hasChildNodes())
	{
		var sql = xml.getElementsByTagName('sql').item(0).firstChild.nodeValue;
		alert(mysqlerror.firstChild.nodeValue+'\nsql='+sql);
		return;
	}
	
	if((error.length>0))
	{
		alert(xml.getElementsByTagName('errordescription').item(0).firstChild.nodeValue);
		return;
	}
	else
	{
		//novy dil vlozen do databaze , zobrazim ho
		location.href='./dkopf.php?teil='+teilneu;
	}
}