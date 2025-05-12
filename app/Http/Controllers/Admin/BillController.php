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
        if (strpos($date, '/') !== false) {
            return Carbon::createFromFormat('d/m/Y', $date);
        }
        return Carbon::createFromFormat('Y-m-d', $date);
    }

    public function pdf($no_bill)
    {
        $bill = Bill::where('no_bill', $no_bill)->first();

        $path = $bill->file_path;

        return response()->download(storage_path("app/{$path}"));
    }


    public function export_order_prlv(string $dateStart, string $dateEnd)
    {
        $today      = Carbon::now();
        $dateString = $today->format('Y-m-d');

        $bills = Bill::with('company.bank_account')
            ->where('no_bill', 'like', 'FACT-%')
            ->whereHas('company', fn($q) => $q->where('bill_payment_method', 0))
            ->whereBetween('generated_at', [$dateStart, $dateEnd])
            ->get()
            ->filter(fn($b) =>
                $b->company->bank_account
                && $b->company->bank_account->no_rum
            );

        $groups = $bills->groupBy('no_bill');

        $nbOfTxs = $groups->count();
        $ctrlSum = number_format(
            $groups->sum(fn($g) => $g->sum('amount_vat_included')),
            2, '.', ''
        );

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $ns   = 'urn:iso:std:iso:20022:tech:xsd:pain.008.001.02';
        $root = $dom->createElementNS($ns, 'Document');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $dom->appendChild($root);

        $cstmr = $dom->createElement('CstmrDrctDbtInitn');
        $root->appendChild($cstmr);

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

        $req = $dom->createElement('ReqdColltnDt');
        $req->appendChild($dom->createTextNode($dateString));
        $pmtInf->appendChild($req);

        $cdtr = $dom->createElement('Cdtr');
        $nm2  = $dom->createElement('Nm');
        $nm2->appendChild($dom->createTextNode('ASTORYA S.G.I.'));
        $cdtr->appendChild($nm2);
        $pmtInf->appendChild($cdtr);

        $ca   = $dom->createElement('CdtrAcct');
        $idI  = $dom->createElement('Id');
        $iban = $dom->createElement('IBAN');
        $iban->appendChild($dom->createTextNode('FR7630004018540001003802740'));
        $idI->appendChild($iban);
        $ca->appendChild($idI);
        $pmtInf->appendChild($ca);

        $cda  = $dom->createElement('CdtrAgt');
        $fi   = $dom->createElement('FinInstnId');
        $bic  = $dom->createElement('BIC');
        $bic->appendChild($dom->createTextNode('BNPAFRPPNAN'));
        $fi->appendChild($bic);
        $cda->appendChild($fi);
        $pmtInf->appendChild($cda);

        $cb   = $dom->createElement('ChrgBr');
        $cb->appendChild($dom->createTextNode('SLEV'));
        $pmtInf->appendChild($cb);

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

        foreach ($groups as $noBill => $group) {
            $bill      = $group->first();
            $ba        = $bill->company->bank_account;
            $amount    = number_format(
                $group->sum('amount_vat_included'),
                2, '.', ''
            );

            $tx = $dom->createElement('DrctDbtTxInf');

            $pid = $dom->createElement('PmtId');
            $in  = $dom->createElement('InstrId');
            $in->appendChild($dom->createTextNode('Prlv ASTORYA S.G.I.'));
            $et  = $dom->createElement('EndToEndId');
            $et->appendChild($dom->createTextNode("Mandat {$ba->no_rum}"));
            $pid->appendChild($in);
            $pid->appendChild($et);
            $tx->appendChild($pid);

            $amt = $dom->createElement('InstdAmt');
            $amt->setAttribute('Ccy', 'EUR');
            $amt->appendChild($dom->createTextNode($amount));
            $tx->appendChild($amt);

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

            $dba  = $dom->createElement('DbtrAgt');
            $fi2  = $dom->createElement('FinInstnId');
            $b2   = $dom->createElement('BIC');
            $b2->appendChild($dom->createTextNode($ba->bic));
            $fi2->appendChild($b2);
            $dba->appendChild($fi2);
            $tx->appendChild($dba);

            $db   = $dom->createElement('Dbtr');
            $nmd  = $dom->createElement('Nm');
            $nmd->appendChild($dom->createTextNode($bill->company->name));
            $db->appendChild($nmd);
            $tx->appendChild($db);

            $dac  = $dom->createElement('DbtrAcct');
            $idc  = $dom->createElement('Id');
            $ib2  = $dom->createElement('IBAN');
            $ib2->appendChild($dom->createTextNode($ba->iban));
            $idc->appendChild($ib2);
            $dac->appendChild($idc);
            $tx->appendChild($dac);

            $rm   = $dom->createElement('RmtInf');
            $us   = $dom->createElement('Ustrd');
            $us->appendChild($dom->createTextNode(
                "Astorya {$noBill} {$bill->company->name}"
            ));
            $rm->appendChild($us);
            $tx->appendChild($rm);

            $pmtInf->appendChild($tx);
        }

        $xml  = $dom->saveXML();
        $file = "OrdresPrlv.xml";
        Storage::disk('local')->put("exports/{$file}", $xml);

        return response()->download(
            storage_path("app/exports/{$file}")
        );
    }

}
