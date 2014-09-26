select 
		drueck.auftragsnr,
		drueck.teil,
		drueck.taetnr,
		drueck.schicht,
		drueck.datum,
		drueck.persnr,
		dpers.name,
		drueck.`pos-pal-nr` as pal,
		drueck.`stück` as stk,
		drueck.`auss-stück` as aussstk,
		drueck.auss_typ,
		drueck.`vz-soll` as vzkd,
		drueck.`vz-ist` as vzaby,
		if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,(`stück`)*`vz-soll`) as sumvzkd,
		if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,(`stück`)*`vz-ist`) as sumvzaby,
		`verb-zeit` as sumverb,
		gew,
		brgew,
		`muster-platz` as musterplatz,
		`muster-vom` as mustervom
from drueck

join dpers using (persnr)

join dkopf using (teil)

where ((auftragsnr between 111380 and 111380) and (`pos-pal-nr` between 0 and 9999))

order by auftragsnr,teil,pal,taetnr,datum,persnr