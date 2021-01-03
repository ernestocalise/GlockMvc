<?php
namespace ernestocalise\glockmvc;
use ernestocalise\glockmvc\Exception\ForbiddenException;
class Request {
  public function getPath() {
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $position = strpos($path, '?');
    if ($position === false) {
      return $path;
    }
    else {
      $path = substr($path, 0, $position);
      return $path;
    }
  }
  public function getMethod() {
      return strtolower($_SERVER['REQUEST_METHOD']);
  }
  public function isGet() {
      return $this->getMethod() === 'get' ? true : false;
  }
  public function isPost() {
      return $this->getMethod() === 'post' ? true : false;
  }
  public function getBody() {
    $body = [];
    if($this->isGet()) {
        foreach($_GET as $key => $value){
            $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
    }
    else {
        if(Application::$app->session->checkCSRFToken($_POST['csrf-token'], $this->getPath())){
            foreach($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        } else {
          throw new ForbiddenException("Unauthorized", 401);
        }
    }

    return $body;
  }
}
 ?>
