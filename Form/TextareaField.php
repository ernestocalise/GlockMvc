<?php
namespace glockmvc\core\form;
use glockmvc\core\Model;
class TextareaField extends BaseField {
    public function renderField():string
    {
        return sprintf('<textarea name="%s" id="%s" class="form-control %s">%s</textarea>',
        $this->attribute,
        $this->attribute,
        $this->model->hasError($this->attribute) ? ' is-invalid ' : '',
        $this->model->{$this->attribute}
    );
    }
}