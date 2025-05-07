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
    $today      = Carbon::now();
    $dateString = $today->format('Y-m-d');

    // 1) Récupérer les factures à prélever
    $bills = Bill::with('company.bank_account')
        ->where('no_bill', 'like', 'FACT-%')
        ->whereHas('company', fn($q) => $q->where('bill_payment_method', 0))
        ->whereBetween('generated_at', [$dateStart, $dateEnd])
        ->get()
        ->filter(fn($b) =>
            $b->company->bank_account
            && $b->company->bank_account->no_rum
        );

    // 2) Grouper par numéro de facture
    $groups = $bills->groupBy('no_bill');

    // 3) Calcul des totaux
    $nbOfTxs = $groups->count();
    $ctrlSum = number_format(
        $groups->sum(fn($g) => $g->sum('amount_vat_included')),
        2, '.', ''
    );

    // 4) Création du DOM
    $dom = new \DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;
    $ns   = 'urn:iso:std:iso:20022:tech:xsd:pain.008.001.02';
    $root = $dom->createElementNS($ns, 'Document');
    // ajouter les namespaces xsi et xsd
    $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $root->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
    $dom->appendChild($root);

    $cstmr = $dom->createElement('CstmrDrctDbtInitn');
    $root->appendChild($cstmr);

    // 4a) Group Header
    $grpHdr = $dom->createElement('GrpHdr');
    $cstmr->appendChild($grpHdr);
    foreach ([
        'MsgId'   => "ASTORYA S.G.I. {$dateString}",
        'CreDtTm' => $dateString . 'T' . $today->format('H:i:s'),
        'NbOfTxs' => (string) $nbOfTxs,
        'CtrlSum' => $ctrlSum,
    ] as $tag => $val) {
        $el = $dom->createElement($tag);
        $el->appendChild($dom->createTextNode($val));
        $grpHdr->appendChild($el);
    }
    // InitgPty + bloc Id/OrgId/BICOrBEI
    $initg = $dom->createElement('InitgPty');
    $nm1   = $dom->createElement('Nm');
    $nm1->appendChild($dom->createTextNode('ASTORYA S.G.I.'));
    $initg->appendChild($nm1);
    $id    = $dom->createElement('Id');
    $orgId = $dom->createElement('OrgId');
    $bicBE = $dom->createElement('BICOrBEI');
    $bicBE->appendChild($dom->createTextNode('CMCIFR2A'));
    $orgId->appendChild($bicBE);
    $id->appendChild($orgId);
    $initg->appendChild($id);
    $grpHdr->appendChild($initg);

    // 4b) PmtInf
    $pmtInf = $dom->createElement('PmtInf');
    $cstmr->appendChild($pmtInf);
    foreach ([
        'PmtInfId'  => "ASTORYA S.G.I. {$dateString}",
        'PmtMtd'    => 'DD',
        'BtchBookg' => 'true',
        'NbOfTxs'   => (string) $nbOfTxs,
        'CtrlSum'   => $ctrlSum,
    ] as $tag => $val) {
        $el = $dom->createElement($tag);
        $el->appendChild($dom->createTextNode($val));
        $pmtInf->appendChild($el);
    }
    // PmtTpInf
    $tp = $dom->createElement('PmtTpInf');
    $svc = $dom->createElement('SvcLvl');
    $cd  = $dom->createElement('Cd');
    $cd->appendChild($dom->createTextNode('SEPA'));
    $svc->appendChild($cd);
    $tp->appendChild($svc);
    $lcl = $dom->createElement('LclInstrm');
    $cd2 = $dom->createElement('Cd');
    $cd2->appendChild($dom->createTextNode('CORE'));
    $lcl->appendChild($cd2);
    $tp->appendChild($lcl);
    $seq = $dom->createElement('SeqTp');
    $seq->appendChild($dom->createTextNode('RCUR'));
    $tp->appendChild($seq);
    $pmtInf->appendChild($tp);

    // ReqdColltnDt
    $req = $dom->createElement('ReqdColltnDt');
    $req->appendChild($dom->createTextNode($dateString));
    $pmtInf->appendChild($req);

    // Cdtr
    $cdtr = $dom->createElement('Cdtr');
    $nm2  = $dom->createElement('Nm');
    $nm2->appendChild($dom->createTextNode('ASTORYA S.G.I.'));
    $cdtr->appendChild($nm2);
    $pmtInf->appendChild($cdtr);

    // CdtrAcct
    $ca   = $dom->createElement('CdtrAcct');
    $idI  = $dom->createElement('Id');
    $iban = $dom->createElement('IBAN');
    $iban->appendChild($dom->createTextNode('FR7630004018540001003802740'));
    $idI->appendChild($iban);
    $ca->appendChild($idI);
    $pmtInf->appendChild($ca);

    // CdtrAgt
    $cda  = $dom->createElement('CdtrAgt');
    $fi   = $dom->createElement('FinInstnId');
    $bic  = $dom->createElement('BIC');
    $bic->appendChild($dom->createTextNode('BNPAFRPPNAN'));
    $fi->appendChild($bic);
    $cda->appendChild($fi);
    $pmtInf->appendChild($cda);

    // ChrgBr
    $cb   = $dom->createElement('ChrgBr');
    $cb->appendChild($dom->createTextNode('SLEV'));
    $pmtInf->appendChild($cb);

    // CdtrSchmeId
    $cs  = $dom->createElement('CdtrSchmeId');
    $i2  = $dom->createElement('Id');
    $pr  = $dom->createElement('PrvtId');
    $ot  = $dom->createElement('Othr');
    $iOt = $dom->createElement('Id');
    $iOt->appendChild($dom->createTextNode('FR85ZZZ597358'));
    $ot->appendChild($iOt);
    $sm  = $dom->createElement('SchmeNm');
    $pr2 = $dom->createElement('Prtry');
    $pr2->appendChild($dom->createTextNode('SEPA'));
    $sm->appendChild($pr2);
    $ot->appendChild($sm);
    $pr->appendChild($ot);
    $i2->appendChild($pr);
    $cs->appendChild($i2);
    $pmtInf->appendChild($cs);

    // 5) Un DrctDbtTxInf par facture
    foreach ($groups as $noBill => $group) {
        $bill      = $group->first();
        $ba        = $bill->company->bank_account;
        $amount    = number_format(
            $group->sum('amount_vat_included'),
            2, '.', ''
        );

        $tx = $dom->createElement('DrctDbtTxInf');

        // PmtId
        $pid = $dom->createElement('PmtId');
        $in  = $dom->createElement('InstrId');
        $in->appendChild($dom->createTextNode('Prlv ASTORYA S.G.I.'));
        $et  = $dom->createElement('EndToEndId');
        $et->appendChild($dom->createTextNode("Mandat {$ba->no_rum}"));
        $pid->appendChild($in);
        $pid->appendChild($et);
        $tx->appendChild($pid);

        // InstdAmt
        $amt = $dom->createElement('InstdAmt');
        $amt->setAttribute('Ccy', 'EUR');
        $amt->appendChild($dom->createTextNode($amount));
        $tx->appendChild($amt);

        // Mandate info
        $dr   = $dom->createElement('DrctDbtTx');
        $mri  = $dom->createElement('MndtRltdInf');
        $mid  = $dom->createElement('MndtId');
        $mid->appendChild($dom->createTextNode($ba->no_rum));
        $dos  = $dom->createElement('DtOfSgntr');
        $dos->appendChild($dom->createTextNode(
            Carbon::parse($ba->effective_starting_date)->format('Y-m-d')
        ));
        $amd  = $dom->createElement('AmdmntInd');
        $amd->appendChild($dom->createTextNode('false'));
        $mri->appendChild($mid);
        $mri->appendChild($dos);
        $mri->appendChild($amd);
        $dr->appendChild($mri);
        $tx->appendChild($dr);

        // DbtrAgt
        $dba  = $dom->createElement('DbtrAgt');
        $fi2  = $dom->createElement('FinInstnId');
        $b2   = $dom->createElement('BIC');
        $b2->appendChild($dom->createTextNode($ba->bic));
        $fi2->appendChild($b2);
        $dba->appendChild($fi2);
        $tx->appendChild($dba);

        // Debtor
        $db   = $dom->createElement('Dbtr');
        $nmd  = $dom->createElement('Nm');
        $nmd->appendChild($dom->createTextNode($bill->company->name));
        $db->appendChild($nmd);
        $tx->appendChild($db);

        // Debtor account
        $dac  = $dom->createElement('DbtrAcct');
        $idc  = $dom->createElement('Id');
        $ib2  = $dom->createElement('IBAN');
        $ib2->appendChild($dom->createTextNode($ba->iban));
        $idc->appendChild($ib2);
        $dac->appendChild($idc);
        $tx->appendChild($dac);

        // Remittance
        $rm   = $dom->createElement('RmtInf');
        $us   = $dom->createElement('Ustrd');
        $us->appendChild($dom->createTextNode(
            "Astorya {$noBill} {$bill->company->name}"
        ));
        $rm->appendChild($us);
        $tx->appendChild($rm);

        $pmtInf->appendChild($tx);
    }

    // 6) Enregistrement & download
    $xml  = $dom->saveXML();
    $file = "OrdresPrlv.xml";
    Storage::disk('local')->put("exports/{$file}", $xml);

    return response()->download(
        storage_path("app/exports/{$file}")
    );
}

}
