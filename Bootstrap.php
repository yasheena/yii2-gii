<?php

namespace yasheena\gii;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * @package yasheena\yii2-gii
 */
class Bootstrap implements BootstrapInterface
{
	/**
	 * Bootstrap method to be called during application bootstrap stage.
	 *
	 * @param Application $app the application currently running
	 */
	public function bootstrap($app)
	{
		if ($app->hasModule('gii')) {
			if (!isset($app->getModule('gii')->generators['yasheena-gii'])) {
				$app->getModule('gii')->generators['yasheea-gii-crud']['class'] = 'yasheena\gii\crud\Generator';
			}
		}
	}
}
