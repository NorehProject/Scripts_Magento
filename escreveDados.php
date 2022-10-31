<?php

date_default_timezone_set('America/Sao_Paulo');
$mageFilename = '../../app/Mage.php';
require_once($mageFilename);
ini_set('display_errors', 1);
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$catalogCollection = Mage::getModel('catalog/category')->getCollection()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('isnewcategory', 1)
    ->addAttributeToFilter('is_active', 1);

$arquivo = fopen('data-Catalog_Category.txt', 'w+');

foreach ($catalogCollection as $category) {

    /* ESCREVENDO NO CSV */
    if ($arquivo == false) {
        die('Não foi possivel criar o arquivo.');
    }

    

    $texto = "{$category['estado']}@@{$category['cidade']}@@{$category['meta_title']}@@{$category['meta_description']}@@{$category['latitude']}@@{$category['longitude']}@@{$category['cat_h1']}@@{$category['cat_maps_title']}@@{$category['custom_tags_title']}@@{$category['custom_tags']}@@{$category['iframe_mapa']}##";
    
    $texto = preg_replace( "/\n/", "^^", $texto );
    $texto = preg_replace( "/\r/", "", $texto );
    
    fwrite($arquivo, $texto);
}

fclose($arquivo);
