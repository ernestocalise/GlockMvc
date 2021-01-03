<?php
namespace ernestocalise\glockmvc\CLI;
class FileBuilder {
    public string $path;
    public function __construct($path)
    {
        $this->path = $path;
    }
    public function createFile($fileName, $text){
        $myfile = fopen("$this->path"."$fileName.php", "w") or die("Unable to open file!");
        fwrite($myfile, $text);
        fclose($myfile);
    }
    public function migration($migrationName) {
        $code = "
        <?php \n
              use ernestocalise\glockmvc\Database\Migration;
              use ernestocalise\glockmvc\Database\Table;
              use ernestocalise\glockmvc\Database\Column;
              class ".$migrationName." extends Migration{
                public function up(){
                  \$db = \ernestocalise\glockmvc\Application::\$app->db;
                }
                public function down(){
                  \$db = \ernestocalise\glockmvc\Application::\$app->db;
                }
              }
        ?>";
        $mName= 
       $this->createFile("/migrations"."/m".$this->getLastMigration().$migrationName,$code);
       $this->logMessage("created Migration m". $this->getLastMigration().$migrationName);
    }
    public function migrationForTable($migrationName) {
      $code = 
      "<?php \n
      use ernestocalise\glockmvc\Database\Migration;
      use ernestocalise\glockmvc\Database\Table;
      use ernestocalise\glockmvc\Database\Column;
      class ".$migrationName." extends Migration{
        public function up(){
          \$db = \ernestocalise\glockmvc\Application::\$app->db;
          \$table = \$this->createTable(\"tableName\");
          \$table->addColumn(\$table->column(\"id\", Column::TYPE_INT)->primaryKey()->autoIncrement());
        }
        public function down(){
          \$db = \ernestocalise\glockmvc\Application::\$app->db;
          \$db->execute(\$this->dropTableIfExists(\"users\"));
        }
      }
      ?>";
      $this->createFile("/migrations/$migrationName",$code);    
      $this->logMessage("Created Migration $migrationName");
    }
    public function Model($modelName){
        $code =
        "<?php \n
        namespace app\models;
        use ernestocalise\glockmvc\Model;
        use ernestocalise\glockmvc\Application;
        class $modelName extends Model
        {
            public function rules() : array {
                return [];
            }

            public function labels() : array {
                return [];
            }
        }
        ?>";
        $this->createFile("/models/$modelName", $code);
        $this->logMessage("Created Model $modelName");
    }
    public function getLastMigration(){
        $files = scandir($this->path.'/migrations');
        $effectiveFile = array_diff($files, [".",".."]);
        $cef = count($effectiveFile);
        if($cef===0){
        return "0001";
        }
        else {
            return sprintf('%04d',intval(substr(end($effectiveFile), 1,4))+1);
        }
    }
    public function DbModelAndMigration($modelName){
        $codeForModel =
        "<?php \n
        namespace app\models;
        use ernestocalise\glockmvc\Model;
        use ernestocalise\glockmvc\DbModel;
        class $modelName extends DbModel {
            public static function tableName():string {
                return '$modelName';   
            }
            public static function primaryKey() : string {
                return 'id';
           }
           public function rules():array {
               return [];
           }
           public static function attributes():array{
               return [];
           }
           public function labels():array{
                return [];   
           }
           public function save(){
                return parent::save();
           }
        }
    ?>";
    $this->createFile("/models/$modelName", $codeForModel);
    $this->logMessage("Created Model $modelName !");
    $lastMigration = $this->getLastMigration();    
    $migrationName = "m".$lastMigration."_create_table_$modelName";
    $this->migrationForTable($migrationName);
    }
    public function logMessage($message){
        echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
  }
}
?>
