<?php

namespace Test;

use Takemo101\Egg\Http\Testing\HttpExecuter;

class ControllerTest extends AppTestCase
{
    /**
     * @test
     */
    public function home_test()
    {
        /** @var HttpExecuter */
        $executer = $this->app->make(HttpExecuter::class);

        $response = $executer->get(
            route('home'),
        );

        $this->assertEquals(
            200,
            $response->getStatusCode(),
        );
    }
}
