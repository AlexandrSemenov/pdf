<?php
require_once "vendor/propa/tcpdi/tcpdi.php";

class PdfRotate
{
    public function rotatePDF($file, $degrees, $page = 'all')
    {
        $pdf = new TCPDI();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pagecount = $pdf->setSourceFile($file);

        if($page=="all"){
            for ($i = 1; $i <= $pagecount; $i++) {
                $pageformat = array('Rotate'=>$degrees);
                $tpage = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpage);
                //$info = $pdf->getPageDimensions();
                $orientation = $size['w'] > $size['h'] ? 'L' : 'P';

                $pdf->AddPage($orientation,$pageformat);
                $pdf->useTemplate($tpage);
            }
        }else{
            $rotateFlag = 0;
            for ($i = 1; $i <= $pagecount; $i++){
                if($page == $i){
                    $pageformat = array('Rotate'=>$degrees);
                    $tpage = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tpage);
                    $orientation = $size['w'] > $size['h'] ? 'L' : 'P';

                    $pdf->AddPage($orientation,$pageformat);
                    $pdf->useTemplate($tpage);
                    $rotateFlag = 1;
                }else{
                    if($rotateFlag==1){
                        $rotateFlag = 0;
                        $pageformat = array('Rotate'=>0);

                        $tpage = $pdf->importPage($i);
                        $pdf->AddPage($orientation, $pageformat);
                        $pdf->useTemplate($tpage);
                    }else{
                        $tpage = $pdf->importPage($i);
                        $pdf->AddPage();
                        $pdf->AddPage();
                    }
                }
            }
        }
        $out = realpath($file);

        if(rename($file, $file)){
            $result = $pdf->Output($out, "F");
            if($result == "" ){
                echo "ok";
            }
        }else{
            echo "Failed to rename old PDF";
            die;
        }
    }
}