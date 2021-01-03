<?php
namespace ernestocalise\glockmvc\Database;
class Column {
    //String Related
    public const TYPE_CHAR = "CHAR";
    public const TYPE_VARCHAR = "VARCHAR";
    public const TYPE_TEXT = "TEXT";
    public const TYPE_TINYTEXT = "TINYTEXT";
    public const TYPE_MEDIUMTEXT = "MEDIUMTEXT";
    public const TYPE_LONGTEXT = "LONGTEXT";
    public const TYPE_BINARY = "BINARY";
    public const TYPE_VARBINARY = "VARBINARY";
    //Number Related
    public const TYPE_INT = "INT";
    public const TYPE_TINYINT = "TINYINT";
    public const TYPE_SMALLINT = "SMALLINT";
    public const TYPE_MEDIUMINT = "MEDIUMINT";
    public const TYPE_BIGINT = "BIGINT";
    public const TYPE_DECIMAL = "DECIMAL";
    public const TYPE_FLOAT = "FLOAT";
    public const TYPE_DOUBLE = "DOUBLE";
    public const TYPE_REAL = "REAL";
    public const TYPE_BIT = "BIT";
    public const TYPE_BOOLEAN = "BOOLEAN";
    public const TYPE_SERIAL = "SERIAL";
    // DATE RELATED 
    public const TYPE_DATE = "DATE";
    public const TYPE_DATETIME = "DATETIME";
    public const TYPE_TIMESTAMP = "TIMESTAMP";
    public const TYPE_TIME = "TIME";
    public const TYPE_YEAR = "YEAR";
    // JSON RELATED
    public const TYPE_JSON = "JSON";

    public const APPLY_CASCADE = "CASCADE";
    public const APPLY_RESTRICT = "RESTRICT";
    public const APPLY_SETNULL = "SET NULL";
    public const APPLY_SET_DEFAULT = "SET DEFAULT";


    public string $tableName = '';
    public string $name;
    public string $type;
    public bool $isPrimary = false;
    public bool $unique = false;
    public bool $auto_increment = false;
    public string $default = "";
    public bool $isNullable = true;
    public string $foreignKeyProp = "";
    public string $checkExpression = "";
    public string $onDelete = "";
    public string $onUpdate = "";
    public function __construct($tableName, $name, $type)
    {
        $this->tableName = $tableName;
        $this->name = $name;
        $this->type = $type;
    }
    public function nullable(bool $nullable){
        $this->isNullable = $nullable;
        return $this;
    }
    public function autoIncrement(){
        $this->auto_increment= true;
        $this->isNullable = false;
        return $this;
    }
    public function defaultValue(string $value, bool $isStringValue = true){
        $this->default = ($isStringValue) ? " DEFAULT '$value' " : "DEFAULT $value";
        return $this;
    }
    public function unique(){
        $this->unique = true;
        return $this;
    }
    public function primaryKey(){
        $this->isPrimary = true;
        return $this;
    }
    public function foreignKey(string $tableName, $columnName) {
        $this->foreignKeyProp = " ADD FOREIGN KEY $this->tableName($this->name) REFERENCES $tableName($columnName) ";
        return $this;
    }
    public function OnDelete(string $argument){
        $this->onDelete = " ON DELETE $argument ";
        return $this;
    }
    public function OnUpdate(string $argument) {
        $this->onUpdate = " ON UPDATE $argument ";
        return $this;
    }
    public function check(array $arguments) {
        $this->checkExpression = " CHECK $arguments ";
    }
    public function __toString()
    {
        return sprintf("`%s` %s%s%s%s%s%s%s%s%s", 
        $this->name,
        $this->type,
        $this->isNullable ? "" : " NOT NULL ",
        $this->auto_increment ? " AUTO_INCREMENT" : "",
        $this->unique ? " UNIQUE " : "",
        $this->default,
        $this->checkExpression,
        $this->foreignKeyProp,
        $this->onUpdate,
        $this->onDelete
    );
    }
}

?>