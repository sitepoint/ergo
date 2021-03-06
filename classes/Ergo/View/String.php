<?php

/**
 * A string based view
 */
class Ergo_View_String implements Ergo_View
{
	private $_string;

	/**
	 * Constructor
	 */
	public function __construct($string)
	{
		$this->_string = $string;
	}

	/* (non-phpdoc)
	 * @see Ergo_View::output()
	 */
	public function output()
	{
		return $this->_string;
	}

	/* (non-phpdoc)
	 * @see Ergo_View::stream()
	 */
	public function stream()
	{
		$fp = fopen("php://memory", 'r+');
		fwrite($fp, $this->_string);
		rewind($fp);
		return $fp;
	}
}
