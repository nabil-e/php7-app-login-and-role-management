<?php declare (strict_types = 1);

define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR.'/vendor/autoload.php';

use Tracy\Debugger;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;

//Tracy\Debugger::enable();

/*
 Jefferson :

@See https://bestofphp.com/repo/nette-tracy-php-debugging-and-profiling

*/
if ($_SERVER["APPLICATION_ENV"] == 'Development') {
    Tracy\Debugger::$showBar = true;
    Tracy\Debugger::$strictMode = true; // display all errors
    Tracy\Debugger::enable(Debugger::DEVELOPMENT, ROOT_DIR . '/logs/');
    Tracy\Debugger::$dumpTheme = 'dark'; // default: light
    Tracy\Debugger::$showLocation = true; // shows tooltip with path to the file, where the dump() was called, and tooltips for every dumped objects
}

//echo ROOT_DIR; // renvoie /appdata/sites/WinYUM 

/*
// Fin Jefferson
*/


$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$request = Request::createFromGlobals();

$dispatcher = FastRoute\simpleDispatcher(function 
(FastRoute\RouteCollector $r) {
    $routes = include(ROOT_DIR.'/src/Routes.php');
    foreach ($routes as $route) {
        $r->addRoute(...$route);
    }
});

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = new Response('404 - Page not found', 404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response('405 - Method not allowed', 405);
        break;
    case FastRoute\Dispatcher::FOUND:
        [$controllerName, $method] = explode('#', $routeInfo[1]);
        $parameters = $routeInfo[2];

        $injector = include('Dependencies.php');
        $controller = $injector->make($controllerName);
        $response = $controller->$method($request, $parameters);
        break;
}

if(!$response instanceof Response) {
    throw new \Exception("Controller methode must return a Response object");    
}


$response->prepare($request);
$response->send();

