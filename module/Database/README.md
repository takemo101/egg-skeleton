# Cycle Database Module
CycleORMでデータベースを操作するためのモジュールです。

## インストール
設定ファイルをコピーします。
```bash
php command resource:publish cycle
```

モジュールをアプリケーションに追加します。
```php
<?php

// setting/module.php

use Module\Database\DatabaseModule;
use Takemo101\Egg\Module\Modules;

return function (Modules $modules) {
    $modules->add(
        // ...
        DatabaseModule::class,
    );
};
```
