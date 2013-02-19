<?php

namespace DataEncoder;

class XML implements Interfaces\Encoder
{
	/**
	 * DOM Document object
	 * @var object
	 */
	protected $dom;

	/**
	 * DOMHelper object
	 * @var object
	 */
	protected $domHelper;

	/**
	 * Data to enode
	 * @var array
	 */
	protected $data;

	/**
	 * These array keys will trigger a <link> element if the
	 * value is not an array. arrayKeyTrigger => linkRel
	 * 
	 * @var array
	 */
	protected $linkTriggers = array(
		"url" => "alternate",
		"prev" => "prev",
		"next" => "next",
		"related" => "related",
		"via" => "via",
		"comments" => "comments"
	);

	public function __construct($data)
	{
		$this->data = $this->objectToArray($data);
		$this->dom = new \DOMDocument("1.0", "utf-8");
		$this->domHelper = new \DataEncoder\Utilities\DOM($this->dom);
	}

	/**
	 * Recursivly checks a given varaible and converts any
	 * object to an array.
	 * @param  mixed $data Object or array
	 * @return array
	 */
	protected function objectToArray($data) {
		if (is_object($data)) {
		    $data = get_object_vars($data);
		}

		if (is_array($data)) {
			return array_map(array($this, "objectToArray"), $data);
		}
		else {
			return $data;
		}
	}

	/**
	 * Add a keyword that would trigger a link element.
	 * 
	 * @param string $key Key that would trigger a link element
	 * @param string $rel The "rel" value this trigger equates to
	 */
	protected function addLinkTrigger($key, $rel)
	{
		$this->linkTriggers[$key] = $rel;
	}

	public function render()
	{
		$this->encode();
		
		$this->dom->formatOutput = true;
		$this->dom->preserveWhiteSpace = false; 

		header('Content-type: text/xml');
		echo $this->dom->saveXML();
	}

	protected function encode()
	{
		$bind = $this->domHelper->createElement("feed", $this->dom);
		$this->addElements($this->data, $bind);
	}

	/**
	 * Adds an array of elements to the DOM Document.
	 * 
	 * @param  array  $array 	Associative array of keys and values
	 * @param  object $bind 	Parent object to bind the new element to
	 * @param  string $key 		Override the item's key (used for RSS)
	 * @return null
	 */
	protected function addElements($array, $bind, $keyOverride = null)
	{
		foreach ($array as $key => $value) {
			
			$key = $keyOverride ? $keyOverride : $key;

			$item = $this->domHelper->createElement($key, $bind);

			if (is_array($value)) {
				$singular = !$this->hasSingularIdentifier($value) ? \DataEncoder\Utilities\Inflector::singularize($key) : null;
				$this->encodeArray($value, $item, $singular);
			} else {
				$this->domHelper->addText($value, $item);
			}
		}
	}

	/**
	 * Adds a value to the DOM Document that is an array
	 * 
	 * @param  array  $array 		Associative array of keys and values
	 * @param  object $bind 		Parent object to bind the new element to
	 * @param  string $keyOverride 	Key override
	 * @return null
	 */
	protected function encodeArray($array, $bind, $keyOverride = null)
	{
		foreach ($array as $key => $value) {

			$key = $keyOverride ? $keyOverride : $key;

			if (is_array($value)) {
				$item = $this->domHelper->createElement($key, $bind);
				$this->encodeArray($value, $item);
			} else {
				if (in_array($key, array_keys($this->linkTriggers))) {
					$rel = $this->linkTriggers[$key];
					$this->domHelper->createElement("link", $bind, $value, array("rel" => $rel));
				} else {
					$this->domHelper->createElement($key, $bind, $value);
				}
			}
		}
	}

	/**
	 * If the keys are numbers, we can assume we
	 * need the singular identifier.
	 * 
	 * @param  array  $array Array to check
	 * @return boolean True if a singular identifier is present
	 */
	protected function hasSingularIdentifier($array)
	{
		return (bool) count(array_filter(array_keys($array), "is_string"));
	}
}