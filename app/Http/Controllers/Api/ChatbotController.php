<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    //

    public function index(Request $request)
    {

    // return $request->all();

        $apiKey = env('GROQ_API_KEY');

        $campos = $request->input('campos', '{}');

        $prompt  = 'Eres un asistente que llena formularios.
                          DEBES responder SIEMPRE en formato JSON: {"mensaje": "...", "campos": ' . $campos . '}.
                          Mantén el hilo de la conversación. y no respondas nada que no sea el JSON. tampoco respondas con texto plano.
                          Si es usuario te pregunta algo no relacionado con el sitio web establecimientos, servicios, categorías, o algo
                           que no tenga que ver con el sitio web, responde con un mensaje de no puedo ayudarte con la informacion que no sea realacionada
                           al sitio web en el campo  "mensaje" y deja "campos" vacío, solo agrega los datos de los campos si te lo solicitan por ejemplo si utilizan
                           palabras como , ayudame a crea, o quiero redaccion, o ayudame a completar.
                           a continuacion te envio las consultas del usuario: ';

        $mensajesUsuario = $request->input('mensajes', [
            [
                'role' => 'user',
                'content' => 'hola?'
            ]
        ]);


        $mensajesFinales = [];

        $ultimoMensajeArray = end($mensajesUsuario);

        // si el historial es mayor a 6 mensajes, solo toma los ultimos 6 mensajes y el prompt, si no toma todo el historial
         if (count($mensajesUsuario) > 6) {
            $mensajesUsuario = array_slice($mensajesUsuario, -6);
        }

        foreach ($mensajesUsuario as $msj) {

            $mensajesFinales[] = [
                'role' => $msj['role'],
                'content' => ($msj['role'] === 'user' ? $prompt : '') . $msj['content']
            ];

        }



        $response = Http::withToken($apiKey)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // O el modelo que prefieras de Groq
                'messages' => $mensajesFinales,
                'temperature' => 1,
                'max_completion_tokens' => 8192,
                'top_p' => 1,
                'stream' => false, // Cambiado a false para simplificar la respuesta inicial
                'response_format' => [
                    'type' => 'json_object',
                    // 'json_object_format' => 'gpt_message'
                ]
            ]);


        if ($response->successful()) {
            $resultado = $response->json();
            return response()->json([
                'respuesta' => $resultado['choices'][0]['message']['content'] ?? null
            ]);
        }

        return response()->json([
            'error' => 'No se pudo conectar con Groq',
            'details' => $response->body()
        ], $response->status());
    }
}
