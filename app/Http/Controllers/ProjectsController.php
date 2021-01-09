<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = Project::all();

        return view('projects.index', compact('projects'));
    }

    public function store()
    {
        // Validar

        // Persistir
        Project::create(request(['title', 'description']));

        // Redireccionar
        return redirect('/projects');
    }
}
