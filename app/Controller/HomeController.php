<?php

namespace App\Controller;

class HomeController
{
    /**
     * home page
     *
     * @return string
     */
    public function home(): string
    {
        return latte('page.home');
    }
}
