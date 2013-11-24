<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250" />
</head>
<?php

	odbc_close_all();
	
	$cnx = odbc_connect("pam2011","","");
	if(!$cnx)
	{
		echo "chyba pri odbc_connect !";
		exit();
	}
	
	echo "cnx=$cnx";

	//$stmt = odbc_prepare($cnx,"update 01kmen11 set ulice=? where (pracis=?)");
        $sql = "insert into 01kmen11 (pracis,rodcis,rodcisz,jmeno,prijmeni,rjmeno,dnaroz,mnaroz,pohlavi,prukaz,vydal,narod,prislus,stav,vzdelani,zmensch,ulice,obec,posta,psc,adrstat,telefset)";
        $sql.= " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = odbc_prepare($cnx,$sql);
	
	$executeflag = odbc_execute($stmt,array(
            //pracis,
            99020,
            //rodcis,
            '721010/2229',
            //rodcisz,
            '',
            //jmeno,
            'Test jmeno',
            //prijmeni,
            'test prijmeni',
            //rjmeno,
            'rodne jmeno',
            //dnaroz,
            '1972-10-10',
            //mnaroz,
            'misto naroz',
            //pohlavi,
            'M',
            //prukaz,
            '123456789',
            //vydal,
            'vydal Sokolov',
            //narod,
            'CZ',
            //prislus,
            'CZ',
            //stav,
            '2',
            //vzdelani,
            'VS',
            //zmensch,
            '',
            //ulice,
            'Odboraru 350',
            //obec,
            'Brezova',
            //posta,
            'Brezova',
            //psc,
            '35601',
            //adrstat,
            'CZ',
            //telefset
            '775083078'
         ));

        echo "insert tester executeflag=$executeflag";
	/*
	odbc_exec($cnx,"update 01kmen08 set ulice='Odboraru 350' where (pracis=104)");
	$sql_insert = "insert into 01kmen08 (";
	$sql_insert.= " pracis,";
	$sql_insert.= " rodcis,";
	$sql_insert.= " jmeno,";
	$sql_insert.= " prijmeni,";
	$sql_insert.= " ulice,";
	$sql_insert.= " rjmeno";
	$sql_insert.= " ,dnaroz";
	$sql_insert.= " ,mnaroz";
	
	$sql_insert.= " ) ";
	$sql_insert.= " values(";
	$sql_insert.= " 1,";
	$sql_insert.= " '0654',";
	$sql_insert.= " 'jmeno',";
	$sql_insert.= " 'prijmeni',";
	$sql_insert.= " 'ulice'";
	$sql_insert.= " ,'rjmeno'";
	$sql_insert.= " ,{^2008/01/01}";
	$sql_insert.= " ,'10'";
	$sql_insert.= " )";
	
	odbc_exec($cnx,$sql_insert);
	echo "executeflag=$executeflag";
	*/
	// zkusim vlozit data
	
	//$stmt = odbc_prepare($cnx,"insert into 01kmen08 (pracis,rodcis,jmeno,prijmeni,ulice) values(?,?,?,?,?)");
	//$executeflag = odbc_execute($stmt,array(1,"654","jmeno","prijmeni","ulice"));
	 
	//echo "insert executeflag=$executeflag";
	
	// vypsat vsechny tabulky ve zdroji
	
	
	echo "<h2>tabulky ve zdroji</h2>";
	$resource = odbc_tables($cnx);
	if($resource)
	{
		$nfields = odbc_num_fields($resource);
		echo "nfields = $nfields<br>";

		//	$nfields = 5;

		echo "<table border='1' cellspacing='0' cellpadding='0'>";
		echo "<tr>";
		for($i=1;$i<=$nfields;$i++)
		{
			echo "<th>".odbc_field_name($resource,$i)."</th>";
		}
		echo "</tr>";
		while(odbc_fetch_row($resource))
		{
			echo "<tr>";
			for($j=1;$j<$nfields;$j++)
			{
				$value = trim(odbc_result($resource,$j));
				echo "<td nowrap>".$value."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	
	
	
	
	// vypsat vsechna jmena poli
	//echo "<h2>seznam sloupcu v tabulce 01kmen09</h2>";
	$cursor = odbc_exec($cnx,"select * from 01kmen11");
	if(!$cursor)
	{
		echo "chyba pri odbc_exec";
		odbc_close($cnx);
		exit();
	}
	
	$nfields = odbc_num_fields($cursor);
	
	/*
	echo "nfields = $nfields<br>";
	
	for($i=1;$i<=$nfields;$i++)
	{
		echo "jmenosloupce :<strong>".odbc_field_name($cursor,$i)."</strong>, typsloupce:".odbc_field_type($cursor,$i)."<br>";
	}
	*/
	
	//----------------------------------------------------------------------------------------------------------
	
	
	
	// test na vytazeni nejakych dat z tanulky
	echo "<h2>test nacteni vybranych dat z tabulky 01kmen11</h2>";
	//$cursor = odbc_exec($cnx,"select pracis,rodcis,jmeno,prijmeni,dnaroz,pohlavi,narod,stav,vzdelani,nastup,vystup,ulice,obec,posta,psc,telef from 01kmen08 order by pracis");
	//$cursor = odbc_exec($cnx,"select * from 01kmen11 where (pracis=95 or pracis=549 or pracis=2193 or pracis=2620) order by pracis");
        $cursor = odbc_exec($cnx,"select * from 01kmen11 where pracis=2620 or pracis=99020 order by pracis");
        //$cursor = odbc_exec($cnx,"select * from 01kmen11 order by pracis");
	if(!$cursor)
	{
		echo "chyba pri odbc_exec";
		odbc_close($cnx);
		exit();
	}
	
	$nfields = odbc_num_fields($cursor);
	echo "nfields = $nfields<br>";
	
	//$nfields = 5;
	
	

	while(odbc_fetch_row($cursor))
	{
		echo "<table border='1' cellspacing='0' cellpadding='0'>";	
		for($j=1;$j<=$nfields;$j++)
		{
			echo "<tr>";
			echo "<td align='left' bgcolor='lightblue'>".odbc_field_name($cursor,$j)."</td>";
			$value = odbc_result($cursor,$j);
			if(strlen(trim($value))>0)
				echo "<td>".$value."</td>";
			else
				echo "<td>&nbsp;</td>";
				
			echo "</tr>";	
		}
		echo "</table>";
		echo "<br/><br/><br/>";
	}
	
	
	
	
	
	// zkusim update zaznamu zmenu v ulici
	
	
	odbc_close($cnx);
	
	echo "konec";
?>
</html>