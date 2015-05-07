<?php

namespace console\controllers;

use yii\console\Controller;
use yii\console\Exception;

class StubsController extends Controller
{
    public $outputFile = 'Yii.php';

    protected function getTemplate()
    {
        return <<<TPL
<?php

/**
 * Yii app stub file. Autogenerated by yii2-stubs-generator (stubs console command).
 * Used for enhanced IDE code autocompletion.
 * Updated on {time}
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static \$app;
}
/**{stubs}
 **/
abstract class BaseApplication extends yii\base\Application
{
}

/**{stubs}
 **/
class WebApplication extends yii\web\Application
{
}

/**{stubs}
 **/
class ConsoleApplication extends yii\console\Application
{
}
TPL;
    }

    public function actionIndex($app)
    {
        $path = \Yii::$app->getVendorPath() . DIRECTORY_SEPARATOR . 'Yii.php';

        $components = [];

        foreach (\Yii::$app->requestedParams as $app) {
            $configFile = $app . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'main.php';
            if (!file_exists($configFile)) {
                throw new Exception('Config file doesn\'t exists: ' . $configFile);
            }

            $config = include($configFile);

            foreach ($config['components'] as $name => $component) {
                if (!isset($component['class'])) {
                    continue;
                }

                $components[$name][] = $component['class'];
            }
        }

        $stubs = '';
        foreach ($components as $name => $classes) {
            $classes = implode('|', array_unique($classes));
            $stubs .= "\n * @property {$classes} \$$name";
        }

        $content = str_replace('{stubs}', $stubs, $this->getTemplate());
        $content = str_replace('{time}', date(DATE_ISO8601), $content);

        file_put_contents($path, $content);
    }
}