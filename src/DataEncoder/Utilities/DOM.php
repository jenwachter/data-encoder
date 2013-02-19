<?php

namespace DataEncoder\Utilities;

class DOM
{
	/**
	 * DOM Document object
	 * @var object
	 */
	protected $dom;

	public function __construct($domDocument)
	{
		$this->dom = $domDocument;
	}

	/**
	 * Createa an XML element (tag)
	 * @param 	string $name 		Name of the element
	 * @param  	object $bind 		Parent object to bind the new element to
	 * @param   string $value 		The value of the element (if any) 
	 * @param  	array  $attributes 	Associative array of tag attributes "attribute" => "value"
	 * @return 	The created XML element
	 */
	public function createElement($name, $bind, $value = null, $attributes = array())
	{
		$item = $bind->appendChild($this->dom->createElement($name));

		if ($value) {
			$this->addText($value, $item);
		}

		foreach ($attributes as $k => $v) {
			$this->createAttribute($k, $v, $item);
		}

		return $item;
	}

	/**
	 * Add a text node to an XML element (tag)
	 * 
	 * @param   string $text Text value to add
	 * @param   object $bind XML element to add the text to
	 * @return  null
	 */
	public function addText($text, $bind)
	{
		$bind->appendChild($this->dom->createTextNode($text));
	}

	/**
	 * Create an attribute on an XML element (tag)
	 * 
	 * @param  string $attribute Name of attribute
	 * @param  string $value     Value of attribute
	 * @param  object $bind XML element to add the text to
	 * @return null
	 */
	public function createAttribute($attribute, $value, $bind)
	{
		$attr = $this->dom->createAttribute($attribute);
		$attr->value = $value;
		$bind->appendChild($attr);
	}
}