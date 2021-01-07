<?php

namespace glockmvc\core\form;

use glockmvc\core\Model;

abstract class BaseField
{
    public Model $model;
    public string $attribute;
    public bool $readOnly = false;
    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    abstract public function renderField();
    public function __toString()
    {
        return sprintf(
            '
            <div class="mb-3">
                <label for="%s" class="form-label">%s</label>
                    %s
                <div class="invalid-feedback">
                    %s
                </div>
            </div>
        ',
            $this->attribute,
            $this->model->labels()[$this->attribute] ?? $this->attribute,
            $this->renderField(),
            $this->model->getFirstError($this->attribute)
        );
    }

    public function readOnly(){
        $this->readOnly = true;
        return $this;
      }
}
