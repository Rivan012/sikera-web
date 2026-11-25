<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use Inertia\Inertia;

class CaseStudyController extends Controller
{
    public function index()
    {
        $caseStudies = CaseStudy::all();

        return Inertia::render('Cases/Index', compact('caseStudies'));
    }

    public function show($id)
    {
        $caseStudy = CaseStudy::findOrFail($id);

        return Inertia::render('Cases/Show', ['caseStudy' => $caseStudy]);
    }
}
