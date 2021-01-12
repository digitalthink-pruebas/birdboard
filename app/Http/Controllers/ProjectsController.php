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

    public function show()
    {
        $project = Project::findOrFail(request('project'));

        return view('projects.index', compact('projects'));
    }

    public function store()
    {
        // Validar
        $attributes = request()->validate(['title' => 'required', 'description' => 'required']);

        // Persistir
        Project::create($attributes);

        // Redireccionar
        return redirect('/projects');
    }
}
