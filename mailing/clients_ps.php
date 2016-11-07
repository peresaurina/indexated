<?php

include ('../lib/funcions_generals.php');
include ('../protected/config/main.php');
include ('../protected/models/Mail_Segment.php');
include ('../protected/models/Categoria.php');
include('../lib/functions.php');
// localhost/panell/mailing/clients_ps.php
#ejemplode.com
$sql = "select ps_customer.firstname,ps_customer.id_customer,ps_customer.email,product_id,ps_product_lang.name,Almacen.clvaut,Almacen.texto from ps_orders
            inner join ps_order_detail on ps_order_detail.id_order = ps_orders.id_order
            inner join ps_product_lang on ps_product_lang.id_product = ps_order_detail.product_id
            inner join ps_customer on ps_customer.id_customer = ps_orders.id_customer            
            left join Almacen on Almacen.codigo + 1000000 = ps_product_lang.id_product
            WHERE Almacen.clvaut is not null";

$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {
    echo $row["clvaut"] . " ";
    $categoria_peça_marca_model = Categoria::marca_model_de_clvaut($row["clvaut"]);
    echo $categoria_peça_marca_model . "<br><br>";
    
    $fields["name"] = $row["firstname"];;
    $fields["phone"] = $row["email"];;
    $fields["email"] = $row["email"];
    //$fields["product_id"] = $row["product_id"];
    $fields["marca_model"] = $categoria_peça_marca_model;
    $fields["comment"] = "Peça demanada:".$row["texto"];

    $client_mail_model = new Mail_Segment(null, $fields);
    $client_mail_model->insertIntoDataBase();
}
?>


