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
        $period_bills = Carbon::createFromFormat('d/m/Y', $dateStart)->format('m-Y');
        $date = Carbon::createFromFormat('d/m/Y', $dateStart);

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
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 10);

        Storage::put($path, $pdf->output());

        return response()->file(storage_path("app/{$path}"));
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
        $bills = Bill::with('company', 'company.bank_account')
            ->where('no_bill', 'like', 'FACT-%')
            ->whereHas('company', fn($q) => $q->where('bill_payment_method', 0))
            ->whereBetween('generated_at', [$dateStart, $dateEnd])
            ->get()
            ->filter(fn($b) => $b->company->bank_account && $b->company->bank_account->no_rum);
        dd($bills);

        $today      = Carbon::now();
        $dateString = $today->format('Y-m-d');
        $timestamp  = $today->toIso8601String();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $ns = 'urn:iso:std:iso:20022:tech:xsd:pain.008.001.02';
        $root = $dom->createElementNS($ns, 'Document');
        $dom->appendChild($root);

        $cstmr = $dom->createElement('CstmrDrctDbtInitn');
        $root->appendChild($cstmr);

        $grpHdr = $dom->createElement('GrpHdr');
        $cstmr->appendChild($grpHdr);

        foreach ([
            'MsgId'   => "ASTORYA-PRLV-{$dateString}",
            'CreDtTm' => $timestamp,
            'NbOfTxs' => (string) count($bills),
            'CtrlSum' => number_format($bills->sum('amount_vat_included'), 2, '.', '')
        ] as $tag => $value) {
            $el = $dom->createElement($tag);
            $el->appendChild($dom->createTextNode($value));
            $grpHdr->appendChild($el);
        }

        $initgPty = $dom->createElement('InitgPty');
        $nm = $dom->createElement('Nm');
        $nm->appendChild($dom->createTextNode('ASTORYA S.G.I.'));
        $initgPty->appendChild($nm);
        $grpHdr->appendChild($initgPty);

        $pmtInf = $dom->createElement('PmtInf');
        $cstmr->appendChild($pmtInf);

        foreach ([
            'PmtInfId' => "INF-PRLV-{$dateString}",
            'PmtMtd'   => 'DD',
            'BtchBookg'=> 'true',
            'NbOfTxs'  => (string) count($bills),
            'CtrlSum'  => number_format($bills->sum('amount_vat_included'), 2, '.', '')
        ] as $tag => $value) {
            $el = $dom->createElement($tag);
            $el->appendChild($dom->createTextNode($value));
            $pmtInf->appendChild($el);
        }

        $pmtTpInf = $dom->createElement('PmtTpInf');
        $svcLvl   = $dom->createElement('SvcLvl');
        $cd       = $dom->createElement('Cd');
        $cd->appendChild($dom->createTextNode('SEPA'));
        $svcLvl->appendChild($cd);
        $pmtTpInf->appendChild($svcLvl);

        $lcl = $dom->createElement('LclInstrm');
        $cd2 = $dom->createElement('Cd');
        $cd2->appendChild($dom->createTextNode('CORE'));
        $lcl->appendChild($cd2);
        $pmtTpInf->appendChild($lcl);

        $seq = $dom->createElement('SeqTp');
        $seq->appendChild($dom->createTextNode('RCUR'));
        $pmtTpInf->appendChild($seq);

        $pmtInf->appendChild($pmtTpInf);

        $req = $dom->createElement('ReqdColltnDt');
        $req->appendChild($dom->createTextNode($dateString));
        $pmtInf->appendChild($req);

        $cdtr = $dom->createElement('Cdtr');
        $c = $dom->createElement('Nm');
        $c->appendChild($dom->createTextNode('ASTORYA S.G.I.'));
        $cdtr->appendChild($c);
        $pmtInf->appendChild($cdtr);

        $cdtrAcct = $dom->createElement('CdtrAcct');
        $idIban = $dom->createElement('Id');
        $ibanEl = $dom->createElement('IBAN');
        $ibanEl->appendChild($dom->createTextNode('FR7630004018540001003802740'));
        $idIban->appendChild($ibanEl);
        $cdtrAcct->appendChild($idIban);
        $pmtInf->appendChild($cdtrAcct);

        $cdtrAgt = $dom->createElement('CdtrAgt');
        $finInst = $dom->createElement('FinInstnId');
        $bicEl   = $dom->createElement('BIC');
        $bicEl->appendChild($dom->createTextNode('BNPAFRPPNAN'));
        $finInst->appendChild($bicEl);
        $cdtrAgt->appendChild($finInst);
        $pmtInf->appendChild($cdtrAgt);

        $chrg = $dom->createElement('ChrgBr');
        $chrg->appendChild($dom->createTextNode('SLEV'));
        $pmtInf->appendChild($chrg);

        $cdtrSch = $dom->createElement('CdtrSchmeId');
        $id2     = $dom->createElement('Id');
        $prvt    = $dom->createElement('PrvtId');
        $othr    = $dom->createElement('Othr');
        $idEl    = $dom->createElement('Id');
        $idEl->appendChild($dom->createTextNode('FR85ZZZ597358'));
        $othr->appendChild($idEl);
        $schmeNm = $dom->createElement('SchmeNm');
        $prtry   = $dom->createElement('Prtry');
        $prtry->appendChild($dom->createTextNode('SEPA'));
        $schmeNm->appendChild($prtry);
        $othr->appendChild($schmeNm);
        $prvt->appendChild($othr);
        $id2->appendChild($prvt);
        $cdtrSch->appendChild($id2);
        $pmtInf->appendChild($cdtrSch);

        foreach ($bills as $bill) {
            $ba = $bill->company->bank_account;
            if (! $ba) continue;

            $mandateId   = $ba->no_rum;
            $dtSignature = Carbon::parse($ba->effective_starting_date)->format('Y-m-d');
            $bic         = $ba->bic;
            $iban        = $ba->iban;
            $amount      = number_format($bill->amount_vat_included, 2, '.', '');

            $txInf = $dom->createElement('DrctDbtTxInf');

            $pmtIdEl = $dom->createElement('PmtId');
            foreach ([
                'InstrId'    => "PRLV-{$mandateId}",
                'EndToEndId' => $bill->no_bill
            ] as $tag => $val) {
                $e = $dom->createElement($tag);
                $e->appendChild($dom->createTextNode($val));
                $pmtIdEl->appendChild($e);
            }
            $txInf->appendChild($pmtIdEl);

            $instdAmt = $dom->createElement('InstdAmt');
            $instdAmt->setAttribute('Ccy', 'EUR');
            $instdAmt->appendChild($dom->createTextNode($amount));
            $txInf->appendChild($instdAmt);

            $dr = $dom->createElement('DrctDbtTx');
            $mri = $dom->createElement('MndtRltdInf');
            foreach ([
                'MndtId'    => $mandateId,
                'DtOfSgntr' => $dtSignature,
                'AmdmntInd' => 'false',
            ] as $tag => $val) {
                $e = $dom->createElement($tag);
                $e->appendChild($dom->createTextNode($val));
                $mri->appendChild($e);
            }
            $dr->appendChild($mri);
            $txInf->appendChild($dr);

            $dbtrAgt = $dom->createElement('DbtrAgt');
            $fi2     = $dom->createElement('FinInstnId');
            $b2      = $dom->createElement('BIC');
            $b2->appendChild($dom->createTextNode($bic));
            $fi2->appendChild($b2);
            $dbtrAgt->appendChild($fi2);
            $txInf->appendChild($dbtrAgt);

            $dbtr = $dom->createElement('Dbtr');
            $n    = $dom->createElement('Nm');
            $n->appendChild($dom->createTextNode($bill->company->name));
            $dbtr->appendChild($n);
            $txInf->appendChild($dbtr);

            $dba = $dom->createElement('DbtrAcct');
            $idb = $dom->createElement('Id');
            $i2  = $dom->createElement('IBAN');
            $i2->appendChild($dom->createTextNode($iban));
            $idb->appendChild($i2);
            $dba->appendChild($idb);
            $txInf->appendChild($dba);

            $rmt = $dom->createElement('RmtInf');
            $u   = $dom->createElement('Ustrd');
            $u->appendChild($dom->createTextNode("Facture {$bill->no_bill} – {$bill->company->name}"));
            $rmt->appendChild($u);
            $txInf->appendChild($rmt);

            $pmtInf->appendChild($txInf);
        }

        $xmlString = $dom->saveXML();
        $filename  = "OrdresPrlv.xml";
        Storage::disk('local')->put("exports/{$filename}", $xmlString);

        return response()->download(storage_path("app/exports/{$filename}"));
    }
}
