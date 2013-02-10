# XMLEncoder

A PHP library to make it easier to create XML using the DOM Document object.

## Example

```php
<?php

// Starting out with an array of data structured the way you want the XML to be structured
$data = array(
	"something" => "something",
	"posts" => array(
		array(
			"id" => 123,
			"title" => "This is the title",
			"author" => array(
				"firstName" => "John Doe",
				"lastName" => "Wachter",
				"title" => "Editor",
				"url" => "http://www.somewebsite.com"
			),
			"url" => "http://www.somewebsite.com/path/to/article"
		),
		array(
			"id" => 124,
			"title" => "This is the title of another article",
			"author" => array(
				"firstName" => "Jane",
				"lastName" => "Doe",
				"title" => "Editor",
				"url" => "http://www.somewebsite.com"
			),
			"url" => "http://www.somewebsite.com/path/to/this/article"
		)
	)
);

// Render $data as an RSS deed
$xml = new \XMLEncoder\XMLEncoder($data);
$xml->rss("the title", "the link", "the desc")->render();

// - or -

// Render $data as an XML feed
$xml = new \XMLEncoder\XMLEncoder($data);
$xml->render();

```