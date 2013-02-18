# XMLEncoder

A PHP library to make it easier to create XML using the DOM Document object.

## XML Example

Start out with an array of data structured the way you want the XML to be structured:

```php
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
```

Then create a new XMLEncoder obejct, passing it the array as the only parameter. After that, it's as easy as calling the <code>render()</code> method.

```php
$xml = new \XMLEncoder\XMLEncoder($data);
$xml->render();
```

## RSS Example

The process for creating an RSS feed is very similar, except that the array passed to the XMLEncoder will be a numeric array filled with the items in the feed. Elements in the channel are handled with the <code>rss()</code> method.

```php
$data = array(
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
);


// Render $data as an RSS deed
$xml = new \XMLEncoder\XMLEncoder($data);
$moreChannelElements = array("something" => "something");
$xml->rss("the title", "the link", "the desc", $moreChannelElements)->render();
```