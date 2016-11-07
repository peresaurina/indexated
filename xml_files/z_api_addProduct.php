<?php

define('DEBUG', true);
define('_PS_DEBUG_SQL_', true);

//define('PS_SHOP_PATH', 'http://localhost/prestashop');
//define('PS_WS_AUTH_KEY', 'your-key-here');



require_once('PSWebServiceLibrary.php');
$webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);


$xml_product = $webService->get(array('resource' => 'products?schema=blank'));
$resources_product = $xml_product->children()->children();


$resources_product->id_manufacturer = '1';
$resources_product->id_supplier = '1';
$resources_product->id_category_default = '3';
//$resources_product->new	='1';
$resources_product->cache_default_attribute;
$resources_product->id_default_image;
$resources_product->id_default_combination;
$resources_product->id_tax_rules_group;
//$resources_product->position_in_category;	
$resources_product->manufacturer_name;
//$resources_product->quantity="3";	
$resources_product->type;
$resources_product->id_shop_default;
$resources_product->reference = 'SKUID45';
$resources_product->supplier_reference;
$resources_product->location;
$resources_product->width;
$resources_product->height;
$resources_product->depth;
$resources_product->weight;
$resources_product->quantity_discount;
$resources_product->ean13;
$resources_product->upc;
$resources_product->cache_is_pack;
$resources_product->cache_has_attachments;
$resources_product->is_virtual;
$resources_product->on_sale;
$resources_product->online_only;
$resources_product->ecotax;
$resources_product->minimal_quantity;
$resources_product->price = '20.00';
$resources_product->wholesale_price;
$resources_product->unity;
$resources_product->unit_price_ratio;
$resources_product->additional_shipping_cost;
$resources_product->customizable;
$resources_product->text_fields;
$resources_product->uploadable_files;
$resources_product->active = '1';
$resources_product->redirect_type;
$resources_product->id_product_redirected;
$resources_product->available_for_order;
$resources_product->available_date;
$resources_product->condition = 'new';
$resources_product->show_price;
$resources_product->indexed = '1';
$resources_product->visibility = 'both';
$resources_product->advanced_stock_management;
$resources_product->date_add;
$resources_product->date_upd;
$resources_product->meta_description->language = 'Product Meta Description';
$resources_product->meta_keywords->language = 'Product Meta keywords';
$resources_product->meta_title->language = 'Product Meta Title';
$resources_product->link_rewrite->language = 'product-url-key';
$resources_product->name->language = 'Product Name';
$resources_product->description->language = 'Product Description';
$resources_product->description_short->language = 'Product Short Description ';
$resources_product->available_now->language = '30/10/14';
$resources_product->available_later->language;
$resources_product->associations->categories->addChild('category')->addChild('id', 3);


$xml_product = $webService->add(array('resource' => 'products', 'postXml' => $xml_product->asXML()));
?>

