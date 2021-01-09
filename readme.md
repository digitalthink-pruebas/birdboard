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
