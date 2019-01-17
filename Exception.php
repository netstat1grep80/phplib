<?php
/**
 * JasonZ Framework
 *
 * @category   JasonZ
 * @package    JasonZ
 * @copyright  1565166@qq.com
 * @version    $Id$
 */


/**
 * @package    Jason.Z
 * @copyright  1565166@qq.com
 */
class JasonZ_Exception extends Exception
{
	const CODE_CLASS_NOT_FOUND 	= 1;
	const CODE_ACTION_NOT_FOUND = 2;
	const CODE_DB 				= 3;
	const CODE_THIRDPARTY		= 4;
	const CODE_UNDERCONSTRUCTION= 5;
	const CODE_FATAL = 6;
	
	public function __construct($message, $code)
	{
		parent::__construct($message, $code);
	}
}