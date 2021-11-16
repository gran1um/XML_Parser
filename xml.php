#!/usr/bin/php
<?php
$xml_file = "tss.xml";

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

        $fp=fopen("./cat.txt","a");   
        fwrite($fp, "\r\n" . $sql);  
        fclose($fp);


    }

    foreach ($xml->xpath("//offers/offer") as $segment) {
        $row = $segment->attributes();
        $str=str_replace('https://tss.ru/catalog/','',$segment->url);
        $str= substr($str, 0, -1);
        $description="";
        $specifications="";

        foreach($segment->param as $key=>$param){

                if ($param["name"]=='Детальное описание товара'){
                    $description=$param;
                }
                
                if ($param['name']!='Артикул' & $param['name']!='Картинки'& $param['name']!='Детальное описание товара'& $param['name']!='Описание товара'){
                    $specifications.="<tr><td>".$param['name']."</td><td>".$param."</td></tr>";
                }
                
            }

        
        $specifications='<tbody><tr><td colspan="«2»" class="«table-part»"> <b> Заводские данные </b> </td></tr>'.$specifications.'</tbody>';

        $sql = "delete from goods WHERE (`id` = ".$row["id"]."); insert into goods (id, URL, available, cost,cost2, category, image , name, description, `ext-description`,spec) values(".$row["id"].", '".$str."', ".$row["available"].", '".$segment->price." ₽"."', '".$segment->price."', ".$segment->categoryId.", '".$segment->picture."', '".$segment->name."', '".$segment->description."', '".$description."', '".$specifications."');";

        $path="";
        foreach($segment->picture as $key=>$picture){
            $path.=$picture."|";
            
        }
        $path= substr($path, 0, -1);
        
        $sql1 = "delete from images WHERE (`goods_URL` = '".$str."'); insert into images (goods_URL, path) values('".$str."', '".$path."');";
        $result=$sql.$sql1;

        $fp=fopen("./goods.txt","a");  
        fwrite($fp, "\r\n" . $sql); 
        fwrite($fp, "\r\n" . $sql1);  
        fclose($fp);
    
}
} else {
    exit('Не удалось открыть файл '.$xml_file);
}

?>