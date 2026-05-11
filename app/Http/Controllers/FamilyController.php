<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Family;


class FamilyController extends Controller
{
    public function show(Family $family)
    {
        return view('families.show', compact('family'));
    }
}
