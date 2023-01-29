<?php

namespace App\Support\Latte;

use Latte\Loaders\FileLoader;


/**
 * パス指定をカスタマイズした
 * Latteのテンプレートローダー
 */
class LatteFileLoader extends FileLoader
{
    /**
     * Returns referred template name.
     */
    public function getReferredName(string $file, string $referringFile): string
    {
        return $file;
    }
}
