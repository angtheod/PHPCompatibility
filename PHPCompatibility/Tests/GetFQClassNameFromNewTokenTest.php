<?php
/**
 * Classname determination test file
 *
 * @package PHPCompatibility
 */


/**
 * Classname determination function tests
 *
 * @uses BaseSniffTest
 * @package PHPCompatibility
 */
class GetFQClassNameFromNewTokenTest extends BaseAbstractClassMethodTest
{

    public $filename = 'sniff-examples/utility-functions/get_fqclassname_from_new_token.php';

    /**
     * testGetFQClassNameFromNewToken
     *
     * @group utilityFunctions
     *
     * @requires PHP 5.3
     *
     * @dataProvider dataGetFQClassNameFromNewToken
     *
     * @param int    $stackPtr Stack pointer for a T_NEW token in the test file.
     * @param string $expected The expected fully qualified class name.
     */
    public function testGetFQClassNameFromNewToken($stackPtr, $expected) {
        $result = $this->helperClass->getFQClassNameFromNewToken($this->_phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * dataGetFQClassNameFromNewToken
     *
     * @see testGetFQClassNameFromNewToken()
     *
     * @return array
     */
    public function dataGetFQClassNameFromNewToken()
    {
        return array(
            array(7, '\MyTesting\DateTime'),
            array(16, '\MyTesting\DateTime'),
            array(21, '\DateTime'),
            array(29, '\MyTesting\anotherNS\DateTime'),
            array(38, '\FQNS\DateTime'),
            array(56, '\AnotherTesting\DateTime'),
            array(66, '\AnotherTesting\DateTime'),
            array(72, '\DateTime'),
            array(81, '\AnotherTesting\anotherNS\DateTime'),
            array(91, '\FQNS\DateTime'),
            array(104, '\DateTime'),
            array(109, '\DateTime'),
            array(115, '\AnotherTesting\DateTime'),
        );
    }

}
