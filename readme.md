<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

# Curso Build A Laravel App With TDD (https://laracasts.com/series/build-a-laravel-app-with-tdd)
------------------------------------------------------------------------------------------------
Creación del proyecto Birdboard: a minimal Basecamp-like project management app.

## Lección 1 de 44: Crear proyecto
----------------------------------
	composer create-project --prefer-dist laravel/laravel birdboard "5.7.*"

	Establecer usuario y contraseña de Git

		git config --global user.name "digitalthink-pruebas"
		git config --global user.email "digitalthink.es-pruebas@gmail.com"

	Añadir a Git

		git init
		git add .
		git commit -m "Install Framework"

## Lección 2 de 44: Comencemos con un test
------------------------------------------
    php artisan make:test ProjectsTest

    Si un usuario puede crear un proyecto, este debe poder verse en la base de de datos y en el navegador

    withFaker --> Permite usar el componente Faker para datos de prueba
    RefreshDatabase --> Cuando se ejecute el test sobre la base de datos se realiza Rollback y la base de datos queda
    como antes de ejecutar el test

    Error

        SQLSTATE[HY000] [1045] Access denied for user 'homestead'@'localhost' (using password: YES) (SQL: SHOW FULL TABLES WHERE table_type = 'BASE TABLE')

        Crear base de datos birdboard en mysql
        Configurar archivo .env

Error

    SQLSTATE[42S02]: Base table or view not found: 1146 Table 'birdboard.projects' doesn't exist

    Modificar archivo phpunit.xml para sobreescribir la conexión y establecerla como sqlite

        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>

    Nota: Para corregir el error PDOException: could not find driver hay que cargar el driver de sqlite

        sudo apt-get install php7.4-sqlite

Error:  SQLSTATE[HY000]: General error: 1 no such table: projects

    Crear migración para generar tabla projects
    
        php artisan make:migration create_projects_table

    Nota: No hace falta ejecutar la migración, ya que al ejecutar los test, con el uso de RefreshDatabase las migraciones
    se ejecutan automáticamente

    Nota: Utilizar $this->withoutExceptionHandling(); para no enmascarar las excepciones y que salgan todas

        Ahora sale Symfony\Component\HttpKernel\Exception\NotFoundHttpException : POST http://localhost/projects, ya
        que la ruta /projects no está definida

            Definirna el el archivo web.php

                Route::post('/projects', function () {
                    // Validar
                    // Persistir
                    App\Project::create(request(['title', 'description']));
                    // Redireccionar
                });

Corregir error Class 'App\Project' not found

    php artisan make:model Project
    
Corregir error Add [title] to fillable property to allow mass assignment on [App\Project].

    protected $fillable=['title', 'description']; // Esto permite asignación masiva a estas columnas
        
Corregir error SQLSTATE[HY000]: General error: 1 table projects has no column named title

    En el archivo de migración ejecutar $table->string('title');
    Insertar también para la columna description $table->text('description');

Corregir error Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException

    Route::get('/projects', function () {
        $projects = App\Project::all();
    
        return view('projects.index', compact('projects'));
    });

Corregir error View [projects.index] not found.

    Crear archivo index.blade.php en carpeta resources/views/projects

Refactorizar: Crear controlador para proyectos

    php artisan make:controller ProjectsController

    Mover lógica de negocio al controlador

        public function index()
        {
            $projects = App\Project::all();
    
            return view('projects.index', compact('projects'));
        }
    
        public function store()
        {
            // Validar
            // Persistir
            App\Project::create(request(['title', 'description']));
            // Redireccionar
        }

    En web.php quedaría

        Route::get('/projects', 'ProjectsController@index');
        
        Route::post('/projects', 'ProjectsController@store');

	Añadir a Git

		git add .
		git commit -m "Crear primeros test"

## Lección 3 de 44: Validando peticiones
----------------------------------------
    Cuando se intente dar de alta un proyecto sin título, se deben tener errores, que serán almacenados en sesión y serán
    verificables a través de assertSessionHasErrors

    Pueden filtrase los test a ejecutar con --fileter
        Ej: vendor/bin/phpunit --filter a_project_requires_a_title

    Podemos definir un alias para facilitar la ejecución de comandos
        alias pf="vendor/bin/phpunit --filter"

    Hacer lo mismo para el campo description

    Crear model factory 
        php artisan make:factory ProjectFactory --model="App\Project"

    Poblar mediante tinker
        php artisan tinker
            Podemos crear elementos mediante 2 comandos
                factory('App\Project')->create() // Persiste en la base de datos
                factory('App\Project')->make() // No persiste en la base de datos y devuelve el resultado como un objeto
                factory('App\Project')->raw() // No persiste en la base de datos y devuelve el resultado como un array

    	Añadir a Git

            git add .
            git commit -m "Validando peticiones"

## Lección 4 de 44: Test sobre modelos
--------------------------------------
Cuando un usuario crea un proyecto puede visualizar el título y la descripción

Creado método a_user_can_view_a_project

Crear método show en ProjectsController

    public function show()
    {
        $project = Project::findOrFail(request('project')); // si el proyecto a visualizar no existe se lanza una excepción

        return view('projects.index', compact('projects'));
    }

Creado archivo resources\views\projecs\show.blade.php

Creado test unitario para probar rutas

    php artisan make:test ProjectTest --unit

Crear 5 proyectos de prueba a través de tinker

    factory('App\Project', 5)->create();

Añadir a Git

    git add .
    git commit -m "Lección 4 de 44: Test sobre modelos"
    git push origin main

## Lección 5 de 44: Un proyecto debe tener un propietario
---------------------------------------------------------
Creada función public function a_project_requires_an_owner() en ProjectsTest.php

Crear dato owner_id en la tabla

Ejecutar migración

    php artisan migrate:refresh

Ejecutar php artisan make:auth

Crear test unitario UserTest

    php artisan make:test UserTest --unit

Añadir a Git

    git add .
    git commit -m "Lección 5 de 44: Un proyecto debe tener un propietario"
    git push origin main

## Lección 6 de 44: Ámbito de visibilidad
-----------------------------------------
Un proyecto solo se puede visualizar por su creador.

Poner middleware en ruta para estar logado y ver los proyectos que me pertenecen

    Route::get('/projects', 'ProjectsController@index')->middleware('auth');

Crear proyecto para el usuario que he creado (tinker)

    App\Project::forceCreate(['title' => 'My project', 'description' => 'Lorem ipsum', 'owner_id' => 12]);

    El owner_id se saca de la tabla users

Agrupar todos los middleware en uno

    Route::get('/projects', 'ProjectsController@index')->middleware('auth');
    Route::get('/projects/{project}', 'ProjectsController@show')->middleware('auth');
    Route::post('/projects', 'ProjectsController@store')->middleware('auth');

    Pasa a ser 

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/projects', 'ProjectsController@index');
        Route::get('/projects/{project}', 'ProjectsController@show');
        Route::post('/projects', 'ProjectsController@store');
    });

Añadir a Git

    git add .
    git commit -m "Lección 6 de 44: Ámbito de visibilidad"
    git push origin main
