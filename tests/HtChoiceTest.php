<?php

#mdx:h al
require_once('vendor/autoload.php');

#mdx:h SimpleChoice hidden
require_once('tests/SimpleChoice.php');

/* 
# HtChoice
This library can be extended to easily implement widgets that are based on choice.
It is not tied to any specific html element and does not define if the user is supposed
to choose only one option or multiple options. This is up to your implementation.

**Notice**: a lot of functionality is already inherited from base abstract classes. So
I highly recommend reading these other materials before you start developing with this library:

- [HtField]()
- [HtWidget]()

## Installation

Use composer:

```
composer require flsouto/htchoice
```

## Usage

For demonstrating how one would extend this abstract class to implement his own choice type of widget, I
have prepared a `SimpleChoice` class which provides a text-based list of options and an input field where
the user is supposed to type in the value of the desired option. Of course in the real world this would be
useless since better control elements such as select, radio buttons and checkboxes already exist for that. 
However this simplified version will be good for showing how custom choice elements can
be implemented if you don't like sticking to the standard ones. So here is the implementation of our `SimpleChoice` class:

#mdx:SimpleChoiceClass -h:al

*/
class HtChoiceTest extends \PHPUnit_Framework_TestCase{


/*

### Setting the array of options
In order to feed the choice widget with options for the user to choose we must use the `options` method which accepts an associative array:

#mdx:Render

Outputs:

#mdx:Render -o httidy
*/
	function testRender(){
		#mdx:Render
		$choice = new SimpleChoice('color');
		$choice->options([
			1 => 'Black',
			2 => 'White',
			3 => 'Gray'
		]);
		#/mdx echo $choice
		$this->assertContains("input","$choice");
		$this->assertContains("1 - Black","$choice");
		$this->assertContains("3 - Gray","$choice");
	}

/*
### Selecting an option

In the next example we want the third option (i.e. the "Fall" season) to be marked as selected:

#mdx:Selected

Outputs:

#mdx:Selected -o httidy
*/
	function testSelected(){
		#mdx:Selected
		$choice = new SimpleChoice('season');
		$choice->options([
			1 => 'Spring',
			2 => 'Summer',
			3 => 'Fall',
			4 => 'Winter'
		]);
		$choice->context(['season'=>3]); // selects season 3 (Fall)
		#/mdx echo $choice
		$this->assertContains("<b>3 - Fall","$choice");
	}

/*
### Readonly mode

We can use the inherited `readonly` setter to tell the widget we don't want users interacting with it:

#mdx:Readonly

Outputs:

#mdx:Readonly -o httidy
*/
	function testReadonly(){
		#mdx:Readonly
		$choice = new SimpleChoice('season');
		$choice->options([
			1 => 'Spring',
			2 => 'Summer',
			3 => 'Fall',
			4 => 'Winter'
		]);
		$choice->context(['season'=>3])
			->readonly(true);
		#/mdx echo $choice
		$this->assertContains("1 - Spring", "$choice");
		$this->assertNotContains("input", "$choice");
	}

/*
### Options as Numeric Arrays

Besides associative arrays, the `options` method accepts numeric arrays. In this case
each option's value will be the same as its own label:

#mdx:OptionsArray

Outputs:

#mdx:OptionsArray -o httidy
*/
	function testOptionsArray(){
		#mdx:OptionsArray
		$choice = new SimpleChoice("language","Type in the desired language code:");
		$choice->options(['en','es','pt','fr']);
		#/mdx echo $choice
		$this->assertContains("- en", "$choice");
		$this->assertNotContains("0 - en", "$choice");

	}

/*
### Options as Datasets

Usually you will be fetching options from the database and these will come in the form of rows, also known as a
dataset structure. The `options` setter understands that as well:

#mdx:OptionsRows

Outputs:

#mdx:OptionsRows -o httidy

**Notice:** the column names don't need to be `id` and `name`. They can be anything as long as they occur in the first
and second positions of each row. In other words, the first column will always be the value, and the second column will always be the label.

*/
	function testOptionsRows(){
		#mdx:OptionsRows
		$choice = new SimpleChoice("category");
		$choice->options([
			['id'=>1,'name'=>'Action'],
			['id'=>2,'name'=>'Drama'],
			['id'=>3,'name'=>'Sci-fi']
		])->context(['category'=>2]); // selects category 2
		
		#/mdx echo $choice
		$this->assertContains("1 - Action","$choice");
		$this->assertContains("<b>2 - Drama","$choice");

	}



/*
### Options as Array of Objects

This is the same as passing a dataset, only that each row is an object instead of an array:

#mdx:OptionsObjects

Outputs:

#mdx:OptionsObjects -o httidy
*/
	function testOptionsObjects(){
	
		#mdx:OptionsObjects
		$choice = new SimpleChoice("category");
	
		$option1 = new StdClass();
		$option1->id = 1;
		$option1->name = 'Action';
	
		$option2 = new StdClass();
		$option2->id = 2;
		$option2->name = 'Drama';

		$choice->options([$option1, $option2])
			->context(['category'=>2]); // selects category 2
		
		#/mdx echo $choice
		$this->assertContains("1 - Action","$choice");
		$this->assertContains("<b>2 - Drama","$choice");

	}

/*
### Options as Tuples

The `options` also accepts an array of tuples in the form: [value1, label1], [value2,label2], and so on...

#mdx:OptionsTuples

Outputs:

#mdx:OptionsTuples -o httidy
*/
	function testOptionsTuples(){
		#mdx:OptionsTuples
		$choice = new SimpleChoice("category");
		$choice->options([[1,'Action'],[2,'Drama'],[3,'Sci-fi']]);
		#/mdx echo $choice
		$this->assertContains("1 - Action","$choice");

	}


/*
### Lazy Loaded Options

Last but not least, you can pass a function to the `options` method for returning the options only just
before the widget is rendered. This is ideal, for instance, if you are fetching the options from the database:

#mdx:OptionsFunction

Outputs:

#mdx:OptionsFunction -o httidy
*/
	function testOptionsFunction(){
		#mdx:OptionsFunction
		$choice = new SimpleChoice("category");
		$choice->options(function(){
			// pretend this was fetched from the db
			$rows = [
				['id'=>1,'name'=>'Action'],
				['id'=>2,'name'=>'Drama'],
				['id'=>3,'name'=>'Sci-fi']
			];

			return $rows;

		})->context(['category'=>3]); // selects category 3
		#/mdx echo $choice
		$this->assertContains("<b>3 - Sci-fi","$choice");

	}	

/*
**Notice:** the value returned by the lazy loader can be anything supported by the `options` method:
associative arrays, numeric arrays, array of tuples, and even another function, as you can see in the example below:

#mdx:OptionsFunction2

Outputs:

#mdx:OptionsFunction2 -o httidy
*/
	function testOptionsFunction2(){

		#mdx:OptionsFunction2
		$choice = new SimpleChoice("category");
		$choice->options(function(){
			// return a function which returns an associative array:
			return function(){
				return [1=>'Category A',2=>'Category B',3=>'Category C'];
			};
		});

		$choice->context(['category'=>2]); // selects category 2
		#/mdx echo $choice
		$this->assertContains("<b>2","$choice");

	}


}