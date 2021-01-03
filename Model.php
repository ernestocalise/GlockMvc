<?php
namespace glockmvc\core;
abstract class Model {
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_ACCEPTED = 'accepted';
    public const RULE_UNIQUE = 'unique';
    public array $errors = [];

    public function loadData($data) {
      foreach($data as $key => $value) {
        if( property_exists($this,$key)){
          $this->{$key} = $value;
        }
      }
    }

    public function labels() :array {
         return [];
    }
    abstract public function rules(): array;
    public function addError(string $attribute, string $message)
    {
          $this->errors[$attribute][] = $message;
    }
    private function addErrorForRule(string $attribute, string $rule, $params = [] ) {
      $message = $this->errorMessages()[$rule] ?? '';
      foreach ($params as $key => $value){
          $message = str_replace("{{$key}}", $value, $message);
      }
      $this->errors[$attribute][] = $message;
    }
    public function errorMessages(){
      return [
        self::RULE_REQUIRED => 'Campo obbligatorio',
        self::RULE_EMAIL => 'Inserire un indirizzo E-Mail valido',
        self::RULE_MIN => 'Il campo deve essere almeno di {min} caratteri',
        self::RULE_MAX => 'Il campo deve essere massimo di {max} caratteri',
        self::RULE_MATCH => 'il campo deve essere uguale al campo {match}',
        self::RULE_ACCEPTED => 'il campo deve essere compilato',
        self::RULE_UNIQUE => 'Esiste già un record con questo {field} nel database!'
      ];
    }

    public function hasError($attribute){
      return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute){
      return $this->errors[$attribute][0] ?? '';
    }
    public function getLabel($attribute) {
         return $this->labels[$attribute] ?? $attribute;
    }
    public function validate() {
      foreach($this->rules() as $attribute => $rules) {
        $value = $this->{$attribute};
        foreach($rules as $rule) {
          $ruleName = $rule;
          if(is_array($ruleName)){
            $ruleName = $rule[0];
          }
          if($ruleName === self::RULE_REQUIRED && !$value){
            $this->addErrorForRule($attribute, self::RULE_REQUIRED);
          }
          if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
            $this->addErrorForRule($attribute, self::RULE_EMAIL);
          }
          if($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
            $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
          }
          if($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
            $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
          }
          if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){
               $rule['match'] = $this->getLabel($rule['match']);
            $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
          }
          if($ruleName === self::RULE_ACCEPTED && ($value != "yes" || $value != "on" || $value !=1 || $value !=true)){
            // TODO : The rules must be innested
            $this->addErrorForRule($attribute, self::RULE_ACCEPTED, $rule);
          }
          if($ruleName === self::RULE_UNIQUE){
               $className = $rule['class'];
               $uniqueAttr = $rule['attribute'] ?? $attribute;
               $tableName = $className::tableName();
               $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
               $statement->bindValue(":attr", $value);
               $statement->execute();
               $record = $statement->fetchObject();
               if($record){
                    $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
               }
          }
        }
      }

      return empty($this->errors);
    }
}
