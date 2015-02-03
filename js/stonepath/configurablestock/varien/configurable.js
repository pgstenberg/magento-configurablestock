

/*
* Getting the attribute-id by using reg-exp in the specified element
*/
function getAttributeIdFromElement(element){
	return element.id.replace(/[a-z]*/, '');
}

/*
* Get the index of the an option with specific value
*/
function getOptionIndexFromValue(options,value){
	for(var i=0;i<options.length;i++){
        if(value == options[i].value)
        	return i;
    }
    return -1;
}


//TO-DO: fix disable after first select

/*
* Calling the parent method and making some own modifications on the element
*/
function relabelOption(parentMethod, element) {
	parentMethod(element);
    
	//GET ATTRIBUTE_ID FROM ELEMENT
	var attribute_id = getAttributeIdFromElement(element);
	
	//GET ALL OPTIONS
    var options = this.getAttributeOptions(attribute_id);	
	
	//LOOP OPTIONS
	for(var i=0;i<options.length;i++){	
		var option_id = options[i].id;
		
		//CHECKING IF ATTRIBUTE IS NOT BASE
		if(!this.config.stonepath_confstock[attribute_id][option_id].base){
			var option_index = getOptionIndexFromValue(element.options,option_id);	
			
			
			//CHECK IF ACTIVE SELECT
			if(option_index != -1){
				var option_disable = this.config.stonepath_confstock[attribute_id][option_id]['disable'];
				var option_label = this.config.stonepath_confstock[attribute_id][option_id]['label'];
				
				if(option_disable)
					element.options[option_index].disabled = true;
				
				element.options[option_index].text = this.getOptionLabel(element.options[option_index].config, element.options[option_index].config.price) + " (" + option_label +")";
			}
		}
	}
	


}


//Wrapped for Magento ver 1.9
Product.Config.prototype.reloadOptionLabels  = Product.Config.prototype.reloadOptionLabels.wrap(relabelOption);


//Wrapped for Magento < ver 1.8
Product.Config.prototype.fillSelect  = Product.Config.prototype.fillSelect.wrap(relabelOption);
