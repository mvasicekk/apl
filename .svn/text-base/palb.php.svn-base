<?php



function getPicasaAlbumsArray($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    $albumsArray = array();

    $feed = simplexml_load_string($output);
    if($feed!==FALSE) {
        $alba = $feed->entry;
        foreach($alba as $cislo=>$album) {
            //vytazeni namespaces podle http://www.sitepoint.com/blogs/2005/10/20/simplexml-and-namespaces/
            $ns_gphoto = $album->children('http://schemas.google.com/photos/2007');
            $ns_media = $album->children('http://search.yahoo.com/mrss/');
            $attributes = $ns_media->group->thumbnail->attributes();
            $thumbnail_url = $attributes['url'];

            array_push($albumsArray, array(
                    'id'=>$ns_gphoto->id,
                    'numphotos'=>$ns_gphoto->numphotos,
                    'title'=>$ns_media->group->title,
                    'description'=>$ns_media->group->description,
                    'thumbnail'=>$thumbnail_url,
                    )
                    
            );
        }
        return $albumsArray;
    }
    else {
        return FALSE;
    }
}

//------------------------------------------------------------------------------------------------------------------
$albums = getPicasaAlbumsArray('http://picasaweb.google.com/data/feed/api/user/babycentrum.slunecnice');
$maxColumns = 5;

if($albums!==FALSE) {
    echo "<table>";
    $column = 0;
    echo "<tr>";
    foreach ($albums as $album) {
        foreach ($album as $parametr=>$obsah) {
            if(strstr($parametr,'thumbnail')){
                echo "<td id='a".$column."'>";
                echo "<img src='".$obsah."'>";
                echo "</td>";
            }
        }
        $column++;
        if($column%$maxColumns==0){
                echo "</tr><tr>";
        }
    }
    if($column%$maxColumns!=0)
    echo "</tr>";
    echo "</table>";
}
else {
    echo "nemohu ziskat informace o albech";
}