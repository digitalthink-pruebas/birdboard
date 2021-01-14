<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;

class ProjectsController extends Controller
{
    public function index()
    {
        //$projects = Project::all();

        $projects = auth()->user()->projects;

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {

        //if (auth()->id() != $project->owner_id)
        if (auth()->user()->isNot($project->owner))
        {
            abort(403);
        }

        return view('projects.show', compact('project'));
    }

    public function store()
    {
        // Validar
        $attributes = request()->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        // El id del usuario es el del usuario autenticado
        //$attributes['owner_id'] = auth()->id();

        // Persistir
        auth()->user()->projects()->create($attributes);

        //Project::create($attributes);

        // Redireccionar
        return redirect('/projects');
    }
}
