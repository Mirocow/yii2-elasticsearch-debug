<?php

namespace mirocow\elasticsearch\debug;

use yii\base\Action;
use yii\web\HttpException;

/**
 * Class PrettyAction
 * @package mirocow\elasticsearch\debug
 */
class PrettyAction extends Action
{
    /**
     * @var DebugPanel
     */
    public $panel;


    public function run($seq, $tag)
    {
        $this->controller->loadData($tag);

        $timings = $this->panel->calculateTimings();

        if (!isset($timings[$seq])) {
            throw new HttpException(404, 'Log message not found.');
        }

        return '<pre>' . json_encode(json_decode($timings[$seq]['query']), JSON_PRETTY_PRINT) . '</pre>';
    }
}
