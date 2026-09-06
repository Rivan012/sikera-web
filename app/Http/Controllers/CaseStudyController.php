<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;

class CaseStudyController extends Controller
{
    public function index()
    {
        $caseStudies = CaseStudy::all();
        return view('cases.index', compact('caseStudies'));
    }

    public function show($id)
    {
        $case = CaseStudy::findOrFail($id);
        return view('cases.show', compact('case'));
    }
}
