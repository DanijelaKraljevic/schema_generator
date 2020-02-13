<?php
/**
 * Created by PhpStorm.
 * User: Danijela
 * Date: 11/15/2018
 * Time: 9:38 PM
 */
namespace app\commands;

use yii\base\InlineAction;
use yii\console\ExitCode;
use app\modules\ConsoleModule;
use yii\console\Controller as ConsoleController;
use yii\gii\console\GenerateAction;


date_default_timezone_set("Europe/Sarajevo");


/**
 *
 * You can use this command to generate files(json schema, swagger) based on DB table.
 * Built with Gii code generators.
 *
 * ```
 * $ ./yii generator/jsonschema --tableName=city --modelClass=City --ac=Post
 * ```
 * All files will be generated in app\commands\generated subdirectories.
 * @author Danijela
 */
class Controller extends ConsoleController
{

    /**
     * @var \app\modules\ConsoleModule
     */
    public $module;
    /**
     * @var bool whether to overwrite all existing code files when in non-interactive mode.
     * Defaults to false, meaning none of the existing code files will be overwritten.
     * This option is used only when `--interactive=0`.
     */
    public $overwrite = false;
    /**
     * @var array a list of the available code generators
     */
    public $generators = [];

    /**
     * @var array generator option values
     */
    private $_options = [];

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        foreach ($this->generators as $id => $config) {
            $this->generators[$id] = \Yii::createObject($config);
        }
    }

    public function actionIndex(){

        $this->run('/help', ['generator']);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        return isset($this->_options[$name]) ? $this->_options[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        $this->_options[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function createAction($id)
    {
        /** @var $action GenerateAction */
        $action = parent::createAction($id);
        foreach ($this->_options as $name => $value) {
            $action->generator->$name = $value;
        }
        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = [];
        foreach ($this->generators as $name => $generator) {
            $actions[$name] = [
                'class' => 'yii\gii\console\GenerateAction',
                'generator' => $generator,
            ];
        }
        return $actions;
    }


    /**
     * {@inheritdoc}
     */
    public function getUniqueID()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function options($id)
    {
        $options = parent::options($id);
        $options[] = 'overwrite';

        if (!isset($this->generators[$id])) {
            return $options;
        }

        $attributes = $this->generators[$id]->attributes;
        unset($attributes['templates']);
        return array_merge(
            $options,
            array_keys($attributes)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getActionHelpSummary($action)
    {
        if ($action instanceof InlineAction) {
            return parent::getActionHelpSummary($action);
        } else {
            /** @var $action GenerateAction */
            return $action->generator->getName();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionHelp($action)
    {
        if ($action instanceof InlineAction) {
            return parent::getActionHelp($action);
        } else {
            /** @var $action GenerateAction */
            $description = $action->generator->getDescription();
            return wordwrap(preg_replace('/\s+/', ' ', $description));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionArgsHelp($action)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getActionOptionsHelp($action)
    {
        if ($action instanceof InlineAction) {
            return parent::getActionOptionsHelp($action);
        }
        /** @var $action GenerateAction */
        $attributes = $action->generator->attributes;
        unset($attributes['templates']);
        $hints = $action->generator->hints();

        $options = parent::getActionOptionsHelp($action);
        foreach ($attributes as $name => $value) {
            $type = gettype($value);
            $options[$name] = [
                'type' => $type === 'NULL' ? 'string' : $type,
                'required' => $value === null && $action->generator->isAttributeRequired($name),
                'default' => $value,
                'comment' => isset($hints[$name]) ? $this->formatHint($hints[$name]) : '',
            ];
        }

        return $options;
    }

    protected function formatHint($hint)
    {
        $hint = preg_replace('%<code>(.*?)</code>%', '\1', $hint);
        $hint = preg_replace('/\s+/', ' ', $hint);
        return wordwrap($hint);
    }



}