# Latte View Module
Latteでテンプレートレンダリングするためのモジュールです。

## インストール
設定ファイルをコピーします。
```bash
php command resource:publish latte
```

モジュールをアプリケーションに追加します。
```php
<?php

// setting/module.php

use Module\View\ViewModule;
use Takemo101\Egg\Module\Modules;

return function (Modules $modules) {
    $modules->add(
        // ...
        ViewModule::class,
    );
};
```
