<?php
/**
 * F\Technical\Database\Service is
 * a class to handle database operations.
 *
 * <LICENSETXT>
 *
 * @category  F
 * @author    Pascal Renaut <prenaut.ext@orange.com>
 * @package    F\Technical\Database\Adapter
 * @copyright Copyright (c) 2012 <COPYRIGHT>
 * @license   <LICENSE>
 * @version   $Id: $
 */

namespace F\Technical\Database;

/**
 * @see tests/unit/php/bootstrap.php
 */
require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @see F/Technical/Base/Test/Service.php
 */
require_once 'F/Technical/Base/Test/Service.php';

/**
 * F\Technical\Database\Service is
 * a class to handle database operations.
 *
 * @category F
 * @package F\Technical\Database
 * @copyright  Copyright (c) 2012 <COPYRIGHT>
 * @license    <LICENSE>
 * @version    Release: @package_version@
 * @since      Class available since Release 0.0.1
 */
class ServiceTest
extends \F\Technical\Base\Test\Service
{
	/**
	 * @return F\Technical\Database\Service
	 */
	public function s()
	{
		return parent::s();
	}
	/**
	 * @return F\Technical\Database\Adapter\Mock
	 */
    public function m()
	{
		return parent::m();
	}

	public function mockGetConnectionSuccess()
	{
	    $this->mock('isConnected', true);
	}

	/**
     * fetchAll
     */
	public function testFetchAllWithNoDataReturned()
	{
	    $this->mockGetConnectionSuccess();
	    $this->mock('fetchAll', array(array()));
	    $actual = $this->s()->fetchAll('requete', array('unKnownData'));
	    $this->assertEquals(array(array()), $actual);
	}

    public function testFetchAllWithSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('fetchAll', array(array('knownData')));
        $actual = $this->s()->fetchAll('requete', array('knownData'));
        $this->assertEquals(array(array('knownData')), $actual);
    }

    public function testFetchAllWithNoDatabaseConnectedThrowRuntimeException()
    {
        $this->mock('isConnected', false);
        $this->mock('fetchAll');
        $this->setExpectedException('RuntimeException', 'aucune connection à une base de données', 503);
        $this->s()->fetchAll('requete', array('knownData'));
    }

    /**
     * isConnected
     */
    public function testIsConnectedWithSuccess()
    {
        $this->mock('isConnected', true);
        $actual=$this->s()->isConnected();
        $this->assertEquals(true, $actual);
    }

    public function testIsConnectedWithNoSuccess()
    {
        $this->mock('isConnected', false);
        $actual=$this->s()->isConnected();
        $this->assertEquals(false, $actual);
    }

    /**
     * execScriptFile
     */
    public function testExecScriptFileWithFileNotFoundThrowException()
    {
    	$this->mock('isConnected', true);
    	$this->mock('getFileContent', new \RuntimeException('file not found'));


    	$this->setExpectedException('RuntimeException', 'file not found');
    	$this->s()->execScriptFile('FileNotFound');
    }

    public function testExecScriptFileWithNoDatabaseConnexionThrowException()
    {
    	$this->mock('isConnected', false);
    	$this->mock('getFileContent', "une requetes;\nrequetes 2;\n");

    	$this->setExpectedException('RuntimeException', 'aucune connection à une base de données', 503);
    	$this->s()->execScriptFile('SqlFileButNoDbConnection');
    }

    public function testExecScriptFileWithSuccess()
    {
    	$sql=<<<EOF
--
-- Contenu de la table `Country`
--
TRUNCATE `langkeyword`;

# un commentaire
INSERT INTO `contractlinespend` (`idContractLineSpend`, `originalCurrencyContractLineSpend`, `originalAmountContractLineSpend`, `euroAmountContractLineSpend`, `dateContractLineSpend`, `idContractEntity`, `idContractLine`, `idSpendType`, `insertionDate`, `lastModificationDate`, `isSavingFormulaBase`, `isCancelled`) VALUES
(100, 'eur', 1000, 1000, '2011-09-01', '0050', 10, '1', '2011-09-07', '2011-09-07', 0, 0),
(300, 'eur', 10, 10, '2011-09-01', '0400', 10, '--', '2011-09-07', '2011-09-07', 1, 0),
(700, 'eur', 3000, 3000, '2011-09-01', '0312', 30, '#', '2011-09-07', '2011-09-07', 0, 0);
EOF;
    	$this->mockGetConnectionSuccess();
    	$this->mock('getFileContent', $sql);
    	$this->mock('executeDirectQuery');
    	$this->mock('executeDirectQuery');

    	$res = $this->s()->execScriptFile('SqlFile');


    	$this->assertTrue($res instanceof \F\Technical\Database\Service);
    	$this->assertEquals( array ("TRUNCATE `langkeyword`"),
    			$this->m()->getCallArgs('executeDirectQuery'));
    	$this->assertEquals( array ("INSERT INTO `contractlinespend` (`idContractLineSpend`, `originalCurrencyContractLineSpend`, `originalAmountContractLineSpend`, `euroAmountContractLineSpend`, `dateContractLineSpend`, `idContractEntity`, `idContractLine`, `idSpendType`, `insertionDate`, `lastModificationDate`, `isSavingFormulaBase`, `isCancelled`) VALUES (100, 'eur', 1000, 1000, '2011-09-01', '0050', 10, '1', '2011-09-07', '2011-09-07', 0, 0), (300, 'eur', 10, 10, '2011-09-01', '0400', 10, '--', '2011-09-07', '2011-09-07', 1, 0), (700, 'eur', 3000, 3000, '2011-09-01', '0312', 30, '#', '2011-09-07', '2011-09-07', 0, 0);"),
    			$this->m()->getCallArgs('executeDirectQuery', 1));
    }

    /**
     * connect
     */
    public function testConnectWithConfigParamSuccess()
    {
        $this->mock('connect', 'connection');
        $this->assertInstanceOfService( $this->s()->connect('uneconfig'));
        $this->assertEquals(array('uneconfig'), $this->m()->getCallArgs('connect'));
    }

    public function testConnectWithoutConfigParamGetDefaultSuccess()
    {
        $this->mock('getConnectConfig', 'uneconfig');
        $this->mock('connect', 'connection');
        $this->assertInstanceOfService( $this->s()->connect());
        $this->assertEquals(array('uneconfig'), $this->m()->getCallArgs('connect'));
    }

    /**
     * checkConnection
     */
    function testCheckConnectionWithNoConnectionThrowRuntimeException()
    {
        $this->mock('isConnected', false);
        $this->setExpectedException('RuntimeException', 'aucune connection à une base de données', 503);
        $this->s()->checkConnection();
    }

    function testCheckConnectionWithSuccess()
    {
        $this->mockGetConnectionSuccess();
        $actual = $this->s()->checkConnection();
        $expected = get_class($this->s());
        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * getLevelTransaction
     */
    public function testGetLevelTransactionWithSuccess()
    {
        $this->assertEquals(0, $this->s()->getTransactionLevel());
    }

    /**
     * beginTransaction
     */
    public function testBeginTransactionWithSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('beginTransaction');
        $actual = $this->s()->beginTransaction();
        $this->assertInstanceOfService( $actual);
    }

    public function testBeginTransactionWithMultiBeginTransactionGetTransactionLevelSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('beginTransaction');
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->assertEquals(3, $this->s()->getTransactionLevel());
    }

    /**
     * rollbackTransaction
     */
    public function testRollbackTransactionWithSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('rollbackTransaction');
        $actual = $this->s()->rollbackTransaction();
        $this->assertInstanceOfService( $actual);
    }

    public function testRollbackTransactionAfterMultiBeginTransactionNotRollbackSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('beginTransaction');
        // comme il ne doit pas y avoir d'appel à rollback on ne le mock pas
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->rollbackTransaction();
        $this->s()->rollbackTransaction();
        $this->assertEquals(1, $this->s()->getTransactionLevel());
    }

    public function testRollbackTransactionAfterSeveralBeginTransactionAndRollbackAsManySuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('beginTransaction');
        $this->mock('rollbackTransaction');
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->rollbackTransaction();
        $this->s()->rollbackTransaction();
        $actual = $this->s()->rollbackTransaction();
        $this->assertInstanceOfService( $actual);
        $this->assertEquals(0, $this->s()->getTransactionLevel());
    }

    public function testRollbackTransactionAfterSeveralBeginTransactionAndMoreRollbackSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('beginTransaction');
    	$this->mock('rollbackTransaction');
    	$this->s()->beginTransaction();
    	$this->s()->beginTransaction();
    	$this->s()->beginTransaction();
    	$this->s()->rollbackTransaction();
    	$this->s()->rollbackTransaction();
    	$this->s()->rollbackTransaction();
    	$actual = $this->s()->rollbackTransaction();
    	$this->assertInstanceOfService( $actual);
    	$this->assertEquals(0, $this->s()->getTransactionLevel());
    }

    /**
     * commitTransaction
     */
    public function testCommitTransactionWithSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('commitTransaction');
        $actual = $this->s()->commitTransaction();
        $this->assertInstanceOfService( $actual);
    }

    public function testCommitTransactionAfterMultiBeginTransactionNotRollbackSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('beginTransaction');
        // comme il ne doit pas y avoir d'appel à rollback on ne le mock pas
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->commitTransaction();
        $this->s()->commitTransaction();
        $this->assertEquals(1, $this->s()->getTransactionLevel());
    }

    public function testCommitTransactionAfterSeveralBeginTransactionAndRollbackAsManySuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('beginTransaction');
        $this->mock('commitTransaction');
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->beginTransaction();
        $this->s()->commitTransaction();
        $this->s()->commitTransaction();
        $actual = $this->s()->commitTransaction();
        $this->assertInstanceOfService( $actual);
        $this->assertEquals(0, $this->s()->getTransactionLevel());
    }

    public function testCommitTransactionAfterSeveralBeginTransactionAndMoreRollbackSuccess()
    {
        $this->mockGetConnectionSuccess();
        $this->mock('beginTransaction');
    	$this->mock('commitTransaction');
    	$this->s()->beginTransaction();
    	$this->s()->beginTransaction();
    	$this->s()->beginTransaction();
    	$this->s()->commitTransaction();
    	$this->s()->commitTransaction();
    	$this->s()->commitTransaction();
    	$actual = $this->s()->commitTransaction();
    	$this->assertInstanceOfService( $actual);
    	$this->assertEquals(0, $this->s()->getTransactionLevel());
    }
}