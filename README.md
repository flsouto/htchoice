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

```php
<?php

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

```



### Setting the array of options
In order to feed the choice widget with options for the user to choose we must use the `options` method which accepts an associative array:

```php
<?php
require_once('vendor/autoload.php');

$choice = new SimpleChoice('color');
$choice->options([
	1 => 'Black',
	2 => 'White',
	3 => 'Gray'
]);

echo $choice;
```

Outputs:

```html

<div class="widget 58aba87188334" style="display:block">
  Type in the desired option:
 <input name="color" value="" size="10" /> <br />
  1 - Black<br />
  2 - White<br />
  3 - Gray<br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

### Selecting an option

In the next example we want the third option (i.e. the "Fall" season) to be marked as selected:

```php
<?php
require_once('vendor/autoload.php');

$choice = new SimpleChoice('season');
$choice->options([
	1 => 'Spring',
	2 => 'Summer',
	3 => 'Fall',
	4 => 'Winter'
]);
$choice->context(['season'=>3]); // selects season 3 (Fall)

echo $choice;
```

Outputs:

```html

<div class="widget 58aba8718eeab" style="display:block">
  Type in the desired option:
 <input name="season" value="3" size="10" /> <br />
  1 - Spring<br />
  2 - Summer<br />
  <b>3 - Fall (selected)</b><br />
  4 - Winter<br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

### Readonly mode

We can use the inherited `readonly` setter to tell the widget we don't want users interacting with it:

```php
<?php
require_once('vendor/autoload.php');

$choice = new SimpleChoice('season');
$choice->options([
	1 => 'Spring',
	2 => 'Summer',
	3 => 'Fall',
	4 => 'Winter'
]);
$choice->context(['season'=>3])
	->readonly(true);

echo $choice;
```

Outputs:

```html

<div class="widget 58aba8718f4b0" style="display:block">
  1 - Spring<br />
  2 - Summer<br />
  <b>3 - Fall (selected)</b><br />
  4 - Winter<br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

### Options as Numeric Arrays

Besides associative arrays, the `options` method accepts numeric arrays. In this case
each option's value will be the same as its own label:

```php
<?php
require_once('vendor/autoload.php');

$choice = new SimpleChoice("language","Type in the desired language code:");
$choice->options(['en','es','pt','fr']);

echo $choice;
```

Outputs:

```html

<div class="widget 58aba8718f9d2" style="display:block">
  Type in the desired language code:
 <input name="language" value="" size="10" /> <br />
  - en<br />
  - es<br />
  - pt<br />
  - fr<br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

### Options as Datasets

Usually you will be fetching options from the database and these will come in the form of rows, also known as a
dataset structure. The `options` setter understands that as well:

```php
<?php
require_once('vendor/autoload.php');

$choice = new SimpleChoice("category");
$choice->options([
	['id'=>1,'name'=>'Action'],
	['id'=>2,'name'=>'Drama'],
	['id'=>3,'name'=>'Sci-fi']
])->context(['category'=>2]); // selects category 2

echo $choice;
```

Outputs:

```html

<div class="widget 58aba8718ff68" style="display:block">
  Type in the desired option:
 <input name="category" value="2" size="10" /> <br />
  1 - Action<br />
  <b>2 - Drama (selected)</b><br />
  3 - Sci-fi<br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

**Notice:** the column names don't need to be `id` and `name`. They can be anything as long as they occur in the first
and second positions of each row. In other words, the first column will always be the value, and the second column will always be the label.


### Options as Array of Objects

This is the same as passing a dataset, only that each row is an object instead of an array:

```php
<?php
require_once('vendor/autoload.php');

$choice = new SimpleChoice("category");

$option1 = new StdClass();
$option1->id = 1;
$option1->name = 'Action';

$option2 = new StdClass();
$option2->id = 2;
$option2->name = 'Drama';

$choice->options([$option1, $option2])
	->context(['category'=>2]); // selects category 2

echo $choice;
```

Outputs:

```html

<div class="widget 58aba8719055e" style="display:block">
  Type in the desired option:
 <input name="category" value="2" size="10" /> <br />
  1 - Action<br />
  <b>2 - Drama (selected)</b><br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

### Options as Tuples

The `options` also accepts an array of tuples in the form: [value1, label1], [value2,label2], and so on...

```php
<?php
require_once('vendor/autoload.php');

$choice = new SimpleChoice("category");
$choice->options([[1,'Action'],[2,'Drama'],[3,'Sci-fi']]);

echo $choice;
```

Outputs:

```html

<div class="widget 58aba87190b0f" style="display:block">
  Type in the desired option:
 <input name="category" value="" size="10" /> <br />
  1 - Action<br />
  2 - Drama<br />
  3 - Sci-fi<br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

### Lazy Loaded Options

Last but not least, you can pass a function to the `options` method for returning the options only just
before the widget is rendered. This is ideal, for instance, if you are fetching the options from the database:

```php
<?php
require_once('vendor/autoload.php');

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

echo $choice;
```

Outputs:

```html

<div class="widget 58aba871910a0" style="display:block">
  Type in the desired option:
 <input name="category" value="3" size="10" /> <br />
  1 - Action<br />
  2 - Drama<br />
  <b>3 - Sci-fi (selected)</b><br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

**Notice:** the value returned by the lazy loader can be anything supported by the `options` method:
associative arrays, numeric arrays, array of tuples, and even another function, as you can see in the example below:

```php
<?php
require_once('vendor/autoload.php');

$choice = new SimpleChoice("category");
$choice->options(function(){
	// return a function which returns an associative array:
	return function(){
		return [1=>'Category A',2=>'Category B',3=>'Category C'];
	};
});

$choice->context(['category'=>2]); // selects category 2

echo $choice;
```

Outputs:

```html

<div class="widget 58aba87191663" style="display:block">
  Type in the desired option:
 <input name="category" value="2" size="10" /> <br />
  1 - Category A<br />
  <b>2 - Category B (selected)</b><br />
  3 - Category C<br />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```