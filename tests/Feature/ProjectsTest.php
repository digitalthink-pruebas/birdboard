<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsTest extends TestCase
{
    use withFaker, RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph
        ];

        // Petición de inserción de nuevo proyecto
        // Una vez insertado debe redirigir a la página de visualización de los proyectos existentes
        $this->post('/projects', $attributes)
                ->assertRedirect('/projects');

        // La base de datos debe contener una tabla denominada projects con los datos de $attributes insertados
        $this->assertDatabaseHas('projects', $attributes);

        // Cuando se recupere el proyecto debe poder visualizarse el título en la vista
        $this->get('/projects')->assertSee($attributes['title']);
    }

    /** @test  */
    public function a_project_requires_a_title()
    {
        $attributes = factory('App\Project')->raw(['title' => '']);

        // Cuando se insserta un proyecto, este debe contener un título
        $this->post('/projects', $attributes)
            ->assertSessionHasErrors('title');
    }

    /** @test  */
    public function a_project_requires_a_description()
    {
        $attributes = factory('App\Project')->raw(['description' => '']);

        // Cuando se insserta un proyecto, este debe contener un título
        $this->post('/projects', $attributes)
            ->assertSessionHasErrors('description');
    }

    /** @test  */
    public function a_user_can_view_a_project()
    {
        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create();

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }
}
