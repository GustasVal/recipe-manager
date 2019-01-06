<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CookbookFormRequest;
use App\Cookbook;
use Auth;

class CookbookController extends Controller
{
    public function createForm() {
        return view('cookbooks.create');
    }

    public function create(CookbookFormRequest $request) {
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $cookbook = Cookbook::create($input);
        if ($cookbook->id) {
            \Toast::success('Kochbuch erfolgreich erstellt');
            return view('cookbooks.create');
        } else {
            abort(500);
        }
    }
}