<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictController extends Controller
{
    public function predict(Request $request)
    {
        $request->validate([
            'image' => 'required|image'
        ]);

        $response = Http::attach(
            'file',
            fopen($request->file('image')->getPathname(), 'r'),
            $request->file('image')->getClientOriginalName()
        )->post('http://127.0.0.1:8000/predict');

        return response()->json($response->json());
    }
}
