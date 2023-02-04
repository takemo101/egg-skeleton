<?php

namespace App\Support\Path;

use Takemo101\Egg\Support\Filesystem\PathHelper;

/**
 * このアプリケーションのパスを取得する
 */
class AppPath
{
    /**
     * @var PathHelper
     */
    private readonly PathHelper $helper;

    /**
     * constructor
     *
     * @param string $resourcePath
     * @param string $lattePath
     */
    public function __construct(
        public readonly string $resourcePath,
        public readonly string $lattePath,
    ) {
        $this->helper = new PathHelper();
    }

    /**
     * リソースのパスを取得
     *
     * @param string|null $path
     * @return string
     */
    public function resourcePath(?string $path = null): string
    {
        return $path
            ? $this->helper->join(
                $this->resourcePath,
                $path,
            )
            : $this->resourcePath;
    }

    /**
     * Latteのパスを取得
     *
     * @param string|null $path
     * @return string
     */
    public function lattePath(?string $path = null): string
    {
        return $path
            ? $this->helper->join(
                $this->lattePath,
                $path,
            )
            : $this->lattePath;
    }
}
