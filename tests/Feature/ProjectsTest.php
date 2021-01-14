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

        $this->actingAs(factory('App\User')->create());

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
        $this->actingAs(factory('App\User')->create());

        $attributes = factory('App\Project')->raw(['title' => '']);

        // Cuando se insserta un proyecto, este debe contener un título
        $this->post('/projects', $attributes)
            ->assertSessionHasErrors('title');
    }

    /** @test  */
    public function a_project_requires_a_description()
    {
        $this->actingAs(factory('App\User')->create());

        $attributes = factory('App\Project')->raw(['description' => '']);

        // Cuando se insserta un proyecto, este debe contener un título
        $this->post('/projects', $attributes)
            ->assertSessionHasErrors('description');
    }

    /** @test  */
    public function guest_cannot_create_projects()
    {
        //$this->withoutExceptionHandling();

        //$attributes = factory('App\Project')->raw(['owner_id' => null]);
        // Cuando se insserta un proyecto, este debetener un propietario
        //$this->post('/projects', $attributes)
        //    ->assertSessionHasErrors('owner_id');

        $attributes = factory('App\Project')->raw();

        // Si el usuario no está autenticado debe ser redirigido a la página de login
        $this->post('/projects', $attributes)
            ->assertRedirect('login');

    }

    /** @test  */
    public function guest_cannot_view_projects()
    {
        //$this->withoutExceptionHandling();

        // Si el usuario no está autenticado debe ser redirigido a la página de login
        $this->get('/projects')
            ->assertRedirect('login');
    }

    /** @test  */
    public function guest_cannot_view_a_single_project()
    {
        //$this->withoutExceptionHandling();

        $project = factory('App\Project')->create();

        // Si el usuario no está autenticado debe ser redirigido a la página de login
        $this->get($project->path())
            ->assertRedirect('login');
    }

    /** @test  */
    public function a_user_can_view_a_their_project()
    {
        $this->withoutExceptionHandling();

        $this->be(factory('App\User')->create());

        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }
}
