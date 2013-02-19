# DataEncoder

Eventually this library will be built out to do various types of data encoding. For now, only XML and RSS are available.

## XML Example

Start out with an array of data structured the way you want the XML to be structured. Keep in mind that groups of objects should be included like the below array of "posts" where the key is the plural.

```php
$data = array(
	"something" => "something",
	"posts" => array(
		array(
			"title" => "This is the title",
			"author" => array(
				"firstName" => "John",
				"lastName" => "Smith"
			),
			"url" => "http://www.somewebsite.com/path/to/article"
		),
		array(
			"title" => "This is the title of another article",
			"author" => array(
				"firstName" => "Jane",
				"lastName" => "Doe"
			),
			"url" => "http://www.somewebsite.com/path/to/this/article"
		)
	)
);
```

Then create a new XML Encoder object, passing it the array of data. After that, it's as easy as calling the <code>render()</code> method.

```php
$xml = new \DataEncoder\XML($data);
$xml->render();
```

## RSS Example

The process for creating an RSS feed is a bit different, as RSS feeds have very specific requirements. When instantiating a new RSS Encoder, there are three required and one optional parameter. For this example, I'll show the method calls and then describe each parameter.

```php
$xml = new \DataEncoder\RSS($channelElements, $itemElements, $data, $dataMap);
$xml->render();
```

### $channelElements

An associative array of channel elements. Please note that per RSS specification, "title," "link," and "description" elements are required. Optional elements include "lastBuildDate," "language," etc...

```php
$channelElements = array(
	"title" => "The Title of my feed",
	"link" => "http://www.google.com",
	"description" => "The description of my feed",
	"lastBuildDate" => 1361242244
);
```


### $itemElements

To prevent a whole lot of processing before even instantiating the RSS Encoder to only pass the fields you want to include in the feed, the second parameter allows you to pick and choose which elements you want to include. Please note that these values correspond to the $dataMap array first and then to the item keys passed in through $data.

```php
$itemElements = array("id", "title", "description", "link", "comments", "pubDate");
```


### $data
An array of items in the feed.

```php
$data = array(
	array(
		"title" => "This is the title",
		"author" => array(
			"firstName" => "John",
			"lastName" => "Smith"
		),
		"url" => "http://www.somewebsite.com/path/to/article"
	),
	array(
		"title" => "This is the title of another article",
		"author" => array(
			"firstName" => "Jane",
			"lastName" => "Doe"
		),
		"url" => "http://www.somewebsite.com/path/to/this/article"
	)
);
```


### $dataMap

In further effort to prevent a lot of processing before even instantiating the RSS Encoder, the final (and optional) parameter is an associative array mapping fields in the items passed $data array to RSS-specific fields.

```php
$dataMap = array(
	"headline" => "title",                         
	"body" => "description",
	"url" => "link",
	"publish_date" => "pubDate"
);
```