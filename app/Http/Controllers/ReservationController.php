<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

class ReservationController extends Controller
{
    public function __construct(
        private Reservation $reservation
    ) {
    }

    public function index(): Collection
    {
        return $this->reservation->all();
    }

    public function show($id): Reservation
    {
        return $this->reservation->find($id);
    }

    public function store(Request $request): Reservation
    {
        return $this->reservation->create($request->all());
    }

    public function update(Request $request, $id): Reservation
    {
        $reservation = $this->reservation->find($id);
        $reservation->update($request->all());
        return $reservation;
    }
}
