<?php

use App\Contracts\Randomizer;
use App\Services\SimpleRandomizer;
use DI\Container;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Message\ResponseFactory;
use Sunrise\Http\Message\ServerRequestFactory;
use Sunrise\Http\Router\Loader\ConfigLoader;
use Sunrise\Http\Router\Loader\LoaderInterface;
use Sunrise\Http\Router\Router;

use function DI\create;
use function DI\factory;

return [
    Connection::class               => factory(function (Container $container) {
        return DriverManager::getConnection($container->get('db'));
    }),
    'db'                            => [
        'host'     => getenv('MYSQL_HOST'),
        'port'     => (int)getenv('MYSQL_PORT'),
        'dbname'   => getenv('MYSQL_DATABASE'),
        'user'     => getenv('MYSQL_USER'),
        'password' => getenv('MYSQL_PASSWORD'),
        'driver'   => 'pdo_mysql',
    ],
    'commands.patterns'             => [
        'App\\Commands\\' => '/src/Commands/*.php',
    ],
    Randomizer::class               => create(SimpleRandomizer::class),
    LoaderInterface::class          => factory(function (Container $container) {
        $loader = new ConfigLoader();
        $loader->setContainer($container);
        $loader->attach('config/routes.php');

        return $loader;
    }),
    ResponseFactoryInterface::class => factory(function () {
        return new ResponseFactory();
    }),
    Router::class                   => factory(function (Container $container) {
        $router = new Router();
        $router->load($container->get(LoaderInterface::class));

        return $router;
    }),
    ServerRequestInterface::class   => factory(function () {
        return ServerRequestFactory::fromGlobals();
    }),
];
