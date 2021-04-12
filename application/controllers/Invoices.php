<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->user_data = $this->session->userdata('USER_DATA');
        $this->user_id = $this->user_data['user_id'];
    }

	public function index()
	{
	    $this->db->select('id, invoice_name, invoice_pdf_link, date_created');
	    $this->db->where('user_id', $this->user_id);
	    $result = $this->db->get('invoices')->result_array();

        $this->db->select('id');
        $this->db->where('user_id', $this->user_id);
        $count = count($this->db->get('invoices')->result_array());

        if(!empty($this->user_data)){
            $page_data = [
                'content'   => $this->load->view('invoices', ['invoices' => $result], true),
                'user_data' => $this->user_data,
                'count'     => $count,
                'js'        => '/assets/js/invoices.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
        else
        {
            show_404();
        }
	}

	public function save_invoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $post = $this->input->post();
        $items_array = json_decode($post['items'], true);

        $this->db->insert('invoices', [
            'user_id'           => $this->user_id,
            'invoice_name'      => $post['invoice_name'],
            'logo'              => $post['file_name'],
            'from'              => $post['from'],
            'bill_to'           => $post['bill_to'],
            'ship_to'           => $post['ship_to'],
            'date'              => $post['date'],
            'payment_terms'     => $post['payment_terms'],
            'date_due'          => $post['date_due'],
            'notes'             => $post['notes'],
            'terms'             => $post['terms'],
            'tax'               => $post['tax'],
            'tax_value'         => $post['tax_value'],
            'discount'          => $post['discount'],
            'discount_value'    => $post['discount_value'],
            'shipping_value'    => $post['shipping_value'],
            'amount_paid'       => $post['amount_paid'],
            'subtotal'          => $post['subtotal'],
            'total'             => $post['total'],
            'balance_due'       => $post['balance_due'],
            'currency'          => $post['currency'],
            'template'          => $post['template'],
            'head_color'        => $post['head_color'],
            'body_color'        => $post['body_color'],
            'background_color'  => $post['background_color'],
            'font'              => $post['font'],
            'font_color'        => $post['font_color'],
            'logo_position'     => $post['logo_position'],
            'date_created'      => date("Y-m-d H:i:s")
        ]);

        $invoice_id = $this->db->insert_id();

        foreach ($items_array as $items) {
            if ($items['single_item'] != '' && $items['single_rate'] != '' && $items['single_quantity'] != '')
            {
                $this->db->insert('invoice_items', [
                    'invoice_id' => $invoice_id,
                    'user_id'    => $this->user_id,
                    'item'       => $items['single_item'],
                    'rate'       => $items['single_rate'],
                    'quantity'   => $items['single_quantity']
                ]);
            }
        }

        $invoice_pdf_link = $this->generate_pdf($post, $invoice_id);

        $this->db->where('id', $invoice_id);
        $this->db->update('invoices', [
            'invoice_pdf_link'  => $invoice_pdf_link,
        ]);

        $this->helper->json('Success', 'Successfully saved invoice.');
    }

    public function edit_invoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $post = $this->input->post();
        $items_array = json_decode($post['items'], true);

        $this->db->where('id', $post['invoice_id']);
        $this->db->update('invoices', [
            'invoice_name'      => $post['invoice_name'],
            'logo'              => $post['file_name'],
            'from'              => $post['from'],
            'bill_to'           => $post['bill_to'],
            'ship_to'           => $post['ship_to'],
            'date'              => $post['date'],
            'payment_terms'     => $post['payment_terms'],
            'date_due'          => $post['date_due'],
            'notes'             => $post['notes'],
            'terms'             => $post['terms'],
            'tax'               => $post['tax'],
            'tax_value'         => $post['tax_value'],
            'discount'          => $post['discount'],
            'discount_value'    => $post['discount_value'],
            'shipping_value'    => $post['shipping_value'],
            'amount_paid'       => $post['amount_paid'],
            'subtotal'          => $post['subtotal'],
            'total'             => $post['total'],
            'balance_due'       => $post['balance_due'],
            'template'          => $post['template'],
            'head_color'        => $post['head_color'],
            'body_color'        => $post['body_color'],
            'background_color'  => $post['background_color'],
            'font'              => $post['font'],
            'font_color'        => $post['font_color'],
            'logo_position'     => $post['logo_position'],
            'currency'          => $post['currency'],
        ]);

        $rows = explode(',', $post['remove_row']);
        foreach ($rows as $row)
        {
            $this->db->where('id', $row);
            $this->db->delete('invoice_items');
        }

        foreach ($items_array as $items) {
            if ($items['single_item'] != '' && $items['single_rate'] != '' && $items['single_quantity'] != '')
            {
                if(isset($items['item_id']) && !empty($items['item_id']))
                {
                    $this->db->where('id', $items['item_id']);
                    $this->db->where('invoice_id', $post['invoice_id']);
                    $this->db->update('invoice_items', [
                        'item'       => $items['single_item'],
                        'rate'       => $items['single_rate'],
                        'quantity'   => $items['single_quantity']
                    ]);
                }
                else
                {
                    $this->db->insert('invoice_items', [
                        'invoice_id' => $post['invoice_id'],
                        'user_id'    => $this->user_id,
                        'item'       => $items['single_item'],
                        'rate'       => $items['single_rate'],
                        'quantity'   => $items['single_quantity']
                    ]);
                }
            }
        }

        $invoice_pdf_link = $this->generate_pdf($post, $post['invoice_id']);

        $this->db->where('id', $post['invoice_id']);
        $this->db->update('invoices', [
            'invoice_pdf_link'  => $invoice_pdf_link,
        ]);

        $this->helper->json('Success', 'Successfully edited invoice.');
    }

    public function delete_invoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $invoice_id = $this->input->post('invoice_id');

        if (is_numeric($invoice_id)) {
            $this->db->where('id', $invoice_id);
            $this->db->delete('invoices');

            $this->db->where('invoice_id', $invoice_id);
            $this->db->delete('invoice_items');

            $this->helper->json('Success', 'Invoice successfully deleted.');
        }
        else{
            $this->helper->json('Error', 'Invoice is not deleted.');
        }
    }

    public function download_pdf()
    {
        $get = $this->input->get();
        $pdf = $get['pdf'];

        if (isset($pdf) && !empty($pdf))
        {
            $exploded_string = explode('/', $pdf);
            $x = explode('-',$exploded_string[3]);
            $downloadName = $x[0]."-".$x[1];

            $pdfFile = '/var/www/invoicepls.com/html'.$pdf;
            // check if file exist in server
            if(file_exists($pdfFile)) {
                header('Content-Transfer-Encoding: Binary');
                header('Content-Type: application/pdf');
                header('Content-Length: ' . filesize($pdfFile));
                header('Content-Disposition: attachment; filename=' . $downloadName . '.pdf');
                readfile($pdfFile);
                exit();
            }else{
                echo "File not found.";
            }
        }
    }

    public function hex_to_rgb($hex) {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = [$r, $g, $b];
        return $rgb;
    }

    public function generate_pdf($post, $invoice_id)
    {
        require_once APPPATH."/third_party/fpdf/fpdf.php";

        $currency = $post['currency'];
        $items_array = json_decode($post['items'], true);

        $head_color = $this->hex_to_rgb($post['head_color']);
        $body_color = $this->hex_to_rgb($post['body_color']);
        $background_color = $this->hex_to_rgb($post['background_color']);
        $font_color = $this->hex_to_rgb($post['font_color']);
        $font = $post['font'];

        if($post['template'] == '1')
        {
            $pdf = new FPDF();
            $pdf->AddFont($font,'',strtolower($font).'.php');
            $pdf->AddPage();

            //for background
            $pdf->SetAutoPageBreak(false);
            $pdf->SetXY(0,0);
            $h = $pdf->GetPageHeight();
            $w = $pdf->GetPageWidth();
            $pdf->SetFillColor($background_color[0],$background_color[1],$background_color[2]);
            $pdf->Cell($w, $h, '', '', '', '', true);
            $pdf->SetXY(10,10);

            $pdf->SetFont($font,'',26);
            $pdf->SetTextColor($font_color[0], $font_color[1], $font_color[2]);
            $pdf->SetFillColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->Cell(190, 4, '', 0,1, 'L', true);

            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $pdf->SetXY(10, 14);
            if($post['logo_position'] == 'left'){
                $pdf->Cell(190,52, $post['invoice_name'].'  ', 0,0,'R', true);
            }
            else{
                $pdf->Cell(190,52, '  '.$post['invoice_name'], 0,0,'', true);
            }

            $pdf->SetXY(10, 80);
            $pdf->SetFont($font,'',13);
            $pdf->Cell(120,10,"From: ".$post['from']);
            $pdf->Cell(12,10,'Date: '.$post['date'],0,1, 'L');
            $pdf->Cell(120,10,"Bill to: ".$post['bill_to'],0,0,'L');
            $pdf->Cell(22,10,'Due Date: '.$post['date_due'],0,1, 'L');
            if(isset($post['ship_to']) && !empty($post['ship_to']))
            {
                $pdf->Cell(120,10,'Ship to: '.$post['ship_to'],0,0,"L");
            }
            else{
                $pdf->Cell(120,10,'');
            }
            if(isset($post['payment_terms']) && empty($post['payment_terms']))
            {
                $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
                $pdf->Cell(70,10,'Balance Due: '.$post['balance_due'],0,1, 'L', true);
            }
            else{
                $pdf->Cell(36,10,'Payment Terms: '.$post['payment_terms'],0,1, 'L');
                $pdf->Cell(120,10);
                $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
                $pdf->Cell(70,10,'Balance Due: '.$post['balance_due'],0,1, 'L', true);
            }

            if(isset($post['file_name']) && !empty($post['file_name']))
            {
                if($post['logo_position'] == 'left'){
                    $pdf->Image('/var/www/invoicepls.com/html/assets/uploads/'.$post['file_name'], 20, 20, 30);
                }
                else{
                    $pdf->Image('/var/www/invoicepls.com/html/assets/uploads/'.$post['file_name'], 160, 20, 30);
                }
            }
            $pdf->Ln();
            $pdf->SetFont($font,'',13);
            //Table head
            $pdf->SetXY(10, 125);
            $pdf->SetFillColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->Cell(100, 8, '   Item', 0,0, 'L', true);
            $pdf->Cell(30, 8, 'Quantity', 0,0, 'C', true);
            $pdf->Cell(30, 8, 'Rate', 0,0, 'C', true);
            $pdf->Cell(30, 8, 'Amount', 0,1, 'C', true);
            //Table body
            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $Y= 133;
            foreach ($items_array as $items){
                $pdf->SetXY(14,$Y);
                $pdf->MultiCell(100, 8, $items['single_item'], 0, 'L', true);
                $H = $pdf->GetY();
                $height= $H-$Y;
                //for spacing in table
                $pdf->SetXY(10,$Y);
                $pdf->Cell(4, $height, '', 0,0, 'L', true);
                $pdf->SetXY(110,$Y);
                $pdf->Cell(30, $height, $items['single_quantity'], 0,0, 'C', true);
                $pdf->SetXY(140,$Y);
                $pdf->Cell(30, $height, $items['single_rate'].' '.$currency, 0,0, 'C', true);
                if (!empty($items['single_quantity']) && !empty($items['single_rate']))
                {
                    $pdf->SetXY(170,$Y);
                    $pdf->Cell(0, $height, $items['single_quantity'] * $items['single_rate']." ".$currency, 0,1, 'C', true);
                }
                else{
                    $pdf->SetXY(170,$Y);
                    $pdf->Cell(0, $height, "0 ".$currency, 0,1, 'C', true);
                }
                $pdf->SetDrawColor($head_color[0], $head_color[1], $head_color[2]);
                $pdf->SetLineWidth(0.3);
                $pdf->Line(10.2, $Y, 210-10.2, $Y);
                $Y=$H;
            }
            $pdf->SetXY(10,$Y + 10);
            $pdf->SetFont($font,'',13);
            $pdf->Cell(20,10,'Subtotal: '.$post['subtotal'],0,0, 'L');
            $pdf->Cell(0,10,'',0,1, 'R');

            if (isset($post['tax_value']) && !empty($post['tax_value'])){
                if($post['tax'] == 'flat'){
                    $pdf->Cell(10,10,'Tax: + '.$post['tax_value'].' '.$currency,0,0, 'L');
                    $pdf->Cell(0,10,'',0,1, 'R');
                } else{
                    $tax = (floatval($post['tax_value']) / 100) * floatval($post['subtotal']);
                    $pdf->Cell(24,10,'Tax ('.$post['tax_value'].'%): + '.$tax.' '.$currency,0,0, 'L');
                    $pdf->Cell(0,10,'',0,1, 'R');
                }
            }
            if (isset($post['discount_value']) && !empty($post['discount_value'])){
                if($post['discount'] == 'flat'){
                    $pdf->Cell(20,10,'Discount: - '.$post['discount_value'].' '.$currency,0,0, 'L');
                    $pdf->Cell(0,10,'',0,1, 'R');
                } else{
                    $discount = (floatval($post['discount_value']) / 100) * floatval($post['subtotal']);
                    $pdf->Cell(34,10,'Discount ('.$post['discount_value'].'%): - '.$discount.' '.$currency,0,0, 'L');
                    $pdf->Cell(0,10,'',0,1, 'R');
                }
            }
            if (isset($post['shipping_value']) && !empty($post['shipping_value'])){
                $pdf->Cell(20,10,'Shipping: + '.$post['shipping_value'].' '.$currency,0,0, 'L');
                $pdf->Cell(0,10,'',0,1, 'R');
            }
            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $pdf->Cell(70,10,'Total: '.$post['total'],0,1, 'L', true);

            if (isset($post['amount_paid']) && !empty($post['amount_paid'])){
                $pdf->Cell(30,10,'Amount paid: - '.$post['amount_paid'].' '.$currency,0,0, 'L');
                $pdf->Cell(0,10,'',0,1, 'R');
            }
            $pdf->Ln();
            if(isset($post['notes']) && !empty($post['notes'])){
                $pdf->MultiCell(190,10, "Notes: ".$post['notes'], 0,'L');
            }
            if(isset($post['terms']) && !empty($post['terms'])){{
            }
                $pdf->MultiCell(190,10, 'Terms: '.$post['terms'], 0,'L');
            }
            $pdf->SetAutoPageBreak(false);
            $pdf->SetXY(10, 283);
            $pdf->SetFillColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->Cell(190, 4, '', 0,1, 'L', true);

            $user_mail = md5($this->user_data['email']);
            $date = date("Y-m-d");
            $link = '/assets/uploads/INVOICE-'.$invoice_id.'-'.$user_mail.'-'.$date.'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html/'.$link);

            return $link;
        }
        else if($post['template'] == '2')
        {
            $pdf = new FPDF();
            $pdf->AddFont($font,'',strtolower($font).'.php');
            $pdf->AddPage();
            $pdf->SetAutoPageBreak(false);
            $pdf->SetXY(0,0);
            $h = $pdf->GetPageHeight();
            $w = $pdf->GetPageWidth();
            $pdf->SetFillColor($background_color[0],$background_color[1],$background_color[2]);
            $pdf->Cell($w, $h, '', '', '', '', true);
            $pdf->SetXY(10,10);
            $pdf->SetFont($font,'',24);
            $pdf->SetTextColor(255);
            $x = $pdf->GetPageWidth();
            $pdf->SetFillColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->SetXY(0, 0);
            if($post['logo_position'] == 'left'){
                $pdf->Cell($x, 50, $post['invoice_name'].'   ', '', '', 'R', true);
            } else{
                $pdf->Cell($x, 50,'   '.$post['invoice_name'], '', '', 'L', true);
            }
            if(isset($post['file_name']) && !empty($post['file_name']))
            {
                if($post['logo_position'] == 'left')
                {
                    $pdf->Image('/var/www/invoicepls.com/html/assets/uploads/'.$post['file_name'], 10, 6, 30);
                } else{
                    $pdf->Image('/var/www/invoicepls.com/html/assets/uploads/'.$post['file_name'], 170, 6, 30);
                }
            }
            $pdf->SetFont($font,'',13);
            $pdf->SetXY(10, 55);
            $pdf->SetTextColor($font_color[0], $font_color[1], $font_color[2]);
            $pdf->Cell(100,10,"From: ", 0, 0, 'L');
            $pdf->Cell(52,10,'Date: ',0,0, 'L');
            $pdf->Cell(0,10,'Date Due: ',0,1, 'L');
            $pdf->SetXY(10, 63);
            $pdf->Cell(100,10, $post['from'],0,0, 'L');
            $pdf->Cell(52,10,$post['date'],0,0, 'L');
            $pdf->Cell(0,10,$post['date_due'],0,1, 'L');

            $pdf->SetDrawColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->SetLineWidth(0.5);
            $pdf->Line(10, 73, 200, 73);
            $pdf->SetXY(10, 74);
            $pdf->Cell(100,10,"Bill To: ", 0, 1, 'L');
            $pdf->SetXY(10, 82);
            $pdf->Cell(100,10,$post['bill_to'],0,1, 'L');

            $Y = $pdf->GetY() + 5;

            if(!empty($post['ship_to']) && !empty($post['payment_terms'])){
                $pdf->SetXY(10, 90);
                $pdf->Cell(100,10,"Ship To: ", 0, 0, 'L');
                $pdf->Cell(52,10,'Payment Terms: ',0,1, 'L');
                $pdf->SetXY(10, 98);
                $pdf->Cell(100,10,$post['ship_to'],0,0, 'L');
                $pdf->Cell(52,10,$post['payment_terms'],0,0, 'L');
                $Y = $pdf->GetY() + 10;
            } elseif (!empty($post['ship_to'])){
                $pdf->SetXY(10, 90);
                $pdf->Cell(100,10,"Ship To: ", 0, 1, 'L');
                $pdf->SetXY(10, 98);
                $pdf->Cell(100,10,$post['ship_to'],0,0, 'L');
                $Y = $pdf->GetY() + 10;
            } elseif (!empty($post['payment_terms'])){
                $pdf->SetXY(10, 90);
                $pdf->Cell(52,10,'Payment Terms: ',0,1, 'L');
                $pdf->SetXY(10, 98);
                $pdf->Cell(52,10,$post['payment_terms'],0,0, 'L');
                $Y = $pdf->GetY() + 10;
            } else{
                $pdf->Cell(52,10,'',0,0, 'L');
                $Y = $pdf->GetY() + 16;
            }

            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $pdf->SetFont($font,'',18);
            $pdf->SetXY(10, $Y);
            $pdf->Cell(90,10,'Balance Due: '.$post['balance_due'],0,1, 'L', true);

            $Y = $pdf->GetY();
            $pdf->SetDrawColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->SetLineWidth(0.5);
            $pdf->Line(10, $Y + 3, 200, $Y + 3);

            $pdf->SetXY(10, 125);
            $pdf->SetFont($font,'',13);
            $pdf->SetTextColor(255);
            $pdf->SetFillColor($head_color[0], $head_color[1], $head_color[2]);

            $pdf->Cell(100, 8, '   Item', 0,0, 'L', true);
            $pdf->Cell(30, 8, 'Quantity', 0,0, 'C', true);
            $pdf->Cell(30, 8, 'Rate', 0,0, 'C', true);
            $pdf->Cell(30, 8, 'Amount', 0,1, 'C', true);

            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $Y= 133;
            foreach ($items_array as $items){
                $pdf->SetTextColor($font_color[0], $font_color[1], $font_color[2]);
                $pdf->SetXY(14,$Y);
                $pdf->MultiCell(100, 8, $items['single_item'], 0, 'L', true);
                $H = $pdf->GetY();
                $height= $H-$Y;
                $pdf->SetXY(10,$Y);
                $pdf->Cell(4, $height, '', 0,0, 'L', true);
                $pdf->SetXY(110,$Y);
                $pdf->Cell(30, $height, $items['single_quantity'], 0,0, 'C', true);
                $pdf->SetXY(140,$Y);
                $pdf->Cell(30, $height, $items['single_rate'].' '.$currency, 0,0, 'C', true);
                if (!empty($items['single_quantity']) && !empty($items['single_rate']))
                {
                    $pdf->SetXY(170,$Y);
                    $pdf->Cell(0, $height, $items['single_quantity'] * $items['single_rate']." ".$currency, 0,1, 'C', true);
                }
                else{
                    $pdf->SetXY(170,$Y);
                    $pdf->Cell(0, $height, "0 ".$currency, 0,1, 'C', true);
                }
                $pdf->SetDrawColor($head_color[0], $head_color[1], $head_color[2]);
                $pdf->SetLineWidth(0.3);
                $pdf->Line(10.2, $Y, 210-10.2, $Y);
                $Y=$H;
            }
            $pdf->SetXY(10,$Y + 10);
            $pdf->SetFont($font,'',13);
            $pdf->Cell(20,10,'Subtotal: '.$post['subtotal'],0,1, 'L');

            if (isset($post['tax_value']) && !empty($post['tax_value'])){
                if($post['tax'] == 'flat'){
                    $pdf->Cell(10,10,'Tax: + '.$post['tax_value'].' '.$currency,0,1, 'L');
                } else{
                    $tax = (floatval($post['tax_value']) / 100) * floatval($post['subtotal']);
                    $pdf->Cell(44,10,'Tax ('.$post['tax_value'].'%): + '.$tax.' '.$currency,0,1, 'L');
                }
            }
            if (isset($post['discount_value']) && !empty($post['discount_value'])){
                if($post['discount'] == 'flat'){
                    $pdf->Cell(40,10,'Discount: - '.$post['discount_value'].' '.$currency,0,1, 'L');
                } else{
                    $discount = (floatval($post['discount_value']) / 100) * floatval($post['subtotal']);
                    $pdf->Cell(54,10,'Discount ('.$post['discount_value'].'%): - '.$discount.' '.$currency,0,1, 'L');
                }
            }
            if (isset($post['shipping_value']) && !empty($post['shipping_value'])){
                $pdf->Cell(40,10,'Shipping: + '.$post['shipping_value'].' '.$currency,0,1, 'L');
            }
            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $pdf->Cell(70,10,'Total: '.$post['total'],0,1, 'L', true);

            if (isset($post['amount_paid']) && !empty($post['amount_paid'])){
                $pdf->Cell(30,10,'Amount paid: - '.$post['amount_paid'].' '.$currency,0,1, 'L');
            }
            $pdf->Ln();
            $pdf->SetAutoPageBreak(false);
            if(isset($post['notes']) && !empty($post['notes'])){
                $pdf->MultiCell(190,10, "Notes: ".$post['notes'], 0,'L');
            }
            if(isset($post['terms']) && !empty($post['terms'])){{
            }
                $pdf->MultiCell(190,10, 'Terms: '.$post['terms'], 0,'L');
            }

            $user_mail = md5($this->user_data['email']);
            $date = date("Y-m-d");
            $link = '/assets/uploads/INVOICE-'.$invoice_id.'-'.$user_mail.'-'.$date.'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html'.$link);

            return $link;
        }
        else
        {
            $pdf = new FPDF();
            $pdf->AddFont($font,'',strtolower($font).'.php');
            $pdf->AddPage();
            $pdf->SetAutoPageBreak(false);
            $pdf->SetXY(0,0);
            $h = $pdf->GetPageHeight();
            $w = $pdf->GetPageWidth();
            $pdf->SetFillColor($background_color[0],$background_color[1],$background_color[2]);
            $pdf->Cell($w, $h, '', '', '', '', true);
            $pdf->SetXY(10,10);
            $pdf->SetFont($font,'',24);
            $pdf->SetTextColor($font_color[0], $font_color[1], $font_color[2]);
            $pdf->SetAutoPageBreak(false);
            $x = $pdf->GetPageHeight();
            $pdf->SetFillColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->SetXY(0, 0);
            $pdf->Cell(6, $x, '', 0,0,'', true);
            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $pdf->SetXY(6.1, 0);
            if($post['logo_position'] == 'left')
            {
                $w = $pdf->GetPageWidth();
                $pdf->Cell($w, 50, $post['invoice_name'].'     ', '', '', 'R', true);
            }else{
                $pdf->Cell($x, 50, '  '.$post['invoice_name'], '', '', 'L', true);
            }
            if(isset($post['file_name']) && !empty($post['file_name']))
            {
                if($post['logo_position'] == 'left'){
                    $pdf->Image('/var/www/invoicepls.com/html/assets/uploads/'.$post['file_name'], 15, 6, 30);
                } else{
                    $pdf->Image('/var/www/invoicepls.com/html/assets/uploads/'.$post['file_name'], 170, 6, 30);
                }
            }

            $pdf->SetFont($font,'',13);
            $pdf->SetXY(10, 55);
            $pdf->Cell(100,10,"From: ", 0, 0, 'L');
            $pdf->Cell(52,10,'Date: ',0,0, 'L');
            $pdf->Cell(0,10,'Date Due: ',0,1, 'L');
            $pdf->SetXY(10, 63);
            $pdf->Cell(100,10,$post['from'],0,0, 'L');
            $pdf->Cell(52,10,$post['date'],0,0, 'L');
            $pdf->Cell(0,10,$post['date_due'],0,1, 'L');

            $pdf->SetDrawColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->SetLineWidth(0.5);
            $pdf->Line(5, 73, 210, 73);
            $pdf->SetXY(10, 74);
            $pdf->Cell(100,10,"Bill To: ", 0, 1, 'L');
            $pdf->SetXY(10, 82);
            $pdf->Cell(100,10,$post['bill_to'],0,1, 'L');

            $Y = $pdf->GetY() + 5;

            if(!empty($post['ship_to']) && !empty($post['payment_terms'])){
                $pdf->SetXY(10, 90);
                $pdf->Cell(100,10,"Ship To: ", 0, 0, 'L');
                $pdf->Cell(52,10,'Payment Terms: ',0,1, 'L');
                $pdf->SetXY(10, 98);
                $pdf->Cell(100,10,$post['ship_to'],0,0, 'L');
                $pdf->Cell(52,10,$post['payment_terms'],0,0, 'L');
                $Y = $pdf->GetY() + 10;
            } elseif (!empty($post['ship_to'])){
                $pdf->SetXY(10, 90);
                $pdf->Cell(100,10,"Ship To: ", 0, 1, 'L');
                $pdf->SetXY(10, 98);
                $pdf->Cell(100,10,$post['ship_to'],0,0, 'L');
                $Y = $pdf->GetY() + 10;
            } elseif (!empty($post['payment_terms'])){
                $pdf->SetXY(10, 90);
                $pdf->Cell(52,10,'Payment Terms: ',0,1, 'L');
                $pdf->SetXY(10, 98);
                $pdf->Cell(52,10,$post['payment_terms'],0,0, 'L');
                $Y = $pdf->GetY() + 10;
            } else{
                $pdf->Cell(52,10,'',0,0, 'L');
                $Y = $pdf->GetY() + 16;
            }

            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $pdf->SetFont($font,'',18);
            $pdf->SetXY(10, $Y);
            $pdf->Cell(90,10,'Balance Due: '.$post['balance_due'],0,1, 'L', true);

            $pdf->SetXY(10, 125);
            $pdf->SetFont($font,'',13);
            $pdf->SetTextColor(255);
            $pdf->SetFillColor($head_color[0], $head_color[1], $head_color[2]);
            $pdf->Cell(100, 8, '   Item', 0,0, 'L', true);
            $pdf->Cell(30, 8, 'Quantity', 0,0, 'C', true);
            $pdf->Cell(30, 8, 'Rate', 0,0, 'C', true);
            $pdf->Cell(30, 8, 'Amount', 0,1, 'C', true);

            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $Y= 133;
            foreach ($items_array as $items){
                $pdf->SetTextColor($font_color[0], $font_color[1], $font_color[2]);
                $pdf->SetXY(14,$Y);
                $pdf->MultiCell(100, 8, $items['single_item'], 0, 'L', true);
                $H = $pdf->GetY();
                $height= $H-$Y;
                $pdf->SetXY(10,$Y);
                $pdf->Cell(4, $height, '', 0,0, 'L', true);
                $pdf->SetXY(110,$Y);
                $pdf->Cell(30, $height, $items['single_quantity'], 0,0, 'C', true);
                $pdf->SetXY(140,$Y);
                $pdf->Cell(30, $height, $items['single_rate'].' '.$currency, 0,0, 'C', true);
                if (!empty($items['single_quantity']) && !empty($items['single_rate']))
                {
                    $pdf->SetXY(170,$Y);
                    $pdf->Cell(30, $height, $items['single_quantity'] * $items['single_rate']." ".$currency, 0,1, 'C', true);
                }
                else{
                    $pdf->SetXY(170,$Y);
                    $pdf->Cell(30, $height, "0 ".$currency, 0,1, 'C', true);
                }
                $pdf->SetDrawColor($head_color[0], $head_color[1], $head_color[2]);
                $pdf->SetLineWidth(0.3);
                $pdf->Line(10.2, $Y, 210-10.2, $Y);
                $Y=$H;
            }
            $pdf->SetXY(10,$Y + 10);
            $pdf->SetFont($font,'',13);
            $pdf->Cell(150,10,'Subtotal: ',0,0, 'R');
            $pdf->Cell(40,10,$post['subtotal'],0,1, 'R');

            if (isset($post['tax_value']) && !empty($post['tax_value'])){
                if($post['tax'] == 'flat'){
                    $pdf->Cell(150,10,'Tax: ',0,0, 'R');
                    $pdf->Cell(40,10,'+ '.$post['tax_value'].' '.$currency,0,1, 'R');
                } else{
                    $tax = (floatval($post['tax_value']) / 100) * floatval($post['subtotal']);
                    $pdf->Cell(150,10,'Tax ('.$post['tax_value'].'%): ',0,0, 'R');
                    $pdf->Cell(40,10,'+ '.$tax.' '.$currency,0,1, 'R');
                }
            }
            if (isset($post['discount_value']) && !empty($post['discount_value'])){
                if($post['discount'] == 'flat'){
                    $pdf->Cell(150,10,'Discount: ',0,0, 'R');
                    $pdf->Cell(40,10,'- '.$post['discount_value'].' '.$currency,0,1, 'R');
                } else{
                    $discount = (floatval($post['discount_value']) / 100) * floatval($post['subtotal']);
                    $pdf->Cell(150,10,'Discount ('.$post['discount_value'].'%): ',0,0, 'R');
                    $pdf->Cell(40,10,'- '.$discount.' '.$currency,0,1, 'R');
                }
            }
            if (isset($post['shipping_value']) && !empty($post['shipping_value'])){
                $pdf->Cell(150,10,'Shipping: ',0,0, 'R');
                $pdf->Cell(40,10,'+ '.$post['shipping_value'].' '.$currency,0,1, 'R');
            }
            $pdf->SetFillColor($body_color[0], $body_color[1], $body_color[2]);
            $pdf->Cell(130,10,'',0,0, 'L');
            $pdf->Cell(20,10,'Total: ',0,0, 'R', true);
            $pdf->Cell(40,10, $post['total'],0,1, 'R', true);

            if (isset($post['amount_paid']) && !empty($post['amount_paid'])){
                $pdf->Cell(150,10,'Amount paid: ',0,0, 'R');
                $pdf->Cell(40,10,'- '.$post['amount_paid'].' '.$currency,0,1, 'R');
            }
            $pdf->Ln();
            $pdf->SetAutoPageBreak(false);
            $pdf->SetFont($font,'',13);
            if(isset($post['notes']) && !empty($post['notes'])){
                $pdf->MultiCell(190,10, "Notes: ".$post['notes'], 0,'L');
            }
            if(isset($post['terms']) && !empty($post['terms'])){{
            }
                $pdf->MultiCell(190,10, 'Terms: '.$post['terms'], 0,'L');
            }
            $user_mail = md5($this->user_data['email']);
            $date = date("Y-m-d");
            $link = '/assets/uploads/INVOICE-'.$invoice_id.'-'.$user_mail.'-'.$date.'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html'.$link);

            return $link;
        }
    }
}
