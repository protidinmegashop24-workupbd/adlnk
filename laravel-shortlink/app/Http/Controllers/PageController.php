<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return $this->show('about');
    }

    public function contact(): View
    {
        return $this->show('contact');
    }

    public function privacy(): View
    {
        return $this->show('privacy');
    }

    public function terms(): View
    {
        return $this->show('terms');
    }

    public function cookies(): View
    {
        return $this->show('cookies');
    }

    public function acceptableUse(): View
    {
        return $this->show('acceptable-use');
    }

    public function dmca(): View
    {
        return $this->show('dmca');
    }

    private function show(string $slug): View
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('pages.show', ['page' => $page]);
    }
}
