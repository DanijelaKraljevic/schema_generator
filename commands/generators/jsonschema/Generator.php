<?php
/**
 * Created by PhpStorm.
 * User: Danijela
 * Date: 11/15/2018
 * Time: 10:27 PM
 */

namespace app\commands\generators\jsonschema;

use yii\gii\CodeFile;
use yii\gii\generators\model\Generator as ModelGenerator;

class Generator extends ModelGenerator
{
    /**
     * HTTP method
     *
     * @var string
     */
    public $httpMethod;

    public $db = 'db';
    public $ns = 'app\commands\generated\jsonschema';

    public function rules()
    {
        return array_merge(parent::rules(), [
             [['httpMethod'], 'string', 'skipOnEmpty' => false],
             [['httpMethod'], 'required'],
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Json Schema Generator';
    }
    /**
     * Overridden
     *
     */
    public function getViewPath()
    {
        return \Yii::getAlias('@app/commands/generated/jsonschema/' . $this->tableName.ucfirst($this->httpMethod));
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['schema.php'];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $db = $this->getDbConnection();

        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $tableSchema = $db->getTableSchema($tableName);

            $id = str_replace("_", "-", $tableName);

            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'id' => $id,
                'action' => $this->httpMethod,
                'tableSchema' => $tableSchema,
                'properties' => $this->generateProperties($tableSchema),
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
            ];
            $files[] = new CodeFile(
                \Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . ucfirst($this->httpMethod).'.json',
                $this->render('schema.php', $params)
            );
        }

        return $files;
    }
}
