<?php
require_once "vendor/propa/tcpdi/tcpdi.php";

class PdfDelete
{
    public function deletePdf($file, $page)
    {
        $pdf = new TCPDI();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pagecount = $pdf->setSourceFile($file);

        for ($i = 1; $i <= $pagecount; $i++){
            if($page != $i){

                $pageformat = array('Rotate'=>0);
                $tpage = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpage);
                $orientation = $size['w'] > $size['h'] ? 'L' : 'P';

                $pdf->AddPage($orientation, $pageformat);
                $pdf->useTemplate($tpage);
            }
        }
        $out = realpath($file);
        $pdf->Output($out, "F");

    }
}
