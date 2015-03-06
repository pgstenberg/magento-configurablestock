<?php

/**
 * Custom ConfigurableProductBlock that extends the core ConfigurableProductBlock
 * 
 * This class prepares and sets the additionalconfig data that are utilized in the javascripts.
 * 
 *
 * @author Per-Gustaf Stenberg <per-gustaf.stenberg@stonepath.se>
 * @version 0.1.0
 */

class Stonepath_ConfigurableStock_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
	
	public static $collectionName = 'stonepath_confstock';
	
	/**
	* Adding the additional javascript that are needed for updating the dropdownbox.
	*/
	protected function _prepareLayout()
    {
       	$this->getLayout()->getBlock('head')->addJs('stonepath/configurablestock/varien/configurable.js');
        return parent::_prepareLayout();
    }

	/**
	* Override the getAdditionalConfig method to add the extra configuration data needed for the dropdownbox.
	*/
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
    			
    				//Disable option if out of stock
    				$return_array[self::$collectionName][$productAttributeId][$attributeValue]['disable'] = !$inStock;
    				
    				//Checks whether product is in stock
    				if($inStock){
    					
    					//Checking the outofstock threshold
    					if($productQty > $stock_threshold){
    						$return_array[self::$collectionName][$productAttributeId][$attributeValue]['label'] = $this->__('In stock: %s',intval($productQty));
    					}else{
    						$return_array[self::$collectionName][$productAttributeId][$attributeValue]['label'] = $this->__('Only %s left',intval($productQty));
    					}
    						
    					
    				}else{
    					$return_array[self::$collectionName][$productAttributeId][$attributeValue]['label'] = $this->__('Out of stock');
    				}
    				
    				$return_array[self::$collectionName][$productAttributeId][$attributeValue]['base'] = false;
    			}else{
    				$return_array[self::$collectionName][$productAttributeId][$attributeValue]['base'] = true;
    			}
    		}
    	}
    
    	
        return $return_array;
    }
    
    /**
    * Returns the highest attribute position
    */
    private function getHighestAttributePosition(){
    	$lowest_position = 0;
    	foreach ($this->getAllowAttributes() as $attribute) {
    		$attribute_position = $attribute->getPosition();
    		if($attribute_position > $lowest_position)
    			$lowest_position = $attribute_position;
    	}
    	
    	return $lowest_position;
    }
    
    /*
    * Returns all products that are saleable or 'allowed' to be shown
    */
	public function getAllowProducts()
    {
    	
    	$pre_value = Mage::helper('catalog/product')->getSkipSaleableCheck();
    	
    	Mage::helper('catalog/product')->setSkipSaleableCheck(true);
    	$products = parent::getAllowProducts();
    	Mage::helper('catalog/product')->setSkipSaleableCheck($pre_value);
    	
    	$filtered_products = array();
    	
    	foreach($products as $productKey => $product) {
    		//$statusModel = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('status')->addAttributeToFilter('entity_id',$product->getID())->getFirstItem();
			$statusModel = Mage::getModel('catalog/product')->load($product->getID());
    		if($statusModel->getStatus() != 2){
    			$filtered_products[$productKey] = $product;
    		}
    	}
    	
    	
    	return $filtered_products;
    }
	
}


?>