<?php
/**
 * F\Technical\I18n\Service is
 * a class to handle i18n operations.
 *
 * <LICENSETXT>
 *
 * @category  F
 * @author    Pierre Laurent Massard <pmassard.ext@orange.com>
 * @package    F\Technical\I18n\Adapter
 * @copyright Copyright (c) 2012 <COPYRIGHT>
 * @license   <LICENSE>
 * @version   $Id: $
 */

namespace F\Technical\I18n;

/**
 * @see tests/integration/php/bootstrap.php
 */
require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @see F/Technical/Base/Test/Integration/Service.php
 */
require_once 'F/Technical/Base/Test/Integration/Service.php';

/**
 * F\Technical\I18n\Service is
 * a class to handle i18n operations.
 *
 * @category F
 * @package F\Technical\I18n
 * @copyright  Copyright (c) 2012 <COPYRIGHT>
 * @license    <LICENSE>
 * @version    Release: @package_version@
 * @since      Class available since Release 0.0.1
 */
class ServiceTest
    extends \F\Technical\Base\Test\Integration\Service
{
    /**
     * @return F\Technical\I18n\Service
     */
    public function s()
    {
        return parent::s();
    }

    public function setUp()
    {
        parent::setUp();
        $this->setDataSetPath(realpath(dirname(__FILE__) . '/../../../dataset/'));
    }


    /**
     * addI18nFile
     */
    public function testAddI18nFileWithFileNotFoundThrowException()
    {
        $filePath = 'nexistepas';
        $this->setExpectedException('RuntimeException', "le fichier 'nexistepas' n'existe pas");
    	$this->s()->AddI18nFile($filePath);
    }

     public function testAddI18nFileWithFileNotFileThrowException()
    {
    	$filePath = __DIR__;
        $this->setExpectedException('RuntimeException', "'".$filePath ."' n'est pas un fichier");
    	$this->s()->AddI18nFile($filePath);
    }

    public function testAddI18nFileWithFileExistsSuccess()
    {
    	$filePath = $this->getDataSetPath().'/fr_FR.php';
    	$initial = $this->s()->getAdapter()->getI18nTranslation();
    	$actual = $this->s()->AddI18nFile($filePath);
    	$expected = get_class($this->s());
    	$this->assertInstanceOf($expected, $actual);

    	$expected = array_merge($initial, array(
    	        'file.test.notfound' => "test Le fichier '%{1}' n'existe pas",
    	        'file.test.badformat' => "test Le fichier '%{1}' n'est pas un fichier plat",
    	        'une.clef' =>"une clef",
    	));
    	$this->assertEquals($expected, $this->s()->getAdapter()->getI18nTranslation());

    }

    /**
     * @see translate
     */
    public function testTranslateWithI18nFileSetReturnMessage()
    {
        $this->s()->AddI18nFile($this->getDataSetPath().'/fr_FR.php');
        $actual = $this->s()->translate('une.clef');
        $expected = 'une clef';
        $this->assertEquals($expected, $actual);
    }

    public function testTranslateWithI18nFileSetReturnMessageWithParam()
    {
    	$this->s()->AddI18nFile($this->getDataSetPath().'/fr_FR.php');
    	$actual = $this->s()->translate('file.test.notfound', 'fichier');
    	$expected = 'test Le fichier \'fichier\' n\'existe pas';
    	$this->assertEquals($expected, $actual);
    }
}