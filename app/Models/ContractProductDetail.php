<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ContractProductDetail extends Model
{
    use HasFactory;

    protected $table = 'contract_product_details';

    protected $fillable = [
        'id',
        'designation',
        'quantity',
        'capacity',
        'monthly_unit_price_without_taxe',
        'billing_started_at',
        'billing_terminated_at',
        'last_billed_at',
        'contract_id',
        'type_product_id',
    ];

    protected $casts = [
        'last_billed_at' => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function type_product()
    {
        return $this->belongsTo(TypeProduct::class);
    }

   public function calculateBillingPeriod($dateStart)
    {
        if (! $this->contract || ! $this->contract->type_period || ! $this->contract->type_period->nb_month) {
            return null;
        }

        $nbMonth   = $this->contract->type_period->nb_month;
        $setupDay  = Carbon::createFromFormat(config('project.date_format'), $this->contract->setup_at)->day;
        $baseDate  = Carbon::createFromFormat(config('project.date_format'), $dateStart);

        // 🕐 Début de période contractuelle
        $day = min($setupDay, $baseDate->daysInMonth);
        $startBilling = $baseDate->copy()->day($day);

        // 📆 Fin de période contractuelle
        if ($this->contract->isTerminationMonth($baseDate)) {
            $endBilling = Carbon::createFromFormat(config('project.date_format'), $this->contract->terminated_at);
        } else {
            $endBilling = $startBilling->copy()->addMonthsNoOverflow($nbMonth)->subDay();
        }

        // ✅ Si l’article a une date de fin plus courte, on l’utilise
        if (!empty($this->billing_terminated_at) && $this->billing_terminated_at !== '01/01/0001') {
            try {
                $articleEnd = Carbon::createFromFormat(config('project.date_format'), $this->billing_terminated_at);
                if ($articleEnd->lt($endBilling)) {
                    $endBilling = $articleEnd;
                }
            } catch (\Throwable $e) {
                // format invalide => on ignore
            }
        }

        return $startBilling->format(config('project.date_format'))
            . ' au '
            . $endBilling->format(config('project.date_format'));
    }

    public function scopeActiveAt(Builder $query, Carbon $date): Builder
    {
        $cutoff = $date->copy()->startOfMonth()->format('Y-m-d');

        return $query->where(function(Builder $q) use ($cutoff) {
            $q->whereNull('billing_terminated_at')
              ->orWhereDate('billing_terminated_at', '0001-01-01')
              ->orWhereDate('billing_terminated_at', '>=', $cutoff);
        });
    }

    protected function rawBillingEnd(): ?Carbon
    {
        // billing_terminated_at est stocké en Y-m-d (puis formaté par accessor)
        $v = $this->getRawOriginal('billing_terminated_at');
        if (!$v || $v === '0001-01-01') return null;
        return Carbon::parse($v);
    }

    protected function rawContractEnd(): ?Carbon
    {
        $raw = optional($this->contract)->getRawOriginal('terminated_at');
        $v = $raw ?? ($this->contract->terminated_at ?? null);
        return $v ? Carbon::parse($v) : null;
    }

    /**
     * Renvoie la date de fin effective (la plus proche) si elle tombe dans [start; end] inclus.
     * - Si les deux dates existent et sont dans la période => on prend la plus proche (la plus tôt).
     * - Si une seule est dans la période => on prend celle-là.
     * - Sinon => null (pas de prorata).
     */
    protected function terminationInPeriod(Carbon $periodStart, Carbon $periodEnd): ?Carbon
    {
        $lineEnd = $this->rawBillingEnd();
        $contEnd = $this->rawContractEnd();

        $candidates = [];
        if ($lineEnd && $lineEnd->betweenIncluded($periodStart, $periodEnd)) {
            $candidates[] = $lineEnd;
        }
        if ($contEnd && $contEnd->betweenIncluded($periodStart, $periodEnd)) {
            $candidates[] = $contEnd;
        }

        if (empty($candidates)) {
            return null;
        }

        usort($candidates, fn($a,$b) => $a->lessThan($b) ? -1 : ($a->equalTo($b) ? 0 : 1));
        return $candidates[0]; // la plus tôt dans la période
    }

    /* =========================
     |  Montants
     |=========================*/

    /**
     * Montant HT sur une PÉRIODE quelconque.
     * Règle :
     *  - Sans date de fin dans la période => pas de prorata, 1 "monthly_base" par mois couvert.
     *  - Si une date de fin (contrat OU ligne) est dans la période =>
     *      * tous les mois AVANT le mois de fin : monthly_base plein
     *      * mois de fin : prorata au jour -> (monthly_base / nb_jours_du_mois) * nb_jours_utilisés
     */
    public function amountForPeriod(Carbon $periodStart, Carbon $periodEnd): float
    {
        // base mensuelle (plein mois)
        $monthlyBase = (float)$this->monthly_unit_price_without_taxe * (float)$this->quantity;

        // Normalise la période sur des mois civils (on travaille mois par mois)
        $cursor = $periodStart->copy()->startOfMonth();
        $periodEndMonth = $periodEnd->copy()->endOfMonth();

        // Cherche une date de fin dans la période
        $term = $this->terminationInPeriod($periodStart->copy()->startOfDay(), $periodEnd->copy()->endOfDay());

        $total = 0.0;

        while ($cursor->lte($periodEndMonth)) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd   = $cursor->copy()->endOfMonth();
            $daysInMonth = $monthStart->daysInMonth;

            // Si pas de fin dans la période -> tous les mois couverts sont facturés plein
            if (!$term) {
                // Mois couvert par la période ?
                if ($monthEnd->lt($periodStart) || $monthStart->gt($periodEnd)) {
                    // hors période
                } else {
                    $total += $monthlyBase;
                }
                $cursor->addMonthsNoOverflow();
                continue;
            }

            // Il y a une fin dans la période
            if ($term->year === $monthStart->year && $term->month === $monthStart->month) {
                // Mois de fin -> prorata sur les jours utilisés dans ce mois
                // On part du 1er du mois (mois fiscal), sauf si la période commence après
                $activeStart = $periodStart->greaterThan($monthStart) ? $periodStart->copy() : $monthStart->copy();
                $activeEnd   = $term->lessThan($monthEnd) ? $term->copy() : $monthEnd->copy();

                if ($activeEnd->gte($activeStart)) {
                    $monthlyBase = (float)$this->monthly_unit_price_without_taxe;
                    $daysUsed = $activeStart->diffInDays($activeEnd) + 1; // inclusif

                    // Si ça couvre tout le mois (fin = dernier jour), on facture plein
                    if ($activeStart->lte($monthStart) && $activeEnd->gte($monthEnd)) {
                        $total += $monthlyBase;
                    } else {
                        $total += $monthlyBase * ($daysUsed / $daysInMonth);
                    }
                }
            } elseif ($monthEnd->lt($term)) {
                // Mois strictement avant le mois de fin => plein mois
                // (à condition d'être dans la période demandée)
                if (!($monthEnd->lt($periodStart) || $monthStart->gt($periodEnd))) {
                    $total += $monthlyBase;
                }
            } else {
                // Mois après le mois de fin => rien
            }

            $cursor->addMonthsNoOverflow();
        }

        return round($total, 2);
    }

    /**
     * Compat avec ton code existant :
     * Calcule le montant pour le MOIS de $date (période = mois civil).
     */
    public function proratedBase(Carbon $date): float
    {
        $start = $date->copy()->startOfMonth();
        $end   = $date->copy()->endOfMonth();
        return $this->amountForPeriod($start, $end);
    }

    public function proratedWithVat(Carbon $date): float
    {
        $vatRate = $this->type_product->type_vat->percent ?? 0;
        return round($this->proratedBase($date) * (1 + $vatRate / 100), 2);
    }

    public function proratedBaseFormatted($dateStart): string
    {
        return number_format($this->proratedBase($dateStart), 2, ',', ' ');
    }

    public function proratedWithVatFormatted($dateStart): string
    {
        return number_format($this->proratedWithVat($dateStart), 2, ',', ' ');
    }

    public function isTerminationMonth($dateStart): bool
    {
        return $this->billing_terminated_at
            && Carbon::createFromFormat(config('project.date_format'), $this->billing_terminated_at)->year === $dateStart->year
            && Carbon::createFromFormat(config('project.date_format'), $this->billing_terminated_at)->month === $dateStart->month;
    }

    public function calculateTotalPriceWithoutTaxe($date)
    {
        // pour rester cohérent, on réutilise le calcul standard sur le mois de $date
        return number_format($this->proratedBase(Carbon::parse($date)), 2, ',', ' ');
    }

    public function getTotalPriceAttribute()
    {
        // Ancien attribut : garde le plein mois
        $base = (float)$this->monthly_unit_price_without_taxe * (float)$this->quantity;
        return round($base, 2);
    }

    public function getFormattedMonthlyUnitPriceWithoutTaxeAttribute()
    {
        return number_format($this->monthly_unit_price_without_taxe, 2, ',', ' ');
    }

    public function getMonthlyUnitPriceWithTaxeAttribute()
    {
        return number_format(
            $this->monthly_unit_price_without_taxe * (1 + ($this->type_product->type_vat->percent / 100)),
            2,
            ',',
            ' '
        );
    }

    public function getBillingStartedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function getBillingTerminatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setBillingStartedAtAttribute($value)
    {
        $this->attributes['billing_started_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function setBillingTerminatedAtAttribute($value)
    {
        $this->attributes['billing_terminated_at'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function canGenerateForPeriod(Carbon $periodStart, Carbon $periodEnd): bool
    {
        $start = $this->nextBillableStart($periodStart);
        $end   = $this->lastBillableEnd($periodEnd);

        // impossible si la période est inversée / ou déjà entièrement facturée
        return $start->lte($end);
    }

    protected function nextBillableStart(Carbon $periodStart): Carbon
    {
        $start = $periodStart->copy()->startOfDay();

        // optionnel : ne pas facturer avant la mise en place du contrat
        if ($this->contract?->setup_at) {
            $setup = Carbon::createFromFormat(config('project.date_format'), $this->contract->setup_at)->startOfDay();
            $start = $start->max($setup);
        }

        return $start;
    }

    public function getEffectiveStartDate(): ?Carbon
    {
        $v = $this->billing_started_at;

        if (!$v || $v === '0001-01-01') {
            return null;
        }

        try {
            return Carbon::createFromFormat(config('project.date_format'), $v);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getEffectiveEndDate(): ?Carbon
    {
        $v = $this->billing_terminated_at;

        // 0001-01-01 = aucune date de fin, donc actif !
        if (!$v || $v === '0001-01-01') {
            return null;
        }

        try {
            return Carbon::createFromFormat(config('project.date_format'), $v);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function nextBillableAfter(): ?\Carbon\Carbon
    {
        $raw = $this->getRawOriginal('last_billed_at');
        return $raw ? \Carbon\Carbon::parse($raw)->addDay()->startOfDay() : null;
    }


    public function shouldListForPeriod(Carbon $periodStart,Carbon $periodEnd, string $billingPeriod): bool
    {
        // ----- DATES LIGNE -----
        $lineStartRaw = $this->getRawOriginal('billing_started_at');
        $lineEndRaw   = $this->getRawOriginal('billing_terminated_at');

        $lineStart = $lineStartRaw && $lineStartRaw !== '0001-01-01'
            ? Carbon::parse($lineStartRaw)->startOfDay()
            : null;

        $lineEnd = $lineEndRaw && $lineEndRaw !== '0001-01-01'
            ? Carbon::parse($lineEndRaw)->endOfDay()
            : null;

        // ----- DATES CONTRAT -----
        $contractStart = Carbon::createFromFormat(
            config('project.date_format'),
            $this->contract->setup_at
        )->startOfDay();

        $contractEnd = $this->contract->terminated_at
            ? Carbon::createFromFormat(
                config('project.date_format'),
                $this->contract->terminated_at
            )->endOfDay()
            : null;

        // ----- DERNIÈRE FACTURATION -----
        $lastRaw    = $this->getRawOriginal('last_billed_at');
        $lastBilled = $lastRaw ? Carbon::parse($lastRaw)->startOfDay() : null;

        // ----- INTERVALLE ACTIF -----
        $activeStart = $lineStart ?: $contractStart;

        $activeEnd = collect([$lineEnd, $contractEnd])
            ->filter()
            ->sort()
            ->first(); // peut être null

        // 1️⃣ Pas actif sur la période → exclu
        if ($activeStart->gt($periodEnd)) {
            return false;
        }

        if ($activeEnd && $activeEnd->lt($periodStart)) {
            return false;
        }

        // ----- DATE DE RÉFÉRENCE (échéance logique) -----
        $referenceDate = $lastBilled
            ? $lastBilled->copy()->addDay()
            : $activeStart->copy();

        // ❌ pas de rattrapage du passé
        if ($referenceDate->lt($periodStart)) {
            return false;
        }

        $billingPeriodStart = Carbon::createFromFormat(
            config('project.date_format'),
            explode(' au ', $billingPeriod)[0]
        )->startOfDay();

        $billiedPeriodEnd = Carbon::createFromFormat(
            config('project.date_format'),
            explode(' au ', $billingPeriod)[1]
        )->endOfDay();

        if (
            $lastBilled &&
            $billingPeriodStart &&
            $billiedPeriodEnd &&
            $lastBilled->betweenIncluded(
                $billingPeriodStart->copy()->startOfDay(),
                $billiedPeriodEnd->copy()->endOfDay()
            )
        ) {
            return false;
        }

        $facturationStart = $lastBilled
            ? $lastBilled->copy()->addDay()
            : $activeStart->copy();

        $facturationEnd = $activeEnd; // fin réelle (contrat / ligne)


        if ($facturationStart->gt($facturationEnd)) {
            return false;
        }
        if (
            $lastBilled &&
            $facturationStart &&
            $facturationEnd &&
            $lastBilled->betweenIncluded(
                $facturationStart->copy()->startOfDay(),
                $facturationEnd->copy()->endOfDay()
            )
        ) {
            return false;
        }

        // ----- MOIS RENOUVELABLE -----
        $cycle = max(1, (int) $this->contract->type_period->nb_month);

        $refMonth    = $referenceDate->copy()->startOfMonth();
        $periodMonth = $periodStart->copy()->startOfMonth();

        $monthsDiff = $refMonth->diffInMonths($periodMonth);

        return ($monthsDiff % $cycle) === 0;
    }


    /** True si ce détail doit apparaître pour la période affichée (mois courant) */
    public function shouldListForPeriodOfficiel(\Carbon\Carbon $periodStart, \Carbon\Carbon $periodEnd): bool
    {
        // Dates brutes utiles
        $lineEndRaw   = $this->getRawOriginal('billing_terminated_at');
        $lineEnd      = $lineEndRaw && $lineEndRaw !== '0001-01-01' ? \Carbon\Carbon::parse($lineEndRaw) : null;

        $contractEnd  = $this->contract?->terminated_at
            ? \Carbon\Carbon::createFromFormat(config('project.date_format'), $this->contract->terminated_at)
            : null;

        $lastRaw      = $this->getRawOriginal('last_billed_at');
        $lastBilled   = $lastRaw ? \Carbon\Carbon::parse($lastRaw)->startOfDay() : null;

        // Fin effective = plus tôt entre fin de ligne et fin de contrat (si présentes)
        $effectiveEnd = null;
        if ($lineEnd && $contractEnd) {
            $effectiveEnd = $lineEnd->lt($contractEnd) ? $lineEnd->copy() : $contractEnd->copy();
        } elseif ($lineEnd) {
            $effectiveEnd = $lineEnd->copy();
        } elseif ($contractEnd) {
            $effectiveEnd = $contractEnd->copy();
        }

        // ✅ Nouvelle règle: ne pas afficher si la fin < last_billed_at
        if ($effectiveEnd && $lastBilled && $effectiveEnd->lt($lastBilled)) {
            return false;
        }

        // 1) Si une fin tombe dans la période → on l’affiche (prorata final)
        if (
            ($lineEnd && $lineEnd->betweenIncluded($periodStart, $periodEnd)) ||
            ($contractEnd && $contractEnd->betweenIncluded($periodStart, $periodEnd))
        ) {
            return true;
        }

        // 2) Jamais facturé → n’afficher que si on est sur le bon mois d’échéance (cycle)
        if (is_null($lastBilled)) {
            $setup = \Carbon\Carbon::createFromFormat(config('project.date_format'), $this->contract->setup_at)->startOfMonth();
            $nb    = max(1, (int) ($this->contract->type_period->nb_month ?? 1));
            $monthsDiff = $setup->diffInMonths($periodStart->copy()->startOfMonth());
            $isOnCycle  = $periodStart->gte($setup) && ($monthsDiff % $nb === 0);
            return $isOnCycle;
        }

        // 3) Déjà facturé au moins une fois → on affiche si l’on peut refacturer pendant cette période
        // (= lendemain de last_billed_at ≤ fin de période)
        $nextBillable = $lastBilled->copy()->addDay()->startOfDay();
        return $nextBillable->lte($periodEnd->copy()->endOfDay());
    }

    public function crossesPeriod(Carbon $periodStart, Carbon $periodEnd): bool
    {
        $start = $this->getEffectiveStartDate() ?? $periodStart;
        $end   = $this->getEffectiveEndDate()   ?? $periodEnd;

        // commence après la période
        if ($start->gt($periodEnd)) return false;

        // se termine avant la période
        if ($end->lt($periodStart)) return false;

        return true;
    }


    /** Date de fin “facturable” (bornée par une éventuelle terminaison) */
    protected function lastBillableEnd(Carbon $periodEnd): Carbon
    {
        $end = $periodEnd->copy()->endOfDay();

        // si la ligne a une fin antérieure, on clippe
        if ($this->billing_terminated_at) {
            $lineEnd = Carbon::createFromFormat(config('project.date_format'), $this->billing_terminated_at)->endOfDay();
            $end = $end->min($lineEnd);
        }

        // si le contrat a une fin antérieure, on clippe
        if ($this->contract?->terminated_at) {
            $ctEnd = Carbon::createFromFormat(config('project.date_format'), $this->contract->terminated_at)->endOfDay();
            $end = $end->min($ctEnd);
        }

        return $end;
    }
}
