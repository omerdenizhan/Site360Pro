<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function toggle(Request $request)
    {
        $theme = in_array($request->input('theme'), ['light', 'dark'], true) ? $request->input('theme') : 'light';
        session(['theme' => $theme]);
        return response()->json(['status' => 'success', 'theme' => $theme]);
    }
}
