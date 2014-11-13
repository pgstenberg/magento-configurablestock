<?php

class Stonepath_ConfigurableStock_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
	
	protected function _prepareLayout()
    {
       	$this->getLayout()->getBlock('head')->addJs('stonepath/configurablestock/varien/configurable.js');
        return parent::_prepareLayout();
    }

    protected function _getAdditionalConfig()
    {
    	$childProducts = $this->getProduct()->getTypeInstance(true)->getUsedProducts(null, $this->getProduct());
    	
     	foreach ($childProducts as $product) {
     		$productId  = $product->getId();
     		$productStockitem = $product->getStockItem();
     		$productQty = $productStockitem->getQty();
     		$stock_threshold = Mage::getStoreConfig('cataloginventory/options/stock_threshold_qty'); 
     		$inStock = $productStockitem->getIsInStock();
     		$highest_position = $this->getHighestAttributePosition();
     	
    	foreach ($this->getAllowAttributes() as $attribute) {
    			$productAttribute = $attribute->getProductAttribute();
            	$productAttributeId = $productAttribute->getId();
            	$attributeValue = $product->getData($productAttribute->getAttributeCode());
            	
            		
    			if($attribute->getPosition() == $highest_position){
    				$return_array['stocktest'][$productAttributeId][$attributeValue]['disable'] = !$inStock;
    				
    				if($inStock){
    					
    					if($productQty > $stock_threshold){
    						$return_array['stocktest'][$productAttributeId][$attributeValue]['label'] = $this->__('In stock: %s',intval($productQty));
    					}else{
    						$return_array['stocktest'][$productAttributeId][$attributeValue]['label'] = $this->__('Only %s left',intval($productQty));
    					}
    						
    					
    				}else{
    					$return_array['stocktest'][$productAttributeId][$attributeValue]['label'] = $this->__('Out of stock');
    				}
    				
    				$return_array['stocktest'][$productAttributeId][$attributeValue]['base'] = false;
    			}else{
    				$return_array['stocktest'][$productAttributeId][$attributeValue]['base'] = true;
    			}
    		}
    	}
    
    	
        return $return_array;
    }
    
    private function getHighestAttributePosition(){
    	$lowest_position = 0;
    	foreach ($this->getAllowAttributes() as $attribute) {
    		$attribute_position = $attribute->getPosition();
    		if($attribute_position > $lowest_position)
    			$lowest_position = $attribute_position;
    	}
    	
    	return $lowest_position;
    }
    
	public function getAllowProducts()
    {
    	$pre_value = Mage::helper('catalog/product')->getSkipSaleableCheck();
    	
    	Mage::helper('catalog/product')->setSkipSaleableCheck(true);
    	$products = parent::getAllowProducts();
    	Mage::helper('catalog/product')->setSkipSaleableCheck($pre_value);
    	
    	foreach($products as $productKey => $product) {
    		if($product->getStatus() == 2){
    			unset($products[$productKey]);
    		}
    	}
    	
    	return $products;
    }
	
}


?>