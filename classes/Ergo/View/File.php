<?php

/**
 * A file based view
 */
class Ergo_View_File implements Ergo_View
{
	private $_file;

	/**
	 * Constructor
	 */
	public function __construct($file)
	{
		$this->_file = $file;
	}

	/* (non-phpdoc)
	 * @see Ergo_View::output()
	 */
	public function output()
	{
		return file_get_contents($this->_file);
	}

	/* (non-phpdoc)
	 * @see Ergo_View::stream()
	 */
	public function stream()
	{
		return fopen("php://memory", 'r+');
	}
}
