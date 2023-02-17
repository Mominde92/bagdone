<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrivacyController extends Controller
{
    protected $namespace = null;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('frontend.privacy.privacy');
    }

    public function terms(Request $request)
    {
        return view('frontend.terms.terms');
    }



}
