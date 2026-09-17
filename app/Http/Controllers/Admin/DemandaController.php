<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use Illuminate\Http\Request;

class DemandaController extends Controller
{
    public function index(Request $request)
    {
        $demandas = Demanda::with(['instituicao', 'professor', 'departamento'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->get();

        return view('admin.demandas.index', [
            'demandas' => $demandas,
            'status' => $request->status,
        ]);
    }
}
