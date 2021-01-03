<?php
namespace glockmvc\core\;
abstract class DbModel extends Model{
     protected int $id;
     public const RELATION_ONE_TO_ONE = 'ONE_TO_ONE';
     public const RELATION_MANY_TO_MANY = 'MANY_TO_MANY';
     public const RELATION_ONE_TO_MANY = 'ONE_TO_MANY';
     abstract public static function tableName(): string;
     abstract public static function attributes(): array;
     abstract public static function primaryKey(): string;
     public function save() {
          $tableName = $this->tableName();
          $attributes = $this->attributes();
          $params = array_map(fn($attr) => ":$attr", $attributes);
          $statement = self::prepare("INSERT INTO $tableName (".implode(',', $attributes).")
          VALUES (".implode(',', $params).")");
          foreach ($attributes as $attribute) {
               $statement->bindValue(":$attribute", $this->{$attribute});
          };
          $statement->execute();
          return true;
     }

     public static function findOne($where) {
          //where = [email => email@esempio.com, nome => peppecalise]
          $tableName = static::tableName();
          $attributes = array_keys($where);
          $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
          $statement = self::prepare("SELECT * FROM $tableName WHERE $sql LIMIT 1");
          foreach($where as $key => $value){
               $statement->bindValue(":$key", $value);
          }
          $statement->execute();
          return $statement->fetchObject(static::class);
     }
     public static function find($id){
          $tableName = static::tableName();
          $statement = self::prepare("SELECT * FROM $tableName WHERE id = :id LIMIT 1");
          $statement->bindValue(":id", $id);
          $statement->execute();
          return $statement->fetchObject(static::class);
     }
     public static function where($field, $operator, $value){
          $tableName = static::tableName();
          $statement = self::prepare("SELECT * FROM $tableName WHERE $field $operator :field");
          $statement->bindValue(":field",$value);
          $statement->execute();
          return static::treatStatementValues($statement);
     }
     public function hasMany(DbModel $model, string $relation = static::RELATION_ONE_TO_MANY, ?string $pivot) {
          if($relation === static::RELATION_ONE_TO_MANY){
               return $this->oneHasMany($model);
          }
          if($relation === static::RELATION_MANY_TO_MANY){
               if($pivot != null && trim($pivot) !== ''){
                    return $this->manyToMany($model, $pivot);
               }
               else {
                    throw new \PDOException;
               }
          }
     }
     public function manyToMany(DbModel $model, string $pivot) {
          $fieldLocal = $this->tableName().'_id';
          $fieldRemote = $model->tableName().'_id';
          $result = [];
          $sql = "SELECT $fieldRemote FROM $pivot WHERE $fieldLocal = :localId AND $fieldRemote = :remoteId";
          $statement = self::prepare($sql);
          $statement->bindValue(":localId", $this->getId());
          $statement->bindValue(":remoteId", $model->getId());
          $statement->execute();
          $pivotResult = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
          foreach($pivotResult as $indexedResult) {
               $result[] = $model::find($indexedResult);
          }
          return $result;
     }
     public function oneHasMany(DbModel $model) {
          $field = $model->tableName().'_id';
          $tableName = $model->tableName();
          $sql = "SELECT * FROM $tableName WHERE $field = :field";
          $statement = self::prepare($sql);
          $statement->bindValue(":field", $this->getId());
          $statement->execute();
          return static::treatStatementValues($statement);
     }
     public function belongsTo(DbModel $model) {
          $field = $model->tableName().'_id';
          $tableName = static::tableName();
          $sql = "SELECT * FROM $tableName WHERE $field = :field";
          $statement = self::prepare($sql);
          $statement->bindValue(":field", $this->getId());
          $statement->execute();
          return static::treatStatementValues($statement);
     }


     protected static function treatStatementValues(\PDOStatement $statement) {
          if($statement->rowCount() === 1) {
               return $statement->fetchObject(static::class);
          }
          else {
               $result = [];
               $rowCount = $statement->rowCount();
               for($i = 0; $i < $rowCount; $i++){
                    $result[] = $statement->fetchObject(static::class);
               }
               return $result;
          }
     }
     private function getID() {
          return $this->id; 
     }
     public static function prepare($sql) {
          return Application::$app->db->pdo->prepare($sql);
     }
}

 ?>
