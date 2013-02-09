<?php

namespace XMLEncoder\Utilities;

class Inflector
{
	public static function singularize($word)
	{
		return \Doctrine\Common\Inflector\Inflector::singularize($word);
	}

	public static function pluralize($word)
	{
		return \Doctrine\Common\Inflector\Inflector::pluralize($word);
	}
}