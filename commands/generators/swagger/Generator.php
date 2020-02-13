<?php
/**
 * Created by PhpStorm.
 * User: Danijela
 * Date: 11/15/2018
 * Time: 10:27 PM
 */

namespace app\commands\generators\swagger;

use yii\gii\CodeFile;
use yii\gii\generators\model\Generator as ModelGenerator;

class Generator extends ModelGenerator
{
    
    public $db = 'db';
    public $ns = 'app\commands\generated\swagger';

    public $tableNamePlural;
    public $modelClass='none';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['modelClass','ac'], 'safe'],
            [['tableNamePlural'], 'required'],
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Swagger (OpenAPI) doc Generator';
    }
    /**
     * Overridden
     *
     */
    public function getViewPath()
    {
        return \Yii::getAlias('@app/commands/generated/swagger/' . $this->tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['template.yaml','schema.php'];
    }
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $db = $this->getDbConnection();

        foreach ($this->getTableNames() as $tableName) {

            $tableSchema = $db->getTableSchema($tableName);
            $tableNamePlural = $this->tableNamePlural;

            if(strpos($tableName,'_')){
                $wordArray = explode('_', $tableName);
                $Table = "";

                foreach($wordArray as $key => $word){
                    if($key != 0){
                        $Table .= " ";
                    }

                    $Table .= ucfirst($word);
                }
            }else{
                $Table = ucfirst ($tableName);
            }

            if(strpos($tableNamePlural,'_')){
                $wordArray = explode('_', $tableNamePlural);
                $Tables = "";

                foreach($wordArray as $key => $word){
                    if($key != 0){
                        $Tables .= " ";
                    }

                    $Tables .= ucfirst($word);
                }

            }else{
                $Tables = ucfirst ($tableNamePlural);
            }

            $TableNoSpace = str_replace(" ","",$Table);
            $id = str_replace("_", "-", $tableName);

            $params = [
                'table' => $tableName,
                'tables' => $tableNamePlural,
                'TableNoSpace'=> $TableNoSpace,
                'Table'=>$Table,
                'Tables'=>$Tables,
                'id' => $id,
                'labels' => $this->generateLabels($tableSchema),
                'tableSchema' => $tableSchema
            ];

            $template = file_get_contents(\Yii::getAlias('@app/commands/generators/swagger/default/template.yaml'));
            $out = str_replace("{tables}", str_replace('_','-',$tableNamePlural), $template);
            $out = str_replace("{Tables}", $Tables, $out);
            $out = str_replace("{table}", $tableName, $out);
            $out = str_replace("{Table}", $Table, $out);
            $out = str_replace("{TableNoSpace}", $TableNoSpace, $out);
            $out = str_replace("{urlId}", $id, $out);
            $out = str_replace("{Tag}", $Table, $out);

            //operations
            $files[] = new CodeFile(
                \Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $tableName .'.yaml',
                $out);

            //schemas
            $files[] = new CodeFile(
                \Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/schemas/' . $TableNoSpace .'.yaml',
                $this->render('schema.php', $params)
            );
        }

        return $files;
    }
}
