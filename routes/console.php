<?php

use Illuminate\Support\Facades\Schedule;

// RNF-04: job agendado para verificação periódica de prazos vencidos
// (RF-04.3: marca milestones em risco; RF-04.4: notifica risco prolongado;
// RF-03.4: sinaliza demandas paradas sem candidatura).
Schedule::command('elo:verificar-prazos')->daily();
