<?php

namespace glockmvc\core\;
use glockmvc\core\\Exception\NotFoundException;
class Router {
    protected array $routes = [];
    public Request $request;
    public Response $response;

    public function __construct(Request $_request, Response $response){
        $this->request = $_request;
        $this->response = $response;
    }

    public function get($path, $callback) {
      $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback) {
      $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if(!$callback) {
          throw new NotFoundException();
          exit();
        }
        if(is_string($callback)) {
          return Application::$app->view->renderView($callback);
        }
        if(is_array($callback)) {
          $controller =  new $callback[0]();
          Application::$app->controller = $controller;
          $controller->action = $callback[1];
          foreach ($controller->getMiddlewares() as $middleware) {
               $middleware->execute();
          }
          $callback[0] = $controller;
        }
        return call_user_func($callback, $this->request, $this->response);
    }
}
 ?>
