<?php

namespace App\Http\Controller;

use App\Repository\BlogRepository;
use Microcms\Client;
use ArrayObject;
use Carbon\Carbon;
use Cycle\ORM\ORMInterface;

class HomeController
{
    /**
     * ホーム
     *
     * @return string
     */
    public function home(ORMInterface $orm)
    {
        /** @var BlogRepository */
        $repository = $orm->getRepository('blog');

        $blogs = $repository->findAll();

        return latte('page.home', compact('blogs'));
    }
}
