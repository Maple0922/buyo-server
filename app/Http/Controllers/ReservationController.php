<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Utils\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReservationController extends Controller
{
    public function __construct(
        private Reservation $reservation,
        private Notification $notification
    ) {
    }

    public function index(Request $request): Collection
    {
        $type = $request->query('t');
        $page = $request->query('p') ?? 0;
        $today = Carbon::today();

        if ($type === 'w') {
            $reservations = $this->reservation->whereBetween('start', [
                $today->clone()->startOfWeek()->addDays($page * 7),
                $today->clone()->endOfWeek()->addDays($page * 7)
            ])->get();
            // 1週間分の日付の配列
            $dates = collect(range(0, 6))
                ->map(
                    fn ($i) => $today
                        ->clone()
                        ->startOfWeek()
                        ->addDays($i + $page * 7)
                        ->format('Y-m-d')
                );
        } else if ($type === 'd') {
            $reservations = $this->reservation->whereDate('start', $today->clone()->addDays($page))->get();
            $dates = collect([$today->clone()->addDays($page)->format('Y-m-d')]);
        } else {
            $dates = collect([]);
        }

        return $dates
            ->map(fn ($date) => [
                'date' => $date,
                'reservations' => $reservations
                    ->filter(fn ($r) => $r->start->format('Y-m-d') === $date)
                    ->map(fn ($reservation) => [
                        'id' => $reservation->id,
                        'name' => $reservation->name,
                        'time' => [
                            'start' => $reservation->start->format('G:i'),
                            'end' => $reservation->end->format('G:i')
                        ]
                    ])
                    ->values()
            ]);
    }

    public function show(string $id): array
    {
        $reservation = $this->reservation->find($id);
        return [
            'id' => $reservation->id,
            'name' => $reservation->name,
            'date' => $reservation->start->format('Y-m-d'),
            'time' => [
                'start' => $reservation->start->format('G:i'),
                'end' => $reservation->end->format('G:i')
            ]
        ];
    }

    public function store(Request $request) // : array
    {
        $id = $this->reservation->generateId();
        $reservation = $this->reservation->create([
            'id' => $id,
            'name' => $request->name,
            'start' => $request->start,
            'end' => $request->end,
            'passcode' => $request->passcode
        ]);

        $this->notification->slack("create", $reservation);

        return $this->show($id);
    }

    public function update(Request $request, string $id): array
    {
        $reservation = $this->reservation->find($id);
        if (!$reservation) abort(404);

        if ($reservation->passcode !== $request->passcode) abort(403);

        $reservation->update([
            'name' => $request->name,
            'start' => $request->start,
            'end' => $request->end
        ]);

        $this->notification->slack("update", $reservation);

        return $this->show($reservation->id);
    }

    public function delete(Request $request, string $id): void
    {
        $reservation = $this->reservation->find($id);
        if (!$reservation) abort(404);

        if ($reservation->passcode !== $request->passcode) abort(403);

        $this->notification->slack("delete", $reservation);

        $reservation->delete();
    }
}
