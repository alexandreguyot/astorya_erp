<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Owner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;


class BillController extends Controller
{
    public function index()
    {
        return view('admin.bill.index');
    }

    function parseDate(string $date): \Carbon\Carbon
    {
        // Si on trouve des slash, on considère d/m/Y
        if (strpos($date, '/') !== false) {
            return Carbon::createFromFormat('d/m/Y', $date);
        }
        // sinon on tente Y-m-d
        return Carbon::createFromFormat('Y-m-d', $date);
    }

    public function pdf($no_bill, $dateStart)
    {
        $bills = Bill::with([
            'contract',
            'contract.type_period',
            'type_period',
            'company.city',
            'contract.contract_product_detail.type_product.type_contract'
        ])
        ->where('no_bill', $no_bill)
        ->get();

        $filename = $bills->first()->no_bill . '.pdf';
        $dt = $this->parseDate($dateStart);
        $dateStart = $dt->format('d/m/Y');

        $period_bills = $dt->format('m-Y');
        $date        = $dt;

        $path = "private/factures/{$period_bills}/{$filename}";

        $contracts = collect();
        foreach ($bills as $bill) {
            $contracts->push($bill->contract);
        }

        $contract = $bills->first()->contract;
        $owner = Owner::first();
        $vatResumes = $this->getVatResumesFromContracts($contracts, $date);
        $totals = $this->getTotalsFromVatResumes($vatResumes);
        $products = collect();
        foreach ($contracts as $contract) {
            foreach ($contract->contract_product_detail as $product) {
                $products->push($product);
            }
        }

        $pdf = Pdf::loadView('pdf.bills', compact(
            'contract',
            'contracts',
            'products',
            'dateStart',
            'owner',
            'vatResumes',
            'totals',
            'bill',
            ))
            ->setOption('enable-local-file-access', true)
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 8)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 8);

        Storage::put($path, $pdf->output());

        return response()->download(
            storage_path("app/{$path}"),
            $filename
        );
    }

    public function getVatResumesFromContracts($contracts, $date = null)
    {
        $vatResumes = [];
        foreach ($contracts as $contract) {
            foreach ($contract->contract_product_detail as $detail) {
                $vat = $detail->type_product->type_vat ?? null;
                if (!$vat) continue;

                $key = $vat->code_vat;

                $ht = $detail->proratedBase($date);
                $tva = $detail->proratedWithVat($date) - $ht;

                if (!isset($vatResumes[$key])) {
                    $vatResumes[$key] = [
                        'code' => $vat->code_vat,
                        'percent' => $vat->percent,
                        'amount_ht' => 0,
                        'amount_tva' => 0,
                    ];
                }

                $vatResumes[$key]['amount_ht'] += $ht;
                $vatResumes[$key]['amount_tva'] += $tva;
            }
        }

        return collect($vatResumes)->map(function ($item) {
            return [
                'code' => $item['code'],
                'percent' => number_format($item['percent'], 2, ',', ' '),
                'amount_ht' => number_format($item['amount_ht'], 2, ',', ' '),
                'amount_tva' => number_format($item['amount_tva'], 2, ',', ' '),
            ];
        });
    }

    public function getTotalsFromVatResumes($vatResumes)
    {
        $totalHt = 0;
        $totalTva = 0;

        foreach ($vatResumes as $item) {
            $ht = (float) str_replace([' ', ','], ['', '.'], $item['amount_ht']);
            $tva = (float) str_replace([' ', ','], ['', '.'], $item['amount_tva']);
            $totalHt += $ht;
            $totalTva += $tva;
        }

        return [
            'total_ht' => number_format($totalHt, 2, ',', ' '),
            'total_tva' => number_format($totalTva, 2, ',', ' '),
            'total_ttc' => number_format($totalHt + $totalTva, 2, ',', ' '),
        ];
    }

    public function export_order_prlv(string $dateStart, string $dateEnd)
    {
        $bills = Bill::with('company.bank_account')
            ->where('no_bill', 'like', 'FACT-%')
            ->whereHas('company', fn($q) => $q->where('bill_payment_method', 0))
            ->whereBetween('generated_at', [$dateStart, $dateEnd])
            ->get()
            ->filter(fn($b) =>
                $b->company->bank_account
                && $b->company->bank_account->no_rum
            );

        $groups = $bills->groupBy(fn($bill) => $bill->company->bank_account->no_rum);

        $today      = Carbon::now();
        $dateString = $today->format('Y-m-d');
        $timestamp  = $today->toIso8601String();

        $nbOfTxs = $bills->groupBy('no_bill')->count();
        $ctrlSum = number_format(
            $groups->sum(fn($group) => $group->sum('amount_vat_included')),
            2,
            '.',
            ''
        );

        // 4) Création du document XML
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $ns   = 'urn:iso:std:iso:20022:tech:xsd:pain.008.001.02';
        $root = $dom->createElementNS($ns, 'Document');
        $dom->appendChild($root);
        $cstmr = $dom->createElement('CstmrDrctDbtInitn');
        $root->appendChild($cstmr);

        // 4a) Group Header
        $grpHdr = $dom->createElement('GrpHdr');
        $cstmr->appendChild($grpHdr);
        foreach ([
            'MsgId'   => "ASTORYA-PRLV-{$dateString}",
            'CreDtTm' => $timestamp,
            'NbOfTxs' => (string) $nbOfTxs,
            'CtrlSum' => $ctrlSum,
        ] as $tag => $val) {
            $el = $dom->createElement($tag, $val);
            $grpHdr->appendChild($el);
        }
        $initgPty = $dom->createElement('InitgPty');
        $nm       = $dom->createElement('Nm', 'ASTORYA S.G.I.');
        $initgPty->appendChild($nm);
        $grpHdr->appendChild($initgPty);

        // 4b) Payment Information block
        $pmtInf = $dom->createElement('PmtInf');
        $cstmr->appendChild($pmtInf);
        foreach ([
            'PmtInfId'  => "INF-PRLV-{$dateString}",
            'PmtMtd'    => 'DD',
            'BtchBookg' => 'true',
            'NbOfTxs'   => (string) $nbOfTxs,
            'CtrlSum'   => $ctrlSum,
        ] as $tag => $val) {
            $el = $dom->createElement($tag, $val);
            $pmtInf->appendChild($el);
        }
        // Type d'instruction de paiement
        $pmtTpInf = $dom->createElement('PmtTpInf');
        $svcLvl   = $dom->createElement('SvcLvl');
        $svcLvl->appendChild($dom->createElement('Cd', 'SEPA'));
        $pmtTpInf->appendChild($svcLvl);
        $lcl = $dom->createElement('LclInstrm');
        $lcl->appendChild($dom->createElement('Cd', 'CORE'));
        $pmtTpInf->appendChild($lcl);
        $pmtTpInf->appendChild($dom->createElement('SeqTp', 'RCUR'));
        $pmtInf->appendChild($pmtTpInf);

        // Date de prélèvement
        $pmtInf->appendChild($dom->createElement('ReqdColltnDt', $dateString));
        // Créancier
        $cdtr = $dom->createElement('Cdtr');
        $cdtr->appendChild($dom->createElement('Nm', 'ASTORYA S.G.I.'));
        $pmtInf->appendChild($cdtr);
        // Compte créancier
        $cdtrAcct = $dom->createElement('CdtrAcct');
        $idIban   = $dom->createElement('Id');
        $idIban->appendChild($dom->createElement('IBAN', 'FR7630004018540001003802740'));
        $cdtrAcct->appendChild($idIban);
        $pmtInf->appendChild($cdtrAcct);
        // Agent créancier
        $cdtrAgt = $dom->createElement('CdtrAgt');
        $finInst = $dom->createElement('FinInstnId');
        $finInst->appendChild($dom->createElement('BIC', 'BNPAFRPPNAN'));
        $cdtrAgt->appendChild($finInst);
        $pmtInf->appendChild($cdtrAgt);
        // Frais
        $pmtInf->appendChild($dom->createElement('ChrgBr', 'SLEV'));
        // Scheme Identification
        $cdtrSch = $dom->createElement('CdtrSchmeId');
        $id2     = $dom->createElement('Id');
        $prvt    = $dom->createElement('PrvtId');
        $othr    = $dom->createElement('Othr');
        $othr->appendChild($dom->createElement('Id', 'FR85ZZZ597358'));
        $schmeNm = $dom->createElement('SchmeNm');
        $schmeNm->appendChild($dom->createElement('Prtry', 'SEPA'));
        $othr->appendChild($schmeNm);
        $prvt->appendChild($othr);
        $id2->appendChild($prvt);
        $cdtrSch->appendChild($id2);
        $pmtInf->appendChild($cdtrSch);

        // 5) Pour chaque mandat, on crée une transaction agrégée
        foreach ($groups as $mandateId => $group) {
            $ba      = $group->first()->company->bank_account;
            $amountT = number_format($group->sum('amount_vat_included'), 2, '.', '');

            $txInf = $dom->createElement('DrctDbtTxInf');

            // Identifiants de paiement
            $pmtIdEl = $dom->createElement('PmtId');
            $pmtIdEl->appendChild($dom->createElement('InstrId', "PRLV-{$mandateId}"));
            $pmtIdEl->appendChild($dom->createElement('EndToEndId', $mandateId));
            $txInf->appendChild($pmtIdEl);

            // Montant
            $instdAmt = $dom->createElement('InstdAmt', $amountT);
            $instdAmt->setAttribute('Ccy', 'EUR');
            $txInf->appendChild($instdAmt);

            // Mandat
            $dr = $dom->createElement('DrctDbtTx');
            $mri = $dom->createElement('MndtRltdInf');
            $mri->appendChild($dom->createElement('MndtId', $mandateId));
            $mri->appendChild($dom->createElement('DtOfSgntr', Carbon::parse($ba->effective_starting_date)->format('Y-m-d')));
            $mri->appendChild($dom->createElement('AmdmntInd', 'false'));
            $dr->appendChild($mri);
            $txInf->appendChild($dr);

            // Agent de débit
            $dbtrAgt = $dom->createElement('DbtrAgt');
            $fin2    = $dom->createElement('FinInstnId');
            $fin2->appendChild($dom->createElement('BIC', $ba->bic));
            $dbtrAgt->appendChild($fin2);
            $txInf->appendChild($dbtrAgt);

            // Débiteur
            $dbtr = $dom->createElement('Dbtr');
            $dbtrName = $dom->createElement('Nm');
            $dbtrName->appendChild(
                $dom->createTextNode($group->first()->company->name)
            );
            $dbtr->appendChild($dbtrName);
            $txInf->appendChild($dbtr);

            // Compte débiteur
            $dba  = $dom->createElement('DbtrAcct');
            $idb  = $dom->createElement('Id');
            $idb->appendChild($dom->createElement('IBAN', $ba->iban));
            $dba->appendChild($idb);
            $txInf->appendChild($dba);

            // Informations de motif (on peut concaténer les no_bill si besoin)
            $rmt = $dom->createElement('RmtInf');
            $ustrd = $dom->createElement('Ustrd',
                "Prélèvement mandat {$mandateId} – total factures : ".count($group)
            );
            $rmt->appendChild($ustrd);
            $txInf->appendChild($rmt);

            $pmtInf->appendChild($txInf);
        }

        // 6) Enregistrement et envoi du fichier
        $xmlString = $dom->saveXML();
        $filename  = "OrdresPrlv.xml";
        Storage::disk('local')->put("exports/{$filename}", $xmlString);

        return response()->download(storage_path("app/exports/{$filename}"));
    }
}
