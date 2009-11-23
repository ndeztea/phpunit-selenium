<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2009, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.3.0
 */

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__, 'PHPUNIT');

/**
 * Implementation of the Selenium RC client/server protocol.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.3.0
 */
class PHPUnit_Extensions_SeleniumTestCase_Driver
{
    /**
     * @var    PHPUnit_Extensions_SeleniumTestCase
     */
    protected $testCase;

    /**
     * @var    string
     */
    protected $testId;

    /**
     * @var    string
     */
    protected $name;

    /**
     * @var    string
     */
    protected $browser;

    /**
     * @var    string
     */
    protected $browserUrl;

    /**
     * @var    boolean
     */
    protected $collectCodeCoverageInformation = FALSE;

    /**
     * @var    string
     */
    protected $host = 'localhost';

    /**
     * @var    integer
     */
    protected $port = 4444;

    /**
     * @var    integer
     */
    protected $httpTimeout = 45;

    /**
     * @var    integer
     */
    protected $seleniumTimeout = 30;

    /**
     * @var    array
     */
    protected $sessionId;

    /**
     * @var    integer
     */
    protected $sleep = 0;

    /**
     * @var    boolean
     */
    protected $useWaitForPageToLoad = TRUE;

    /**
     * @var    boolean
     */
    protected $wait = 5;

    /**
     * @var array
     */
    protected static $autoGeneratedCommands = array();

    /**
     * @var array
     */
    protected $commands = array();

    /**
     * @var array
     */
    protected $verificationErrors = array();

    public function __construct()
    {
        if (empty(self::$autoGeneratedCommands)) {
            self::autoGenerateCommands();
        }
    }

    /**
     * @return string
     */
    public function start()
    {
        if ($this->browserUrl == NULL) {
            throw new PHPUnit_Framework_Exception(
              'setBrowserUrl() needs to be called before start().'
            );
        }

        if (!isset($this->sessionId)) {
            $this->sessionId = $this->getString(
              'getNewBrowserSession',
              array($this->browser, $this->browserUrl)
            );

            $this->doCommand('setTimeout', array($this->seleniumTimeout * 1000));
        }

        return $this->sessionId;
    }

    /**
     */
    public function stop()
    {
        if (!isset($this->sessionId)) {
            return;
        }

        $this->doCommand('testComplete');

        $this->sessionId = NULL;
    }

    /**
     * @param  boolean $flag
     * @throws InvalidArgumentException
     */
    public function setCollectCodeCoverageInformation($flag)
    {
        if (!is_bool($flag)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'boolean');
        }

        $this->collectCodeCoverageInformation = $flag;
    }

    /**
     * @param  PHPUnit_Extensions_SeleniumTestCase $testCase
     */
    public function setTestCase(PHPUnit_Extensions_SeleniumTestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * @param  integer $testId
     */
    public function setTestId($testId)
    {
        $this->testId = $testId;
    }

    /**
     * @param  string $name
     * @throws InvalidArgumentException
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        $this->name = $name;
    }

    /**
     * @param  string $browser
     * @throws InvalidArgumentException
     */
    public function setBrowser($browser)
    {
        if (!is_string($browser)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        $this->browser = $browser;
    }

    /**
     * @param  string $browserUrl
     * @throws InvalidArgumentException
     */
    public function setBrowserUrl($browserUrl)
    {
        if (!is_string($browserUrl)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        $this->browserUrl = $browserUrl;
    }

    /**
     * @param  string $host
     * @throws InvalidArgumentException
     */
    public function setHost($host)
    {
        if (!is_string($host)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
        }

        $this->host = $host;
    }

    /**
     * @param  integer $port
     * @throws InvalidArgumentException
     */
    public function setPort($port)
    {
        if (!is_int($port)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
        }

        $this->port = $port;
    }

    /**
     * @param  integer $timeout for Selenium RC in seconds
     * @throws InvalidArgumentException
     */
    public function setTimeout($timeout)
    {
        if (!is_int($timeout)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
        }

        $this->seleniumTimeout = $timeout;
    }

    /**
     * @param  integer $timeout for HTTP connection to Selenium RC in seconds
     * @throws InvalidArgumentException
     */
    public function setHttpTimeout($timeout)
    {
        if (!is_int($timeout)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
        }

        $this->httpTimeout = $timeout;
    }

    /**
     * @param  integer $seconds
     * @throws InvalidArgumentException
     */
    public function setSleep($seconds)
    {
        if (!is_int($seconds)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
        }

        $this->sleep = $seconds;
    }

    /**
     * Sets the number of seconds to sleep() after *AndWait commands
     * when setWaitForPageToLoad(FALSE) is used.
     *
     * @param  integer $seconds
     * @throws InvalidArgumentException
     */
    public function setWait($seconds)
    {
        if (!is_int($seconds)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
        }

        $this->wait = $seconds;
    }

    /**
     * Sets whether waitForPageToLoad (TRUE) or sleep() (FALSE)
     * is used after *AndWait commands.
     *
     * @param  boolean $flag
     * @throws InvalidArgumentException
     */
    public function setWaitForPageToLoad($flag)
    {
        if (!is_bool($flag)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'boolean');
        }

        $this->useWaitForPageToLoad = $flag;
    }

    /**
     * This method implements the Selenium RC protocol.
     *
     * @param  string $command
     * @param  array  $arguments
     * @return mixed
     * @method unknown  addLocationStrategy()
     * @method unknown  addLocationStrategyAndWait()
     * @method unknown  addScript()
     * @method unknown  addScriptAndWait()
     * @method unknown  addSelection()
     * @method unknown  addSelectionAndWait()
     * @method unknown  allowNativeXpath()
     * @method unknown  allowNativeXpathAndWait()
     * @method unknown  altKeyDown()
     * @method unknown  altKeyDownAndWait()
     * @method unknown  altKeyUp()
     * @method unknown  altKeyUpAndWait()
     * @method unknown  answerOnNextPrompt()
     * @method unknown  assignId()
     * @method unknown  assignIdAndWait()
     * @method unknown  attachFile()
     * @method unknown  break()
     * @method unknown  captureEntirePageScreenshot()
     * @method unknown  captureEntirePageScreenshotAndWait()
     * @method unknown  captureEntirePageScreenshotToStringAndWait()
     * @method unknown  captureScreenshotAndWait()
     * @method unknown  captureScreenshotToStringAndWait()
     * @method unknown  check()
     * @method unknown  checkAndWait()
     * @method unknown  chooseCancelOnNextConfirmation()
     * @method unknown  chooseCancelOnNextConfirmationAndWait()
     * @method unknown  chooseOkOnNextConfirmation()
     * @method unknown  chooseOkOnNextConfirmationAndWait()
     * @method unknown  click()
     * @method unknown  clickAndWait()
     * @method unknown  clickAt()
     * @method unknown  clickAtAndWait()
     * @method unknown  close()
     * @method unknown  contextMenu()
     * @method unknown  contextMenuAndWait()
     * @method unknown  contextMenuAt()
     * @method unknown  contextMenuAtAndWait()
     * @method unknown  controlKeyDown()
     * @method unknown  controlKeyDownAndWait()
     * @method unknown  controlKeyUp()
     * @method unknown  controlKeyUpAndWait()
     * @method unknown  createCookie()
     * @method unknown  createCookieAndWait()
     * @method unknown  deleteAllVisibleCookies()
     * @method unknown  deleteAllVisibleCookiesAndWait()
     * @method unknown  deleteCookie()
     * @method unknown  deleteCookieAndWait()
     * @method unknown  deselectPopUp()
     * @method unknown  deselectPopUpAndWait()
     * @method unknown  doubleClick()
     * @method unknown  doubleClickAndWait()
     * @method unknown  doubleClickAt()
     * @method unknown  doubleClickAtAndWait()
     * @method unknown  dragAndDrop()
     * @method unknown  dragAndDropAndWait()
     * @method unknown  dragAndDropToObject()
     * @method unknown  dragAndDropToObjectAndWait()
     * @method unknown  dragDrop()
     * @method unknown  dragDropAndWait()
     * @method unknown  echo()
     * @method unknown  fireEvent()
     * @method unknown  fireEventAndWait()
     * @method unknown  focus()
     * @method unknown  focusAndWait()
     * @method string   getAlert()
     * @method array    getAllButtons()
     * @method array    getAllFields()
     * @method array    getAllLinks()
     * @method array    getAllWindowIds()
     * @method array    getAllWindowNames()
     * @method array    getAllWindowTitles()
     * @method string   getAttribute()
     * @method array    getAttributeFromAllWindows()
     * @method string   getBodyText()
     * @method string   getConfirmation()
     * @method string   getCookie()
     * @method string   getCookieByName()
     * @method integer  getCursorPosition()
     * @method integer  getElementHeight()
     * @method integer  getElementIndex()
     * @method integer  getElementPositionLeft()
     * @method integer  getElementPositionTop()
     * @method integer  getElementWidth()
     * @method string   getEval()
     * @method string   getExpression()
     * @method string   getHtmlSource()
     * @method string   getLocation()
     * @method string   getLogMessages()
     * @method integer  getMouseSpeed()
     * @method string   getPrompt()
     * @method array    getSelectOptions()
     * @method string   getSelectedId()
     * @method array    getSelectedIds()
     * @method string   getSelectedIndex()
     * @method array    getSelectedIndexes()
     * @method string   getSelectedLabel()
     * @method array    getSelectedLabels()
     * @method string   getSelectedValue()
     * @method array    getSelectedValues()
     * @method unknown  getSpeed()
     * @method unknown  getSpeedAndWait()
     * @method string   getTable()
     * @method string   getText()
     * @method string   getTitle()
     * @method string   getValue()
     * @method boolean  getWhetherThisFrameMatchFrameExpression()
     * @method boolean  getWhetherThisWindowMatchWindowExpression()
     * @method integer  getXpathCount()
     * @method unknown  goBack()
     * @method unknown  goBackAndWait()
     * @method unknown  highlight()
     * @method unknown  highlightAndWait()
     * @method unknown  ignoreAttributesWithoutValue()
     * @method unknown  ignoreAttributesWithoutValueAndWait()
     * @method boolean  isAlertPresent()
     * @method boolean  isChecked()
     * @method boolean  isConfirmationPresent()
     * @method boolean  isCookiePresent()
     * @method boolean  isEditable()
     * @method boolean  isElementPresent()
     * @method boolean  isOrdered()
     * @method boolean  isPromptPresent()
     * @method boolean  isSomethingSelected()
     * @method boolean  isTextPresent()
     * @method boolean  isVisible()
     * @method unknown  keyDown()
     * @method unknown  keyDownAndWait()
     * @method unknown  keyDownNative()
     * @method unknown  keyDownNativeAndWait()
     * @method unknown  keyPress()
     * @method unknown  keyPressAndWait()
     * @method unknown  keyPressNative()
     * @method unknown  keyPressNativeAndWait()
     * @method unknown  keyUp()
     * @method unknown  keyUpAndWait()
     * @method unknown  keyUpNative()
     * @method unknown  keyUpNativeAndWait()
     * @method unknown  metaKeyDown()
     * @method unknown  metaKeyDownAndWait()
     * @method unknown  metaKeyUp()
     * @method unknown  metaKeyUpAndWait()
     * @method unknown  mouseDown()
     * @method unknown  mouseDownAndWait()
     * @method unknown  mouseDownAt()
     * @method unknown  mouseDownAtAndWait()
     * @method unknown  mouseMove()
     * @method unknown  mouseMoveAndWait()
     * @method unknown  mouseMoveAt()
     * @method unknown  mouseMoveAtAndWait()
     * @method unknown  mouseOut()
     * @method unknown  mouseOutAndWait()
     * @method unknown  mouseOver()
     * @method unknown  mouseOverAndWait()
     * @method unknown  mouseUp()
     * @method unknown  mouseUpAndWait()
     * @method unknown  mouseUpAt()
     * @method unknown  mouseUpAtAndWait()
     * @method unknown  mouseUpRight()
     * @method unknown  mouseUpRightAndWait()
     * @method unknown  mouseUpRightAt()
     * @method unknown  mouseUpRightAtAndWait()
     * @method unknown  open()
     * @method unknown  openWindow()
     * @method unknown  openWindowAndWait()
     * @method unknown  pause()
     * @method unknown  refresh()
     * @method unknown  refreshAndWait()
     * @method unknown  removeAllSelections()
     * @method unknown  removeAllSelectionsAndWait()
     * @method unknown  removeScript()
     * @method unknown  removeScriptAndWait()
     * @method unknown  removeSelection()
     * @method unknown  removeSelectionAndWait()
     * @method unknown  retrieveLastRemoteControlLogs()
     * @method unknown  rollup()
     * @method unknown  rollupAndWait()
     * @method unknown  runScript()
     * @method unknown  runScriptAndWait()
     * @method unknown  select()
     * @method unknown  selectAndWait()
     * @method unknown  selectFrame()
     * @method unknown  selectPopUp()
     * @method unknown  selectPopUpAndWait()
     * @method unknown  selectWindow()
     * @method unknown  setBrowserLogLevel()
     * @method unknown  setBrowserLogLevelAndWait()
     * @method unknown  setContext()
     * @method unknown  setCursorPosition()
     * @method unknown  setCursorPositionAndWait()
     * @method unknown  setMouseSpeed()
     * @method unknown  setMouseSpeedAndWait()
     * @method unknown  setSpeed()
     * @method unknown  setSpeedAndWait()
     * @method unknown  shiftKeyDown()
     * @method unknown  shiftKeyDownAndWait()
     * @method unknown  shiftKeyUp()
     * @method unknown  shiftKeyUpAndWait()
     * @method unknown  shutDownSeleniumServer()
     * @method unknown  store()
     * @method unknown  submit()
     * @method unknown  submitAndWait()
     * @method unknown  type()
     * @method unknown  typeAndWait()
     * @method unknown  typeKeys()
     * @method unknown  typeKeysAndWait()
     * @method unknown  uncheck()
     * @method unknown  uncheckAndWait()
     * @method unknown  useXpathLibrary()
     * @method unknown  useXpathLibraryAndWait()
     * @method unknown  waitForCondition()
     * @method unknown  waitForPageToLoad()
     * @method unknown  waitForPopUp()
     * @method unknown  windowFocus()
     * @method unknown  windowMaximize()
     */
    public function __call($command, $arguments)
    {
        $wait = FALSE;

        if (substr($command, -7, 7) == 'AndWait') {
            $command = substr($command, 0, -7);
            $wait    = TRUE;
        }

        switch ($command) {
            case 'addLocationStrategy':
            case 'addScript':
            case 'addSelection':
            case 'allowNativeXpath':
            case 'altKeyDown':
            case 'altKeyUp':
            case 'answerOnNextPrompt':
            case 'assignId':
            case 'attachFile':
            case 'break':
            case 'captureEntirePageScreenshot':
            case 'captureEntirePageScreenshotToString':
            case 'captureScreenshot':
            case 'captureScreenshotToString':
            case 'check':
            case 'chooseCancelOnNextConfirmation':
            case 'chooseOkOnNextConfirmation':
            case 'click':
            case 'clickAt':
            case 'close':
            case 'contextMenu':
            case 'contextMenuAt':
            case 'controlKeyDown':
            case 'controlKeyUp':
            case 'createCookie':
            case 'deleteAllVisibleCookies':
            case 'deleteCookie':
            case 'deselectPopUp':
            case 'doubleClick':
            case 'doubleClickAt':
            case 'dragAndDrop':
            case 'dragAndDropToObject':
            case 'dragDrop':
            case 'echo':
            case 'fireEvent':
            case 'focus':
            case 'goBack':
            case 'highlight':
            case 'ignoreAttributesWithoutValue':
            case 'keyDown':
            case 'keyDownNative':
            case 'keyPress':
            case 'keyPressNative':
            case 'keyUp':
            case 'keyUpNative':
            case 'metaKeyDown':
            case 'metaKeyUp':
            case 'mouseDown':
            case 'mouseDownAt':
            case 'mouseMove':
            case 'mouseMoveAt':
            case 'mouseOut':
            case 'mouseOver':
            case 'mouseUp':
            case 'mouseUpAt':
            case 'mouseUpRight':
            case 'mouseUpRightAt':
            case 'open':
            case 'openWindow':
            case 'pause':
            case 'refresh':
            case 'removeAllSelections':
            case 'removeScript':
            case 'removeSelection':
            case 'retrieveLastRemoteControlLogs':
            case 'rollup':
            case 'runScript':
            case 'select':
            case 'selectFrame':
            case 'selectPopUp':
            case 'selectWindow':
            case 'setBrowserLogLevel':
            case 'setContext':
            case 'setCursorPosition':
            case 'setMouseSpeed':
            case 'setSpeed':
            case 'shiftKeyDown':
            case 'shiftKeyUp':
            case 'shutDownSeleniumServer':
            case 'store':
            case 'submit':
            case 'type':
            case 'typeKeys':
            case 'uncheck':
            case 'useXpathLibrary':
            case 'windowFocus':
            case 'windowMaximize':
            case isset(self::$autoGeneratedCommands[$command]): {
                // Pre-Command Actions
                switch ($command) {
                    case 'open':
                    case 'openWindow': {
                        if ($this->collectCodeCoverageInformation) {
                            $this->deleteCookie('PHPUNIT_SELENIUM_TEST_ID', 'path=/');

                            $this->createCookie(
                              'PHPUNIT_SELENIUM_TEST_ID=' . $this->testId,
                              'path=/'
                            );
                        }
                    }
                    break;
                    case 'store':
                        // store is a synonym of storeExpression
                        // and RC only understands storeExpression
                        $command = 'storeExpression';
                    break;
                }

                if (isset(self::$autoGeneratedCommands[$command]) && self::$autoGeneratedCommands[$command]['functionHelper']) {
                    $helperArguments = array($command, $arguments, self::$autoGeneratedCommands[$command]);
                    call_user_func_array(array($this, self::$autoGeneratedCommands[$command]['functionHelper']), $helperArguments);
                } else {
                    $this->doCommand($command, $arguments);
                }

                // Post-Command Actions
                switch ($command) {
                    case 'addLocationStrategy':
                    case 'allowNativeXpath':
                    case 'assignId':
                    case 'captureEntirePageScreenshot':
                    case 'captureScreenshot':
                    case 'captureScreenshotToString': {
                        // intentionally empty
                    }
                    break;

                    default: {
                        if ($wait) {
                            if ($this->useWaitForPageToLoad) {
                                $this->waitForPageToLoad($this->seleniumTimeout * 1000);
                            } else {
                                sleep($this->wait);
                            }
                        }

                        if ($this->sleep > 0) {
                            sleep($this->sleep);
                        }

                        $this->testCase->runDefaultAssertions($command);
                    }
                }
            }
            break;

            case 'getWhetherThisFrameMatchFrameExpression':
            case 'getWhetherThisWindowMatchWindowExpression':
            case 'isAlertPresent':
            case 'isChecked':
            case 'isConfirmationPresent':
            case 'isCookiePresent':
            case 'isEditable':
            case 'isElementPresent':
            case 'isOrdered':
            case 'isPromptPresent':
            case 'isSomethingSelected':
            case 'isTextPresent':
            case 'isVisible': {
                return $this->getBoolean($command, $arguments);
            }
            break;

            case 'getCursorPosition':
            case 'getElementHeight':
            case 'getElementIndex':
            case 'getElementPositionLeft':
            case 'getElementPositionTop':
            case 'getElementWidth':
            case 'getMouseSpeed':
            case 'getSpeed':
            case 'getXpathCount': {
                $result = $this->getNumber($command, $arguments);

                if ($wait) {
                    $this->waitForPageToLoad($this->seleniumTimeout * 1000);
                }

                return $result;
            }
            break;

            case 'getAlert':
            case 'getAttribute':
            case 'getBodyText':
            case 'getConfirmation':
            case 'getCookie':
            case 'getCookieByName':
            case 'getEval':
            case 'getExpression':
            case 'getHtmlSource':
            case 'getLocation':
            case 'getLogMessages':
            case 'getPrompt':
            case 'getSelectedId':
            case 'getSelectedIndex':
            case 'getSelectedLabel':
            case 'getSelectedValue':
            case 'getTable':
            case 'getText':
            case 'getTitle':
            case 'getValue': {
                $result = $this->getString($command, $arguments);

                if ($wait) {
                    $this->waitForPageToLoad($this->seleniumTimeout * 1000);
                }

                return $result;
            }
            break;

            case 'getAllButtons':
            case 'getAllFields':
            case 'getAllLinks':
            case 'getAllWindowIds':
            case 'getAllWindowNames':
            case 'getAllWindowTitles':
            case 'getAttributeFromAllWindows':
            case 'getSelectedIds':
            case 'getSelectedIndexes':
            case 'getSelectedLabels':
            case 'getSelectedValues':
            case 'getSelectOptions': {
                $result = $this->getStringArray($command, $arguments);

                if ($wait) {
                    $this->waitForPageToLoad($this->seleniumTimeout * 1000);
                }

                return $result;
            }
            break;

            case 'waitForCondition':
            case 'waitForFrameToLoad':
            case 'waitForPopUp': {
                if (count($arguments) == 1) {
                    $arguments[] = $this->seleniumTimeout * 1000;
                }

                $this->doCommand($command, $arguments);
                $this->testCase->runDefaultAssertions($command);
            }
            break;

            case 'waitForPageToLoad': {
                if (empty($arguments)) {
                    $arguments[] = $this->seleniumTimeout * 1000;
                }

                $this->doCommand($command, $arguments);
                $this->testCase->runDefaultAssertions($command);
            }
            break;

            default: {
                $this->stop();

                throw new BadMethodCallException(
                  "Method $command not defined."
                );
            }
        }
    }

    /**
     * Send a command to the Selenium RC server.
     *
     * @param  string $command
     * @param  array  $arguments
     * @return string
     * @author Shin Ohno <ganchiku@gmail.com>
     * @author Bjoern Schotte <schotte@mayflower.de>
     */
    protected function doCommand($command, array $arguments = array())
    {
        if (!ini_get('allow_url_fopen')) {
            throw new PHPUnit_Framework_Exception(
              'Could not connect to the Selenium RC server because allow_url_fopen is disabled.'
            );
        }

        $url = sprintf(
          'http://%s:%s/selenium-server/driver/?cmd=%s',
          $this->host,
          $this->port,
          urlencode($command)
        );

        $numArguments = count($arguments);

        for ($i = 0; $i < $numArguments; $i++) {
            $argNum = strval($i + 1);
            $url .= sprintf('&%s=%s', $argNum, urlencode(trim($arguments[$i])));
        }

        if (isset($this->sessionId)) {
            $url .= sprintf('&%s=%s', 'sessionId', $this->sessionId);
        }

        $this->commands[] = sprintf('%s(%s)', $command, join(', ', $arguments));

        $context = stream_context_create(
          array(
            'http' => array(
              'timeout' => $this->httpTimeout
            )
          )
        );

        $handle = @fopen($url, 'r', FALSE, $context);

        if (!$handle) {
            throw new PHPUnit_Framework_Exception(
              'Could not connect to the Selenium RC server.'
            );
        }

        stream_set_blocking($handle, 1);
        stream_set_timeout($handle, 0, $this->httpTimeout * 1000);

        $info     = stream_get_meta_data($handle);
        $response = '';

        while (!$info['eof'] && !$info['timed_out']) {
            $response .= fgets($handle, 4096);
            $info = stream_get_meta_data($handle);
        }

        fclose($handle);

        if (!preg_match('/^OK/', $response)) {
            $this->stop();

            throw new PHPUnit_Framework_Exception(
              sprintf(
                "Response from Selenium RC server for %s.\n%s.\n",
                $this->commands[count($this->commands)-1],
                $response
              )
            );
        }

        return $response;
    }

    /**
     * Send a command to the Selenium RC server and treat the result
     * as a boolean.
     *
     * @param  string $command
     * @param  array  $arguments
     * @return boolean
     * @author Shin Ohno <ganchiku@gmail.com>
     * @author Bjoern Schotte <schotte@mayflower.de>
     */
    protected function getBoolean($command, array $arguments)
    {
        $result = $this->getString($command, $arguments);

        switch ($result) {
            case 'true':  return TRUE;

            case 'false': return FALSE;

            default: {
                $this->stop();

                throw new PHPUnit_Framework_Exception(
                  'Result is neither "true" nor "false": ' . PHPUnit_Util_Type::toString($result, TRUE)
                );
            }
        }
    }

    /**
     * Send a command to the Selenium RC server and treat the result
     * as a number.
     *
     * @param  string $command
     * @param  array  $arguments
     * @return numeric
     * @author Shin Ohno <ganchiku@gmail.com>
     * @author Bjoern Schotte <schotte@mayflower.de>
     */
    protected function getNumber($command, array $arguments)
    {
        $result = $this->getString($command, $arguments);

        if (!is_numeric($result)) {
            $this->stop();

            throw new PHPUnit_Framework_Exception(
              'Result is not numeric: ' . PHPUnit_Util_Type::toString($result, TRUE)
            );
        }

        return $result;
    }

    /**
     * Send a command to the Selenium RC server and treat the result
     * as a string.
     *
     * @param  string $command
     * @param  array  $arguments
     * @return string
     * @author Shin Ohno <ganchiku@gmail.com>
     * @author Bjoern Schotte <schotte@mayflower.de>
     */
    protected function getString($command, array $arguments)
    {
        try {
            $result = $this->doCommand($command, $arguments);
        }

        catch (RuntimeException $e) {
            $this->stop();

            throw $e;
        }

        return (strlen($result) > 3) ? substr($result, 3) : '';
    }

    /**
     * Send a command to the Selenium RC server and treat the result
     * as an array of strings.
     *
     * @param  string $command
     * @param  array  $arguments
     * @return array
     * @author Shin Ohno <ganchiku@gmail.com>
     * @author Bjoern Schotte <schotte@mayflower.de>
     */
    protected function getStringArray($command, array $arguments)
    {
        $csv     = $this->getString($command, $arguments);
        $token   = '';
        $tokens  = array();
        $letters = preg_split('//', $csv, -1, PREG_SPLIT_NO_EMPTY);
        $count   = count($letters);

        for ($i = 0; $i < $count; $i++) {
            $letter = $letters[$i];

            switch($letter) {
                case '\\': {
                    $letter = $letters[++$i];
                    $token .= $letter;
                }
                break;

                case ',': {
                    $tokens[] = $token;
                    $token    = '';
                }
                break;

                default: {
                    $token .= $letter;
                }
            }
        }

        $tokens[] = $token;

        return $tokens;
    }

    public function getVerificationErrors()
    {
        return $this->verificationErrors;
    }

    public function clearVerificationErrors()
    {
        $this->verificationErrors = array();
    }

    protected function assertCommand($command, $arguments, $info)
    {
        $method = $info['originalMethod'];
        $result = $this->__call($method, $arguments);

        if ($info['isBoolean']) {
            if (!isset($info['negative']) || !$info['negative']) {
                PHPUnit_Framework_Assert::assertTrue($result);
            } else {
                PHPUnit_Framework_Assert::assertFalse($result);
            }
        } else {
            $expected = array_pop($arguments);

            if (strpos($expected, 'exact:') === 0) {
                $expected = substr($expected, strlen('exact:'));

                if (!isset($info['negative']) || !$info['negative']) {
                    PHPUnit_Framework_Assert::assertEquals($expected, $result);
                } else {
                    PHPUnit_Framework_Assert::assertNotEquals($expected, $result);
                }
            } else {
                if (strpos($expected, 'regexp:') === 0) {
                    $expected = substr($expected, strlen('regexp:'));
                } else {
                    if (strpos($expected, 'glob:') === 0) {
                        $expected = substr($expected, strlen('glob:'));
                    }

                    $expected = str_replace(
                      array('*', '?'), array('.*', '.?'), $expected
                    );
                }

                $expected = str_replace('/', '\/', $expected);

                if (!isset($info['negative']) || !$info['negative']) {
                    PHPUnit_Framework_Assert::assertRegExp(
                      '/' . $expected . '/', $result
                    );
                } else {
                    PHPUnit_Framework_Assert::assertNotRegExp(
                      '/' . $expected . '/', $result
                    );
                }
            }
        }
    }

    protected function verifyCommand($command, $arguments, $info)
    {
        try {
            $this->assertCommand($command, $arguments, $info);
        }

        catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
    }

    protected function waitForCommand($command, $arguments, $info)
    {
        for ($second = 0; ; $second++) {
            if ($second > $this->httpTimeout) {
                PHPUnit_Framework_Assert::fail('timeout');
            }

            try {
                $this->assertCommand($command, $arguments, $info);
                return;
            }

            catch (Exception $e) {
            }

            sleep(1);
        }
    }

    /**
     * Parses the docblock of PHPUnit_Extensions_SeleniumTestCase_Driver::__call
     * for get*(), is*(), assert*(), verify*(), assertNot*(), verifyNot*(),
     * store*(), waitFor*(), and waitForNot*() methods.
     */
    protected static function autoGenerateCommands()
    {
        $method     = new ReflectionMethod(__CLASS__, '__call');
        $docComment = $method->getDocComment();

        if (preg_match_all('(@method\s+(\w+)\s+([\w]+)\(\))', $docComment, $matches)) {
            foreach ($matches[2] as $method) {
                if (preg_match('/^(get|is)([A-Z].+)$/', $method, $matches)) {
                    $baseName  = $matches[2];
                    $isBoolean = $matches[1] == 'is';

                    if (preg_match('/^(.*)Present$/', $baseName, $matches)) {
                        $notBaseName = $matches[1].'NotPresent';
                    } else {
                        $notBaseName = 'Not'.$baseName;
                    }

                    self::$autoGeneratedCommands['store' . $baseName] = array(
                      'functionHelper' => FALSE
                    );

                    self::$autoGeneratedCommands['assert' . $baseName] = array(
                      'originalMethod' => $method,
                      'isBoolean'      => $isBoolean,
                      'functionHelper' => 'assertCommand'
                    );

                    self::$autoGeneratedCommands['assert' . $notBaseName] = array(
                      'originalMethod' => $method,
                      'isBoolean'      => $isBoolean,
                      'negative'       => TRUE,
                      'functionHelper' => 'assertCommand'
                    );

                    self::$autoGeneratedCommands['verify' . $baseName] = array(
                      'originalMethod' => $method,
                      'isBoolean'      => $isBoolean,
                      'functionHelper' => 'verifyCommand'
                    );

                    self::$autoGeneratedCommands['verify' . $notBaseName] = array(
                      'originalMethod' => $method,
                      'isBoolean'      => $isBoolean,
                      'negative'       => TRUE,
                      'functionHelper' => 'verifyCommand'
                    );

                    self::$autoGeneratedCommands['waitFor' . $baseName] = array(
                      'originalMethod' => $method,
                      'isBoolean'      => $isBoolean,
                      'functionHelper' => 'waitForCommand'
                    );

                    self::$autoGeneratedCommands['waitFor' . $notBaseName] = array(
                      'originalMethod' => $method,
                      'isBoolean'      => $isBoolean,
                      'negative'       => TRUE,
                      'functionHelper' => 'waitForCommand'
                    );
                }
            }
        }
    }
}
?>
