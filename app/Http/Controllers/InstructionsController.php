<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InstructionsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Instructions/Edit', [
            'instructions' => Auth::user()->instructions,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'instructions' => 'nullable|string|max:2000',
        ]);

        Auth::user()->update([
            'instructions' => $request->instructions,
        ]);

        return back()->with('success', 'Instructions mises à jour !');
    }
}