<?php

namespace mirocow\elasticsearch\debug;

use Yii;
use yii\debug\Panel;
use yii\log\Logger;

/**
 * Class DebugPanel
 * @package mirocow\elasticsearch\debug
 */
class DebugPanel extends Panel
{
    public $db = 'elasticsearch';

    private $_timings = null;

    public function init()
    {
        $this->actions['elasticsearch-query'] = [
            'class' => 'mirocow\\elasticsearch\\debug\\DebugAction',
            'panel' => $this,
            'db' => $this->db,
        ];

        $this->actions['js-pretty'] = [
            'class' => 'mirocow\\elasticsearch\\debug\\PrettyAction',
            'panel' => $this,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Elasticsearch';
    }

    /**
     * @return string short name of the panel, which will be use in summary.
     */
    public function getSummaryName()
    {
        return 'ES';
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        return ['messages' => $this->getProfileLogs()];
    }

    /**
     * Returns all profile logs of the current request for this panel. It includes categories such as:
     * 'yii\db\Command::query', 'yii\db\Command::execute'.
     * @return array
     */
    public function getProfileLogs()
    {
        $target = $this->module->logTarget;

        return $target->filterMessages(
            $target->messages,
            Logger::LEVEL_PROFILE | Logger::LEVEL_TRACE, [
            'mirocow\elasticsearch\components\indexes\AbstractSearchIndex::execute',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSummary()
    {
        $timings = $this->calculateTimings();
        $queryCount = count($timings);
        $queryTime = 0;
        foreach ($timings as $timing) {
            $queryTime += $timing['duration'];
        }
        $queryTime = number_format($queryTime * 1000) . ' ms';
        $url = $this->getUrl();

        return Yii::$app->view->render('@mirocow/elasticsearch/debug/views/default/summary', [
            'panel' => $this,
            'queryCount' => $queryCount,
            'queryTime' => $queryTime,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        $timings = $this->calculateTimings();

        $searchModel = new SearchModel;
        $searchModel->load(Yii::$app->request->getQueryParams());
        $dataProvider = $searchModel->search($timings);

        return Yii::$app->view->render('@mirocow/elasticsearch/debug/views/default/detail', [
            'panel' => $this,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @return array|null
     */
    public function calculateTimings()
    {
        if ($this->_timings !== null) {
            return $this->_timings;
        }
        $messages = isset($this->data['messages']) ? $this->data['messages'] : [];
        $timings = [];
        $stack = [];
        foreach ($messages as $i => $log) {
            list($token, $level, $category, $timestamp) = $log;
            $log[5] = $i;
            if ($level == Logger::LEVEL_PROFILE_BEGIN) {
                $stack[] = $log;
            } elseif ($level == Logger::LEVEL_TRACE) {
                list($message, $query) = explode("\n", $token);
            } elseif ($level == Logger::LEVEL_PROFILE_END) {
                if (($last = array_pop($stack)) !== null && $last[0] === $token) {
                    $timings[$last[5]] = [
                        'queries' => count($stack),
                        'route' => $token,
                        'timestamp' => $last[3] * 1000,
                        'duration' => $timestamp - $last[3],
                        'trace' => $last[4],
                        'query' => $query,
                        'seq' => $last[5],
                    ];
                    $query = '';
                }
            }
        }

        $now = microtime(true);
        while (($last = array_pop($stack)) !== null) {
            $delta = $now - $last[3];
            $timings[$last[5]] = [
                'queries' => count($stack),
                'route' => $last[0],
                'timestamp' => $last[2] * 1000,
                'duration' => $delta,
                'trace' => $last[4],
                'seq' => $last[5],
            ];
        }
        ksort($timings);

        return $this->_timings = $timings;
    }

}