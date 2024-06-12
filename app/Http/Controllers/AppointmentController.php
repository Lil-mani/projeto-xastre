<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Userdata;

class AppointmentController extends Controller
{
    public function index()
{
    $appointments = Appointment::get();
    return response()->json($appointments);
}

    public function store(Request $request)
    {
        $request->validate([
            'patient' => 'required|integer',
            'medic' => 'required|integer',
            'time' => 'required|date',
            'done' => 'required',
        ]);

        Appointment::create($request->all());
        return response()->json(['message' => 'Consulta enviada com sucesso!'], 200);
    }

    public function show()
    {
        $appointments = Appointment::get();
        return response()->json($appointments);
    }

    public function update($id)
    {
        try {
        $appointment = Appointment::findOrFail($id);
        $appointment->done = true;
        $appointment->save();
        return response()->json(['message'=> 'Consulta atualizada.'],200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message'=> 'Consulta não encontrada'],404);
        }
    }
}
