<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
// use PhpParser\Node\Stmt\Return_;

class AttendeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
        $this->middleware('throttle:api')->only(['store', 'destroy']);
        $this->authorizeResource(Attendee::class, 'attendee');
    }

    use CanLoadRelationships;
    private array $relations = ['user'];
    public function index(Event $event)
    {
        $attendees = $this->loadRelationships($event->attendees()->latest());

        return AttendeeResource::collection($attendees->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee = $event->attendees()->create([
            'user_id' => $request->user()->id,
        ]);

        return new AttendeeResource($this->loadRelationships($attendee));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource($this->loadRelationships($attendee));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        // $this->authorize('delete-attendee', [$event, $attendee]);
        $attendee->delete();

        return response(status: 204);
    }
}
