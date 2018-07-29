<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFavoriteController extends Controller
{
    public function store(Request $request, $id)
    {
        \Auth::user()->favo($id);
        return redirect()->back();
    }

    public function destroy($id)
    {
        \Auth::user()->unfavo($id);
        return redirect()->back();
    }
}
