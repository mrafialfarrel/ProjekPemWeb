<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        return redirect('/dashboard' . ($request->has('filter') ? '?filter=' . urlencode($request->query('filter')) : ''));
    }
}