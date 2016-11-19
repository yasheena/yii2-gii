<?php
/**
 * This module is based on the original yii2-gii module.
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yasheena\gii\generators\crud;

use Yii;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\base\Exception;

/**
 * Generates CRUD
 *
 * @property array $columnNames Model column names. This property is read-only.
 * @property string $controllerID The controller ID (without the module ID prefix). This property is
 * read-only.
 * @property array $searchAttributes Searchable attributes. This property is read-only.
 * @property boolean|\yii\db\TableSchema $tableSchema This property is read-only.
 * @property string $viewPath The controller view path. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\Generator
{
    public $modelClass;
    public $controllerClass;
    public $viewPath;
    public $baseControllerClass = 'yii\web\Controller';
    public $indexWidgetType = 'grid';
    public $searchModelClass = '';
    public $generated = false;
    public $enablePjax = true;
    private $store;

    public function init()
    {
    	$this->enableI18N = true;
    	$this->store = new valuestore();
        $this->attachBehavior('valuestore', $this->store);
    	parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Yasheena CRUD Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a controller and views that implement CRUD
            operations for the specified data model using the great modules of kartik-v.<br><br>
        	Many parameters of the Grid can be defined and this data is stored for
        	reusing the generator to update the Grid settings till final version. This
        	settings inculdes the handling of foreign keys, search fields, column
        	formats, withs, visibility, order, labels and alignment and also additional
        	buttons (including glyphicons) in the footer area for actions and mass actions.<br><br>
        	The template is splitted into two parts, so you can write already code in one
        	part and update settings of the grid in the other part by using the generator.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['controllerClass', 'modelClass', 'searchModelClass', 'baseControllerClass'], 'filter', 'filter' => 'trim'],
            [['modelClass', 'controllerClass', 'baseControllerClass', 'indexWidgetType'], 'required'],
            [['searchModelClass'], 'compare', 'compareAttribute' => 'modelClass', 'operator' => '!==', 'message' => 'Search Model Class must not be equal to Model Class.'],
            [['modelClass', 'controllerClass', 'baseControllerClass', 'searchModelClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['modelClass'], 'validateClass', 'params' => ['extends' => BaseActiveRecord::className()]],
            [['baseControllerClass'], 'validateClass', 'params' => ['extends' => Controller::className()]],
            [['controllerClass'], 'match', 'pattern' => '/Controller$/', 'message' => 'Controller class name must be suffixed with "Controller".'],
            [['controllerClass'], 'match', 'pattern' => '/(^|\\\\)[A-Z][^\\\\]+Controller$/', 'message' => 'Controller class name must start with an uppercase letter.'],
            [['controllerClass', 'searchModelClass'], 'validateNewClass'],
            [['indexWidgetType'], 'in', 'range' => ['grid', 'list']],
            [['modelClass'], 'validateModelClass'],
            [['enableI18N', 'enablePjax'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
            [['viewPath'], 'safe'],
        ], $this->store->rules());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'modelClass' => 'Model Class',
            'controllerClass' => 'Controller Class',
            'viewPath' => 'View Path',
            'baseControllerClass' => 'Base Controller Class',
            'indexWidgetType' => 'Widget Used in Index Page',
            'searchModelClass' => 'Search Model Class',
            'enablePjax' => 'Enable Pjax',
        	'dat_headerInfo' => 'List Header (optional text)',
        	'dat_footerInfo' => 'List Footer (optional text)',
        	'dat_use_serialcolumn' => 'Use a serial column',
        	'dat_use_checkboxcolumn' => 'Use a checkbox column',
        	'dat_checkboxcolumn_field' => 'Field for direct update:',
        	'dat_lft_button' => 'Left buttion in List Footer',
        	'dat_rgt_button' => 'Right buttion in List Footer',
]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'modelClass' => 'This is the ActiveRecord class associated with the table that CRUD will be built upon.
                You should provide a fully qualified class name, e.g., <code>app\models\Post</code>.',
            'controllerClass' => 'This is the name of the controller class to be generated. You should
                provide a fully qualified namespaced class (e.g. <code>app\controllers\PostController</code>),
                and class name should be in CamelCase with an uppercase first letter. Make sure the class
                is using the same namespace as specified by your application\'s controllerNamespace property.',
            'viewPath' => 'Specify the directory for storing the view scripts for the controller. You may use path alias here, e.g.,
                <code>/var/www/basic/controllers/views/post</code>, <code>@app/views/post</code>. If not set, it will default
                to <code>@app/views/ControllerID</code>',
            'baseControllerClass' => 'This is the class that the new CRUD controller class will extend from.
                You should provide a fully qualified class name, e.g., <code>yii\web\Controller</code>.',
            'indexWidgetType' => 'This is the widget type to be used in the index page to display list of the models.
                You may choose either <code>GridView</code> or <code>ListView</code>',
            'searchModelClass' => 'This is the name of the search model class to be generated. You should provide a fully
                qualified namespaced class name, e.g., <code>app\models\PostSearch</code>.',
            'enablePjax' => 'This indicates whether the generator should wrap the <code>GridView</code> or <code>ListView</code>
                widget on the index page with <code>yii\widgets\Pjax</code> widget. Set this to <code>true</code> if you want to get
                sorting, filtering and pagination without page refreshing.',
        	'dat_headerInfo' => 'This text will be displayed in the header area of the table.',
        	'dat_footerInfo' => 'This text will be displayed in the footer area of the table.',
        	'dat_use_serialcolumn' => 'Add a column with serial numbering of the rows',
        	'dat_use_checkboxcolumn' => 'Add a column with checkboxes to allow the user to select multiple rows for a mass action or for direct marking in database',
        	'dat_checkboxcolumn_field' => 'Datebase field to update directly with the checkbox value (true/false). Make sure this field has no unique index. Using \'---\' will execute the controller function \'actionCheckboxChanged\' instead of updating the database. For setting default values to the checkboxes in that case define a function \'getCheckboxDefaultValue($key, $index, $widget)\' in the model and rerun the generator to update \'_index.php\'.', 
        	'dat_lft_button' => 'Add button(s) or a dropdown in the left footer area to handle selected rows in a mass action:<br><b>None</b>: Immediate action on changing a checkbox<br><b>Buttons</b>: One or more single buttons<br><b>Dropdown</b>: Actions listed in a dropdown list',
        	'dat_rgt_button' => 'Add button(s) or a dropdown in the right footer area:<br><b>None</b>: No button at all<br><b>Buttons</b>: One or more single buttons<br><b>Dropdown</b>: Actions listed in a dropdown list',
]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['controller.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['baseControllerClass', 'indexWidgetType']);
    }

    /**
     * Checks if model class is valid
     */
    public function validateModelClass()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $class must have primary key(s).");
        }
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $viewPath = $this->getViewPath();

        $storefile = "$viewPath/.generator";
        if (file_exists($storefile)) {
        	if(count($this->store->col_name) == 0) {
        		@$this->store->setJSON(file_get_contents($storefile));
        		$this->enableI18N = $this->store->dat_enableI18N;
        		$this->enablePjax = $this->store->dat_enablePjax;
        		$this->messageCategory = $this->dat_messageCategory;
        	}
        }
        if ($this->enableI18N == null) {
    		$this->enableI18N = true;
    	}
        if ($this->enablePjax == null) {
    		$this->enablePjax = true;
    	}
        if ($this->messageCategory == null) {
    		$this->messageCategory = 'app';
    	}
    	if ($this->store->dat_use_serialcolumn == null) {
    		$this->store->dat_use_serialcolumn = true;
    	}

    	foreach(['lft', 'rgt'] as $idx) {
    		$_btn = 'dat_' . $idx . '_button';
    		$_lbl = $idx . '_label';
    		$_url = $idx . '_url';
    		$_ico = $idx . '_icon';
    		$_hnt = $idx . '_hint';
        	if ($this->store->$_btn == null) {
	    		$this->store->$_btn = 'None';
    		}
    		$labels = $this->store->$_lbl;
	    	$urls = $this->store->$_url;
    		$icons = $this->store->$_ico;
    		$hints = $this->store->$_hnt;
    		foreach ($labels as $index => $lbl) {
    			if (($lbl == '') && ($icons[$index] == '---')) {
	    			unset($labels[$index]);
    				unset($urls[$index]);
    				unset($icons[$index]);
    				unset($hints[$index]);
    			} else {
    				if ($index > 0) {
    					if ($urls[$index] == '') {
    						$urls[$index] = $this->createUrl(($lbl == '') ? ($idx . '-action-' . $index) : $lbl);
    					}
    				}
    			}
	    	}
   			$labels[] = '';
    		$urls[] = '';
	    	$icons[] = '---';
	    	$hints[] = '';
    		$this->store->$_lbl = array_values($labels);
    		$this->store->$_url = array_values($urls);
	    	$this->store->$_ico = array_values($icons);
	    	$this->store->$_hnt = array_values($hints);
    	}

    	$tableSchema = $this->getTableSchema();
        $model = new $this->modelClass;
        $db = $model->getDb();
        foreach ($this->getColumnNames() as $columnName) {
        	$index = $this->store->getIndexOfName($columnName);
        	$replacenames = null;
        	$format = 'text';
			$visible = 'visible';
			$align = 'left';
        	if ($tableSchema) {
        		foreach ($tableSchema->foreignKeys as $index2 => $key) {
    	    		if (array_key_exists($columnName, $key)) {
        				$replacenames = array('---');
        				$tableSchemaRepl = $db->getTableSchema($key[0]);
        				foreach ($tableSchemaRepl->columns as $index3 => $column) {
        					if($index3 != $key[$columnName]) {
        						$replacenames[] = $key[0] . '.' . $index3;
	        				}
    	    			}
        				$replacenames = $this->createDropdownList($replacenames);
        				break;
    	    		}
        		}
				$type = $tableSchema->columns[$columnName]->dbType;
				if (false !== ($n = strpos($type, '('))) {
					$type = substr($type, 0, $n);
				}
        		switch($type) {
        			case 'int':
        			case 'tinyint':
        			case 'smallint':
        			case 'mediumint':
        			case 'bigint':
        				$format = 'integer';
        				$align = 'right';
        				break;
        			case 'decimal':
        			case 'float':
        			case 'double':
        			case 'real':
        				$format = 'decimal';
        				$align = 'right';
        				break;
        			case 'boolean':
        				$format = 'boolean';
        				$align = 'center';
        				break;
        			case 'varchar':
        			case 'char':
        			case 'year':
        				break;
        			case 'text':
        			case 'tinytext':
        			case 'mediumtext':
        			case 'longtext':
        				$format = 'ntext1line';
        				break;
        			case 'date':
        				$format = 'date';
        				break;
        			case 'timestamp':
        			case 'datetime':
        				$format = 'datetime';
        				break;
        			case 'time':
        				$format = 'time';
        				break;
        			case 'bit':
        				break;
        			case 'enum':
        				break;
        			case 'set':
        				break;
        			case 'binary':
        			case 'varbinary':
						$visible = 'hidden';
        				break;
        			case 'blob':
        			case 'tinyblob':
        			case 'mediumblob':
        			case 'longblob':
						$visible = 'hidden';
        				break;
        			case 'point':
					case 'multipoint':
        			case 'polygon':
        			case 'multipolygon':
        			case 'linestring':
        			case 'multilinestring':
        			case 'geometry':
					case 'geometrycollection':
						$visible = 'hidden';
						break;
					default:
						$visible = 'hidden';
						break;
        		}
        	}
        	$this->store->set('col_replacename', $index, $replacenames);
       		if (!$this->store->is_set('col_format', $index)) {
       			$this->store->set('col_format', $index, $format);
       		}
       		if (!$this->store->is_set('col_align', $index)) {
       			$this->store->set('col_align', $index, $align);
       		}
       		if (!$this->store->is_set('col_visible', $index)) {
       			if (strtolower($columnName) == 'id') {
       				$this->store->set('col_visible', $index, 'invisible');
       			} else {
       				$this->store->set('col_visible', $index, $visible);
       			}
       		}
        }
        $this->store->removeUntouchedNames();
        $this->store->dat_enableI18N = $this->enableI18N;
        $this->store->dat_enablePjax = $this->enablePjax;
        $this->dat_messageCategory = $this->messageCategory;
        $file = new CodeFile($storefile, $this->store->getJSON());
		if ($file->operation == 'overwrite') {
			$file->operation = 'create';
		}
        $files = [$file];
        
        $controllerClass = str_replace('\\', '/', ltrim($this->controllerClass, '\\'));
    	$controllerFile = Yii::getAlias('@' . $controllerClass . '.php');
        $files[] = new CodeFile($controllerFile, $this->render('controller.php'));

    	$controllerFile2 = Yii::getAlias('@' . $this->underscoreIt($controllerClass) . '.php');
        $file = new CodeFile($controllerFile2, $this->render('_controller.php'));
        if ($file->operation == 'overwrite') {
        	$file->operation = 'create';
        }
        $files[] = $file;
        
        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $filename) {
            if (empty($this->searchModelClass) && $filename === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $filename) && pathinfo($filename, PATHINFO_EXTENSION) === 'php') {
                $file = new CodeFile("$viewPath/$filename", $this->render("views/$filename"));
				if ($filename == '_index.php') {
					if ($file->operation == 'overwrite') {
				       	$file->operation = 'create';
					}
				}
		        $files[] = $file;
            }
        }

        $this->generated = true;       
        return $files;
    }

    /**
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, -10);

        return Inflector::camel2id($class);
    }

    /**
     * @return string the controller view path
     */
    public function getViewPath()
    {
        if (empty($this->viewPath)) {
            return Yii::getAlias('@app/views/' . $this->getControllerID());
        } else {
            return Yii::getAlias($this->viewPath);
        }
    }

    public function getNameAttribute()
    {
        foreach ($this->getColumnNames() as $name) {
            if (!strcasecmp($name, 'name') || !strcasecmp($name, 'title')) {
                return $name;
            }
        }
        /* @var $class \yii\db\ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();

        return $pk[0];
    }

    /**
     * Generates code for active field
     * @param string $attribute
     * @return string
     */
    public function generateActiveField($attribute)
    {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field(\$model, '$attribute')->passwordInput()";
            } else {
                return "\$form->field(\$model, '$attribute')";
            }
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        } elseif ($column->type === 'text') {
            return "\$form->field(\$model, '$attribute')->textarea(['rows' => 6])";
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "\$form->field(\$model, '$attribute')->dropDownList("
                    . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)).", ['prompt' => ''])";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "\$form->field(\$model, '$attribute')->$input()";
            } else {
                return "\$form->field(\$model, '$attribute')->$input(['maxlength' => true])";
            }
        }
    }

    /**
     * Generates code for active search field
     * @param string $attribute
     * @return string
     */
    public function generateActiveSearchField($attribute)
    {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false) {
            return "\$form->field(\$model, '$attribute')";
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        } else {
            return "\$form->field(\$model, '$attribute')";
        }
    }

    /**
     * Generates column format
     * @param \yii\db\ColumnSchema $column
     * @return string
     */
    public function generateColumnFormat($column)
    {
        if ($column->phpType === 'boolean') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return 'ntext1line';
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            return 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            return 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            return 'url';
        } else {
            return 'text';
        }
    }
    
    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules()
    {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }
        $types = [];
        foreach ($table->columns as $column) {
        	if ($this->store->hasSearchField($column->name)) {
	            switch ($column->type) {
    	            case Schema::TYPE_SMALLINT:
        	        case Schema::TYPE_INTEGER:
            	    case Schema::TYPE_BIGINT:
                	    $types['integer'][] = $column->name;
                    	break;
	                case Schema::TYPE_BOOLEAN:
    	                $types['boolean'][] = $column->name;
        	            break;
            	    case Schema::TYPE_FLOAT:
                	case Schema::TYPE_DOUBLE:
	                case Schema::TYPE_DECIMAL:
    	            case Schema::TYPE_MONEY:
        	            $types['number'][] = $column->name;
            	        break;
                	case Schema::TYPE_DATE:
	                case Schema::TYPE_TIME:
    	            case Schema::TYPE_DATETIME:
        	        case Schema::TYPE_TIMESTAMP:
            	    default:
                	    $types['safe'][] = $column->name;
                    	break;
	            }
        	}
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    /**
     * @return array searchable attributes
     */
    public function getSearchAttributes()
    {
    	$list = array();
        foreach ($this->getColumnNames() as $col) {
        	if ($this->store->hasSearchField($col)) {
        		$list[] = $col;
        	}
        }
        return $list;
    }

    /**
     * Generates the attribute labels for the search model.
     * @return array the generated attribute labels (name => label)
     */
    public function generateSearchLabels()
    {
        /* @var $model \yii\base\Model */
        $model = new $this->modelClass();
        $attributeLabels = $model->attributeLabels();
        $labels = [];
        foreach ($this->getColumnNames() as $name) {
        	if ($this->store->hasSearchField($name)) {
        		if (isset($attributeLabels[$name])) {
	                $labels[$name] = $attributeLabels[$name];
    	        } else {
        	        if (!strcasecmp($name, 'id')) {
            	        $labels[$name] = 'ID';
                	} else {
                    	$label = Inflector::camel2words($name);
	                    if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
    	                    $label = substr($label, 0, -3) . ' ID';
        	            }
            	        $labels[$name] = $label;
                	}
                }
            }
        }

        return $labels;
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
        	foreach ($table->columns as $column) {
        		if ($this->store->hasSearchField($column->name)) {
            		$columns[$column->name] = $column->type;
        		}
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'id' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }

    /**
     * Generates action parameters
     * @return string
     */
    public function generateActionParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            return '$id';
        } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     * Generates parameter tags for phpdoc
     * @return array parameter tags for phpdoc
     */
    public function generateActionParamComments()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param ' . $table->columns[$pks[0]]->phpType . ' $id'];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
        }
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * @return boolean|\yii\db\TableSchema
     */
    public function getTableSchema()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema();
        } else {
            return false;
        }
    }

    /**
     * @return array model column names
     */
    public function getColumnNames()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema()->getColumnNames();
        } else {
            /* @var $model \yii\base\Model */
            $model = new $class();

            return $model->attributes();
        }
    }
    
    /**
     * Modified original function "generateString" to handle variablenames in placeholders
     * The variablenames must be enclosed by '', i.e. generateString('Hello {0}!', '$name')
     * @return string
     */
    public function generateString($string = '', $placeholders = [])
    {
    	if ($string == '') {
    		return "''";
    	}
    	$s = parent::generateString($string, $placeholders);
    	$s = preg_replace('/\'(\$[a-zA-Z0-9->\$\(\)\[\]_\\\:]+)\'/', '$1', $s);
    	return $s;
    }
    
    /**
     * Use "generateString" with the messageCatergory of the Yasheena CRUD generator
     * @param string $string
     * @param unknown $placeholders
     * @return unknown
     */
    public function generateMyString($string = '', $placeholders = [])
    {
    	$tmp = $this->messageCategory;
    	$this->messageCategory = 'yashgen';
    	$result = $this->generateString($string, $placeholders);
    	$this->messageCategory = $tmp;
    	return $result;
    }
    
    public function createUrl($name)
    {
    	$name = str_replace([' ', '_', '#', '*', '+', '/'], ['-'], trim(strtolower($name)));
    	preg_match_all('/[-a-z0-9]/', $name, $tmp);
    	$name = preg_replace('/[-]+/', '-', implode($tmp[0]));
    	$name = ltrim($name, '-');
    	return $name;
    }
    
    public function createDropdownList($values, $prefix = '')
    {
    	$list = [];
    	foreach ($values as $value) {
    		$list[$value] = $prefix . $value;
    	}
    	return $list;
    }
    
    public function getListButtonType()
    {
    	return $this->createDropdownList(['None', 'Buttons', 'Dropdown']);
    }
    
    public function getListVisible()
    {
    	return $this->createDropdownList(['visible', 'invisible', 'hidden']);
    }
    
    public function getListAlign()
    {
    	return $this->createDropdownList(['left', 'center', 'right']);
    }
    
    public function getListFormat()
    {
    	return $this->createDropdownList(['text', 'ntext', 'ntext1line', 'ntextshort', 'boolean', 'integer', 'decimal', 'currency', 'percent', 'timestamp', 'datetime', 'date', 'time', 'email', 'url', 'html', 'image', 'spellout', 'size']);
    }
    
    public function getListSearch()
    {
    	return $this->createDropdownList(['text', 'list', 'none']);
    }
    
    public function getGlyphiconNames()
    {
    	return [	
    		'asterisk', 'plus', 'minus', 'euro', 'cloud', 'envelope', 'pencil', 'glass', 'music', 'search', 'heart', 'star', 'star-empty', 'user', 'film', 'th-large', 'th', 'th-list',
    		'ok', 'remove', 'zoom-in', 'zoom-out', 'off', 'signal', 'cog', 'trash', 'home', 'file', 'time', 'road', 'download-alt', 'download', 'upload', 'inbox', 'play-circle',
    		'repeat', 'refresh', 'list-alt', 'lock', 'flag', 'headphones', 'volume-off', 'volume-down', 'volume-up', 'qrcode', 'barcode', 'tag', 'tags', 'book', 'bookmark', 'print',
    		'camera', 'font', 'bold', 'italic', 'text-height', 'text-width', 'align-left', 'align-center', 'align-right', 'align-justify', 'list', 'indent-left', 'indent-right',
    		'facetime-video', 'picture', 'map-marker', 'adjust', 'tint', 'edit', 'share', 'check', 'move', 'step-backward', 'fast-backward', 'backward', 'play', 'pause', 'stop',
    		'forward', 'fast-forward', 'step-forward', 'eject', 'chevron-left', 'chevron-right', 'plus-sign', 'minus-sign', 'remove-sign', 'ok-sign', 'question-sign', 'info-sign',
    		'screenshot', 'remove-circle', 'ok-circle', 'ban-circle', 'arrow-left', 'arrow-right', 'arrow-up', 'arrow-down', 'share-alt', 'resize-full', 'resize-small', 'exclamation-sign',
    		'gift', 'leaf', 'fire', 'eye-open', 'eye-close', 'warning-sign', 'plane', 'calendar', 'random', 'comment', 'magnet', 'chevron-up', 'chevron-down', 'retweet', 'shopping-cart',
    		'folder-close', 'folder-open', 'resize-vertical', 'resize-horizontal', 'hdd', 'bullhorn', 'bell', 'certificate', 'thumbs-up', 'thumbs-down', 'hand-right', 'hand-left',
    		'hand-up', 'hand-down', 'circle-arrow-right', 'circle-arrow-left', 'circle-arrow-up', 'circle-arrow-down', 'globe', 'wrench', 'tasks', 'filter', 'briefcase', 'fullscreen',
    		'dashboard', 'paperclip', 'heart-empty', 'link', 'phone', 'pushpin', 'usd', 'gbp', 'sort', 'sort-by-alphabet', 'sort-by-alphabet-alt', 'sort-by-order', 'sort-by-order-alt',
    		'sort-by-attributes', 'sort-by-attributes-alt', 'unchecked', 'expand', 'collapse-down', 'collapse-up', 'log-in', 'flash', 'log-out', 'new-window', 'record', 'save', 'open',
    		'saved', 'import', 'export', 'send', 'floppy-disk', 'floppy-saved', 'floppy-remove', 'floppy-save', 'floppy-open', 'credit-card', 'transfer', 'cutlery', 'header', 'compressed',
    		'earphone', 'phone-alt', 'tower', 'stats', 'sd-video', 'hd-video', 'subtitles', 'sound-stereo', 'sound-dolby', 'sound-5-1', 'sound-6-1', 'sound-7-1', 'copyright-mark',
    		'registration-mark', 'cloud-download', 'cloud-upload', 'tree-conifer', 'tree-deciduous', 'cd', 'save-file', 'open-file', 'level-up', 'copy', 'paste', 'alert', 'equalizer',
    		'king', 'queen', 'pawn', 'bishop', 'knight', 'baby-formula', 'tent', 'blackboard', 'bed', 'apple', 'erase', 'hourglass', 'lamp', 'duplicate', 'piggy-bank', 'scissors',
    		'bitcoin', 'yen', 'ruble', 'scale', 'ice-lolly', 'ice-lolly-tasted', 'education', 'option-horizontal', 'option-vertical', 'menu-hamburger', 'modal-window', 'oil', 'grain',
    		'sunglasses', 'text-size', 'text-color', 'text-background', 'object-align-top', 'object-align-bottom', 'object-align-horizontal', 'object-align-left', 'object-align-vertical',
    		'object-align-right', 'triangle-right', 'triangle-left', 'triangle-bottom', 'triangle-top', 'superscript', 'subscript', 'menu-left', 'menu-right', 'menu-down', 'menu-up'
    	];
    }
    
    public function getListIcons()
    {
    	return $this->createDropdownList(array_merge(['---'], $this->getGlyphiconNames()), ' ');
    }
    
    public function getCheckboxcolumnField()
    {
    	$list = ['---'];
    	if (($table = $this->getTableSchema()) !== false) {
	        foreach ($table->columns as $column) {
    	        switch ($column->type) {
       				case 'int':
       				case 'tinyint':
       				case 'smallint':
	       			case 'mediumint':
    	   			case 'bigint':
       				case 'boolean':
       				case 'bit':
       					$list[] = $column->name;
       					break;
    	        }
            }
        }
    	return $this->createDropdownList($list);	
    }
    
    public function getGlyphiconOptions()
    {
    	$list = [];
    	foreach ($this->getGlyphiconNames() as $name) {
    		$list[$name] = ['class' => 'glyphicon glyphicon-' . $name]; 
    	}
    	return $list;
    }
    
    public function colLabel($label, $hint)
    {
    	return '<th><label class="control-label help" for="" data-original-title="" title="">' . $label . '</label><div class="hint-block">' .$hint . '</div></th>';
    }
    
    public function underscoreIt($classname) 
    {
    	if ((false === ($n = strrpos($classname, '/'))) && (false === ($n = strrpos($classname, '\\')))) {
    		return '_' . $classname;
    	}
    	return substr($classname, 0, $n + 1) . '_' . substr($classname, $n + 1);
    }
}
