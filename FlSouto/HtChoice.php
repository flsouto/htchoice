<?php

namespace FlSouto;

abstract class HtChoice extends HtWidget{

	protected $options = [];

	function options($options){
		if(is_array($options)){
			$this->options = [];
			$current = current($options);
			if(is_array($current)||is_object($current)){
				// dataset
				foreach($options as $row){
					$values = array_values((array)$row);
					if(count($values)<2){
						throw new InvalidArgumentException("each option must have at least two fields");
					}
					$this->options[$values[0]] = $values[1];
				}
			} else {
				$key = key($options);
				if(is_string($key)||$key>0){
					// assoc array
					$this->options = $options;
				} else {
					// numeric array
					foreach($options as $value){
						$this->options[$value] = $value;
					}
				}
			}
		} else if(is_callable($options)) {
			$this->options = $options;
		} else {
			throw new InvalidArgumentException("options must be an array or a callable");
		}
		return $this;
	}

	protected function resolveOptions(){
		while(is_callable($this->options)){
			$this->options(call_user_func($this->options));
		}		
	}

	protected function compareOptionsValues($option_value, $input_value){
		if(!is_null($input_value)){
			if("$input_value"==="0" && "$option_value"==="0"){
				return true;
			} else if($input_value==$option_value) {
				return true;
			}
		}
		return false;
	}

}