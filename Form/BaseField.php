<?php

namespace ernestocalise\glockmvc\form;

use ernestocalise\glockmvc\Model;

abstract class BaseField
{
    public Model $model;
    public string $attribute;
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
          <div class="form-group">
              <label>%s</label>
              %s
              <div class="invalid-feedback">
                  %s
              </div>
          </div>
        ',
            $this->model->labels()[$this->attribute] ?? $this->attribute,
            $this->renderField(),
            $this->model->getFirstError($this->attribute)
        );
    }
}
