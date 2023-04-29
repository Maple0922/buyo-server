<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReservationController extends Controller
{
    public function __construct(
        private Reservation $reservation
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
        } else if ($type === 'd') {
            $reservations = $this->reservation->whereDate('start', $today->clone()->addDays($page))->get();
        } else {
            $reservations = $this->reservation->get();
        }

        return $reservations
            ->groupBy(fn ($r) => $r->start->format('Y-m-d'))
            ->map(fn ($reservations, $date) => [
                'date' => $date,
                'reservations' => $reservations
                    ->sortBy('start')
                    ->map(fn ($reservation) => [
                        'id' => $reservation->id,
                        'name' => $reservation->name,
                        'time' => [
                            'start' => $reservation->start->format('H:i'),
                            'end' => $reservation->end->format('H:i')
                        ]
                    ])
                    ->values()
            ])
            ->sortBy('date')
            ->values();
    }

    public function show(string $id): array
    {
        $reservation = $this->reservation->find($id);
        return [
            'id' => $reservation->id,
            'name' => $reservation->name,
            'date' => $reservation->start->format('Y-m-d'),
            'time' => [
                'start' => $reservation->start->format('H:i'),
                'end' => $reservation->end->format('H:i')
            ]
        ];
    }

    public function store(Request $request) // : array
    {
        $id = $this->reservation->generateId();
        $this->reservation->create([
            'id' => $id,
            'name' => $request->name,
            'start' => $request->start,
            'end' => $request->end,
            'passcode' => $request->passcode
        ]);

        return $this->show($id);
    }

    public function update(Request $request, string $id): array
    {
        \Log::channel('single')->emergency($request->all());
        $reservation = $this->reservation->find($id);
        if (!$reservation) abort(404);

        if ($reservation->passcode !== $request->passcode) abort(403);

        $reservation->update([
            'name' => $request->name,
            'start' => $request->start,
            'end' => $request->end
        ]);

        return $this->show($reservation->id);
    }
}
