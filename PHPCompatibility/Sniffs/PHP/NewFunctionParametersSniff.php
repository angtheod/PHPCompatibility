<?php
/**
 * PHPCompatibility_Sniffs_PHP_NewFunctionParametersSniff.
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Wim Godden <wim.godden@cu.be>
 */

/**
 * PHPCompatibility_Sniffs_PHP_newFunctionParametersSniff.
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Wim Godden <wim.godden@cu.be>
 */
class PHPCompatibility_Sniffs_PHP_NewFunctionParametersSniff extends PHPCompatibility_Sniff
{

    /**
     * If true, forbidden functions will be considered regular expressions.
     *
     * @var bool
     */
    protected $patternMatch = false;
    
    /**
     * A list of new functions, not present in older versions.
     *
     * The array lists : version number with false (not present) or true (present).
     * The index is the location of the parameter in the parameter list, starting at 0 !
     * If's sufficient to list the first version where the function appears.
     *
     * @var array
     */
    public $newFunctionParameters = array(
                                        'dirname' => array(
                                            1 => array(
                                                'name' => 'depth',
                                                '5.6' => false,
                                                '7.0' => true
                                            ),
                                        ),
                                        'unserialize' => array(
                                            1 => array(
                                                'name' => 'options',
                                                '5.6' => false,
                                                '7.0' => true
                                            ),
                                        ),
                                        'session_start' => array(
                                            0 => array(
                                                'name' => 'options',
                                                '5.6' => false,
                                                '7.0' => true
                                            )
                                        ),
                                        'strstr' => array(
                                            2 => array(
                                                'name' => 'before_needle',
                                                '5.2' => false,
                                                '5.3' => true
                                            )
                                        )
                                    );


    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    public $error = false;
    
    /**
     * 
     * @var array
     */
    private $newFunctionParametersNames;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        // Everyone has had a chance to figure out what forbidden functions
        // they want to check for, so now we can cache out the list.
        $this->newFunctionParametersNames = array_keys($this->newFunctionParameters);
    
        if ($this->patternMatch === true) {
            foreach ($this->newFunctionParametersNames as $i => $name) {
                $this->newFunctionParametersNames[$i] = '/'.$name.'/i';
            }
        }
    
        return array(T_STRING);
    }//end register()
    
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $ignore = array(
                T_DOUBLE_COLON,
                T_OBJECT_OPERATOR,
                T_FUNCTION,
                T_CONST,
        );

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if (in_array($tokens[$prevToken]['code'], $ignore) === true) {
            // Not a call to a PHP function.
            return;
        }

        $function = strtolower($tokens[$stackPtr]['content']);

        if (in_array($function, $this->newFunctionParametersNames) === false) {
            return;
        }
        
        if (isset($tokens[$stackPtr + 1]) && $tokens[$stackPtr + 1]['type'] == 'T_OPEN_PARENTHESIS') {
            $closeParenthesis = $tokens[$stackPtr + 1]['parenthesis_closer'];

            $nextComma = $stackPtr + 1;
            $cnt = 0;
            while ($nextComma = $phpcsFile->findNext(array(T_COMMA, T_CLOSE_PARENTHESIS), $nextComma + 1, $closeParenthesis + 1)) {
                if ($tokens[$nextComma]['type'] == 'T_CLOSE_PARENTHESIS' && $nextComma != $closeParenthesis) {
                    continue;
                }
                if (isset($this->newFunctionParameters[$function][$cnt])) {
                    $this->addError($phpcsFile, $nextComma, $function, $cnt);
                }
                $cnt++;
            }
            
        }
    }//end process()


    /**
     * Generates the error or wanrning for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the function
     *                                        in the token array.
     * @param string               $function  The name of the function.
     * @param string               $pattern   The pattern used for the match.
     *
     * @return void
     */
    protected function addError($phpcsFile, $stackPtr, $function, $parameterLocation)
    {
        $error = '';

        $this->error = false;
        foreach ($this->newFunctionParameters[$function][$parameterLocation] as $version => $present) {
            if ($version != 'name' && $this->supportsBelow($version)) {
                if ($present === false) {
                    $this->error = true;
                    $error .= 'in PHP version ' . $version . ' or earlier';
                }
            }
        }
        
        if (strlen($error) > 0) {
            $error = 'The function ' . $function . ' does not have a parameter ' . $this->newFunctionParameters[$function][$parameterLocation]['name'] . ' ' . $error;

            if ($this->error === true) {
                $phpcsFile->addError($error, $stackPtr);
            } else {
                $phpcsFile->addWarning($error, $stackPtr);
            }
        }

    }//end addError()

}//end class
