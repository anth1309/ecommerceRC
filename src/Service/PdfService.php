<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\RequestStack;

class PdfService
{
    private $domPdf;
    public function __construct(
        private RequestStack $requestStack,
    ) {

        $this->domPdf = new Dompdf();
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Garamond');
        $this->domPdf->setOptions($pdfOptions);
    }


    public function showPdfFile($html)
    {
        $session = $this->requestStack->getSession();
        $bascket = $session->get('bascket', []);
        $lastReference = $session->get('lastReference');
        $fichier = $lastReference . '.pdf';
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->render();
        $this->domPdf->stream($fichier, [
            'Attachement' => false
        ]);
        // $session->remove('bascket');
        //$session->remove("orderId");
    }
}
