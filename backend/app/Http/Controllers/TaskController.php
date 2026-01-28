<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Essential for high-speed direct queries

class TaskController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    // Logic: Ask the Task Model to fetch all rows from the MySQL tasks table
    return \App\Models\Task::all();
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    // 1. Validation Logic: Ensure 'title' is present
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    // 2. Data Flow: Create the task in MySQL using validated data
    $task = Task::create($validated);

    // 3. Response: Send the new task back as JSON with a 'Created' status code
    // FIX: "fresh()" re-reads the task from MySQL so it includes the 'status'
    return response()->json($task->fresh(), 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Task $task)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Task $task)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id) // Note: We use $id instead of Task $task for speed
  {
    // Logic: If the request has 'title', it's a text edit.
    // If not, it's just a status toggle.
    if ($request->has('title')) {
      $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
      ]);
      //   $task->update($validated);
      DB::table('tasks')->where('id', $id)->update([
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'updated_at' => now(), // Manual update required when using Facade
      ]);
    } else {
      // 2. Logic for Instant Status Toggles
      // Logic: If it was pending, make it completed. If completed, make it pending.
      // Get current status string directly from DB (avoids loading whole model)
      $currentStatus = DB::table('tasks')->where('id', $id)->value('status');
      $newStatus = ($currentStatus === 'pending') ? 'completed' : 'pending';

      DB::table('tasks')->where('id', $id)->update([
        'status' => $newStatus,
        'updated_at' => now(),
      ]);
    }

    return response()->json(DB::table('tasks')->where('id', $id)->first());
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id) // Note: We use $id instead of Task $task for speed
  {
    // Logic: Tell MySQL to remove this specific row
    // $task->delete();

    // Tell MySQL to delete the row directly by ID
    DB::table('tasks')->where('id', $id)->delete();

    // Response: Send a 204 No Content (Standard for successful deletion)
    return response()->noContent();
  }
}
