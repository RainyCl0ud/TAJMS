<?php

namespace App\Http\Controllers;


class SidebarController extends Controller
{
    public function coordinator() 
    {
     return view ('sidebar.coordinator');
    }

    public function trainee() 
    {
     return view ('sidebar.trainee');
    }

}
