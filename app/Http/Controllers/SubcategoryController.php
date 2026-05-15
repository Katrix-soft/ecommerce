<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subcategory;

class SubcategoryController extends Controller
{
    public function show(Subcategory $subcategory){
        return view('subcategories.show', compact('subcategory'));
    }
    
}
