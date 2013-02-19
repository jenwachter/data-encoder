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
	 * The RSS channel DOM object
	 * @var boolean
	 */
	protected $channel;

	/**
	 * These array keys will trigger a <link> element if the
	 * value is not an array. Key: array key; Value: link rel.
	 * 
	 * @var array
	 */
	protected $linkTriggers = array();

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
	public function rss($title, $link, $description, $otherElements = array())
	{
		$this->rss = $this->domHelper->createElement("rss", $this->dom, null, array("version" => "2.0"));
		$this->channel = $this->domHelper->createElement("channel", $this->rss);

		$channelElements = array(
			"title" => $title,
			"link" => $link,
			"description" => $description
		);

		$channelElements = array_merge($channelElements, $otherElements);
		$this->addElements($channelElements, $this->channel);

		return $this;
	}

	

	protected function encode()
	{
		$this->addElements($this->data, $this->channel, "item");
	}
}