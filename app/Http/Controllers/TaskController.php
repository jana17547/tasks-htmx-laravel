<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        if (!in_array($status, ['all', 'active', 'done'], true)) {
            $status = 'all';
        }

        $tasksQuery = Task::query();

        if ($q !== '') {
            $tasksQuery->where('title', 'like', "%{$q}%");
        }

        if ($status === 'active') {
            $tasksQuery->where('done', false);
        } elseif ($status === 'done') {
            $tasksQuery->where('done', true);
        }

        $tasks = $tasksQuery
            ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->latest()
            ->get();

        $stats = $this->buildStats($tasks->count(), $status, $q);

        if ($request->header('HX-Request')) {
            return view('tasks._region', compact('tasks', 'stats', 'status', 'q'));
        }

        return view('tasks.index', compact('tasks', 'stats', 'status', 'q'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:120'],
        ], [
            'title.required' => 'Unesi naziv task-a.',
            'title.max' => 'Naziv je predugačak (max 120).',
        ]);

        if ($validator->fails()) {
            return response(
                view('tasks._form_errors', ['bag' => $validator->errors()])->render(),
                422
            )->header('HX-Retarget', '#form-feedback')
                ->header('HX-Reswap', 'innerHTML');
        }

        [$title, $priority, $dueDate] = $this->parseSmartInput((string) $request->input('title'));

        $task = Task::create([
            'title' => $title,
            'priority' => $priority,
            'due_date' => $dueDate,
        ]);

        $stats = $this->buildStats();
        $toast = ['type' => 'success', 'message' => 'Task dodat ✅'];

        $oob  = view('tasks._oob', compact('stats', 'toast'))->render();
        $item = view('tasks._item', compact('task'))->render();

        return response($oob . $item, 200);
    }

    public function toggle(Request $request, Task $task)
    {
        $task->update(['done' => ! $task->done]);

        $q = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');

        $stats = $this->buildStats();
        $toast = [
            'type' => 'success',
            'message' => $task->done ? 'Označeno kao završeno ✅' : 'Vraćeno u aktivno ↩️',
        ];

        $oob = view('tasks._oob', compact('stats', 'toast'))->render();

        if (!$this->matches($task, $q, $status)) {
            return response($oob, 200)->header('HX-Reswap', 'delete');
        }

        $item = view('tasks._item', compact('task'))->render();
        return response($oob . $item, 200);
    }

    public function edit(Task $task)
    {
        return view('tasks._edit_form', compact('task'));
    }

    public function row(Task $task)
    {
        return view('tasks._item', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:120'],
            'priority' => ['required', 'in:low,normal,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response(
                view('tasks._edit_form', ['task' => $task, 'bag' => $validator->errors()])->render(),
                422
            );
        }

        $task->update([
            'title' => (string) $request->input('title'),
            'priority' => (string) $request->input('priority'),
            'due_date' => $request->input('due_date') ?: null,
        ]);

        $q = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');

        $stats = $this->buildStats();
        $toast = ['type' => 'success', 'message' => 'Sačuvano 💾'];

        $oob = view('tasks._oob', compact('stats', 'toast'))->render();

        if (!$this->matches($task, $q, $status)) {
            return response($oob, 200)->header('HX-Reswap', 'delete');
        }

        $item = view('tasks._item', compact('task'))->render();
        return response($oob . $item, 200);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        $stats = $this->buildStats();

        $toast = [
            'type' => 'danger',
            'message' => 'Task obrisan.',
            'undo' => [
                'label' => 'Vrati',
                'url' => route('tasks.restore', ['id' => $task->id]),
            ],
        ];

        return response(view('tasks._oob', compact('stats', 'toast'))->render(), 200);
    }

    public function restore(Request $request, int $id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();

        $q = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');

        $stats = $this->buildStats();
        $toast = ['type' => 'success', 'message' => 'Vraćeno ✅'];

        $oob = view('tasks._oob', compact('stats', 'toast'))->render();

        if (!$this->matches($task, $q, $status)) {
            $toast = ['type' => 'success', 'message' => 'Vraćeno, ali sakriveno zbog filtera/search-a.'];
            $oob = view('tasks._oob', compact('stats', 'toast'))->render();
            return response($oob, 200);
        }

        $item = view('tasks._item', compact('task'))->render();
        return response($oob . $item, 200);
    }

    public function clearCompleted(Request $request)
    {
        Task::where('done', true)->delete();
        return $this->index($request);
    }

    // helpers
    private function buildStats(int $shown = null, string $status = 'all', string $q = ''): array
    {
        $total = Task::count();
        $done  = Task::where('done', true)->count();
        $active = $total - $done;
        $pct = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        return [
            'total' => $total,
            'done' => $done,
            'active' => $active,
            'pct' => $pct,
            'shown' => $shown,
            'status' => $status,
            'q' => $q,
        ];
    }

    private function matches(Task $task, string $q, string $status): bool
    {
        if ($q !== '' && stripos($task->title, $q) === false) return false;
        if ($status === 'active' && $task->done) return false;
        if ($status === 'done' && ! $task->done) return false;
        return true;
    }

    // Smart add: "! ..." high, "? ..." low, "@YYYY-MM-DD" due_date
    private function parseSmartInput(string $raw): array
    {
        $raw = trim($raw);

        $priority = 'normal';
        if (str_starts_with($raw, '!')) {
            $priority = 'high';
            $raw = ltrim(substr($raw, 1));
        } elseif (str_starts_with($raw, '?')) {
            $priority = 'low';
            $raw = ltrim(substr($raw, 1));
        }

        $dueDate = null;
        if (preg_match('/@(\d{4}-\d{2}-\d{2})\b/', $raw, $m)) {
            $dueDate = $m[1];
            $raw = trim(str_replace($m[0], '', $raw));
        }

        return [$raw, $priority, $dueDate];
    }
}
