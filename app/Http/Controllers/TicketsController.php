<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index()
    {
        return view('tickets.tickets');
    }

    public function details()
    {
        return view('tickets.ticket-details');
    }

    public function creation()
    {
        return view('tickets.ticket-creation');
    }

}
