<?php

class Masterdef_ProductsManager_Block_Adminhtml_Products 
    extends Mage_Core_Block_Template
{
    /**
     * Search products
     **/
    public function getProducts()
    {
        $search = $this->getRequest()->getParam('search');
        $searchSKU = $this->getRequest()->getParam('search_sku');

        if ($search) {
            // search by product name or SKU
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'name', 'like' => "%{$search}%"),
                        array('attribute' => 'sku', 'like' => "%{$search}%"),
                        )
                    )
                ->load();
        } else {
            // no search parameters found
            return false;
        }

        // process search results
        $ret = array();
        $sindex_name = array();
        $sindex_price = array();
        $sindex_stock = array();
        foreach ($products as $product) {
            $_id = $product->getId();
            //$product = Mage::getModel('catalog/product')->load($_id);
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

            $qty = $stock->getQty() * $stock->getIsInStock();
            $price = $product->getPrice();
            $ret[$_id]['name'] = $product->getName();
            $ret[$_id]['price'] = Mage::helper('core')->currency($price);
            $ret[$_id]['stock'] = round($qty);
            $ret[$_id]['sku'] = $product->getSku();
        }

        return array('products' => $ret);
    }
}

