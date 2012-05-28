<?php
ini_set('display_errors', 'On');
require_once(__DIR__.'/../../lib/bootstrap.php');

abstract class AbstractRegularUrlTest extends AbstractUrlTest {     
 
    /**
     *
     * @param string $inputUrl
     * @return \webignition\Url\Url 
     */
    protected function newUrl($inputUrl) {
        return new \webignition\Url\Url($inputUrl);
    }
}