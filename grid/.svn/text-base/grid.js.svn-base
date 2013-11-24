var xmlHttp = createXmlHttpRequestObject();

var xsltFileUrl = "grid.xsl";

var feedGridUrl = "grid.php";

var gridDivId = "gridDiv";

var statusDivId = "statusDiv";

var tempRow;

var editableId = null;

var stylesheetDoc;

function init()
{
	// test jestli prohlizes podporuje XSL transformace
	if(window.XMLHttpRequest && window.XSLTProcessor && window.DOMParser)
	{
		loadStylesheet();
		loadGridPage(1);
		return;
	}

	if(window.ActiveXObject && createMsxml2DOMDocumentObject())
	{
		loadStylesheet();
		loadGridPage(1);
		return;
	}

	alert("vas prohlizec nepodporuje transformace xslt");
}

function createMsxml2DOMDocumentObject()
{
	var msxml2DOM;
	var msxml2DOMDocumentVersions = new Array(	"Msxml2.DOMDocument.6.0",
							"Msxml2.DOMDocument.5.0",
							"Msxml2.DOMDocument.4.0");
	for(var i=0;i<msxml2DOMDocumentVersions.length && !msxml2DOM;i++)
	{
		try
		{
			msxml2DOM=new ActiveXObject(msxml2DOMDocumentVersions[i]);
		}
		catch(e)
		{
		}

	}

	if(!msxml2DOM)
	{
		alert("nemam MSXML funkcionalitu, starej prohlizec");
	}
	else
		return msxml2DOM;
}

function createXmlHttpRequestObject()
{
	var xmlHttp;

	try
	{
		xmlHttp=new XMLHttpRequest();
	}
	catch(e)
	{
		var XmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0",
						"MSXML2.XMLHTTP.5.0",
						"MSXML2.XMLHTTP.4.0",
						"MSXML2.XMLHTTP.3.0",
						"MSXML2.XMLHTTP");
		for(var i=0;i<XmlHttpVersions.length && !xmlHttp; i++)
		{
			try
			{
				xmlHttp = new ActiveXObject(XmlHttpVersions[i]);
			}
			catch(e)
			{
			}
		}
	}

	if(!xmlHttp)
		alert("nemuzu vytvorit xmlhttprequest");
	else
		return xmlHttp;
}


function loadStylesheet()
{
	xmlHttp.open("GET",xsltFileUrl,false);
	xmlHttp.send(null);

	if(this.DOMParser)
	{
		var dp = new DOMParser();
		stylesheetDoc = dp.parseFromString(xmlHttp.responseText,"text/xml");
	}
	else if(window.ActiveXObject)
	{
		stylesheetDoc = createMsxml2DOMDocumentObject();
		stylesheetDoc.async = false;
		stylesheetDoc.load(xmlHttp.responseXML);
	}
}

function loadGridPage(pageNo)
{
	editableId=false;

	if(xmlHttp && (xmlHttp.readyState == 4 || xmlHttp.readyState ==0 ))
	{
		var query = feedGridUrl + "?action=FEED_GRID_PAGE&page="+pageNo;
		xmlHttp.open("GET",query,true);
		xmlHttp.onreadystatechange=handleGridPageLoad;
		xmlHttp.send(null)
	}
}

function handleGridPageLoad()
{
	if(xmlHttp.readyState==4)
	{
		if(xmlHttp.status==200)
		{
			response = xmlHttp.responseText;
			if(response.indexOf("ERRNO")>=0 || response.indexOf("error")>=0 || response.length==0)
			{
				alert(response.length==0 ? "Server error:" : response);
				return;
			}

			xmlResponse = xmlHttp.responseXML;

			if(window.XMLHttpRequest && window.XSLTProcessor && window.DOMParser )
			{
				var xsltProcessor = new XSLTProcessor();
				xsltProcessor.importStylesheet(stylesheetDoc);
				page=xsltProcessor.transformToFragment(xmlResponse,document);
				var gridDiv = document.getElementById(gridDivId);
				gridDiv.innerHTML="";
				gridDiv.appendChild(page);
			}
			else if (window.ActiveXObject)
			{
				var theDocument = createMsxml2DOMDocumentObject();
				theDocument.async = false;
				theDocument.load(xmlResponse);
				var gridDiv = document.getElementById(gridDivId);
				gridDiv.innerHTML=theDocument.transformNode(stylesheetDoc);
			}

		}
		else
		{
			alert("Error reading server response !");
		}
	}
}

function editId(id,editMode)
{
	var productRow = document.getElementById(id).cells;

	if(editMode)
	{
		if(editableId) editId(editableId,false);
		save(id);
		productRow[1].innerHTML = "<input class='editName' type='text' name='name' value='" + productRow[1].innerHTML+"' >";
		productRow[2].innerHTML = "<input class='editPrice' type='text' name='price' value='" + productRow[2].innerHTML+"' >";
		productRow[3].getElementsByTagName("input")[0].disabled=false;
	}
}
