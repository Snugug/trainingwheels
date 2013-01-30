<?php

namespace TrainingWheels\Common;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use TrainingWheels\Course\CourseFactory;
use TrainingWheels\Job\JobFactory;
use TrainingWheels\Log\Log;
use TrainingWheels\Store\DataStore;
use Igorw\Silex\ConfigServiceProvider;
use Exception;

/**
 * Bootstrap loads the essential Training Wheels components and injects them
 * into the Silex application for use by both the REST endpoints and the Console
 * application.
 */
class BootstrapServiceProvider implements ServiceProviderInterface {

  public function register(Application $app) {
    // The base of the controller application.
    $base_path = realpath(__DIR__ . '/../../../');

    // Configuration.
    $config_file = $base_path . '/config/config.yml';
    if (!is_file($config_file)) {
      throw new Exception("The configuration file could not be found at $config_file");
    }
    $app->register(New ConfigServiceProvider($config_file));

    // Logging. We add a monolog service provider, which is what Silex will use
    // internally.
    $log_file = $base_path . '/log/tw.log';
    $app->register(new MonologServiceProvider(), array(
        'monolog.logfile' => $log_file,
        'monolog.name' => 'tw',
        'monolog.level' => $app['debug'] ? Logger::DEBUG : Logger::INFO,
    ));
    $app['monolog'] = $app->share($app->extend('monolog', function($monolog, $app) {
      // Change the default handler to use a better log file format, that doesn't print
      // empty square brackets after each line.
      $handler = $monolog->popHandler();
      $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message% \n");
      $handler->setFormatter($formatter);
      $monolog->pushHandler($handler);
      return $monolog;
    }));

    // Locale - needed for twig forms.
    $app->register(new TranslationServiceProvider(), array(
      'locale' => 'en',
      'translation.class_path' => $base_path . 'vendor/symfony',
      'translator.messages' => array(),
    ));

    // We then use the same Monolog instance on the Training Wheels Log object,
    // so that our application logs all end up in the same place.
    $app['tw.log'] = new Log($app['monolog']);

    // Training Wheels objects.
    $app['tw.datastore'] = new DataStore($app['connections']['mongo']);
    $app['tw.course_factory'] = new CourseFactory($app['tw.datastore']);
    $app['tw.job_factory'] = new JobFactory($app['tw.datastore'], $app['tw.course_factory']);
  }

  public function boot(Application $app) {
  }
}

