<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    /**
     * Afficher la page de politique de confidentialité
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('privacy');
    }
}


