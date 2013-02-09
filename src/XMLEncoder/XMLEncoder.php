<?php

namespace XMLEncoder;

class XMLEncoder
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
	 * The RSS DOM object, if applicable
	 * @var boolean
	 */
	protected $rss = null;

	protected $linkKeys = array(
		"url" => "alternate",
		"prev" => "prev",
		"next" => "next",
		"related" => "related",
		"via" => "via",
		"comments" => "comments"
	);


	public function __construct($data)
	{
		$this->data = $data;
		$this->dom = new \DOMDocument("1.0", "utf-8");
		$this->domHelper = new \XMLEncoder\Utilities\DOMHelper($this->dom);
	}

	/**
	 * Designate this XML as an RSS feed.
	 * 
	 * @param  string $title         The name of the channel.
	 * @param  string $link          The URL to the website cooresponding to this channel.
	 * @param  string $description   Description of the channel.
	 * @param  array  $otherElements Associative array of other channel elements
	 *                               to include. "element" => "value"
	 * @return self
	 */
	public function rss($title, $link, $description, $otherElements)
	{
		$this->rss = $this->domHelper->createElement("rss", $this->dom, null, array("version" => "2.0"));
		$channel = $this->domHelper->createElement("channel", $this->rss);

		$channelElements = array(
			"title" => $title,
			"link" => $link,
			"description" => $description
		) + $otherElements;

		foreach ($channelElements as $k => $v) {
			$this->domHelper->createElement($k, $channel, $v);
		}

		return $this;
	}

	public function render()
	{
		$this->parse();
		
		$this->dom->formatOutput = true;
		$this->dom->preserveWhiteSpace = false; 

		header('Content-type: text/xml');
		echo $this->dom->saveXML();
	}

	protected function parse()
	{
		$bind = $this->rss ? $this->rss : $this->domHelper->createElement("feed", $this->dom);
		$this->addElements($this->data, $bind);
	}

	/**
	 * Adds an array of elements to the DOM Document.
	 * 
	 * @param  array $array Associative array of keys and values
	 * @param  object $bind Parent object to bind the new element to
	 * @return null
	 */
	protected function addElements($array, $bind)
	{
		foreach ($array as $key => $value) {

			$item = $this->domHelper->createElement($key, $bind);
			// $item = $bind->appendChild($this->dom->createElement($key));

			if (is_array($value)) {
				$singular = !$this->hasSingularIdentifier($value) ? \XMLEncoder\Utilities\Inflector::singularize($key) : null;
				$this->encodeArray($value, $item, $singular);
			} else {
				$item->appendChild($this->dom->createTextNode($value));
			}
		}
	}

	/**
	 * Adds a value to the DOM Document that is an array
	 * 
	 * @param  array $array Associative array of keys and values
	 * @param  object $bind Parent object to bind the new element to
	 * @return null
	 */
	protected function encodeArray($array, $bind, $singular = null)
	{
		foreach ($array as $key => $value) {

			$key = $singular ? $singular : $key;
			$value = is_object($value) ? (array) $value : $value;

			if (is_array($value)) {
				$item = $this->domHelper->createElement($key, $bind);
				$this->encodeArray($value, $item);
			} else {
				if (in_array($key, array_keys($this->linkKeys))) {
					$rel = $this->linkKeys[$key];
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