# yii2-elasticsearch-debug

![](https://raw.githubusercontent.com/Mirocow/yii2-elasticsearch-debug/master/panel.png)

# Install

```php
if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
        'panels' => [
            'elasticsearch' => [
                'class' => 'mirocow\\elasticsearch\\debug\\DebugPanel',
            ],
        ],
    ];
}
];
