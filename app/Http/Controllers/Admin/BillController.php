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

    public function pdf($no_bill)
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
        $period_bills = Carbon::createFromFormat('d/m/Y', $bills->first()->started_at)->format('m-Y');
        $dateStart = Carbon::createFromFormat('d/m/Y', $bills->first()->started_at)->format('d/m/Y');

        $path = "private/factures/{$period_bills}/{$filename}";

        $contracts = collect();
        foreach ($bills as $bill) {
            $contracts->push($bill->contract);
        }

        $contract = $bills->first()->contract;
        $owner = Owner::first();
        $vatResumes = $this->getVatResumesFromContracts($contracts);
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
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 10);

        Storage::put($path, $pdf->output());

        return response()->file(storage_path("app/{$path}"));
    }

    public function getVatResumesFromContracts($contracts)
    {
        $vatResumes = [];

        foreach ($contracts as $contract) {
            foreach ($contract->contract_product_detail as $detail) {
                $vat = $detail->type_product->type_vat ?? null;
                if (!$vat) continue;

                $key = $vat->code_vat;

                $ht = $detail->monthly_unit_price_without_taxe * $detail->quantity;
                $tva = $ht * ($vat->percent / 100);

                if (!isset($vatResumes[$key])) {
                    $vatResumes[$key] = [
                        'code' => $vat->code_vat,
                        'account' => $vat->account_vat,
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
                'account' => $item['account'],
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
        $owner = Owner::first();
        $today      = Carbon::now();
        $dateString = $today->format('Y-m-d');
        $timestamp  = $today->toIso8601String();

        // Prepare date range
        $from = $dateStart . ' 00:00:00';
        $to   = $dateEnd   . ' 23:59:59';

        $bills = Bill::with('company.bank_account')
            ->whereBetween('generated_at', [$from, $to])
            ->get();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $ns   = 'urn:iso:std:iso:20022:tech:xsd:pain.008.001.02';
        $root = $dom->createElementNS($ns, 'Document');
        $dom->appendChild($root);

        $cstmr = $dom->createElement('CstmrDrctDbtInitn');
        $root->appendChild($cstmr);

        // Group Header
        $grpHdr = $dom->createElement('GrpHdr');
        $cstmr->appendChild($grpHdr);

        // MsgId
        $el = $dom->createElement('MsgId');
        $el->appendChild($dom->createTextNode("ASTORYA-PRLV-{$dateString}"));
        $grpHdr->appendChild($el);

        // CreDtTm
        $el = $dom->createElement('CreDtTm');
        $el->appendChild($dom->createTextNode($timestamp));
        $grpHdr->appendChild($el);

        // NbOfTxs
        $el = $dom->createElement('NbOfTxs');
        $el->appendChild($dom->createTextNode((string) $bills->count()));
        $grpHdr->appendChild($el);

        // CtrlSum
        $ctrlSum = number_format($bills->sum('amount_vat_included'), 2, '.', '');
        $el = $dom->createElement('CtrlSum');
        $el->appendChild($dom->createTextNode($ctrlSum));
        $grpHdr->appendChild($el);

        // InitgPty
        $initgPty = $dom->createElement('InitgPty');
        $c = $dom->createElement('Nm');
        $c->appendChild($dom->createTextNode('ASTORYA S.G.I.'));
        $initgPty->appendChild($c);
        $grpHdr->appendChild($initgPty);

        // Payment Information
        $pmtInf = $dom->createElement('PmtInf');
        $cstmr->appendChild($pmtInf);

        foreach (['PmtInfId' => "INF-PRLV-{$dateString}", 'PmtMtd' => 'DD', 'BtchBookg' => 'true'] as $tag => $value) {
            $el = $dom->createElement($tag);
            $el->appendChild($dom->createTextNode($value));
            $pmtInf->appendChild($el);
        }

        // NbOfTxs and CtrlSum in PmtInf
        $el = $dom->createElement('NbOfTxs');
        $el->appendChild($dom->createTextNode((string) $bills->count()));
        $pmtInf->appendChild($el);

        $el = $dom->createElement('CtrlSum');
        $el->appendChild($dom->createTextNode($ctrlSum));
        $pmtInf->appendChild($el);

        // PmtTpInf
        $pmtTpInf = $dom->createElement('PmtTpInf');
        $svcLvl   = $dom->createElement('SvcLvl');
        $c = $dom->createElement('Cd'); $c->appendChild($dom->createTextNode('SEPA')); $svcLvl->appendChild($c);
        $pmtTpInf->appendChild($svcLvl);
        $lcl = $dom->createElement('LclInstrm');
        $c = $dom->createElement('Cd'); $c->appendChild($dom->createTextNode('CORE')); $lcl->appendChild($c);
        $pmtTpInf->appendChild($lcl);
        $c = $dom->createElement('SeqTp'); $c->appendChild($dom->createTextNode('RCUR')); $pmtTpInf->appendChild($c);
        $pmtInf->appendChild($pmtTpInf);

        $el = $dom->createElement('ReqdColltnDt');
        $el->appendChild($dom->createTextNode($dateString));
        $pmtInf->appendChild($el);

        $cdtr = $dom->createElement('Cdtr');
        $c = $dom->createElement('Nm'); $c->appendChild($dom->createTextNode('ASTORYA S.G.I.')); $cdtr->appendChild($c);
        $pmtInf->appendChild($cdtr);

        $cdtrAcct = $dom->createElement('CdtrAcct');
        $idIban   = $dom->createElement('Id');
        $c = $dom->createElement('IBAN'); $c->appendChild($dom->createTextNode('FR7630004018540001003802740')); $idIban->appendChild($c);
        $cdtrAcct->appendChild($idIban);
        $pmtInf->appendChild($cdtrAcct);

        $cdtrAgt = $dom->createElement('CdtrAgt');
        $finInst = $dom->createElement('FinInstnId');
        $c = $dom->createElement('BIC'); $c->appendChild($dom->createTextNode('BNPAFRPPNAN')); $finInst->appendChild($c);
        $cdtrAgt->appendChild($finInst);
        $pmtInf->appendChild($cdtrAgt);

        $c = $dom->createElement('ChrgBr'); $c->appendChild($dom->createTextNode('SLEV')); $pmtInf->appendChild($c);

        $cdtrSchmeId = $dom->createElement('CdtrSchmeId');
        $id2 = $dom->createElement('Id');
        $prvt = $dom->createElement('PrvtId');
        $othr = $dom->createElement('Othr');
        $c = $dom->createElement('Id'); $c->appendChild($dom->createTextNode('FR85ZZZ597358')); $othr->appendChild($c);
        $sch = $dom->createElement('SchmeNm'); $c = $dom->createElement('Prtry'); $c->appendChild($dom->createTextNode('SEPA')); $sch->appendChild($c); $othr->appendChild($sch);
        $prvt->appendChild($othr);
        $id2->appendChild($prvt);
        $cdtrSchmeId->appendChild($id2);
        $pmtInf->appendChild($cdtrSchmeId);

        foreach ($bills as $bill) {
            $ba = $bill->company->bank_account;
            if (! $ba) continue;

            $mandateId   = $ba->no_rum;
            $dtSignature = Carbon::parse($ba->effective_starting_date)->format('Y-m-d');
            $bic         = $ba->bic;
            $iban        = $ba->iban;
            $amount      = number_format($bill->amount_vat_included, 2, '.', '');

            $txInf = $dom->createElement('DrctDbtTxInf');

            // PmtId
            $pmtId = $dom->createElement('PmtId');
            foreach (['InstrId' => "PRLV-{$mandateId}", 'EndToEndId' => $bill->no_bill] as $tag => $val) {
                $el = $dom->createElement($tag);
                $el->appendChild($dom->createTextNode($val));
                $pmtId->appendChild($el);
            }
            $txInf->appendChild($pmtId);

            $instdAmt = $dom->createElement('InstdAmt');
            $instdAmt->setAttribute('Ccy', 'EUR');
            $instdAmt->appendChild($dom->createTextNode($amount));
            $txInf->appendChild($instdAmt);

            $dr = $dom->createElement('DrctDbtTx');
            $mri = $dom->createElement('MndtRltdInf');
            $c = $dom->createElement('MndtId');       $c->appendChild($dom->createTextNode($mandateId));      $mri->appendChild($c);
            $c = $dom->createElement('DtOfSgntr');    $c->appendChild($dom->createTextNode($dtSignature));  $mri->appendChild($c);
            $c = $dom->createElement('AmdmntInd');    $c->appendChild($dom->createTextNode('false'));       $mri->appendChild($c);
            $dr->appendChild($mri);
            $txInf->appendChild($dr);

            $dbtrAgt = $dom->createElement('DbtrAgt');
            $fi2     = $dom->createElement('FinInstnId');
            $c = $dom->createElement('BIC'); $c->appendChild($dom->createTextNode($bic)); $fi2->appendChild($c);
            $dbtrAgt->appendChild($fi2);
            $txInf->appendChild($dbtrAgt);

            $dbtr = $dom->createElement('Dbtr');
            $c    = $dom->createElement('Nm'); $c->appendChild($dom->createTextNode($bill->company->name)); $dbtr->appendChild($c);
            $txInf->appendChild($dbtr);

            $dba = $dom->createElement('DbtrAcct');
            $idb = $dom->createElement('Id');
            $c   = $dom->createElement('IBAN'); $c->appendChild($dom->createTextNode($iban)); $idb->appendChild($c);
            $dba->appendChild($idb);
            $txInf->appendChild($dba);

            $rmt = $dom->createElement('RmtInf');
            $c   = $dom->createElement('Ustrd');
            $c->appendChild($dom->createTextNode("Facture {$bill->no_bill} – {$bill->company->name}"));
            $rmt->appendChild($c);
            $txInf->appendChild($rmt);

            $pmtInf->appendChild($txInf);
        }

        // Save & Download
        $xmlString = $dom->saveXML();
        $filename  = "OrdresPrlv-{$dateStart}_{$dateEnd}.xml";
        Storage::disk('local')->put("exports/{$filename}", $xmlString);

        return response()->download(
            storage_path("app/exports/{$filename}")
        );
    }
}
