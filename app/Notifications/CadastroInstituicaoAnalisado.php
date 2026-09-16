<?php

namespace App\Notifications;

use App\Models\Instituicao;

/**
 * RF-01.1 + RF-07.1: avisa a instituição de que a coordenação analisou seu
 * cadastro, liberando (ou não) o acesso à plataforma.
 */
class CadastroInstituicaoAnalisado extends NotificacaoElo
{
    public function __construct(public Instituicao $instituicao)
    {
    }

    public function titulo(object $notifiable): string
    {
        return $this->instituicao->isAtiva()
            ? 'Cadastro aprovado'
            : 'Cadastro não aprovado';
    }

    public function mensagem(object $notifiable): string
    {
        if ($this->instituicao->isAtiva()) {
            return "O cadastro da {$this->instituicao->nome} foi validado pela coordenação de extensão. "
                .'Você já pode entrar na plataforma e cadastrar sua primeira demanda.';
        }

        return "O cadastro da {$this->instituicao->nome} não foi aprovado pela coordenação de extensão. "
            .'Entre em contato com a coordenação para mais informações.';
    }

    public function url(object $notifiable): string
    {
        return route('login', absolute: false);
    }
}
