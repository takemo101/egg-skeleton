<?php

namespace App\Entity;

use  App\Repository\BlogRepository;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity(
    repository: BlogRepository::class,
    table: 'blogs',
)]
class Blog
{
    public function __construct(
        #[Column(type: 'string', primary: true)]
        public string $id,
        #[Column(type: 'string')]
        private string $title,
    ) {
        //
    }
}
