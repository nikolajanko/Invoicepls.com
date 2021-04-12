<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if($this->session->userdata('USER_DATA') != null){
            $this->user_data = $this->session->userdata('USER_DATA');
            $this->user_id = $this->user_data['user_id'];
        }
    }

	public function index()
	{
        if(!empty($this->user_data)){

            $this->db->select('*');
            $this->db->where('user_id', $this->user_id);
            $user = $this->db->get('users')->row_array();

            $this->db->select('id');
            $this->db->where('user_id', $this->user_id);
            $count = count($this->db->get('invoices')->result_array());

            $page_data = [
                'content'   => $this->load->view('invoice', ['user' => $user], true),
                'user_data' => $this->user_data,
                'count'     => $count,
                'js'        => '/assets/js/invoice.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
        else
        {
            $user = [
                'notes' => '',
                'terms' => '',
                'currency' => '',
                'logo' => '',
                'user_id' => '',
                'first_name' => '',
                'last_name' => '',
            ];
            $page_data = [
                'content'   => $this->load->view('invoice', ['user' => $user], true),
                'js'        => '/assets/js/invoice.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
	}

	public function view($id = 0)
    {
        $this->db->select('id');
        $this->db->where('id', $id);
        $this->db->where('user_id', $this->user_id);
        $result = $this->db->get('invoices')->row_array();

        $this->db->select('id');
        $this->db->where('user_id', $this->user_id);
        $count = count($this->db->get('invoices')->result_array());

        if (empty($result))
        {
            show_404();
        }

        if(isset($this->user_data))
        {
            if($id != '' && is_numeric($id))
            {
                $this->db->select('*');
                $this->db->where('user_id', $this->user_id);
                $user = $this->db->get('users')->row_array();

                $this->db->select('*');
                $this->db->where('id', $id);
                $this->db->where('user_id', $this->user_id);
                $invoice_data = $this->db->get('invoices')->row_array();

                $this->db->select('*');
                $this->db->where('invoice_id', $id);
                $this->db->where('user_id', $this->user_id);
                $invoice_items = $this->db->get('invoice_items')->result_array();

                if(!empty($this->user_data)){
                    $page_data = [
                        'content'   => $this->load->view('invoice', ['user' => $user, 'invoice_data' => $invoice_data, 'invoice_items' => $invoice_items], true),
                        'user_data' => $this->user_data,
                        'count'     => $count,
                        'js'        => '/assets/js/invoice.js',
                        'css'       => '/assets/css/invoice.css'
                    ];
                    $this->load->view('master/master_template', $page_data);
                }
            }
            else{
                show_404();
            }
        }
        else{
            show_404();
        }

    }

	public function upload_logo()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $logo = $_FILES['logo'];

        if(isset($logo) && $logo["error"] == 0){
            $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
            $filename = $logo["name"];
            $filetype = $logo["type"];
            $filesize = $logo["size"];

            // Verify file extension
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!array_key_exists($ext, $allowed))
                $this->helper->json('Error', 'Please select a valid file format.');

            // Verify file size - 5MB maximum
            $maxsize = 5 * 1024 * 1024;
            if($filesize > $maxsize)
                $this->helper->json('Error', 'File size is larger than the allowed limit.');

            // Verify MYME type of the file
            if(in_array($filetype, $allowed)){
                // Check whether file exists before uploading it
                /*if(file_exists("/var/www/invoicepls.com/html/assets/uploads/" . $filename)){
                    $this->helper->json('Error', "File already exists.");
                } else{
                    move_uploaded_file($logo["tmp_name"], "/var/www/invoicepls.com/html/assets/uploads/" . $filename);
                    $this->helper->json('Success', 'Your file was uploaded successfully.', $filename);
                }*/
                move_uploaded_file($logo["tmp_name"], "/var/www/invoicepls.com/html/assets/uploads/" . $filename);
                $this->helper->json('Success', 'Your file was uploaded successfully.', $filename);
            } else{
                $this->helper->json('Error', 'There was a problem uploading your file. Please try again.');
            }
        } else {
            $this->helper->json('Error', $logo['error']);
        }
    }

    public function send_invoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $post = $this->input->post();
        $pdf = $this->pdf_to_send($post);

        if(isset($post['email_to']) && empty($post['email_to']))
            $this->helper->json('Error', "Please enter your client's e-mail address.");

        if(isset($post['email_from']) && empty($post['email_from']))
            $this->helper->json('Error', 'Please enter your e-mail address.');

        if(isset($post['email_subject']) && empty($post['email_subject']))
            $this->helper->json('Error', 'Please enter e-mail subject.');

        if(isset($post['email_message']) && empty($post['email_message']))
            $this->helper->json('Error', 'This field cannot be empty.');

        $this->load->library('email');

        $this->email->from($post['email_from'], 'Invoicepls.com');
        $this->email->to($post['email_to']);
        $this->email->subject($post['email_subject']);
        $this->email->message($post['email_message']);

        $this->email->attach($pdf, '', $post['invoice_name'].'.pdf');
        $this->email->send();


        $this->helper->json('Success', 'Email sent successfully.');
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

    public function generate_pdf()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        require_once APPPATH."/third_party/fpdf/fpdf.php";

        $post = $this->input->post();
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

            $pdf->SetTextColor($font_color[0], $font_color[1], $font_color[2]);
            $pdf->SetFont($font,'',26);
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

            $name = md5($post['invoice_name']).'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html/assets/uploads/'.$name);

            $path = '/assets/uploads/'.$name;
            $download_path = "https://invoicepls.com/assets/uploads/".$name;

            $this->helper->json('Success', 'Successfully', [
                'path' => $path,
                'download_path' => $download_path,
                'name' => $post['invoice_name'].'.pdf'
            ]);
        }
        else if ($post['template'] == '2')
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
            $pdf->SetTextColor($font_color[0], $font_color[1], $font_color[2]);
            $pdf->SetFont($font,'',13);
            $pdf->SetXY(10, 55);
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

            $name = md5($post['invoice_name']).'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html/assets/uploads/'.$name);

            $path = '/assets/uploads/'.$name;
            $download_path = "https://invoicepls.com/assets/uploads/".$name;

            $this->helper->json('Success', 'Successfully', [
                'path' => $path,
                'download_path' => $download_path,
                'name' => $post['invoice_name'].'.pdf'
            ]);
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

            $name = md5($post['invoice_name']).'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html/assets/uploads/'.$name);

            $path = '/assets/uploads/'.$name;
            $download_path = "https://invoicepls.com/assets/uploads/".$name;

            $this->helper->json('Success', 'Successfully', [
                'path' => $path,
                'download_path' => $download_path,
                'name' => $post['invoice_name'].'.pdf'
            ]);
        }
    }

    public function pdf_to_send($post)
    {
        require_once APPPATH."/third_party/fpdf/fpdf.php";

        $post = $this->input->post();
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

            $name = md5($post['invoice_name']).'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html/assets/uploads/'.$name);

            return '/var/www/invoicepls.com/html/assets/uploads/'.$name;
        }
        else if ($post['template'] == '2')
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

            $name = md5($post['invoice_name']).'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html/assets/uploads/'.$name);

            return '/var/www/invoicepls.com/html/assets/uploads/'.$name;
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

            $name = md5($post['invoice_name']).'.pdf';
            $pdf->Output('F', '/var/www/invoicepls.com/html/assets/uploads/'.$name);

            return '/var/www/invoicepls.com/html/assets/uploads/'.$name;
        }
    }
}
