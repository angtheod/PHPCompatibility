<?php
/**
 * PHPCompatibility_Sniffs_PHP_ForbiddenFunctionParametersWithSameName.
 *
 * PHP version 7.0
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Wim Godden <wim@cu.be>
 */

/**
 * PHPCompatibility_Sniffs_PHP_ForbiddenFunctionParametersWithSameName.
 *
 * Functions can not have multiple parameters with the same name since PHP 7.0
 *
 * PHP version 7.0
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Wim Godden <wim@cu.be>
 */
class PHPCompatibility_Sniffs_PHP_ForbiddenFunctionParametersWithSameNameSniff extends PHPCompatibility_Sniff
{

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    protected $error = true;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);

    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.0')) {
            $tokens = $phpcsFile->getTokens();
            $token  = $tokens[$stackPtr];
            // Skip function without body.
            if (isset($token['scope_opener']) === false) {
                return;
            }

            // Get function name.
            $methodName = $phpcsFile->getDeclarationName($stackPtr);
            
            // Get all parameters from method signature.
            $paramNames = array();
            foreach ($phpcsFile->getMethodParameters($stackPtr) as $param) {
                $paramNames[] = strtolower($param['name']);
            }
            
            if (count($paramNames) != count(array_unique($paramNames))) {
                $phpcsFile->addError('Functions can not have multiple parameters with the same name since PHP 7.0', $stackPtr);
            }
        }
    }//end process()

}//end class
