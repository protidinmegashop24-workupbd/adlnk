<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }

    public function cookies(): View
    {
        return view('pages.cookies');
    }

    public function acceptableUse(): View
    {
        return view('pages.acceptable-use');
    }

    public function dmca(): View
    {
        return view('pages.dmca');
    }
}
