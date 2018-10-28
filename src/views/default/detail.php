<?php

use yii\helpers\Html;
use yii\web\View;

echo \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'db-panel-detailed-grid',
    'options' => ['class' => 'detail-grid-view table-responsive'],
    'filterModel' => $searchModel,
    'filterUrl' => $panel->getUrl(),
    'columns' => [
        [
            'attribute' => 'timestamp',
            'label' => 'Time',
            'value' => function ($data) {
                $timeInSeconds = $data['timestamp'] / 1000;
                $millisecondsDiff = (int) (($timeInSeconds - (int) $timeInSeconds) * 1000);

                return date('H:i:s.', $timeInSeconds) . sprintf('%03d', $millisecondsDiff);
            },
            'headerOptions' => [
                'class' => 'sort-numerical'
            ]
        ],
        [
            'attribute' => 'route',
            'value' => function ($data) {
                return $data['route'];
            },
            'options' => [
                'width' => '30%',
            ],
            'headerOptions' => [
                //'class' => 'sort-numerical'
            ]
        ],
        [
            'attribute' => 'query',
            'label' => 'Query',
            'value' => function ($data) use ($panel) {
                $output = [];

                $output[] = Html::tag('div', Html::encode($data['query']));

                if (!empty($data['trace'])) {
                    $output[] = Html::ul($data['trace'], [
                        'class' => 'trace',
                        'item' => function ($trace) use ($panel) {
                            return '<li>' . $panel->getTraceLine($trace) . '</li>';
                        },
                    ]);
                }

                $output[] = Html::tag('p', '', ['class' => 'js-pretty-text']);

                $output[] = Html::tag(
                    'div',
                    Html::a('[+] Pretty', ['js-pretty', 'seq' => $data['seq'], 'tag' => Yii::$app->controller->summary['tag']]),
                    ['class' => 'js-pretty']
                );

                return implode("", $output);
            },
            'format' => 'raw',
            'options' => [
                'width' => '60%',
            ],
        ],
        [
            'attribute' => 'hits',
            'value' => function ($data) {
                return $data['hits'];
            },
            'options' => [
                'width' => '5%',
            ],
            'headerOptions' => [
                //'class' => 'sort-numerical'
            ]
        ],
        [
            'attribute' => 'aggs',
            'value' => function ($data) {
                return $data['aggs'];
            },
            'options' => [
                'width' => '5%',
            ],
            'headerOptions' => [
                //'class' => 'sort-numerical'
            ]
        ],
        [
            'attribute' => 'duration',
            'value' => function ($data) {
                return sprintf('%.1f ms', $data['duration'] * 1000);
            },
            'options' => [
                'width' => '10%',
            ],
            'headerOptions' => [
                'class' => 'sort-numerical'
            ]
        ],
    ],
]);

$this->registerJs('debug_js_pretty();', View::POS_READY);
?>

<script>
function debug_js_pretty() {
    $('.js-pretty a').on('click', function(e) {
        e.preventDefault();

        var $json = $('.js-pretty-text', $(this).parent().parent());

        if ($json.is(':visible')) {
            $json.hide();
            $(this).text('[+] Pretty');
        } else {
            $json.load($(this).attr('href')).show();
            $(this).text('[-] Pretty');
        }
    });
}
</script>