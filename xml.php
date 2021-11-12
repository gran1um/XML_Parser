#!/usr/bin/php
<?php
$xml_file = "tss1.xml";

if (file_exists($xml_file)) {
    $xml = simplexml_load_file($xml_file);
    $i=0;

    $fp=fopen("./cat.txt","w");  
        fwrite($fp, "");  
        fclose($fp);
    $fp=fopen("./goods.txt","w");  
        fwrite($fp, "");  
        fclose($fp);
    foreach ($xml->xpath("//categories/category") as $segment) {
        $row = $segment->attributes();
        if ($row["parentId"]==null){
            $str="0";
        }
        else {
            $str=$row["parentId"];
        };

        $sql = "delete from category WHERE (`id` = ".$row["id"]."); insert into category (id,  category, parent_id) values(".$row["id"].", '".$segment."', '".$str."');";
        echo($sql);
        $fp=fopen("./cat.txt","a");   
        fwrite($fp, "\r\n" . $sql);  
        fclose($fp);


    }

    foreach ($xml->xpath("//offers/offer") as $segment) {
        $row = $segment->attributes();
        $str=str_replace('https://tss.ru/catalog/','',$segment->url);
        $sql = "delete from goods WHERE (`id` = ".$row["id"]."); insert into goods (id, URL, available, cost,cost2, category, image , name, ext-description) values(".$row["id"].", '".$str."', ".$row["available"].", '".$segment->price." ₽"."', '".$segment->price."', ".$segment->categoryId.", '".$segment->picture."', '".$segment->name."', '".$segment->description."');";

        $path="";
        foreach($segment->picture as $key=>$picture){
            $path+=$picture.":";
            
        }
        $path= substr($path, 0, -1);
        $sql1 = "delete from images WHERE (`URL` = ".$str."); insert into images (URL, path) values(".$str."', ".$path." ');";

        echo($sql."\r\n");
        echo($sql1."\r\n");
        $fp=fopen("./goods.txt","a");  
        fwrite($fp, "\r\n" . $sql);  
        fclose($fp);
    
}
} else {
    exit('Не удалось открыть файл '.$xml_file);
}

?>