<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PreUserController extends Controller
{
    public function index()
    {
        $pageTitle = 'Pre user dashboard'; 
        return view  ('pre_user.dashboard' , compact ('pageTitle'));
    }
   
}
