<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::with(['departamento', 'instituicao'])
            ->when($request->papel, fn ($q, $papel) => $q->where('papel', $papel))
            ->when($request->busca, fn ($q, $busca) => $q->where(fn ($q2) => $q2
                ->where('nome', 'like', "%{$busca}%")
                ->orWhere('email', 'like', "%{$busca}%")))
            ->orderBy('papel')
            ->orderBy('nome')
            ->get();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'papel' => $request->papel,
            'busca' => $request->busca,
        ]);
    }
}
