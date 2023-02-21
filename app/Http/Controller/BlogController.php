<?php

namespace App\Http\Controller;

use App\Repository\BlogRepository;
use Carbon\Carbon;
use Microcms\Client;
use ArrayObject;
use Cycle\ORM\ORMInterface;
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
    public function show(ORMInterface $orm, string $id)
    {
        /** @var BlogRepository */
        $repository = $orm->getRepository('blog');

        $blog = $repository->findByPK($id);

        return latte('page.blog.show', compact('blog'));
    }
}
