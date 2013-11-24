// JavaScript Document

function validatedatum()
{
	var datum = document.getElementById('Datum');
	//alert(datum.value);	
}

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
	teillangArray = xml.getElementsByTagName('teillang');
	teilbezArray = xml.getElementsByTagName('bezeichnung');
	
// vytvorim tabulku s vysledkama
	var div="<table>";
	for(i=0;i<teilArray.length;i++)
	{
		dil=teilArray.item(i).firstChild.data;

		div+="<tr id='tr"+i+"' onclick='handleOnClickSuggest(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);'>";
		div+="<td>"+kundeArray.item(i).firstChild.data+"</td>";
		//tlustydil="<font color='red'><b>"+dil.substring(0,kw.value.length)+"</b></font>";
		//zbytekdilu=dil.substring(kw.value.length,dil.length);		
		div+="<td id='a"+i+"'>"+teilArray.item(i).firstChild.data+"</td>";
		//div+="<td id='a"+i+"'>"+tlustydil+zbytekdilu+"</td>";
		div+="<td>"+teillangArray.item(i).firstChild.data+"</td>";
		div+="<td>"+teilbezArray.item(i).firstChild.data+"</td>";
		div+="<td>"+gewArray.item(i).firstChild.data+"</td>";
		div+="<tr>";
		//dily+="kunde:"++"teil:"+teilArray.item(i).firstChild.data+"bez:"+teilbezArray.item(i).firstChild.data+"original:"+teillangArray.item(i).firstChild.data+"gew:"+gewArray.item(i).firstChild.data+"<br>";
	}
	div+="</table>";
	foot.innerHTML=div;
	if(teilArray.length>0)
	{
		scroll.style.left=getRealPos(kw,"x")+'px';//kw.style.left;
		scroll.style.top=getRealPos(kw,"y")+'px';//kw.style.top;
		//bez.value=getRealPos(kw,"x")+'px'+";"+getRealPos(kw,"y")+'px';
		scroll.style.visibility = "visible";
		scroll.style.height="80px";
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
	// posunout se na dalsi poilcko v TAB poradi
    	var pole = new Array("auftragsnr","teil","pal","beh","stk","nachlager","weiter");
	var element =  document.getElementById("teil").id;
	var i = zjistiId(element, pole) +1;
	document.getElementById(pole[i]).focus();
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

function refreshdatum(text)
{
	var datum = document.getElementById('Datum');
	if(text=="ERROR")
	{
		//alert('Fehler bei Datumeingabe / chyba pri zadani datumu');
		datum.value=text;
		datum.focus();
		datum.select();
	}
	else
	{
		datum.value=text;
		//datum.style.backgroundColor="white";
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
	
    var pole = new Array("auftragsnr","teil","pal","beh","stk","nachlager","weiter");
    var element =  oEvent.target.id;
    if (oEvent.keyCode == 13) {
		var i = zjistiId(element, pole) +1;
		document.getElementById(pole[i]).focus();
		oEvent.preventDefault();
	  }
    else{
      return true;
    } 
	
}
  
EventUtil.addEventHandler(document, "keypress", checkCR);
EventUtil.addEventHandler(window,"onload",init);



function beforeSubmit(){
//alert("Právì odesíláte data!")
}

function init()
{
	// retrieve the input control for the keyword
	var oKeyword = document.getElementById("keyword");
	// prevent browser from starting the autofill function
	oKeyword.setAttribute("autocomplete", "off");
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
	updateKeywordValue(document.getElementById("tr0"));
	selectRange(oKeyword,start,oKeyword.value.length);
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
