<?php

namespace App\Http\Controller;

use Microcms\Client;
use ArrayObject;
use Carbon\Carbon;

class HomeController
{
    /**
     * ホーム
     *
     * @return string
     */
    public function home(Client $client)
    {
        $contents = $client->list('blogs', [
            "limit" => 10,
        ]);

        /** @var ArrayObject[] $blogs */
        $blogs = array_map(
            fn (object $blog) => new ArrayObject([
                'id' => $blog->id,
                'eyecatch' => isset($blog->eyecatch)
                    ? $blog->eyecatch->url
                    : null,
                'title' => $blog->title,
                'content' => $blog->content,
                'category' => isset($blog->category)
                    ? new ArrayObject([
                        'id' => $blog->category->id,
                        'name' => $blog->category->name,
                    ])
                    : null,
                'publishedAt' => new Carbon($blog->publishedAt),
                'createdAt' => new Carbon($blog->createdAt),
                'updatedAt' => new Carbon($blog->updatedAt),
            ], ArrayObject::ARRAY_AS_PROPS),
            $contents->contents,
        );

        return latte('page.home', compact('blogs'));
    }
}
