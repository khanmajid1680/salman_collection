<?php
// Include the main TCPDF library (search for installation path).
$this->mypdf_class->tcpdf(); 
global $master_pdf;
global $yy;
$master_pdf      = $sales_data; 
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
        $inv_no  = ($master_pdf[0]['sm_with_gst']>0) ? 'INVOICE' : 'ESTIMATE';

        $tbl_header = '

                    <table cellpadding="0" style="border-top-color:#000;border-left-color:#000;border-right-color:#000;"> 
                        <br/><br/>
                        <tr>
                            <td width="100%"style="text-align:center;font-size:20px"><h2>SALMAN COLLECTION</h2></td>
                        </tr>
                        <tr>
                            <td width="100%" style="text-align:center;font-size:14px; line-height:18px">
                                48-D, Nakhuda Street, Shop No. 2, Below Beg Mohd. Baug,<br/>
                                Mohd. Ali Road, Mumbai - 400 003, INDIA<br/>
                                Tel (T): 022 2342 1587 / 2342 1588<br/>
                                Mob (M): 8591691428 / 9167446593 / 9323988060<br/>
                                FOLLOW US ON: Insta: @_.salmancollection._<br/>
                                <b>Web</b>: www.salmancollection.in
                            </td>
                        </tr>
                        <tr>
                            <td width="100%"  style="text-align:center;font-size:15px"><h2>'.$title.'</h2></td>
                        </tr>
                    </table>
                    <br/>
                    <table border="1" cellpadding="4" style="font-size:13px;">
                        <tr>
                            <td width="30%"><b>'.strtoupper($_SESSION['user_branch']).'</b></td>
                            <td width="25%"><b>'.$inv_no.' NO:</b> '.$master_pdf[0]['sm_bill_no'].'</td>
                            <td width="45%"><b>DATE:</b> '.date('d-m-Y',strtotime($master_pdf[0]['sm_bill_date'])).'</td>
                        </tr>
                        <tr>
                            <td><b>SALESMAN:</b> '.$master_pdf[0]['user_fullname'].'</td>
                            <td><b>SP. MOBILE:</b> '.$master_pdf[0]['user_mobile'].'</td>
                            <td><b>TRANSPORT:</b> '.$master_pdf[0]['transport_name'].'</td>
                        </tr>
                    </table>
                    <br/>
                    <table border="1" cellpadding="5" style="font-size:13px;">
                        <tr>
                            <td width="50%"><b>BILL TO:</b> '.$master_pdf[0]['account_name'].'<br/>
                                <b>Mobile:</b> '.$master_pdf[0]['account_mobile'].'<br/>
                                 <b>Add :</b> '.nl2br($master_pdf[0]['account_address']).'<br/>
                                <b>GSTIN : </b> '.$master_pdf[0]['account_gst_no'].'<br/>
                            </td>
                            <td width="50%" style="height: 115px"><b>SHIP TO:</b> '.$master_pdf[0]['shipping_account_name'].'<br/>
                                <b>Mobile:</b> '.$master_pdf[0]['shipping_account_mobile'].'<br/>
                                <b>Add :</b> '.nl2br($master_pdf[0]['shipping_account_address']).'<br/>
                                <b>GSTIN : </b> '.$master_pdf[0]['shipping_account_gst_no'].'
                            </td>
                        </tr>
                    </table>

                <table  border="1" cellpadding="3">';
                    if($master_pdf[0]['sm_with_gst']>0){
                        $tbl_header .= '<tr style="font-size:12px;background-color:#e6e6e6;">
                                    <th style="width:4%;"><b>No</b></th>
                                    <th style="width:12%;"><b>ITEMS</b></th>
                                    <th style="width:15%;text-align:center;"><b>STYLE</b></th>
                                    <th style="width:11%;text-align:center;"><b>HSN</b></th>
                                    <th style="width:7%;text-align:center;"><b>QTY</b></th>
                                    <th style="width:9%;text-align:center;"><b>RATE</b></th>
                                    <th style="width:9%;text-align:center;"><b>AMT</b></th>
                                    <th style="width:9%;text-align:center;"><b>DISC</b></th>
                                    <th style="width:9%;text-align:center;"><b>TAXABLE</b></th>
                                    <th style="width:6%;text-align:center;"><b>GST%</b></th>
                                    <th style="width:9%;text-align:center;"><b>TOTAL</b></th>
                                </tr>';
                    }else{

                         $tbl_header .= '<tr style="font-size:12px;background-color:#e6e6e6;">
                                    <th style="width:4%;"><b>No</b></th>
                                    <th style="width:17%;"><b>ITEMS</b></th>
                                    <th style="width:15%;text-align:center;"><b>STYLE</b></th>
                                    <th style="width:10%;text-align:center;"><b>QTY</b></th>
                                    <th style="width:12%;text-align:center;"><b>RATE</b></th>
                                    <th style="width:6%;text-align:center;"><b>PER</b></th>
                                    <th style="width:12%;text-align:center;"><b>AMT</b></th>
                                    <th style="width:12%;text-align:center;"><b>DISC</b></th>
                                    <th style="width:12%;text-align:center;"><b>TOTAL</b></th>
                                </tr>';
                    }
                $tbl_header .= '        
                    </table>';

        $this->writeHTML($tbl_header, true, false, false, false, '');
        $yy = $this->GetY(); 
        $yy = $yy - 6;
        $this->line(5,$yy,5,230);
        $this->line(205,$yy,205,230);

        $this->SetTopMargin($yy + 1);
    }

    // Page footer
    public function Footer() 
    {
        $this->SetFont('Helvetica', 'B', 10,false);
        global $master_pdf;

        $taxable_tag =($master_pdf[0]['sm_taxable_amt']>0 && $master_pdf[0]['sm_with_gst']>0) ? 'TAXABLE AMT' : '';
        $taxable_amt =($master_pdf[0]['sm_taxable_amt']>0 && $master_pdf[0]['sm_with_gst']>0) ? $master_pdf[0]['sm_taxable_amt'] : '';

        $sgst_tag =($master_pdf[0]['sm_sgst_amt']>0) ? 'SGST AMT' : '';
        $sgst_amt =($master_pdf[0]['sm_sgst_amt']>0) ? $master_pdf[0]['sm_sgst_amt'] : '';

        $cgst_tag =($master_pdf[0]['sm_cgst_amt']>0) ? 'CGST AMT' : '';
        $cgst_amt =($master_pdf[0]['sm_cgst_amt']>0) ? $master_pdf[0]['sm_cgst_amt'] : '';

        $igst_tag =($master_pdf[0]['sm_igst_amt']>0) ? 'IGST AMT' : '';
        $igst_amt =($master_pdf[0]['sm_igst_amt']>0) ? $master_pdf[0]['sm_igst_amt'] : '';


        $qrcode = base_url('public/assets/dist/images/salman_qrcode.jpeg');
        $this->Image($qrcode,175, 5.5, 28, 0, 'JPEG');

        $tbl_footer ='';
        $tbl_footer .='<table width="100%"  border="1" cellpadding="4" >
            <tr style="font-size:12px;">
                <th style="width:45%;"><b>GSTIN :27AIPPM6721KZQ</b></th>
                <th style="width:20%;text-align:left;">TOTAL QTY : '.$master_pdf[0]['sm_total_qty'].'</th>
                <th style="width:20%;text-align:left;"><b>SUB AMT</b></th>
                <th style="width:15%;text-align:right;"><b>'.$master_pdf[0]['sm_sub_total'].'</b></th>
            </tr> 
            <tr style="font-size:12px;">
                 <th style="width:45%;">1. DRY CLEAN COMPULSORY</th>
                <th style="width:20%;text-align:left;"></th>
                <th style="width:20%;text-align:left;">DISCOUNT</th>
                <th style="width:15%;text-align:right;">'.$master_pdf[0]['sm_total_disc'].'</th>
            </tr>
            <tr style="font-size:12px;">
                <th style="width:45%;">2. Bill and Tag compulsory at the time of exchange.</th>
                <th style="width:20%;text-align:left;"></th>
                <th style="width:20%;text-align:left;"><b>'.$taxable_tag.'</b></th>
                <th style="width:15%;text-align:right;"><b>'.$taxable_amt.'</b></th>
            </tr>
            <tr style="font-size:12px;">
                <th style="width:45%;">3. Exchange within 3 days of purchase only (dress materials only)</th>
                <th style="width:20%;text-align:left;"></th>
                <th style="width:20%;text-align:left;"><b>'.$sgst_tag.'</b></th>
                <th style="width:15%;text-align:right;"><b>'.$sgst_amt.'</b></th>
            </tr>
            <tr style="font-size:12px;">
               <th style="width:45%;">4. Readymade dresses once sold will not be exchanged or return</th>
                <th style="width:20%;text-align:left;"></th>
                <th style="width:20%;text-align:left;">'.$cgst_tag.'</th>
                <th style="width:15%;text-align:right;"><b>'.$cgst_amt.'</b></th>
            </tr>
            <tr style="font-size:12px;">
                 <th style="width:45%;">5. No Guarantee for Colour and work.</th>
                <th style="width:20%;text-align:left;"></th>
                <th style="width:20%;text-align:left;">'.$igst_tag.'</th>
                <th style="width:15%;text-align:right;">'.$igst_amt.'</th>
            </tr>
            <tr style="font-size:12px;">
               <th style="width:45%;">6. No ironing on Nylon Thread.</th>
                <th style="width:20%;text-align:left;"></th>
                <th style="width:20%;text-align:left;"><b>NET AMT</b></th>
                <th style="width:15%;text-align:right;"><b>'.$master_pdf[0]['sm_final_amt'].'</b></th>
            </tr>
        </table>
        ';

        $this->writeHTML($tbl_footer, true, false, false, false, '');
        // Set font
        $this->SetFont('Helvetica', 'I', 9);
        // Page number
        $this->Cell(0, 0, 'Page '.$this->getPageNumGroupAlias().'/'.$this->getPageGroupAlias(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
       
    }


}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array('297','210'), true, 'UTF-8', false); 
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
$pdf->SetFooterMargin(70);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 68);

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
                    $dispatch_dt = (empty($value['dispatch_date']))?'': '<span style="font-size:11px"><br/>Del-DT :'.$value['dispatch_date'].'</span>';

                if($master_pdf[0]['sm_with_gst']>0){         
                    $body .= '<tr style="font-size:11px;">
                             <td style="width:4%;border-bottom-color:#ccc;">'.$cnt.'</td>
                            <td style="width:12%;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['design_name'].$dispatch_dt.'</td>
                            <td style="width:15%;text-align:left;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['style_name'].'</td>
                            <td style="width:11%;text-align:left;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['hsn_name'].'</td>
                            <td style="width:7%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_qty'].'</td>
                            <td style="width:9%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_rate'].'</td>
                            <td style="width:9%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_sub_total'].'</td>
                            <td style="width:9%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_disc_amt'].'</td>
                            <td style="width:9%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_taxable_amt'].'</td>
                            <td style="width:6%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_igst_per'].'</td>
                            <td style="width:9%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_sub_total_amt'].'</td>
                        </tr>';
                }else{

                    $body .= '<tr style="font-size:11px;">
                             <td style="width:4%;border-bottom-color:#ccc;">'.$cnt.'</td>
                            <td style="width:17%;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['design_name'].$dispatch_dt.'</td>
                            <td style="width:15%;text-align:left;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['style_name'].'</td>
                            <td style="width:10%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_qty'].'</td>
                            <td style="width:12%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_rate'].'</td>
                            <td style="width:6%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">Nos</td>
                            <td style="width:12%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_sub_total'].'</td>
                            <td style="width:12%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_disc_amt'].'</td>
                            <td style="width:12%;text-align:center;border-bottom-color:#ccc;border-left-color:#ccc;">'.$value['st_sub_total_amt'].'</td>
                        </tr>';

                }
            endforeach;
$body .= '</table>'; 


$pdf->writeHTML($body, true, false, false, false, '');

//Close and output PDF document
if(empty($path)){ 
    $pdf->Output('ORDER-BILL.pdf', 'I');
}else{
    $pdf->Output($path, 'F');
}

//============================================================+
// END OF FILE
//============================================================+