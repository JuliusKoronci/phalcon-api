<?php
use Dotenv\Dotenv;

use Phalcon\Cache\Frontend\Data as PhCacheFrontData;
use Phalcon\Config as PhConfig;
use Phalcon\Di as PhDI;
use Phalcon\Di\FactoryDefault as PhFactoryDefault;
use Phalcon\Mvc\Application as PhApplication;
use Phalcon\Mvc\Micro as PhMicro;
use Phalcon\Mvc\Micro\Collection as PhMicroCollection;
use Phalcon\Registry as PhRegistry;
use Igsem\ApiExceptions\Loggers\PhalconLogger;
use Igsem\ApiExceptions\ApiExceptions;
use Phalcon\Exception;


/**
 * AbstractBootstrap
 *
 * @property PhDI $diContainer
 */
class Bootstrap
{
    /**
     * @var null|PhMicro
     */
    protected $application;

    /**
     * @var null|PhDI
     */
    protected $diContainer;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Runs the application
     *
     * @return PhApplication
     * @throws \Phalcon\Exception
     */
    public function run()
    {
        $this->initDi();
        $this->initLoader();
        $this->initRegistry();
        $this->initEnvironment();
        $this->initApplication();
        $this->initConfig();
        $this->initCache();
        $this->initLogger();
        $this->initDocs();
        $this->initErrorHandler();
        $this->initRoutes();
        $this->initFirewall();
        $this->initJWT();

        return $this->runApplication();
    }

    /**
     * Runs the main application
     *
     * @return PhApplication
     */
    protected function runApplication()
    {
        return $this->application->handle();
    }

    /**
     * Initializes the application
     */
    protected function initApplication()
    {
        $this->application = new PhMicro($this->diContainer);
    }

    /**
     * Initializes the Cache
     */
    protected function initCache()
    {
        /**
         * viewCache
         */
        /** @var \Phalcon\Config $config */
        $config = $this->diContainer->getShared('config');
        $lifetime = $config->get('cache')->get('lifetime', 3600);

        /**
         * cacheData
         */
        $driver = $config->get('cache')->get('driver', 'file');
        $frontEnd = new PhCacheFrontData(['lifetime' => $lifetime]);
        $backEnd = ['cacheDir' => APP_PATH . '/storage/cache/data/'];
        $class = sprintf('\Phalcon\Cache\Backend\%s', ucfirst($driver));
        $cache = new $class($frontEnd, $backEnd);

        $this->diContainer->setShared('cacheData', $cache);
    }

    /**
     * Initializes the Config container
     *
     * @throws Exception
     */
    protected function initConfig()
    {
        $configArray = require APP_PATH . '/etc/config/config.php';
        $config = new PhConfig($configArray);

        $this->diContainer->setShared('config', $config);
    }

    /**
     * Initializes the Di container
     */
    protected function initDi()
    {
        $this->diContainer = new PhFactoryDefault();
        PhDI::setDefault($this->diContainer);
    }

    /**
     * Initializes the environment
     */
    protected function initEnvironment()
    {
        /** @var \Phalcon\Registry $registry */
        $registry = $this->diContainer->getShared('registry');
        $registry->memory = memory_get_usage();
        $registry->executionTime = microtime(true);

        (new Dotenv(APP_PATH))->load();

        $mode = getenv('APP_ENV');
        $mode = (false !== $mode) ? $mode : 'development';

        $registry->mode = $mode;
    }

    /**
     * Initializes the error handlers
     */
    protected function initErrorHandler()
    {
        $app = $this->application;
        $app->notFound(function () use ($app) {
            $app->response->setStatusCode(404, 'Not Found')->sendHeaders();
            echo 'This is crazy, but this page was not found!';
        });
        $registry = $this->diContainer->getShared('registry');
        /** @var PhalconLogger $logger */
        $logger = $this->diContainer->getShared('logger');

        $apiExceptions = new ApiExceptions($logger, ApiExceptions::DEVELOPMENT);
        $apiExceptions->register();

        register_shutdown_function(
            function () use ($logger, $registry) {
                if ('development' === $registry->mode) {
                    $memory = memory_get_usage() - $registry->memory;
                    $execution = microtime(true) - $registry->executionTime;
                    $logger->logMessage(
                        sprintf(
                            'Shutdown completed [%s] - [%s]',
                            $this->timeToHuman($execution),
                            $this->bytesToHuman($memory)
                        )
                    );
                }
            }
        );
    }

    /**
     * Initializes the autoloader
     */
    protected function initLoader()
    {
        /**
         * Use the composer autoloader
         */
        require_once APP_PATH . '/vendor/autoload.php';
    }

    /**
     * Initializes the logger
     */
    protected function initLogger()
    {
        /** @var \Phalcon\Config $config */
        $config = $this->diContainer->getShared('config');
        $path = $config->get('logger')->toArray()['path'];

        $this->diContainer->setShared('logger', new PhalconLogger($path));
    }

    /**
     * Configure Swagger
     */
    protected function initDocs()
    {
        /** @var PhConfig $config */
        $config = $this->diContainer->getShared('config');
        $this->diContainer->setShared(
            'swagger',
            function () use ($config) {
                return $config->get('swagger')->toArray();
            }

        );
    }

    /**
     * Initializes the registry
     */
    protected function initRegistry()
    {
        /**
         * Fill the registry with elements we will need
         */
        $registry = new PhRegistry();
        $registry->contributors = [];
        $registry->executionTime = 0;
        $registry->language = 'en';
        $registry->imageLanguage = 'en';
        $registry->memory = 0;
        $registry->menuLanguages = [];
        $registry->noindex = false;
        $registry->slug = '';
        $registry->releases = [];
        $registry->version = '3.0.0';
        $registry->view = 'index/index';
        $registry->mode = 'development';

        $this->diContainer->setShared('registry', $registry);
    }

    /**
     * Initializes the routes
     */
    protected function initRoutes()
    {
        /** @var PhConfig $config */
        $config = $this->diContainer->getShared('config');
        /** @var array $routes */
        $routes = $config->get('routes')->toArray();
        /** @var array $plugins */
        $plugins = $config->get('plugins')->toArray();

        foreach ($routes as $route) {
            $collection = new PhMicroCollection();
            $collection->setHandler($route['class'], true);
            if (true !== empty($route['prefix'])) {
                $collection->setPrefix($route['prefix']);
            }

            foreach ($route['methods'] as $verb => $methods) {
                foreach ($methods as $endpoint => $action) {
                    $collection->$verb($endpoint, $action);
                }
            }
            $this->application->mount($collection);
        }

        $eventsManager = $this->diContainer->getShared('eventsManager');

        foreach ($plugins as $element) {
            $eventsManager->attach('micro', new $element());
        }

        $this->application->setEventsManager($eventsManager);
    }

    protected function initFirewall()
    {
        /** @var PhConfig $config */
        $config = $this->diContainer->getShared('config');
        $this->diContainer->setShared(
            'firewall',
            function () use ($config) {
                return $config->get('firewall')->toArray();
            }
        );
    }

    protected function initJWT()
    {
        /** @var PhConfig $config */
        $config = $this->diContainer->getShared('config');
        $this->diContainer->setShared(
            'jwt',
            function () use ($config) {
                return $config->get('jwt')->toArray();
            }
        );
    }

    /**
     * Converts milliseconds to human readable format
     *
     * @param float $microseconds
     * @param int $precision
     *
     * @return string
     */
    protected function timeToHuman($microseconds, $precision = 3)
    {
        $units = ['μs', 'ns', 'ms', 's'];
        $micro = max($microseconds, 0);
        $pow = 0;
        if (1000 < $micro) {
            $pow = floor(($micro ? log($micro) : 0) / log(1000));
            $pow = min($pow, count($units) - 1);
            $micro /= (1 << (10 * $pow));
        }
        return round($micro, $precision) . ' ' . $units[$pow];
    }

    /**
     * Converts bytes to a human readable format
     *
     * @param int $bytes
     * @param int $precision
     *
     * @return string
     */
    protected function bytesToHuman($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
