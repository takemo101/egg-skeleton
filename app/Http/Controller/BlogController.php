<?php

namespace App\Http\Controller;

use Carbon\Carbon;
use Microcms\Client;
use ArrayObject;
use Takemo101\Egg\Http\Exception\NotFoundHttpException;

class BlogController
{
    /**
     * 新着記事一覧
     *
     * @return string
     */
    public function index()
    {
        return latte('page.blog.index');
    }

    /**
     * 新着詳細
     *
     * @param string $id
     * @return string
     */
    public function show(Client $client, string $id)
    {
        try {
            $content = $client->get('blogs', $id);
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }

        $blog = new ArrayObject([
            'id' => $content->id,
            'eyecatch' => isset($content->eyecatch)
                ? $content->eyecatch->url
                : null,
            'title' => $content->title,
            'content' => $content->content,
            'category' => isset($content->category)
                ? new ArrayObject([
                    'id' => $content->category->id,
                    'name' => $content->category->name,
                ])
                : null,
            'publishedAt' => new Carbon($content->publishedAt),
            'createdAt' => new Carbon($content->createdAt),
            'updatedAt' => new Carbon($content->updatedAt),
        ], ArrayObject::ARRAY_AS_PROPS);

        return latte('page.blog.show', compact('blog'));
    }
}
