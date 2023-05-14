<?php

namespace Module\Latte\Latte;

use Latte\Engine as Latte;

/**
 * Latteのテンプレート出力クラス
 */
final class LatteViewGenerator
{
    /**
     * @var array<string,mixed>
     */
    private array $shared = [];

    /**
     * constructor
     *
     * @param Latte $latte
     */
    public function __construct(
        private readonly Latte $latte,
    ) {
        //
    }

    /**
     * Latteでテンプレート出力をする
     *
     * @param string $path
     * @param object|mixed[] $params
     * @param string|null $block
     * @return string
     */
    public function generate(
        string $path,
        object|array $params = [],
        ?string $block = null,
    ): string {

        /** @var object|mixed[] */
        $parameters = is_array($params)
            ? [
                ...$this->shared,
                ...$params,
            ]
            : $params;

        return $this->latte->renderToString($path, $parameters, $block);
    }

    /**
     * テンプレートに共有する値を登録
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function share(string $key, mixed $value): self
    {
        $this->shared[$key] = $value;

        return $this;
    }
}
