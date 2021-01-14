<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un usuario tiene proyectos
     * @test
     * @return void
     */
    public function a_user_has_projects()
    {
        // Dado un usuario
        $user = factory('App\User')->create();

        // Este usuario puede acceder a los proyectos
        $this->assertInstanceOf(Collection::class, $user->projects);
    }
}
