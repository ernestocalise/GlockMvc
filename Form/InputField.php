<?php
namespace glockmvc\core\form;
use glockmvc\core\Model;
class InputField extends BaseField {

  public const TYPE_TEXT = 'text';
  public const TYPE_PASSWORD = 'password';
  public const TYPE_EMAIL = 'email';
  public const TYPE_NUMBER = 'number';


  public string $type;
  public string $placeHolder = "";
  public function __construct(Model $model, string $attribute) {
    $this->type = self::TYPE_TEXT;
    parent::__construct($model, $attribute);
  }
  public function renderField() : string {
    return sprintf('
        <input name="%s" type="%s" id="%s" value="%s" placeholder="%s" class="form-control %s" %s>
  ',
    $this->attribute,
    $this->type,
    $this->attribute,
    $this->model->{$this->attribute},
    $this->placeHolder,
    $this->model->hasError($this->attribute) ? ' is-invalid ' : '',
    ($this->readonly) ? "readonly" : "" 
  );
  }

  public function emailField() {
    $this->type = self::TYPE_EMAIL;
    return $this;
  }

  public function numberField() {
    $this->type = self::TYPE_NUMBER;
    return $this;
  }

  public function passwordField() {
    $this->type = self::TYPE_PASSWORD;
    return $this;
  }
  public function placeHolder($placeholderText){
    $this->placeHolder = $placeholderText;
    return $this;
  }
}
 ?>
