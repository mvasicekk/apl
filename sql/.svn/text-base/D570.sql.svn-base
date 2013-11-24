select
dkopf.kunde,
dkopf.teil,
dkopf.teilbez,
gew,
`muster-vom` as mustervom,
`muster-platz` as musterplatz,
`muster-freigabe-1` as musterfreigabe1,
`muster-freigabe-1-vom` as musterfreigabe1vom,
`muster-freigabe-2` as musterfreigabe2,
`muster-freigabe-2-vom` as musterfreigabe2vom,
bemerk,
name1,
teillang,
max(aufdat) letztdatum from dauftr join daufkopf using(auftragsnr)
join dkopf using(teil)
join dksd on daufkopf.kunde=dksd.kunde where ((daufkopf.aufdat between '2008-01-01' and '2008-12-31') and (dkopf.kunde=122)) group by dkopf.kunde,dkopf.teil
order by dkopf.teil