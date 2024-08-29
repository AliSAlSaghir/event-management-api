<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller implements HasMiddleware {
  use CanLoadRelationships, AuthorizesRequests;

  private $relations = ['user', 'attendees', 'attendees.user'];

  public static function middleware() {
    return [
      new Middleware('auth:sanctum', except: ['index', 'show']),
    ];
  }

  // public function __construct() {
  //   $this->authorizeResource(Event::class, 'event');
  // }

  /**
   * Display a listing of the resource.
   */
  public function index() {
    $query = Event::query();
    return EventResource::collection($query->latest()->paginate());
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'start_time' => 'required|date',
      'end_time' => 'required|date|after:start_time'
    ]);

    $event = Event::create([
      ...$request->all(),
      'user_id' => $request->user()->id
    ]);

    return new EventResource($this->loadRelationships($event));
  }

  /**
   * Display the specified resource.
   */
  public function show(Event $event) {
    $event->load('user', 'attendees');
    return new EventResource($this->loadRelationships($event));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Event $event) {
    Gate::authorize('modify', $event);

    $request->validate([
      'name' => 'sometimes|string|max:255',
      'description' => 'nullable|string',
      'start_time' => 'sometimes|date',
      'end_time' => 'sometimes|date|after:start_time'
    ]);

    $event->update($request->all());
    return new EventResource($this->loadRelationships($event));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Event $event) {
    Gate::authorize('modify', $event);
    $event->delete();
    return response()->noContent(); // 204 status code
  }
}
