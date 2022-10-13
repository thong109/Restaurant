<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $specials = Menu::get();
        if (!$specials == null) {
            return view('welcome', compact('specials'));
        } else {
            return dd('ád');
        }
    }
    public function thankyou()
    {
        return view('thankyou');
    }
}
