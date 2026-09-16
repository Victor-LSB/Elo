<?php

namespace App\Http\Requests\Instituicao;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isInstituicao() && $this->user()->instituicao->isAtiva();
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'area_sugerida' => ['required', 'string', 'max:100'],
            'nivel_estimado' => ['required', 'integer', 'between:1,3'],
        ];
    }
}
