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

use Module\Latte\LatteModule;
use Takemo101\Egg\Module\Modules;
use Takemo101\Egg\Support\ServiceAccessor\HookAccessor as Hook;

Hook::onByType(
    fn (Modules $modules) => $modules->add(
        // ...
        LatteModule::class,
    ),
);
```
