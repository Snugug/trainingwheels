<?php

namespace TrainingWheels\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\MongoDBHandler;
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
  protected static $instance;
  private $main;
  private $actions;
  private $data;

  /**
   * Constructor.
   */
  public function __construct($main, $data) {
    // Main log is where Silex is sending it's information to. We have access to that as well
    // as creating our own MongoDB log entries.
    $this->main = $main;
    $this->data = $data;

    // Add secondary Mongo log streams.
    $actions_log = new Logger('actions');
    $actions_handler = new MongoDBHandler($data->getConnection(), 'trainingwheels', 'logs_actions');
    $actions_log->pushHandler($actions_handler);
    $this->actions = $actions_log;

    self::$instance = $this;
  }

  /**
   * Delete all log entries in the MongoDB store.
   */
  public function removeDBLogs() {
    $this->data->remove('logs_actions', array());
  }

  /**
   * Build a page with log entries from a particular stream.
   */
  public function renderHTML($stream) {
    // Sort by microtime.
    $log_entries = $this->data->findAll('logs_' . $stream, '_id');
    $processed = array();

    foreach ($log_entries as $value) {
      $commands = '';
      if (isset($value->context['commands'])) {
        $commands = implode('<br />&gt; ', $value->context['commands']);
        $commands = '&gt; ' . $commands;
      }

      $processed[] = array(
        'source' => isset($value->context['source']) ? $value->context['source'] : '',
        'result' => isset($value->context['result']) ? $value->context['result'] : '',
        'time' => isset($value->context['time']) ? round($value->context['time'], 4) : '',
        'params' => isset($value->context['params']) ? $value->context['params'] : '',
        'commands' => $commands,
        'layer' => $value->context['layer'],
        'datetime' => $value->datetime,
        'level_name' => strtolower($value->level_name),
        'message' => $value->message,
      );
    }

    return $processed;
  }

  /**
   * Static logging method.
   */
  public static function log($message, $level, $stream = 'main', $context = array()) {
    if (!isset(self::$instance)) {
      throw new Exception('Training Wheels Log requires you to create a singleton before calling Log::log()');
    }
    $self = self::$instance;

    if (!isset($self->$stream)) {
      throw new Exception("Unrecognized log stream \"$stream\"");
    }
    $log = $self->$stream;

    // Log message layer. We define three layers, 'user', 'app' and 'env',
    // which is useful for formatting the output of the log entries.
    if (isset($context['layer']) && !in_array($context['layer'], array('user', 'app', 'env'))) {
      $layer = $context['layer'];
      throw new Exception("Unrecognized log layer \"$layer\"");
    }

    switch ($level) {
      case L_DEBUG:
        $log->addDebug($message, $context);
        break;
      case L_INFO:
        $log->addInfo($message, $context);
        break;
      case L_WARNING:
        $log->addWarning($message, $context);
        break;
      case L_ERROR:
        $log->addError($message, $context);
        break;
      case L_CRITICAL:
        $log->addCritical($message, $context);
        break;
      case L_ALERT:
        $log->addAlert($message, $context);
        break;
    }
  }
}
