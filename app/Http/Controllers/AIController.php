<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AIController extends Controller
{
    public function generarDescripcion(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $name = trim((string) $request->input('name'));

        $hfKey = env('HF_API_KEY');
        if (!$hfKey) {
            return response()->json([
                'descripcion' => '',
                'error_msg' => 'Falta configuración del servicio de IA.'
            ], 500);
        }

        $url = "https://router.huggingface.co/v1/chat/completions";

        // 1) Clasificación rápida (JSON estricto)
        $categoria = $this->clasificarCategoria($hfKey, $url, $name);

        // 2) Fallback heurístico si viene dudoso
        if (!$categoria) {
            $categoria = $this->heuristicaCategoria($name);
        }

        // 3) Prompt según categoría
        $prompt = $this->buildPromptPorCategoria($categoria, $name);

        // Modelos para generación
        $models = [
            'HuggingFaceH4/zephyr-7b-beta',
            'Qwen/Qwen2.5-7B-Instruct',
            'microsoft/Phi-3-mini-4k-instruct',
        ];

        try {
            foreach ($models as $model) {
                $response = Http::withToken($hfKey)
                    ->acceptJson()
                    ->timeout(90)
                    ->post($url, [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'system', 'content' => 'Eres un redactor profesional enfocado en bienestar y productos terapéuticos.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 320,
                    ]);

                if (!$response->successful()) {
                    continue;
                }

                $json = $response->json();
                $descripcion = data_get($json, 'choices.0.message.content');

                if (is_string($descripcion) && trim($descripcion) !== '') {
                    return response()->json([
                        'descripcion' => $this->normalizeTexto($descripcion),
                    ]);
                }
            }

            return response()->json([
                'descripcion' => '',
                'error_msg' => 'No se pudo generar la descripción.'
            ], 500);

        } catch (\Throwable $e) {
            return response()->json([
                'descripcion' => '',
                'error_msg' => 'Error al generar la descripción.'
            ], 500);
        }
    }

    public function generarImagen(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
        ]);

        $name = trim((string)$request->input('name'));
        $desc = trim((string)$request->input('description', ''));

        $hordeKey = env('HORDE_API_KEY', '0000000000');

        $notes = $this->extractShortNotesFromDesc($desc);

        /**
         * ✅ Prompt orientado a CEPAS (flor/buds) medicinales
         */
        $positive = "High quality macro photo of medical cannabis flower buds (dried flower), "
            . "natural green tones with orange pistils, visible trichomes, "
            . "studio soft lighting, shallow depth of field, clean neutral background, "
            . "photorealistic, sharp focus, product photography. "
            . "Strain name: {$name}. "
            . ($notes !== '' ? "Aroma notes: {$notes}." : "");

        $negative = "text, logo, watermark, label, typography, packaging, jar, bottle, bag, box, "
            . "cartoon, illustration, anime, painting, CGI, 3d render, lowres, blurry, "
            . "human, face, hands, cigarette, joint, smoke, bong, pipe";

        // ✅ Marker para confirmar deploy
        $serverMarker = 'AIController-IMG-2026-02-26-v5';

        // ✅ SAFE para anónimo: <=576
        $params = [
            'n' => 1,
            'width' => 512,
            'height' => 512,
            'steps' => 20,
            'sampler_name' => 'k_euler_a',
        ];

        try {
            $payload = [
                'prompt' => $positive,
                'nsfw'   => false,
                'censor_nsfw' => true,
                'params' => array_merge($params, [
                    'negative_prompt' => $negative,
                ]),
            ];

            // 1) Crear job async
            $create = Http::withHeaders([
                    'apikey' => $hordeKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout(60)
                ->post('https://aihorde.net/api/v2/generate/async', $payload);

            if (!$create->successful()) {
                $status  = $create->status();
                $rawBody = (string) $create->body();

                $msg = 'AI Horde: no se pudo iniciar el job.';
                if ($status === 403 && str_contains($rawBody, 'KudosUpfront')) {
                    $msg = 'AI Horde exige kudos (API key anónima). Baja resolución a <=576x576 o usa una API key con kudos.';
                }

                return response()->json([
                    'ok'            => false,
                    'error_msg'     => $msg,
                    'status'        => $status,
                    'raw'           => mb_substr($rawBody, 0, 900),
                    'server_marker' => $serverMarker,
                    'sent_params'   => $payload['params'],
                    'sent_prompt'   => mb_substr($positive, 0, 240),
                ], $status);
            }

            $job = $create->json();
            $id  = $job['id'] ?? null;

            if (!$id) {
                return response()->json([
                    'ok'            => false,
                    'error_msg'     => 'AI Horde: respuesta sin id de job.',
                    'raw'           => mb_substr((string)$create->body(), 0, 900),
                    'server_marker' => $serverMarker,
                ], 500);
            }

            // 2) Polling corto
            $deadline = microtime(true) + 35.0;
            $statusData = null;

            while (microtime(true) < $deadline) {
                usleep(1600000);

                $check = Http::withHeaders([
                        'apikey' => $hordeKey,
                        'Accept' => 'application/json',
                    ])
                    ->timeout(30)
                    ->get("https://aihorde.net/api/v2/generate/check/{$id}");

                if (!$check->successful()) continue;

                $statusData = $check->json();
                if (!empty($statusData['done'])) break;
            }

            if (!is_array($statusData) || empty($statusData['done'])) {
                return response()->json([
                    'ok'            => false,
                    'error_msg'     => 'La imagen quedó en cola (AI Horde). Intenta de nuevo en unos segundos.',
                    'job_id'        => $id,
                    'server_marker' => $serverMarker,
                ], 503);
            }

            // 3) Resultado final
            $final = Http::withHeaders([
                    'apikey' => $hordeKey,
                    'Accept' => 'application/json',
                ])
                ->timeout(60)
                ->get("https://aihorde.net/api/v2/generate/status/{$id}");

            if (!$final->successful()) {
                $st = $final->status();
                return response()->json([
                    'ok'            => false,
                    'error_msg'     => 'AI Horde: terminado, pero no pude obtener la imagen.',
                    'status'        => $st,
                    'raw'           => mb_substr((string)$final->body(), 0, 900),
                    'server_marker' => $serverMarker,
                ], $st);
            }

            $finalJson = $final->json();
            $generations = $finalJson['generations'] ?? [];
            $img = (is_array($generations) && isset($generations[0]['img'])) ? (string)$generations[0]['img'] : '';

            if (trim($img) === '') {
                return response()->json([
                    'ok'            => false,
                    'error_msg'     => 'AI Horde no devolvió la imagen (img vacío).',
                    'debug'         => $finalJson,
                    'server_marker' => $serverMarker,
                ], 500);
            }

            // ✅ Robust decode: URL / dataURL / base64 con espacios / padding
            $imgRaw = trim($img);
            $binary = null;

            if (preg_match('#^https?://#i', $imgRaw)) {
                $dl = Http::timeout(60)->get($imgRaw);
                if (!$dl->successful()) {
                    return response()->json([
                        'ok'            => false,
                        'error_msg'     => 'AI Horde devolvió URL, pero no pude descargar la imagen.',
                        'status'        => $dl->status(),
                        'img_preview'   => mb_substr($imgRaw, 0, 180),
                        'server_marker' => $serverMarker,
                    ], 502);
                }
                $binary = $dl->body();
            } else {
                $imgClean = preg_replace('#^data:image/\w+;base64,#i', '', $imgRaw);
                $imgClean = preg_replace('/\s+/u', '', $imgClean);
                $imgClean = strtr($imgClean, '-_', '+/');

                $pad = strlen($imgClean) % 4;
                if ($pad !== 0) {
                    $imgClean .= str_repeat('=', 4 - $pad);
                }

                $binary = base64_decode($imgClean, true);
                if ($binary === false) {
                    $binary = base64_decode($imgClean, false);
                }

                if ($binary === false || $binary === '') {
                    return response()->json([
                        'ok'            => false,
                        'error_msg'     => 'No se pudo decodificar la imagen (img no es base64/URL válido).',
                        'img_preview'   => mb_substr($imgRaw, 0, 180),
                        'img_len'       => strlen($imgRaw),
                        'server_marker' => $serverMarker,
                    ], 502);
                }
            }

            // ✅ Detectar extensión por MIME (si está disponible)
            $ext = 'webp';
            if (function_exists('finfo_open')) {
                $f = finfo_open(FILEINFO_MIME_TYPE);
                if ($f) {
                    $mime = finfo_buffer($f, $binary) ?: null;
                    finfo_close($f);

                    if ($mime === 'image/png') $ext = 'png';
                    elseif ($mime === 'image/jpeg') $ext = 'jpg';
                    elseif ($mime === 'image/webp') $ext = 'webp';
                }
            }

            $filename = 'ai-products/' . now()->format('Ymd_His') . '_' . substr(sha1($name), 0, 10) . '.' . $ext;
            Storage::disk('public')->put($filename, $binary);

            // ✅ IMPORTANTÍSIMO: devolver URL por ruta Laravel (evita 403 en /storage)
            $publicUrl = route('admin.ai.img', ['path' => $filename]);

            return response()->json([
                'ok'            => true,
                'url'           => $publicUrl,
                'path'          => $filename,
                'job_id'        => $id,
                'server_marker' => $serverMarker,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'ok'            => false,
                'error_msg'     => 'Excepción generando imagen: ' . $e->getMessage(),
                'server_marker' => $serverMarker,
            ], 500);
        }
    }

    // ✅ NUEVO: servir imagen desde Storage::disk('public') (evita /storage bloqueado)
    public function serveAiImage(string $path)
    {
        $path = ltrim($path, '/');

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
        $content = Storage::disk('public')->get($path);

        return response($content, 200)->header('Content-Type', $mime);
    }

    private function extractShortNotesFromDesc(string $desc): string
    {
        $desc = trim(str_replace(["\r\n", "\r"], "\n", $desc));
        if ($desc === '') return '';

        $lines = array_values(array_filter(array_map('trim', explode("\n", $desc))));
        if (!$lines) return '';

        $pick = '';
        foreach ($lines as $ln) {
            if (Str::startsWith(mb_strtolower($ln), 'aromas y sabores:')) { $pick = $ln; break; }
        }
        if ($pick === '') {
            $pick = $lines[0] ?? '';
        }

        $pick = preg_replace('/\s+/u', ' ', $pick);
        return mb_substr($pick, 0, 140);
    }

    // -----------------------
    // Helpers (los tuyos)
    // -----------------------

    private function clasificarCategoria(string $hfKey, string $url, string $name): ?string
    {
        $prompt =
            "Clasifica el producto según su nombre.\n"
            . "Nombre: {$name}\n\n"
            . "Devuelve SOLO JSON válido sin texto adicional, con este esquema:\n"
            . "{\"categoria\":\"A|B|C|D\",\"confianza\":0.0}\n\n"
            . "Reglas:\n"
            . "- A = Cepa/flor/cultivar.\n"
            . "- B = Aceite/extracto/tintura/vape cartridge.\n"
            . "- C = Accesorio (grinder, papel, pipa, bong, boquillas, etc.).\n"
            . "- D = Otro.\n"
            . "- Si el nombre es creativo tipo \"Coco Nutty\" y NO incluye unidades (ml/mg/g) ni palabras de aceite/accesorio, inclínate por A.\n";

        try {
            $resp = Http::withToken($hfKey)
                ->acceptJson()
                ->timeout(45)
                ->post($url, [
                    'model' => 'Qwen/Qwen2.5-7B-Instruct',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Respondes únicamente con JSON estricto.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.0,
                    'max_tokens' => 80,
                ]);

            if (!$resp->successful()) return null;

            $content = (string) data_get($resp->json(), 'choices.0.message.content', '');
            $data = json_decode(trim($content), true);
            if (!is_array($data)) return null;

            $cat = $data['categoria'] ?? null;
            $conf = $data['confianza'] ?? null;

            if (!in_array($cat, ['A','B','C','D'], true)) return null;
            if (!is_numeric($conf)) $conf = 0;

            if ((float)$conf < 0.55) return null;

            return $cat;

        } catch (\Throwable $e) {
            return null;
        }
    }

    private function heuristicaCategoria(string $name): string
    {
        $n = mb_strtolower($name);

        $oilKw = ['aceite','oil','tintura','extracto','extract','resina','rosin','wax','shatter','destilado','distillate','cartucho','cartridge','vape','pod'];
        foreach ($oilKw as $k) if (str_contains($n, $k)) return 'B';

        $accKw = ['grinder','moledor','papel','paper','raw','filtro','filters','pipa','pipe','bong','boquilla','tip','encendedor','lighter','bateria','battery','vaporizador','vaporizer'];
        foreach ($accKw as $k) if (str_contains($n, $k)) return 'C';

        if (preg_match('/\b(\d+(\.\d+)?\s?(ml|mg|gr|g|kg))\b/u', $n)) {
            if (preg_match('/\b(ml|mg)\b/u', $n)) return 'B';
            return 'D';
        }

        $words = preg_split('/\s+/', trim($n));
        $wc = is_array($words) ? count(array_filter($words)) : 0;

        if ($wc >= 1 && $wc <= 3) return 'A';

        return 'D';
    }

    private function buildPromptPorCategoria(string $categoria, string $name): string
    {
        $baseRules =
            "Reglas generales:\n"
            . "- Tono profesional, enfoque bienestar/uso medicinal.\n"
            . "- No lenguaje recreativo/ilegal.\n"
            . "- No prometer curas; usa \"puede ayudar\", \"se usa para\", \"orientado a\".\n"
            . "- Si no estás seguro de un dato (THC/CBD/%), entrega rangos típicos o marca \"aprox.\".\n"
            . "- Entrega SOLO el texto final (sin explicar tu razonamiento).\n\n";

        if ($categoria === 'A') {
            return
                "Genera una ficha técnica de CEPA (flor/cultivar) basándote en el nombre.\n"
                . "Nombre: {$name}\n\n"
                . $baseRules
                . "Formato EXACTO (cada campo en su propia línea):\n"
                . "Título: (Nombre de la cepa)\n"
                . "Tipo: (Índica / Sativa / Híbrida) + porcentaje aprox. (Ej: 60% índica / 40% sativa)\n"
                . "THC: (rango % aprox.)\n"
                . "CBD: (rango % aprox. o \"bajo\"/\"moderado\")\n"
                . "Terpenos probables: (3 a 5)\n"
                . "Aromas y sabores: (3 a 6)\n"
                . "Efectos esperables: (3 a 6)\n"
                . "Usos terapéuticos habituales: (3 a 6)\n"
                . "Recomendación de uso: (día/noche y por qué, 1-2 líneas)\n"
                . "Nota: (1 línea de seguridad y advertencia)\n";
        }

        if ($categoria === 'B') {
            return
                "Genera una descripción para ACEITE/EXTRACTO/TINTURA con enfoque bienestar.\n"
                . "Nombre: {$name}\n\n"
                . $baseRules
                . "Incluye 3 a 6 líneas con: tipo, concentración estimada (si no se sabe, rangos), uso habitual, beneficios orientados a bienestar y precauciones.\n";
        }

        if ($categoria === 'C') {
            return
                "Genera una descripción para ACCESORIO.\n"
                . "Nombre: {$name}\n\n"
                . $baseRules
                . "Incluye 3 a 6 líneas con: para qué sirve, ventajas prácticas, materiales/cuidado si corresponde.\n";
        }

        return
            "Genera una descripción profesional para un producto relacionado.\n"
            . "Nombre: {$name}\n\n"
            . $baseRules
            . "Entrega 3 a 6 líneas útiles con enfoque bienestar.\n";
    }

    private function normalizeTexto(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        $lines = explode("\n", $text);
        $clean = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/[ \t]+/u', ' ', $line));
            if ($line !== '') $clean[] = $line;
        }

        return trim(implode("\n", $clean));
    }
}