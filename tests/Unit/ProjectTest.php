<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    /**
     * @@test
     * Un proyecto tiene una ruta (path) a la que acceder
     *
     * @return void
     *
     */

    use RefreshDatabase;

    public function it_has_a_path()
    {
        $project = factory('App\Project')->create();

        $this->assertEquals('/projects/' . $project->id, $project->path());
    }
}
