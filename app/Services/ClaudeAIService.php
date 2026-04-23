<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeAIService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key');
        $this->model  = config('services.anthropic.model', 'claude-sonnet-4-20250514');
    }

    public function prioritizeTasks(Collection $tasks, User $user): array
    {
        $taskList = $tasks->map(fn (Task $t) => [
            'id'                => $t->id,
            'title'             => $t->title,
            'category'          => $t->category,
            'due_date'          => $t->due_date?->toDateTimeString(),
            'estimated_minutes' => $t->estimated_minutes,
            'status'            => $t->status,
            'priority_total'    => $t->priority_total,
        ])->values()->toArray();

        $preferences = $user->preferences ?? [];
        $context     = sprintf(
            'Horario productivo: %s - %s. Días laborales: %s. Carga actual: %d tareas activas.',
            $preferences['work_start'] ?? '08:00',
            $preferences['work_end']   ?? '22:00',
            implode(', ', $preferences['work_days'] ?? ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']),
            $tasks->count()
        );

        $prompt = sprintf(
            "Contexto del estudiante: %s\n\nTareas a priorizar:\n%s\n\n" .
            "Devuelve ÚNICAMENTE un JSON array con esta estructura por tarea:\n" .
            '[{"task_id":1,"ai_priority_rank":1,"ai_justification":"...","suggested_day":"lunes","urgency_label":"critica"}]',
            $context,
            json_encode($taskList, JSON_UNESCAPED_UNICODE)
        );

        $response = $this->call($prompt, 1500, 'Eres un asistente académico experto en productividad estudiantil. Responde ÚNICAMENTE en JSON válido sin markdown ni texto adicional.');

        if (! $response) {
            return [];
        }

        $result = json_decode($response, true);

        if (! is_array($result)) {
            return [];
        }

        foreach ($result as $item) {
            $task = $tasks->firstWhere('id', $item['task_id']);
            if ($task) {
                $task->update([
                    'ai_suggestion'    => $item['ai_justification'] ?? null,
                    'ai_prioritized_at' => now(),
                ]);
            }
        }

        return $result;
    }

    public function getSuggestionForTask(Task $task): string
    {
        $prompt = sprintf(
            'Tarea universitaria: "%s" (categoría: %s, fecha límite: %s, tiempo estimado: %d min, score de prioridad: %d). ' .
            'En máximo 2 oraciones, explica por qué tiene esta prioridad y cómo abordarla.',
            $task->title,
            $task->category,
            $task->due_date?->locale('es')->isoFormat('ddd D MMM HH:mm') ?? 'sin fecha',
            $task->estimated_minutes ?? 0,
            $task->priority_total
        );

        return $this->call($prompt, 200, 'Eres un asistente académico conciso. Responde en español, máximo 2 oraciones, sin markdown.') ?? '';
    }

    public function analyzeProductivityPattern(User $user, array $weeklyStats): string
    {
        $prompt = sprintf(
            'Estadísticas semanales del estudiante %s: %s. ' .
            'Genera un insight personalizado en 2-3 oraciones sobre sus patrones de productividad, ' .
            'mencionando qué tipos de tareas tiende a posponer y cómo mejorar.',
            $user->name,
            json_encode($weeklyStats, JSON_UNESCAPED_UNICODE)
        );

        return $this->call($prompt, 300, 'Eres un coach de productividad académica. Responde en español, de forma motivacional y concisa, sin markdown.') ?? '';
    }

    private function call(string $userPrompt, int $maxTokens, string $systemPrompt): ?string
    {
        try {
            $http = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30);

            if (app()->isLocal()) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($this->apiUrl, [
                'model'      => $this->model,
                'max_tokens' => $maxTokens,
                'system'     => $systemPrompt,
                'messages'   => [
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            if ($response->failed()) {
                Log::error('ClaudeAI API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json('content.0.text');
        } catch (\Throwable $e) {
            Log::error('ClaudeAI exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
