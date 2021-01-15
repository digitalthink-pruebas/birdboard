<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use vendor\project\StatusTest;

class ManageProjectsTest extends TestCase
{
    use withFaker, RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(factory('App\User')->create());

        $this->get('/projects/create')->assertStatus(200);

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
    public function guest_cannot_manage_projects()
    {
        //$this->withoutExceptionHandling();

        $project = factory('App\Project')->create();

        // Si el usuario no está autenticado debe ser redirigido a la página de login

        $this->get('/projects')
            ->assertRedirect('login');

        $this->get('/projects/create')
            ->assertRedirect('login');

        $this->get($project->path())
            ->assertRedirect('login');

        $this->post('/projects', $project->toArray())
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

    /** @test  */
    public function an_authenticated_user_cannot_view_the_projects_of_others()
    {
        //$this->withoutExceptionHandling();

        $this->be(factory('App\User')->create());

        $project = factory('App\Project')->create();

        $this->get($project->path())
            ->assertStatus(403);
    }

    /** @test  */
    public function it_belongs_to_an_owner()
    {
        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create();
        $this->assertInstanceOf('App\User', $project->owner);
    }


}
