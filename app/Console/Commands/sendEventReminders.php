<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;

class sendEventReminders extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:send-event-reminders';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Sends notifications to all event attendees when the event is near';

  /**
   * Execute the console command.
   */
  public function handle() {
    $events = Event::with('attendees.user')->whereBetween('start_time', [now(), now()->addDay()])->get();
    $events->each(fn($event) => $event->attendees
      ->each(fn($attendee) => $attendee->user->notify(new EventReminderNotification($event))));
  }
}
