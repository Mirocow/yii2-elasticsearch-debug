<?php

namespace mirocow\elasticsearch\debug;

use yii\data\ArrayDataProvider;
use yii\debug\components\search\Filter;

class SearchModel extends \yii\debug\models\search\Base
{

    public $query;

    public $route;

    public $trace;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['query', 'route', 'trace'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'route' => 'Route',
            'query' => 'Query',
            'timestamp' => 'Time',
        ];
    }

    /**
     * Returns data provider with filled models. Filter applied if needed.
     *
     * @param array $models data to return provider for
     * @return \yii\data\ArrayDataProvider
     */
    public function search($models)
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => $models,
            'pagination' => false,
            'sort' => [
                'attributes' => ['duration', 'route', 'timestamp'],
            ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $filter = new Filter();
        $this->addCondition($filter, 'route', true);
        $this->addCondition($filter, 'query', true);
        $dataProvider->allModels = $filter->filter($models);

        return $dataProvider;
    }
}
