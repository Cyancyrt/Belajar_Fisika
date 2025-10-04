<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use App\Models\SimulationQuestion;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($questionId)
    {
        $question = SimulationQuestion::findOrFail($questionId);
        return view('Dashboard.module.friction.friction_soal3', compact('question'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function submitAnswer($userAnswer, $criteria)
    {
        $targetValue = $criteria['target_value'];
        $tolerance = $criteria['tolerance'];
        $userValue = $userAnswer['calculated_value'] ?? 0;

        return abs($userValue - $targetValue) <= $tolerance;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Simulation $simulation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Simulation $simulation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Simulation $simulation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Simulation $simulation)
    {
        //
    }
}
