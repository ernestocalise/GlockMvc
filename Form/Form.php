<?php
namespace glockmvc\core\\form;
use glockmvc\core\\Application;
use glockmvc\core\\Model;
class Form {
  private string $action;
  public function __construct($action)
  {
    $this->action = $action;
  }
  public static function begin($action, $method) {
    echo sprintf('<form action="%s" method="%s">', $action, $method);
    return new Form($action);
  }
  public static function end() {
    echo '</form>';
  }
  public function field(Model $model, $attribute) {
    return new InputField($model, $attribute);
  }
  public function textAreaField(Model $model, $attribute) {
    return new TextareaField($model, $attribute);
  }
  public function submit(string $text, string $class) {
    echo sprintf(
      '  <div class="form-group">
          <input type="submit" class="%s" value="%s" />
        </div>', $class, $text
      );
  }
  public function csrf() {
    $action = ($this->action == '') ? Application::$app->request->getPath() : $this->action;
    $token = Application::$app->session->setCSRFToken($action);
    echo sprintf(
      '  <input type="hidden" name="csrf-token" value="%s" />',$token
    );
  }
}
?>
