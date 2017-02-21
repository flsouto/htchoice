<?php

#mdx:SimpleChoiceClass

use FlSouto\HtChoice;

class SimpleChoice extends HtChoice{

	// Question to be asked on 'writable' mode
	protected $simpleChoiceQuestion = '';

	function __construct($name, $question='Type in the desired option:'){
		parent::__construct($name);
		$this->simpleChoiceQuestion = $question;
	}

	// Generates the options for our custom widget
	private function renderSimpleChoiceOptions(){
		// always call resolveOptions before accessing $this->options
		$this->resolveOptions();
		$input = $this->value();
		foreach($this->options as $value => $label){
			if($value === $label){
				// if value and label are equal, simplify option output
				$line = "- $value";
			} else {
				$line = "$value - $label";
			}
			if($this->compareOptionsValues($value,$input)){
				// make selected option bold
				$line = "<b>$line (selected)</b>";
			}
			echo $line;
			echo "<br/>\n";
		}
	}

	// Show options + input dialog
	function renderWritable(){
		$attrs = $this->attrs;
		$attrs['value'] = $this->value();
		$attrs['size'] = 10;
		echo "$this->simpleChoiceQuestion \n";
		echo "<input $attrs /> <br/> \n";
		$this->renderSimpleChoiceOptions();
	}

	// show only list of options, without input dialog
	function renderReadonly(){
		$this->renderSimpleChoiceOptions();
	}

}

#/mdx