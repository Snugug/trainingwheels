<?php

namespace TrainingWheels\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Exception;

/**
 * Log message severity
 */
define('L_DEBUG', 0);
define('L_INFO', 1);
define('L_WARNING', 2);
define('L_ERROR', 3);
define('L_CRITICAL', 4);
define('L_ALERT', 5);

class Log {
  public static $instance = NULL;
  private $monolog = NULL;

  /**
   * Constructor.
   */
  public function __construct($monolog) {
    $this->monolog = $monolog;
  }

  /**
   * Create the logger if it's not there yet, and log.
   */
  public static function log($message, $level) {
    if (!isset(self::$instance)) {
      throw new Exception('Training Wheels Log requires you to create a singleton before calling Log::log()');
    }
    $self = self::$instance;
    switch ($level) {
      case L_DEBUG:
        $self->monolog->addDebug($message);
        break;
      case L_INFO:
        $self->monolog->addInfo($message);
        break;
      case L_WARNING:
        $self->monolog->addWarning($message);
        break;
      case L_ERROR:
        $self->monolog->addError($message);
        break;
      case L_CRITICAL:
        $self->monolog->addCritical($message);
        break;
      case L_ALERT:
        $self->monolog->addAlert($message);
        break;
    }
  }

  /**
   * Prevent people creating objects of this type instead of using singleton.
   */
  public function __clone() {
    trigger_error('Clone is not allowed.', E_USER_ERROR);
  }

  /**
   * Prevent people serializing which would be another way to clone the object.
   */
  public function __wakeup() {
    trigger_error('Unserializing is not allowed.', E_USER_ERROR);
  }
}
