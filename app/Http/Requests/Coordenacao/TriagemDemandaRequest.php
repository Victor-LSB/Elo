<?php

namespace App\Http\Requests\Coordenacao;

use Illuminate\Foundation\Http\FormRequest;

class TriagemDemandaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isCoordenacao();
    }

    public function rules(): array
    {
        return [
            'nivel_complexidade' => ['required', 'integer', 'between:1,3'],
            'departamento_id' => ['required', 'exists:departamentos,id'],
            'professor_id' => ['required', 'exists:users,id'],
            'horas_min' => ['required', 'integer', 'min:1'],
            'horas_max' => ['required', 'integer', 'gte:horas_min'],
            'prazo_candidatura_dias' => ['nullable', 'integer', 'min:1'],

            // Checklist ético (RF-06.1)
            'substitui_servico_profissional' => ['required', 'boolean'],
            // RF-06.2: justificativa obrigatória quando a resposta é "sim"
            'justificativa' => ['required_if:substitui_servico_profissional,true', 'nullable', 'string', 'min:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'justificativa.required_if' => 'É obrigatório justificar quando a demanda substitui um serviço profissional.',
        ];
    }
}
