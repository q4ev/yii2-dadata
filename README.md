# yii2-dadata
Wrapper for working with the service easier

Config expected to be

```php
return [
    'db' => require 'db.php',
    'daData' => [
        'class' => 'q4ev\daData\DaData',
        'token' => 'abcdef1234567890abcdef1234567890abcdef00',    
    ],
];
```