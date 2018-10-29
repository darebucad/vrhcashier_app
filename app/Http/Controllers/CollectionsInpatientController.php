<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionsInpatientController extends Controller
{
    
    public function index() {

    	return view('collections.inpatient.index');
    }
}
