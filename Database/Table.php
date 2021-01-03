<?php
namespace glockmvc\core\Database;
class Table {
    public string $tableName;
    public array $columns;
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }
    public function addColumn(Column $column){
        $this->columns[] = $column;
    }
    public function column($name, $type, $limiter = null){
        if($limiter){
            $type = $type."(".$limiter.")";
        }
        return new Column($this->tableName, $name, $type);
    }
    public function __toString()
    {
        $primaryKeyText = "";
        $values = implode(",".PHP_EOL,$this->columns);
        $primaryKey = array_filter(
            $this->columns,
            function ($e) {
                return $e->isPrimary == true;
            }
        );
        if(!empty($primaryKey)){
            $primaryKeyValue = $primaryKey[0]->name;
            $primaryKeyText = ", PRIMARY KEY (`$primaryKeyValue`)";
        }
        return "CREATE TABLE $this->tableName ( ".PHP_EOL.$values.PHP_EOL." $primaryKeyText".PHP_EOL.");";
    }
    public function timestamps(){
        $this->addColumn($this->column("created_at", COLUMN::TYPE_TIMESTAMP)->defaultValue("NOW()", false)->nullable(false));
        $this->addColumn($this->column("updated_at", COLUMN::TYPE_TIMESTAMP)->defaultValue("NOW()", false)->nullable(false)->OnUpdate("NOW()")); 
    }
}