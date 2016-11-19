<?php
/**
 * This is the template for generating the CRUD controller class file, which depends on the variable parts of the generated form.
 */

use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->underscoreIt($generator->controllerClass));
$childControllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$buttonList = ['rgt' => 'right', 'lft' => 'left'];

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use <?= ltrim($generator->baseControllerClass, '\\') ?>;

/**
 * <?= $controllerClass ?> implements the additional actions for <?= $modelClass ?> model depending on the variable parts of the generated form.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
<?php
$reserved = ['Index', 'View', 'Create', 'Update', 'Delete'];
$list = array();
foreach ($buttonList as $src => $name) {
	$_btn = 'dat_' . $src . '_button';
	$_url = $src . '_url';
	$urls = $generator->$_url;
	array_pop($urls);
	if (($generator->$_btn != 'None') && (count($urls)) > 0) {
		$_lbl = $src . '_label';
		$lbls = $generator->$_lbl;
		$_icn = $src . '_icon';
		$icns = $generator->$_icn;
		$type = ($generator->$_btn == 'Dropdown') ? 'dropdown entry' : 'button';
		foreach ($urls as $index => $url) {
			if ($index == 0) continue;
			if (false !== strpos($url, '/')) continue;
			$url = explode('-', $url);
			array_walk($url, function(&$value) { $value = ucfirst($value); });
			$url = implode($url);
			if (in_array($url, $reserved)) continue;
			if ($lbls[$index] != '') {
				$lbl = $lbls[$index];
			} else {
				$lbl = $icns[$index] . '-icon';
			}
			$lbl = $name . ' ' . $type . ' "' . $lbl . '"'; 
			if (array_key_exists($url, $list)) {
				if (!array_key_exists($lbl, $list[$url])) {
					$list[$url][] = $lbl;
				}
			} else {
				$list[$url] = array($lbl);
			}
		}
	}
}
foreach ($list as $url => $info) {
?>
    /**
     * Action of <?= implode(' and ', $info) . '.' ?>
     
     * Overwrite this function in "<?= $childControllerClass ?>".
     * @return mixed
     */
    public function action<?= $url ?>()
    {
		return "action<?= $url ?> is still not implemented in <?= $childControllerClass ?>!";
    }

<?php 
}
if ($generator->dat_use_checkboxcolumn) {
	if ($generator->dat_lft_button == 'None') {
?>
    /**
     * Update the database depending on the changed state of the checkbox     
     * @return mixed
     */
    public function actionCheckboxchanged()
    {
		return 'ok';
    }

<?php
	}
}
?>
}
