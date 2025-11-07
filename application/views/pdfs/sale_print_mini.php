<?php
// Include the main TCPDF library (search for installation path).
$this->mypdf_class->tcpdf(); 
global $master_pdf;
global $trans_pdf;
global $yy;
$master_pdf      = $sales_data;
$trans_pdf      = $trans_data;
$trans_cnt      = count($trans_pdf); 
$path           = isset($master_pdf[0]['file']) ? $master_pdf[0]['file'] : '';
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF 
{
    //Page header
    public function Header()      
    { 
        global $master_pdf ; 

        $this->SetFont('helvetica', '', 9,false);

        $title = ($master_pdf[0]['sm_with_gst']>0) ? 'TAX INVOICE' : 'ESTIMATE';
        $inv_no  = ($master_pdf[0]['sm_with_gst']>0) ? 'INV' : 'EST';
        $GSTIN  = ($master_pdf[0]['sm_with_gst']>0) ? '<br/><b>GSTIN :27AIPPM6721KZQ</b>' : '';

        $tbl_header = '

            <table cellpadding="0" style="border-top-color:#000;border-left-color:#000;border-right-color:#000;"> 
                        <br/><br/>
                        <tr>
                            <td width="100%"style="text-align:center;font-size:16px"><h2>SALMAN<br/> <span style="font-size:16px">COLLECTION</span></h2></td>
                        </tr>
                        <tr>
                            <td width="100%"style="text-align:center;font-size:9px">48-D, Nakhuda Street, Shop No. 2,<br/>Below Beg Mohd. Baug, Mohd Ali. Road,<br/> Mumbai - 400 003, INDIA<br/>Tel (T): 022 2342 1587 / 2342 1588<br/>Mob (M): 8591691428 /9167446593 /9323988060<br/>02223421587 /02223421588<br/>FOLLOW US ON: Insta: @_.salmancollection._ <br/><b>Web</b>: www.salmancollection.in'.$GSTIN.'</td>
                        </tr>
                        <tr>
                            <td width="100%"style="text-align:center;font-size:10px"><h2><i>'.$title.'</i></h2>
                            </td>
                        </tr>
                    </table>
                    
                    <br/>
                    <table border="1"  cellpadding="3" >
                        <tr>
                            <td style="width:50%; text-align:left"><b>'.$inv_no.'</b> : '.$master_pdf[0]['sm_bill_no'].'</td>
                            <td style="width:50%; text-align:left"><b>DATE</b> : '.date('d-m-Y',strtotime($master_pdf[0]['sm_bill_date'])).'</td>
                        </tr>
                        <tr>
                            <td style="width:100%; text-align:left"><b>S.MAN</b> : '.$master_pdf[0]['user_fullname'].' - '.$master_pdf[0]['user_mobile'].'</td>
                        </tr>
                        <tr>
                            <td style="width:100%"><b>CUSTOMER: </b>'.$master_pdf[0]['account_name'].'</td>
                        </tr>
                        <tr>
                            <td style="width:100%"><b>MOB : </b>'.$master_pdf[0]['account_mobile'].'</td>
                        </tr>
                    </table>
                    <table  border="1" cellpadding="3">
                        <tr style="font-size:11px;background-color:#e6e6e6;">
                            <th style="width:40%;"><b> ITEMS</b></th>
                            <th style="width:15%;text-align:center;"><b>QTY</b></th>
                            <th style="width:20%;text-align:center;"><b>RATE</b></th>
                            <th style="width:25%;text-align:center;"><b>AMT</b></th>
                        </tr>
                    </table>';

        $this->writeHTML($tbl_header, true, false, false, false, '');
        $yy = $this->GetY(); 
        $yy = $yy - 6;
        // $this->line(5,$yy,5,230);
        // $this->line(205,$yy,205,230);

        $this->SetTopMargin($yy + 1);
    }

    // Page footer
    public function Footer() 
    {
        $this->SetFont('Helvetica', 'B', 10,false);
        global $master_pdf;
        global $trans_pdf ;
        $trans_cnt      = count($trans_pdf);
        $footer_h       =($trans_cnt*10.2)+ 89; 
       
        $tbl_footer ='';
        $tbl_footer .='<table width="100%"  border="1" cellpadding="4" >
            <tr style="font-size:13px;">
                <th style="width:50%;text-align:left;">QTY : '.$master_pdf[0]['sm_total_qty'].'</th>
                <th style="width:50%;text-align:right;">AMT : '.round($master_pdf[0]['sm_sub_total']).'</th>
            </tr>';
        if($master_pdf[0]['sm_total_disc']>0):    
            $tbl_footer .='<tr style="font-size:13px;">
                    <th style="width:50%;text-align:left;">DISCOUNT :</th>
                    <th style="width:50%;text-align:right;">'.$master_pdf[0]['sm_total_disc'].'</th>
                </tr>';
            endif;
         if($master_pdf[0]['sm_taxable_amt']>0 && $master_pdf[0]['sm_with_gst']>0):    
            $tbl_footer .='<tr style="font-size:13px;">
                    <th style="width:50%;text-align:left;">TAXABLE :</th>
                    <th style="width:50%;text-align:right;">'.$master_pdf[0]['sm_taxable_amt'].'</th>
                </tr>';
            endif;    
        if($master_pdf[0]['sm_cgst_amt']>0):      
        $tbl_footer .='<tr style="font-size:13px;">
                <th style="width:50%;text-align:left;">CGST :</th>
                <th style="width:50%;text-align:right;">'.$master_pdf[0]['sm_cgst_amt'].'</th>
            </tr>';
        endif;
        if($master_pdf[0]['sm_sgst_amt']>0):     
        $tbl_footer .='<tr style="font-size:13px;">
                <th style="width:50%;text-align:left;">SGST :</th>
                <th style="width:50%;text-align:right;">'.$master_pdf[0]['sm_sgst_amt'].'</th>
            </tr>';
        endif;
        if($master_pdf[0]['sm_igst_amt']>0):     
        $tbl_footer .='<tr style="font-size:13px;">
                <th style="width:50%;text-align:left;">IGST :</th>
                <th style="width:50%;text-align:right;">'.$master_pdf[0]['sm_igst_amt'].'</th>
            </tr>';
        endif; 
        if($master_pdf[0]['sm_round_off']>0):     
        $tbl_footer .='<tr style="font-size:13px;">
                <th style="width:50%;text-align:left;">ROUND :</th>
                <th style="width:50%;text-align:right;">'.$master_pdf[0]['sm_round_off'].'</th>
            </tr>';
        endif;     
        $tbl_footer .='<tr style="font-size:15px;font-weight:bold;">
                <th style="width:50%;text-align:left;">TOTAL :</th>
                <th style="width:50%;text-align:right;">'.round($master_pdf[0]['sm_final_amt']).'</th>
            </tr>';

        if($master_pdf[0]['sm_collected_amt']>0):     
            $tbl_footer .='<tr style="font-size:13px;">
                    <th style="width:50%;text-align:left;">ADVANCE :</th>
                    <th style="width:50%;text-align:right;">'.$master_pdf[0]['sm_collected_amt'].'</th>
                </tr>';

            $tbl_footer .='<tr style="font-size:13px;">
                    <th style="width:50%;text-align:left;">BALANCE :</th>
                    <th style="width:50%;text-align:right;">'.$master_pdf[0]['sm_balance_amt'].'</th>
                </tr>';
                    
        endif;     

        $tbl_footer .='<tr style="font-size:8px;">
                <th style="width:100%;">1. DRY CLEAN COMPULSORY.<br/>
                    2. Bill and Tag compulsory at the time of exchange<br/>
                    3. Exchange within 3 days of purchase only (dress materials only)<br/>
                    4. Readymade dresses once sold will not be exchanged or return.<br/>
                    5. No Guarantee for Colour and work.<br/>
                    6. No ironing on Nylon Thread
                </th>
            </tr>
        </table>';

        //$this->writeHTML($tbl_footer, true, false, false, false, '');
        $this->writeHTMLCell(77.5, 10, 3.5,$footer_h,$tbl_footer, 0, 0, 0, true, 'P', true);
        // Set font
    }


}

$page_size = array('85',($trans_cnt*10.2)+170);
$pdf = new MYPDF('P', PDF_UNIT,$page_size, true, 'UTF-8', false);
$file_name = 'Order.pdf';

// $file_name = 'sales_invoice_pdf.pdf';
$file_path = 'INVOICE.pdf';
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Imran Khan');
$pdf->SetTitle('ORDER INVOICE Pdf');
$pdf->SetSubject('ORDER INVOICE Pdf');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(5, PDF_MARGIN_TOP, 5,true);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(2);

// set auto page breaks
// $pdf->SetAutoPageBreak(FALSE, 68);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}


$pdf->SetFont('Helvetica', '', 13,false);
$body = "";
$title = "Original for Buyer";
$pdf->startPageGroup();
$pdf->AddPage();

$body .= '<table cellpadding="3" >'; 
            foreach($trans_data as $key => $value) : 
                    $cnt = $key+1;
                $dispatch_dt = (empty($value['dispatch_date']))? '<span><br/></span>': '<span style="font-size:10px"><br/>Del-Dt :'.$value['dispatch_date'].'</span>';
                    
                $body .= '<tr style="font-size:12px;">
                            <td style="width:40%;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['design_name'].$dispatch_dt.'</td>
                            <td style="width:15%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_qty'].'</td>
                            <td style="width:20%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_rate'].'</td>
                            <td style="width:25%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;border-right-color:#ccc;">'.$value['st_sub_total_amt'].'</td>
                        </tr>';
            endforeach;
$body .= '</table>'; 


$pdf->writeHTML($body, true, false, false, false, '');

if(empty($path)){ 
    $pdf->Output('ORDER-BILL.pdf', 'I');
}else{
    $pdf->Output($path, 'F');
}

//============================================================+
// END OF FILE
//============================================================+