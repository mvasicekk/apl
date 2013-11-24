<?php
include '../fns_dotazy.php';
include './xml_generator.class.php';

dbConnect();

$xml = new c_xml_generator;
$top = $xml->add_node(0, 'table', array('name' => 'Dpers'));

$sql = 'select * from `Dpers` limit 50';
$res = mysql_query($sql) or die(mysql_error());



while($row = mysql_fetch_assoc($res)) {
  $xrow = $xml->add_node($top, 'row');
  foreach($row as $field => $value) {
    $xfield = $xml->add_node($xrow, $field);
    $xml->add_cdata($xfield, $value);
  };
};

mysql_close();

echo $xml->create_xml();
?>
