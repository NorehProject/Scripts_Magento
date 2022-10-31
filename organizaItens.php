<?php

date_default_timezone_set('America/Sao_Paulo');
$mageFilename = '../app/Mage.php';
require_once($mageFilename);
ini_set('display_errors', 1);
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$antes = date('Y-m-d H:i:s', strtotime('-6 month'));

$catalogCollection = Mage::getModel('catalog/category')->getCollection()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('isnewcategory', 0);

$positionAll = [];

$i = 0;

foreach ($catalogCollection as $category) {

    if (
        $category->getId() != 2406 and
        $category->getId() != 2407 and
        $category->getId() != 2408 and
        $category->getId() != 1 and
        preg_match("/2929/", $category->getPath()) == false
    ) {

        $categoriaCarregada = Mage::getModel('catalog/category')->load($category->getId());

        $storeId = $categoriaCarregada->getStore()->getId();

        $productCollection = $categoriaCarregada->getProductCollection()
            ->addAttributeToSelect('*')
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('visibility', 4);


        $productCollection->getSelect()
            ->joinInner(
                'sales_flat_order_item AS order_item',
                "e.entity_id = order_item.product_id and order_item.created_at > '$antes' and order_item.qty_ordered > 0",
                'SUM(order_item.qty_ordered) AS ordered_qty'
            )
            ->group('e.entity_id')
            ->order('ordered_qty DESC');

        $cont = 5;
        $idAlterado = [];

        $positions = $categoriaCarregada->getProductsPosition();
        $keys = array_keys($positions);

        /* RESETA TUDO */
        foreach ($keys as $itemPosition) {

            if ($positions[$itemPosition] != "9999") {

                $positions[$itemPosition] = "9999";
            }
        }

        /* ORGANIZA OS MAIS VENDIDOS */
        foreach ($productCollection as $product) {

            $positions[$product->getId()] = strval($cont++);
        }

        $positionAll[] = $categoriaCarregada->setPostedProducts($positions);

        echo "Terminou a categoria: " . $category->getName() . " || id = " . $category->getId() . " || {$i} de " . count($catalogCollection) . "\n";
        $i++;
    }
}

$i = 0;
echo "setando. \n";
foreach ($positionAll as $setando) {
    $setando->save();
    echo "Setado {$i} de " . count($positionAll) . "\n";
    $i++;
}


$tempo = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
printf("\nProcessado em: %0.2f segundos\n", $tempo);
