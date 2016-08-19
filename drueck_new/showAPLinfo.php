<?php

session_start();
require "../fns_dotazy.php";
dbConnect();
 $data = file_get_contents("php://input");
 $o = json_decode($data);

/////////////////////////////////////////////////////////////////////////////
mysql_query("SET NAMES UTF8");
$sql="select `abg-nr` as abgnr,dtaetkz,name,oper_cz,oper_d from `dtaetkz-abg` order by `abg-nr`";

$result = mysql_query($sql);

                $outputAbgnr = array();
		$outputDtae =array();
                $outputName =array();
                $outputOperCZ=array();
                $outputOperD =array();

                
        $data = array();
        if(mysql_affected_rows()>0){
        while($row=mysql_fetch_array($result)){
            $data[] = array(
                "abgnr" =>   $row['abgnr'],
                "dtaetkz"  =>   $row['dtaetkz'],
                "name"  =>   $row['name'],
                "oper_cz" =>   $row['oper_cz'],
                "oper_d"  =>   $row['oper_d'],
             );
        }
           //print_r($row);
            print_r(json_encode($data));
            return json_encode($data);
         }       
                
                
             