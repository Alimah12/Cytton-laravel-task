<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        return response()->json($task, Response::HTTP_CREATED);
    }

    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

          $query->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
              ->orderBy('due_date', 'asc');

        $tasks = $query->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found', 'data' => []], Response::HTTP_OK);
        }

        return response()->json($tasks, Response::HTTP_OK);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, $id)
    {
        $task = Task::findOrFail($id);

        $current = $task->status;
        $target = $request->input('status');

        if ($current === $target) {
            return response()->json($task, Response::HTTP_OK);
        }

        $allowedNext = [
            'pending' => 'in_progress',
            'in_progress' => 'done',
        ];

        if (!isset($allowedNext[$current]) || $allowedNext[$current] !== $target) {
            return response()->json(['message' => 'Status can only progress sequentially from pending -> in_progress -> done.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $task->status = $target;
        $task->save();

        return response()->json($task, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if ($task->status !== 'done') {
            return response()->json(['message' => 'Only done tasks can be deleted.'], Response::HTTP_FORBIDDEN);
        }

        $task->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function report(Request $request)
    {
        $date = $request->query('date') ?: Carbon::now()->toDateString();

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $date)->toDateString();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date format. Use YYYY-MM-DD.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rows = Task::whereDate('due_date', $parsed)
            ->select('priority', 'status', DB::raw('count(*) as count'))
            ->groupBy('priority', 'status')
            ->get();

        $priorities = ['high', 'medium', 'low'];
        $statuses = ['pending', 'in_progress', 'done'];

        $summary = [];
        foreach ($priorities as $p) {
            $summary[$p] = array_fill_keys($statuses, 0);
        }

        foreach ($rows as $r) {
            $summary[$r->priority][$r->status] = (int) $r->count;
        }

        return response()->json(['date' => $parsed, 'summary' => $summary], Response::HTTP_OK);
    }
}
