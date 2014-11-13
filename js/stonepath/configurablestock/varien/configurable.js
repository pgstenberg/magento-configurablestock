
function getAttributeIdFromElement(element){
	return element.id.replace(/[a-z]*/, '');
}
function getOptionIndexFromValue(options,value){
	for(var i=0;i<options.length;i++){
        if(value == options[i].value)
        	return i;
    }
    return -1;
}

Product.Config.prototype.fillSelect  = Product.Config.prototype.fillSelect.wrap(function(parentMethod, element) {
	parentMethod(element);
	
	//GET ATTRIBUTE_ID FROM ELEMENT
	var attribute_id = getAttributeIdFromElement(element);
	//GET ALL OPTIONS
    var options = this.getAttributeOptions(attribute_id);	
	
	//LOOP OPTIONS
	for(var i=0;i<options.length;i++){	
		var option_id = options[i].id;
		
		//CHECKING IF ATTRIBUTE IS NOT BASE
		if(!this.config.stocktest[attribute_id][option_id].base){
			var option_index = getOptionIndexFromValue(element.options,option_id);	
			
			//CHECK IF ACTIVE SELECT
			if(option_index != -1){
				var option_disable = this.config.stocktest[attribute_id][option_id]['disable'];
				var option_label = this.config.stocktest[attribute_id][option_id]['label'];
				
				if(option_disable)
					element.options[option_index].disabled = true;
				
				element.options[option_index].label = element.options[option_index].label + " (" + option_label +")";
				element.options[option_index].text = element.options[option_index].text + " (" + option_label +")";
			}
		}
	}
	
	
});
