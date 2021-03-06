<?php
/**
 * F\Technical\Base\Test\Base is a class to handle test Base operations.
 *
 * <LICENSETXT>
 *
 * @category  F
 * @author    Francois Schneider <francoisschneider@neuf.fr>
 * @package   F\Technical\Base
 * @copyright Copyright (c) 2011 <COPYRIGHT>
 * @license   <LICENSE>
 * @version   $Id: $
 */

namespace F\Technical\Base\Test;

/**
 * @see F/Technical/Base/Phpunit/TestCase.php
 */
require_once 'F/Technical/Base/Test/Phpunit/TestCase.php';

/**
 * F\Technical\Base\Test\Base is a class to handle test Base operations.
 *
 * @category F
 * @package F\Technical\Base\Test
 * @copyright Copyright (c) 2011 <COPYRIGHT>
 * @license <LICENSE>
 * @version Release: @package_version@
 * @since Class available since Release 0.0.1
 */
abstract class Base
    extends Phpunit\TestCase
{
	/**
	 * @var mixed
	 */
	protected $_service;
	/**
	 * Returns the path of the service to unit-test
	 *
	 * @return string
	 */
	public function getServicePath()
	{
		return preg_replace('|Test$|', '', get_class($this));
	}
	/**
	 * Returns the class of the service to unit-test
	 *
	 * @return string
	 */
	public function getServiceClass()
	{
		$path = dirname(str_replace('\\', '/', $this->getServicePath()));
		return str_replace('/','\\',$path) . '\\Service';
	}
	/**
	 * Set-ups the unit test
	 */
	public function setUp()
	{
		
        $serviceClass = $this->getServiceClass();     
        require_once $this->getServiceClass() . '.php';
		$this->_service = new $serviceClass();
		
		
	}
	/**
	 * Returns the tested service instance.
	 *
	 * @return mixed
	 */
	public function s()
	{
		return $this->_service;
	}
	/**
	 * Returns the unit-tested service
	 *
	 * @return mixed
	 */
	public function getService()
	{
		return $this->_service;
	}
	/**
	 * Returns the current adapter for the unit-tested service
	 *
	 * @return mixed
	 */
	public function getCurrentAdapter()
	{
		return $this->getService()->getAdapter();
	}

    public function __call($name, $args)
    {
        throw new \RuntimeException('Unknown method ' . get_class($this) . "::$name()");
    }

	/**
     * test if actual is instance of current service
     *
     * @param $actual
     *
     *
     */
    public function assertInstanceOfService($actual)
    {
    	$this->assertInstanceOf(get_class($this->s()), $actual);
    }
}