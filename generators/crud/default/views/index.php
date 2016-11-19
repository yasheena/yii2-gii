<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use kartik\dynagrid\DynaGrid;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>


/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$elementName = <?= $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>;

include '_index.php';

// You may modify here the structures defined in _index.php ($gridOptions, $colums, ...)

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
<?= $generator->enablePjax ? '	<?php Pjax::begin(); ?>' : '' ?>

	<?= "<?= " ?>DynaGrid::widget([
		'options' => ['id' => 'dg-<?= StringHelper::basename($generator->modelClass) ?>-index'],
		'allowThemeSetting' => false,
		'gridOptions' => $gridOptions, 
		'columns' => $columns,
	]); ?>
<?= $generator->enablePjax ? '	<?php Pjax::end(); ?>' : '' ?>

</div>
