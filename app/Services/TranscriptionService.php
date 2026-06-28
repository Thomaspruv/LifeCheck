<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranscriptionService
{
    /**
     * Transcribe an audio file using OpenAI Whisper API.
     *
     * @param string $audioContents Raw binary audio data (OGG Opus from Telegram voice)
     * @param string $filename      Filename for the API (e.g., 'voice.ogg')
     * @return string|null          Transcribed text, or null on failure
     */
    public function transcribe(string $audioContents, string $filename = 'voice.ogg'): ?string
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            Log::warning('TranscriptionService: OpenAI API key not configured.');
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])
                ->attach(
                    'file',
                    $audioContents,
                    $filename
                )
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1',
                    'language' => 'fr',
                    'response_format' => 'json',
                ]);

            if ($response->failed()) {
                Log::warning('TranscriptionService: OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            return $data['text'] ?? null;
        } catch (\Throwable $e) {
            Log::error("TranscriptionService: Exception: {$e->getMessage()}");
            return null;
        }
    }
}
