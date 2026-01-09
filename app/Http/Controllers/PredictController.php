<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        $data_prediction = $response->json();
        $prediction_label = $data_prediction['class'] ?? 'unknown';

        $geminiAnalysis = $this->getGeminiExpertReasoning($request->file('image'), $prediction_label);

        return response()->json([
            'prediction' => $data_prediction,
            'expert_analysis' => Str::markdown($geminiAnalysis)
        ]);
    }

    /**
     * New function to handle the Gemini API call
     */
    private function getGeminiExpertReasoning($imageFile, $label)
    {
        $apiKey = env('GEMINI_API_KEY');

        $prompt = "You are a professional Sustainability & Waste Expert. " .
          "I have detected this as '$label'. Even if there is no recycling symbol, " .
          "analyze its physical properties in the image. " .
          "1. What specific plastic/material does it look like? (e.g. HDPE, PET, LDPE) " .
          "2. If it is dirty, how should the user clean it? " .
          "3. Give one creative upcycling idea specifically for this shape/object.";

        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [
                    ['text' => $prompt],
                    ['inline_data' => [
                        'mime_type' => $imageFile->getMimeType(),
                        'data' => base64_encode(file_get_contents($imageFile->getPathname()))
                    ]]
                ]]
            ],
            // 🛡️ Disable all safety blocks to ensure a response is always sent
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
            ]
        ]);

        $data = $response->json();

        // 🕵️ DEBUG: If text is missing, return the raw error message from Google
        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return "Google API Error: " . ($data['error']['message'] ?? 'Check your API Key and Billing status.');
        }

        return $data['candidates'][0]['content']['parts'][0]['text'];
    }
}
