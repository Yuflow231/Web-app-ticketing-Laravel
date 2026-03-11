<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function index()
    {
        return view('projects.projects');
    }

    public function details()
    {
        return view('projects.project-details');
    }

    public function creation()
    {
        return view('projects.project-creation');
    }

}
