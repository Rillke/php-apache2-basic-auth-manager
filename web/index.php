<?php
/*
 * This file is part of the PHP Apache2 Basic Auth Manager package.
 *
 * (c) Rafael Goulart <rafaelgou@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

if (!file_exists(__DIR__ . '/../config.yml')) {
  die(
      '<h1>PHP Apache2 Basic Auth Manager</h1>'
      . '<p>Please create config.yml:<p>'
      . '<pre>cd PATH_TO_PROJECT; cp config.yml.dist config.yml</pre>'
  );
}

$app = new Application();

/**
 * config
 */
$config = Yaml::parse(file_get_contents(__DIR__ . '/../config.yml'));
$app['config'] = $config;
$app['debug'] = $config['debug'];

/**
 * Main providers
 */
$app->register(new ServiceControllerServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), array(
    'locale_fallbacks' => array('pt_br', 'en'),
    'translator.domains' => array(),
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
    'twig.form.templates' => array(
        'bootstrap_3_horizontal_layout.html.twig'
    )
));

$app->register(new SessionServiceProvider());
$app['session.storage.handler'] = new NativeFileSessionHandler(__DIR__ . '/../sessions');

/**
 * Load Routes
 */
$app->register(new App\Provider\RouterProvider());

$app->run();
