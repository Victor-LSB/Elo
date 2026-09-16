<?php

namespace App\Console\Commands;

use App\Models\Auditoria;
use App\Models\Configuracao;
use App\Models\Demanda;
use App\Models\Milestone;
use App\Models\User;
use App\Notifications\DemandaParada;
use App\Notifications\MilestoneEmRisco;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class VerificarPrazosDemandas extends Command
{
    protected $signature = 'elo:verificar-prazos';

    protected $description = 'RF-04.3/04.4 e RNF-04: marca milestones vencidos como "em risco", '
        .'notifica orientador/admin após o período adicional, e sinaliza demandas '
        .'sem candidatura como "paradas".';

    public function handle(): int
    {
        $this->marcarMilestonesEmRisco();
        $this->notificarRiscoProlongado();
        $this->sinalizarDemandasParadas();

        return self::SUCCESS;
    }

    /**
     * RF-04.3: job agendado marca "em risco" milestones vencidos sem
     * atualização, e avisa o grupo e o orientador (RF-07.1).
     */
    private function marcarMilestonesEmRisco(): void
    {
        Milestone::where('status', 'em_andamento')
            ->where('prazo', '<', now())
            ->with('demanda.professor')
            ->get()
            ->each(function (Milestone $m) {
                if (! $m->marcarEmRiscoSeVencido()) {
                    return;
                }

                Auditoria::registrar($m, 'em_andamento', 'em_risco');

                $notificacao = new MilestoneEmRisco($m);
                $m->demanda->grupoAtivo()?->notificarMembros($notificacao);
                $m->demanda->professor?->notify($notificacao);

                $this->info("Milestone #{$m->id} marcado como em risco; grupo e orientador notificados.");
            });
    }

    /**
     * RF-04.4: após dias_ate_habilitar_reabertura em risco sem resposta,
     * notifica orientador e admin (RF-07.1). A decisão de reabrir continua
     * manual, no painel do admin (Admin\ExcecaoController).
     */
    private function notificarRiscoProlongado(): void
    {
        $diasLimite = (int) Configuracao::get('dias_ate_habilitar_reabertura');
        $admins = User::where('papel', 'admin')->get();

        Milestone::where('status', 'em_risco')
            ->where('updated_at', '<', now()->subDays($diasLimite))
            ->with('demanda.professor')
            ->get()
            ->each(function (Milestone $m) use ($admins) {
                $notificacao = new MilestoneEmRisco($m, riscoProlongado: true);

                $m->demanda->professor?->notify($notificacao);
                Notification::send($admins, $notificacao);

                $this->warn("Milestone #{$m->id} em risco prolongado: orientador e admin notificados.");
            });
    }

    /**
     * RF-03.4: demanda sem candidatura no prazo permanece visível com o
     * indicador de "parada há X dias" (calculado na view a partir de
     * data_abertura_candidatura). Aqui apenas avisamos a coordenação e a
     * instituição de que a demanda não está atraindo grupos (RF-07.1).
     */
    private function sinalizarDemandasParadas(): void
    {
        $coordenacao = User::where('papel', 'coordenacao')->get();

        Demanda::where('status', 'aberta_candidatura')
            ->doesntHave('candidaturas')
            ->with('instituicao.usuario')
            ->get()
            ->filter(fn (Demanda $d) => $d->prazoCandidaturaVencido())
            ->each(function (Demanda $d) use ($coordenacao) {
                $notificacao = new DemandaParada($d);

                $d->instituicao->usuario?->notify($notificacao);
                Notification::send($coordenacao, $notificacao);

                $this->info("Demanda #{$d->id} está parada sem candidaturas; coordenação notificada.");
            });
    }
}
