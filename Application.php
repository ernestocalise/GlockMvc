<?php

namespace glockmvc\core;
use \glockmvc\core\Database\{Database, DbModel};
class Application {
  public static string $ROOT_DIR;
  public string $userClass;
  public string $layout = 'main';
  public Router $router;
  public Request $request;
  public Response $response;
  public Session $session;
  public Database $db;
  public static Application $app;
  public ?Controller $controller = null;
  public ?DbModel $user;
  public View $view;
  public function __construct($rootPath, array $config){
      $this->userClass = $config['userClass'];
    self::$ROOT_DIR = $rootPath;
    self::$app = $this;
    $this->request = new Request();
    $this->response = new Response();
    $this->view = new View();
    $this->session = new Session();
    $this->router = new Router($this->request, $this->response);
    $this->db = new Database($config['db']);
    $primaryValue = $this->session->get('user');
    if($primaryValue){
         $primaryKey = $this->userClass::primaryKey();
         $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
    } else { $this->user = null; }
  }

  public function run() {
       try {
            echo $this->router->resolve();
       }
       catch (\Exception $e){
            echo $e;
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('default\_genericError', [
                 'exception' => $e
            ]);
       }
  }
  public function getController(): \glockmvc\core\Controller {
    return $this->controller;
  }
  public function setController(Controller $inController) {
    $this->controller = $inController;
  }
  public static function getInstance(){
     return self::$app;
}
  public function login (DbModel $user) {
       exit();
       $this->user = $user;
       $primaryKey = $user->primaryKey();
       $primaryValue = $user->{$primaryKey};
       $this->session->set('user', $primaryValue);
       return true;
 }
 public function logout() {
      $this->user = null;
      $this->session->remove('user');
}
public static function isGuest() {
     return !self::$app->user;
}
}

?>