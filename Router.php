<?php

namespace glockmvc\core;
use app\controllers\SiteController;
use glockmvc\core\Exception\NotFoundException;
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
    public function route_match($server_route, $user_route){
        //Route /profile/{username}
        $params = [];
        $arrServer = explode("/", $server_route);
        $arrUser = explode("/", $user_route);
        if(count($arrServer) == count($arrUser)){
            for($i = 0; $i<count($arrServer);$i++){
              if(str_starts_with($arrServer[$i], "{") && str_ends_with($arrServer[$i], "}")){
                  $params[substr($arrServer[$i], 1,-1)] = $arrUser[$i];
              }
              else {
                if($arrServer[$i] != $arrUser[$i]){
                  return false;
                }
              }
            }
            return [true, $params];
        } else {
          return false;
        }
    }
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $result = [];
        foreach($this->routes[$method] as $key => $value){
          $result = $this->route_match($key, $path);
          if($result != false){
            $result[0] = $value;
            break;
          }
        }
        //$callback = $this->routes[$method][$path] ?? false;
        if(!$result) {
          throw new NotFoundException();
          exit();
        }
        $callback = $result[0];
        $this->request->parameters = $result[1];
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
