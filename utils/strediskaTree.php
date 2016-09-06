 <meta charset="UTF-8"> 
<?php

require_once '../db.php';

$a = AplDB::getInstance();

function categoryParentChildTree($parent = 0, $level = 0, $spacing = '', $category_tree_array = '') {
    global $a;
    //$parent = $dbConnection->real_escape_string($parent);
    if (!is_array($category_tree_array))
	$category_tree_array = array();

    $sqlCategory = "SELECT stredisko as id,text1,text2,str_nazev,CONCAT(stredisko,' ',str_nazev) as name,str_parent as parent_id FROM strediska_isp WHERE str_parent = '$parent' ORDER BY stredisko ASC";
    $resCategory = $a->getQueryRows($sqlCategory);

    if ($resCategory !== NULL) {
	foreach ($resCategory as $rowCategories) {
	    $category_tree_array[] = array("id" => $rowCategories['id'], "level" => $level, "name" => $spacing . $rowCategories['name'], "node" => $rowCategories);
	    $category_tree_array = categoryParentChildTree($rowCategories['id'], $level+1, '&nbsp;&nbsp;&nbsp;&nbsp;' . $spacing . '-&nbsp;', $category_tree_array);
	}
    }
    return $category_tree_array;
}

$categoryList = categoryParentChildTree();

//echo "<hr>";
//AplDB::varDump($categoryList);

echo "<table border='1'>";
foreach ($categoryList as $key => $value) {
    echo "<tr>";
    $level = $value['level'];
    for($i=0;$i<$level;$i++){
	echo "<td>&nbsp;</td>";
    }
    echo "<td style='white-space:nowrap;'>". $value['node']['name']. '&nbsp;' . $value['node']['text1']. '&nbsp;' . $value['node']['text2'] . '</td>';
    echo "</tr>";
}
echo "</table>";

//d FROM tbl_categories WHERE parent_id = $parent ORDER BY id ASC";
//    $resCategory=$dbConnection->query($sqlCategory);
//	
//    if ($resCategory->num_rows > 0) {
//        while($rowCategories = $resCategory->