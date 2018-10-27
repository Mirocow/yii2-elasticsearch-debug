# yii2-elasticsearch-debug

![](https://monosnap_files.s3.amazonaws.com/ms_61669/quYy31NVdBigm4rom6W8y1epkT5zPA/%25D0%25A0%25D0%25B5%25D0%25B7%25D1%2583%25D0%25BB%25D1%258C%25D1%2582%25D0%25B0%25D1%2582%25D1%258B%2B%25D0%25BF%25D0%25BE%25D0%25B8%25D1%2581%25D0%25BA%25D0%25B0%2B2018-10-27%2B08-57-05.png?Signature=%2Be01GljjJg302M7IU9EiCjEu1P8%3D&Expires=1540621144&AWSAccessKeyId=AKIAJG2SOFH45AI7FPOQ&response-content-disposition=attachment%3B%20filename%2A%3DUTF-8%27%27%25D0%25A0%25D0%25B5%25D0%25B7%25D1%2583%25D0%25BB%25D1%258C%25D1%2582%25D0%25B0%25D1%2582%25D1%258B%2520%25D0%25BF%25D0%25BE%25D0%25B8%25D1%2581%25D0%25BA%25D0%25B0%25202018-10-27%252008-57-05.png&response-content-type=image/png)

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
