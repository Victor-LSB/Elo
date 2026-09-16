<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterInstituicaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome_instituicao' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:ong,escola_publica,associacao_bairro,orgao_publico,outro'],
            'responsavel' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'sobre' => ['nullable', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
