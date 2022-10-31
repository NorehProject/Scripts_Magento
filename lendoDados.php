<?php

date_default_timezone_set('America/Sao_Paulo');
$mageFilename = '../../app/Mage.php';
require_once($mageFilename);
ini_set('display_errors', 1);
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$arquivo = fopen('data-Catalog_Category.txt', 'r');

$arrayTexto = fread($arquivo, filesize('data-Catalog_Category.txt'));

$arrayTexto = $arrayTexto = str_replace('^^', "\n", $arrayTexto);

$arrayFeio = explode('##', $arrayTexto);

foreach ($arrayFeio as $arrumando) {

    $arrayArrumado = explode('@@', $arrumando);

    $arrayFinal[] = $arrayArrumado;
}
fclose($arquivo);

/**
 * [0] estado
 * [1] cidade
 * [2] meta_title
 * [3] meta_description
 * [4] latitude
 * [5] longitude
 * [6] cat_h1
 * [7] cat_maps_title
 * [8] custom_tags_title
 * [9] custom_tags
 * [10] iframe_mapa
 */

$i = 0;

foreach ($arrayFinal as $data) {

    $catalogCollection = Mage::getModel('catalog/category')->getCollection()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('isnewcategory', 1)
        ->addAttributeToFilter('is_active', 1)
        ->addAttributeToFilter('estado', $data[0])
        ->addAttributeToFilter('cidade', $data[1])
        ->getFirstItem();

    $category = $catalogCollection->load($catalogCollection->getId());

    $category
        ->setMetaTitle($data[2])
        ->setMetaDescription($data[3])
        ->setLatitude($data[4])
        ->setLongitude($data[5])
        ->setCatH1($data[6])
        ->setCatMapsTitle($data[7])
        ->setCustomTagsTitle($data[8])
        ->setCustomTags($data[9])
        ->setIframeMapa($data[10]);

    $save[] = $category;

    echo "Terminou a categoria: " . $category->getName() . " || id = " . $category->getId() . " || {$i} de " . count($arrayFinal) . "\n";
    $i++;

}
echo "\n *** SALVANDO *** \n\n";

$i = 1;

foreach ($save as $salvo) {
    $salvo->save();
    echo "{$i} de " . count($save) . "\n";
    $i++;
}
