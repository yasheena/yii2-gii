<?php
/**
 * This is the template for generating the _index file, which is holding the variable parts of the generated form.
 */

use yii\helpers\Html;

$buttonList = ['rgt' => 'right', 'lft' => 'left'];

echo "<?php\n";
?>

/**
 * Do not modify this file if you want to reuse the generator to update settings.
 * Use the file index.php instead to modify the functionality.
 */

use yii\helpers\Html;
use kartik\grid\GridView;
<?php
if ($generator->dat_use_checkboxcolumn && ($generator->dat_lft_button == 'None')) {
	echo "use yii\helpers\Url;\n";
}
foreach ($generator->col_order as $index => $value) {
	if ($generator->col_search[$index] == 'list') {
		echo 'use yii\helpers\ArrayHelper;' . "\n";
		break;
	}
}

foreach ($buttonList as $src => $name) {
	$_btn = 'dat_' . $src . '_button';
	if ($generator->$_btn == 'Dropdown') {
		echo "use yii\bootstrap\ButtonDropdown;\n";
		break;
	}
}

echo "\n\$columns = [\n";
if ($generator->dat_use_serialcolumn) {
	echo "\t['class' => 'kartik\\grid\\SerialColumn', 'order' => 'fixleft'],\n";
}
if ($generator->dat_use_checkboxcolumn) {
	$_btn = 'dat_lft_button';
	echo "\t['class' => 'kartik\\grid\\CheckboxColumn', 'order' => 'fixleft'";
	if ($generator->$_btn == 'None') {
		echo ", 'multiple' => false";
		echo ", 'checkboxOptions' => function(\$model, \$key, \$index, \$widget) {return ['onclick' => 'js:checkboxChanged(this.value, this.checked)'";
		if ($generator->dat_checkboxcolumn_field == '---') {
			$class = new $generator->modelClass;
			if (method_exists($class, 'getCheckboxDefaultValue')) {
				echo ", 'checked' => \$model->getCheckboxDefaultValue(\$key, \$index, \$widget)";
			}
		} else {
			echo ", 'checked' => \$model->" . $generator->dat_checkboxcolumn_field;
		}
		echo "];}";
	} else {
		echo ", 'checkboxOptions' => ['onclick' => 'js:checkboxChanged(this.value, this.checked)']";
	}
	echo "],\n";
}
$extraFormatter = false;
foreach ($generator->getBehavior('valuestore')->sort() as $index) {
	$name		= $generator->col_name[$index];
	$visible	= $generator->col_visible[$index];
	$align		= $generator->col_align[$index];
	$replace	= $generator->col_replace[$index];
	$width		= $generator->col_width[$index];
	$format		= $generator->col_format[$index];
	$search		= $generator->col_search[$index];
	$label		= $generator->col_label[$index];
	if ($visible != 'hidden') {
		if (($visible == 'visible') && ($align == 'left') && ($replace == null) && ($width == '') && ($search == 'text') && ($label == '')
		&& !in_array($format, ['decimal', 'ntextshort'])) {
			echo "	'" . $name . (($format == 'text') ? '' : (':' . $format)) . "',\n";
		} else {
			echo "	[\n";
			if (($replace == null) || ($replace == '---')) {
				echo "		'attribute' => '" . $name . "',\n";
			} else {
				$parts = explode('.', $replace);
				echo "		'attribute' => '" . $parts[0] . "',\n";
				echo "		'value' => '" . $replace ."',\n";
				if ($label == '') {
					$class = new $generator->modelClass;
					$origLabels = $class->attributeLabels();
					echo "		'label' => '" . $origLabels[$name] . "',\n";
				}
			}
			switch($format) {
				case 'text':
					break;
				case 'decimal':
					echo "		'format' => ['" . $format . "', 2],\n";
					break;
				case 'ntextshort':
					echo "		'format' => ['ntext1line', 30],\n";
					break;
				default:
					echo "		'format' => '" . $format . "',\n";
					break;
			}
			if ($label != '') {
				echo "		'label' => '" . $label . "',\n";
			}
			if ($width != '') {
				echo "		'width' => '" . $width . "',\n";
			}
			if ($align != 'left') {
				echo "		'hAlign' => '" . $align . "',\n";
			}
			if ($visible == 'invisible') {
				echo "		'visible' => false,\n";
			}
			if ($search == 'list') {
				if ($replace == null) {
					echo "		'filter' => ArrayHelper::map(" . $generator->modelClass . "::find()->asArray()->all(), '" . $name . "', '" . $name . "'),\n";
				} else {
					echo "		'filter' => ArrayHelper::map(app\\models\\Stock::find()->asArray()->all(), 'id', 'name'),\n";
				}
			}
			echo "	],\n";
		}
		if (in_array($format, ['ntext1line', 'ntextshort'])) {
			$extraFormatter = true;
		}
	}
}
?>
	['class' => 'kartik\grid\ActionColumn', 'order' => 'fixright'],
];

<?php
foreach ($buttonList as $src => $name) {
	$_btn = 'dat_' . $src . '_button';
	$_lbl = $src . '_label';
	$_icn = $src . '_icon';
	$_url = $src . '_url';
	$_hnt = $src . '_hint';
	$icons = $generator->$_icn;
	array_pop($icons);
	$tmp = $icons;
	array_shift($tmp);
	$tmp = array_flip($tmp);
	$useIcons = (count($tmp) > 1) || (array_pop($tmp) != '---');
	$labels = $generator->$_lbl;
	$urls = $generator->$_url;
	$hints = $generator->$_hnt;
	if (count($icons) > 0) {
		echo '$' . $name . 'ButtonItems = [' . "\n";
		foreach ($icons as $index => $icon) {
			if ($index == 0) continue;
			$label = $generator->generateString(Html::encode($labels[$index]));
			$hint = $generator->generateString($hints[$index]);
			switch ($generator->$_btn) {
				case 'Dropdown':
					echo "	['label' => ";
					if ($useIcons) {
						echo '\'<i class="glyphicon glyphicon-' . (($icon == '---') ? 'minus" style="visibility: hidden' : $icon) . '"></i>';
						if ($label != "''") {
							echo ' \' . ' . $label;
						} else {
							echo "'";
						}
					} else {
						echo $label;
					}
					echo ", 'url' => ['" . $urls[$index] . "']"
						. (($hints[$index] == '') ? '' : (", 'options' => ['title' => " . $hint) . "]")
						. "],\n"; 
					break;
				case 'Buttons':
					echo "	Html::a(";
					if ($icon != '---') {
						echo "'<i class=\"glyphicon glyphicon-" . $icon . "\"></i>";
						if ($label != "''") {
							echo  ' \'. ' . $label;
						} else {
							echo "'";
						}
					} else {
						echo $label;
					}
					echo ", ['" . $urls[$index] . "'], ['class'=>'btn btn-default'"
						. (($hints[$index] == '') ? '' : ", 'title' => " . $hint)
						. "]),\n";
					break;
			}
		}
		echo "];\n\n";
	}
}
?>

$gridOptions = [
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'panel' => [
   		'type' => GridView::TYPE_PRIMARY,
   		'heading' => $this->title,
		'before' => '<div style="padding-top: 7px;"><em>' . <?= $generator->generateString($generator->dat_headerInfo) ?> . '</em></div>',
		'after' => <?php
foreach ($buttonList as $src => $name) {
	$_btn = 'dat_' . $src . '_button';
	$_lbl = $src . '_label';
	$labels = $generator->$_lbl;
	array_pop($labels);
	if (($generator->$_btn != 'None') && (count($labels)) > 0) {
		$_ico = $src . '_icon';
		$icons = $generator->$_ico;
		$_url = $src . '_url';
		$urls = $generator->$_url;
		$_hnt = $src . '_hint';
		$hints = $generator->$_hnt;
		echo "'<div class=\"pull-" . $name . " " . $name . "-footer-btn\">'\n";
		switch ($generator->$_btn) {
			case 'Buttons':
				echo "					. implode(' ', \$" . $name . "ButtonItems)\n";
				break;
			case 'Dropdown':
				echo "					. ButtonDropdown::widget([\n"
					. "						'label' => '" . (($icons[0] != '---') ? "<i class=\"glyphicon glyphicon-" . $icons[0] . "\"></i> " : '') . Html::encode($labels[0]) . "',\n"
					. "						'options' => ['class' => 'btn-default'"
						. (($hints[0] == '') ? '' : ", 'title' => " . $generator->generateMyString($hints[0]))
						. "],\n"
					. "						'dropdown' => ['items' => \$" . $name . "ButtonItems, 'encodeLabels' => false],\n"
					. "						'encodeLabel' => false,\n"
					. "					])\n";
				break;
		}
		echo  "					. '</div>'\n					. ";
	}
}
?>
'<div style="padding-top: 5px;"><?= ($generator->dat_lft_button != 'None') ? '&nbsp; &nbsp;' : '' ?><em>' . <?= $generator->generateString($generator->dat_footerInfo) ?> . '</em></div><div class="clearfix"></div>',
	],
<?= $extraFormatter ? '	\'formatter\' => new \yasheena\view\YFormatter(),' : '' ?>

	'responsive' => true,
	'hover' => true,
	'persistResize' => true,
	'toolbar' => [
		[
			'content'=>
				Html::a('<i class="glyphicon glyphicon-plus"></i> ' . <?= $generator->generateMyString('Create {0}', '$elementName') ?>, 
					['create'], ['class'=>'btn btn-success']
				) . ' ' .
				Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''], [
					'class' => 'btn btn-default',
					'title' => <?= $generator->generateMyString('Reset filter/sorting') ?>
						
				]) . ' {dynagridFilter}{dynagridSort}{dynagrid}',
		],
		'{export}',
		'{toggleData}'
	],
	'exportConfig' => [
		GridView::PDF => [],
		GridView::EXCEL => [],
		GridView::CSV => [],
		GridView::TEXT => [],
	],
];

<?php 
if ($generator->dat_use_checkboxcolumn) {
	if ($generator->dat_lft_button == 'None') : ?>
$this->registerJs("
function restoreCheckbox(value){
	var ele = $('.kv-row-checkbox[name=\"selection[]\"][value=' + value + ']');
	ele.prop('checked', !ele.prop('checked')).change();
}
function checkboxChanged(value, checked){
	\$('*').css('cursor', 'progress');
   	\$.ajax({
		url: '" . Url::to(['checkboxchanged']) . "',
		method: 'post',
		dataType: 'text',
		data: {vakue:value, checked:checked}
	})
	.done(function(result) {\$('*').css('cursor', 'default');if(result!='ok'){restoreCheckbox(value);alert(result);}})
	.error(function() {\$('*').css('cursor', 'default');restoreCheckbox(value); alert('" . <?= $generator->generateMyString('Error solving checkbox change!') ?> . "')});
}
", 1);	// 1 => View::POS_HEAD
<?php else: ?>
$this->registerJs("
function checkboxChanged() {
	var sel = '<?= ($generator->dat_lft_button == 'Buttons') ? 'div.left-footer-btn a' : 'div.left-footer-btn button' ?>';
	if ($('.kv-row-checkbox[name=\"selection[]\"]:checked').length > 0) {
		$(sel).off('click').css('opacity', '1');
	} else {	
		$(sel).click(function() { return false; }).css('opacity', '0.5');
	}	
};
", 1);	// 1 => View::POS_HEAD
$this->registerJs("
$(document).on('ready pjax:success', function(){
	checkboxChanged();
});
checkboxChanged();
");
<?php endif;
}

