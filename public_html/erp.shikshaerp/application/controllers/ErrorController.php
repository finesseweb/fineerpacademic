<?php

class ErrorController extends Zend_Controller_Action {

 private $_phpSettings = NULL;
         
 public function init() {

        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
         $this->_phpSettings= $zendConfig->phpSettings->toArray();
         $this->_helper->layout->setLayout("error");

         $config = $zendConfig->mainconfig->toArray();
         $this->view->mainconfig = $config;
    }

  public function errorAction() {
      
      
    $errors = $this->_getParam('error_handler');

    if (!$errors || !$errors instanceof ArrayObject) {
      $this->view->message = 'You have reached the error page';
      return;
    }
$this->view->errorview = $this->_phpSettings;
    switch ($errors->type) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        // 404 error -- controller or action not found
        $this->getResponse()->setHttpResponseCode(404);
        $priority = Zend_Log::NOTICE;
        $this->view->error_code = $this->getResponse()->getHttpResponseCode();
        $this->view->message = "Page Not Found";
        $this->renderScript('error/error_404.phtml');
        break;
      default:
        // application error
       // print_r($this->getResponse());
        $this->getResponse()->setHttpResponseCode(500);
        $priority = Zend_Log::CRIT;
        $this->view->error_code = $this->getResponse()->getHttpResponseCode();
        $this->view->message = 'Application error';
        if ($log = $this->getLog()) {
        $log->log($this->view->message, $priority, $errors->exception);
        $log->log('Request Parameters', $priority, $errors->request->getParams());
        $this->renderScript('error/error_500.phtml');
        }

    // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
                $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
        $this->view->error_code = $this->getResponse()->getHttpResponseCode();
        $this->renderScript('error/error_500.phtml');
        break;
    }

    // Log exception, if logger available
    if ($log = $this->getLog()) {
      $log->log($this->view->message, $priority, $errors->exception);
      $log->log('Request Parameters', $priority, $errors->request->getParams());
    }

    // conditionally display exceptions
    if ($this->getInvokeArg('displayExceptions') == true) {
      $this->view->exception = $errors->exception;
    }

    $this->view->request = $errors->request;
  }

  public function getLog() {
    $bootstrap = $this->getInvokeArg('bootstrap');
    if (!$bootstrap->hasResource('Log')) {
      return false;
    }
    $log = $bootstrap->getResource('Log');
    return $log;
  }

}


