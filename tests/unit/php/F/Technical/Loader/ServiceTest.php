<?php
/**
 * F\Technical\Loader\Service is
 * a class to handle loader operations.
 *
 * <LICENSETXT>
 *
 * @category  F
 * @author    Fran�ois Schneider <francoisschneider@neuf.fr>
 * @package    F\Technical\Loader\Adapter
 * @copyright Copyright (c) 2012 <COPYRIGHT>
 * @license   <LICENSE>
 * @version   $Id: $
 */

namespace F\Technical\Loader;

/**
 * @see tests/unit/php/bootstrap.php
 */
require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @see F/Technical/Base/Test/Service.php
 */
require_once 'F/Technical/Base/Test/Service.php';

/**
 * F\Technical\Loader\Service is
 * a class to handle loader operations.
 *
 * @category F
 * @package F\Technical\Loader
 * @copyright  Copyright (c) 2012 <COPYRIGHT>
 * @license    <LICENSE>
 * @version    Release: @package_version@
 * @since      Class available since Release 0.0.1
 */
class ServiceTest
extends \F\Technical\Base\Test\Service
{
	/**
	 * @return F\Technical\Loader\Service
	 */
	public function s()
	{
		return parent::s();
	}
	/**
	 * @return F\Technical\Loader\Adapter\Mock
	 */
	public function m()
	{
		return parent::m();
	}

    /**
     * just for saying there is no test
     */
	public function testAutoloadWithSuccess()
    {
    	$this->mock('registerNamespaces', $this->s()->getAdapter());
    	$this->mock('autoload');
    	$namespace = array('namespace' => 'directory');
    	$this->assertInstanceOfService($this->s()->autoload($namespace));
    	$this->assertEquals(array($namespace), $this->m()->getCallArgs('registerNamespaces'));
    }
}