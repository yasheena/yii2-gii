<?php

namespace yasheena\gii;

use yii\web\AssetBundle;

class YasheenaGiiAsset extends AssetBundle
{
	public $sourcePath = '@vendor/yasheena/yii2-gii/assets';
	public $js = ['yasheenaGii.js'];
	public $css = ['yasheenaGii.css'];
	public $depends = [
		'yii\bootstrap\BootstrapAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
	public $publishOptions = [
		'forceCopy' => true,
	];
}