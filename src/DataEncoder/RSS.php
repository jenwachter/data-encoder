<?php

namespace DataEncoder;

class RSS extends XML
{
	/**
	 * The RSS DOM object
	 * @var boolean
	 */
	protected $rss;

	/**
	 * RSS version
	 * @var string
	 */
	protected $version = "2.0";

	/**
	 * The RSS channel DOM object
	 * @var boolean
	 */
	protected $channel;

	/**
	 * An associative array mapping RSS-specific fields to
	 * fields in the passed $data array. "UserField" => "RSSField"
	 * @var array
	 */
	protected $dataMap = array();

	/**
	 * Associative array of elements to be include
	 * in the channel. "element" => "value"
	 * @var array
	 */
	protected $channelElements = array();

	/**
	 * Array of elements to include in each item.
	 * @var array
	 */
	protected $itemElements = array();

	/**
	 * Elements that will trigger date formatting
	 * @var array
	 */
	protected $dateElements = array("pubDate", "lastBuildDate");

	/**
	 * These array keys will trigger a <link> element if the
	 * value is not an array. arrayKeyTrigger => linkRel
	 * 
	 * @var array
	 */
	protected $linkTriggers = array();


	/**
	 * Instantiates an RSS Encoder.
	 * 
	 * @param array 	$channelElements 	An assosiative array of channel elements. Per RSS
	 *                                	 	specification: title, link, and description are
	 *                                	 	required. "element" => "value"
	 * @param array 	$itemElements    	An array of elements to include in each data item;
	 *                                 		for example, title, link, author, etc...
	 * @param array 	$data            	An array of items in the feed
	 * @param array  	$dataMap         	An associative array mapping fields in the items
	 *                                  	passed $data array to RSS-specific fields.
	 *                                  	"UserField" => "RSSField"
	 *                                  	For example:
	 *                                  	$dataMap = array(
	 *                                  		"published" => "pubDate",
	 *                                  		"body" => "description"
	 *                                  	);
	 */
	public function __construct($channelElements, $itemElements, $data, $dataMap = array())
	{
		parent::__construct($data);

		$this->channelElements = $channelElements;
		$this->itemElements = $itemElements;
		$this->dataMap = $dataMap;

		$this->setupFeed();
	}

	/**
	 * Creates the RSS and channel objects.
	 * @return null
	 */
	protected function setupFeed()
	{
		$this->rss = $this->domHelper->createElement("rss", $this->dom, null, array("version" => $this->version));
		$this->channel = $this->domHelper->createElement("channel", $this->rss);
		$this->addElements($this->channelElements, $this->channel);
	}

	/**
	 * Encode the data
	 * @return null
	 */
	protected function encode()
	{
		$this->addElements($this->data, $this->channel, "item");
	}

	/**
	 * Adds an array of elements to the DOM Document.
	 * 
	 * @param  array  $array 	Associative array of keys and values
	 * @param  object $bind 	Parent object to bind the new element to
	 * @param  string $key 		Override the item's key
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
				if (in_array($key, $this->dateElements)) {
					$value = date("r", $value);
				}
				$this->domHelper->addText($value, $item);
			}
		}
	}


	/**
	 * Adds a value to the DOM Document that is an array
	 * 
	 * @param  array $array     Array to encode
	 * @param  object $bind 	Parent object to bind the new element to
	 * @return null
	 */
	protected function encodeArray($array, $bind, $keyOverride = null)
	{
		foreach ($array as $key => $value) {

			$key = !empty($this->dataMap[$key]) ? $this->dataMap[$key] : $key;

			if (!in_array($key, $this->itemElements)) {
				continue;
			}

			if (is_array($value)) {
				$item = $this->domHelper->createElement($key, $bind);
				$this->encodeArray($value, $item);
			} else {
				if (in_array($key, $this->dateElements)) {
					$value = date("r", $value);
				}
				$this->domHelper->createElement($key, $bind, $value);
			}
		}
	}
}