<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if($this->session->userdata('USER_DATA') != null)
        {
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

            $page_data = [
                'user_data' => $this->user_data,
                'js'        => '/assets/js/test.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
        else
        {
            $page_data = [
                'js'        => '/assets/js/test.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('testing', $page_data);
        }

    }

    public function pdf()
    {
        require_once APPPATH."/third_party/fpdf/fpdf.php";

        $payment_terms = '';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetXY(0,0);
        $h = $pdf->GetPageHeight();
        $w = $pdf->GetPageWidth();
        $pdf->SetFillColor(135,206,250);
        $pdf->Cell($w, $h, '', '', '', '', true);
        $pdf->SetFont('Arial','',26);
        $pdf->SetFillColor(135,206,250);
        $pdf->Cell(190, 3, '', 0,1, 'L', true);
        $pdf->SetFillColor(235);
        $pdf->Cell(190,40, 'Invoice #1', 0, 0, '', true);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetXY(10, 60);
        $pdf->SetFont('Arial','',13);
        $pdf->SetTextColor(80);
        $pdf->Cell(13,10,"From: ");
        $pdf->SetTextColor(0);
        $pdf->Cell(107,10,"Pera");
        $pdf->SetTextColor(80);
        $pdf->Cell(12,10,'Date: ',0,0, 'L');
        $pdf->SetTextColor(0);
        $pdf->Cell(0,10,'1.1.2020',0,1, 'L');
        $pdf->SetTextColor(80);
        $pdf->Cell(14,10,"Bill to: ",0,0,'L');
        $pdf->SetTextColor(0);
        $pdf->Cell(106,10,"Mika");
        $pdf->SetTextColor(80);
        $pdf->Cell(22,10,'Due Date: ',0,0, 'L');
        $pdf->SetTextColor(0);
        $pdf->Cell(0,10,'20.1.2020',0,1, 'L');
        $pdf->SetTextColor(80);
        $pdf->Cell(17,10,'Ship to: ',0,0,"L");
        $pdf->SetTextColor(0);
        $pdf->Cell(103,10,'Moravac',0,0, 'L');
        $pdf->SetTextColor(80);
        if($payment_terms == '')
        {
            $pdf->SetTextColor(0);
            $pdf->Cell(29,10,'Balance Due: ',0,0, 'L');
            $pdf->Cell(0,10,'200$ USD',0,1, 'L');
        }
        else{
            $pdf->Cell(35,10,'Payment Terms: ',0,0, 'L');
            $pdf->SetTextColor(0);
            $pdf->Cell(0,10,'Nema',0,1, 'L');
            $pdf->Cell(120,10);
            $pdf->Cell(29,10,'Balance Due: ',0,0, 'L');
            $pdf->Cell(0,10,'20.1.2020',0,1, 'L');
        }
        //$pdf->Image('', 160, 15, 30);
        $pdf->Ln();
        $pdf->SetFont('Arial','',12);
        //Table head
        $pdf->SetFillColor(135,206,250);
        $pdf->Cell(100, 8, 'Item', 0,0, 'L', true);
        $pdf->Cell(30, 8, 'Quantity', 0,0, 'L', true);
        $pdf->Cell(30, 8, 'Rate', 0,0, 'L', true);
        $pdf->Cell(30, 8, 'Amount', 0,1, 'L', true);
        //Table body
        $Y= 132;
            $pdf->SetXY(10,$Y);
            $pdf->MultiCell(100, 8, 'asdasd');
            $H = $pdf->GetY();
            $pdf->SetXY(110,$Y);
            $pdf->Cell(30, 8, '5', 0,0);
            $pdf->SetXY(140,$Y);
            $pdf->Cell(30, 8, '5', 0,0);
            $pdf->SetXY(170,$Y);
            $pdf->Cell(0, 8, 5 * 5, 0,1);
            $Y=$H;
        $pdf->SetDrawColor(135,206,250);
        $pdf->Line(10, $Y, 210-10, $Y);
        $pdf->SetXY(10,$Y + 15);
        $pdf->SetFont('Arial','',13);
        $pdf->SetTextColor(80);
        $pdf->Cell(20,10,'Subtotal: ',0,0, 'L');
        $pdf->SetTextColor(0);
        $pdf->Cell(20,10,'250 USD',0,0, 'L');
        $pdf->Cell(0,10,'',0,1, 'R');

        $pdf->SetTextColor(0);
        $pdf->Cell(20,10,'Total: ',0,0, 'L');
        $pdf->Cell(0,10,'',0,1, 'R');
        $pdf->Ln();
        $pdf->SetTextColor(80);
        $pdf->MultiCell(190,10, "Notes: ", 0,'L');
        $pdf->MultiCell(190,10, 'Terms: ', 0,'L');

        $pdf->SetAutoPageBreak(false);
        $pdf->SetXY(10, 285);
        $pdf->SetFillColor(135,206,250);
        $pdf->Cell(190, 3, '', 0,1, 'L', true);

        $pdf->Output();

    }

    public function pdf2()
    {
        require_once APPPATH."/third_party/fpdf/fpdf.php";

        $image = './assets/invoice.png';
        $currency = 'USD';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',24);
        $pdf->SetTextColor(255,255, 255);
        $x = $pdf->GetPageWidth();
        $pdf->SetFillColor(65,105,225);
        $pdf->SetXY(0, 0);
        $pdf->Cell($x, 42, 'Racun za septembar 2020   ', '', '', 'R', true);
        $pdf->Image($image, 10, 6, 30);
        $pdf->SetFont('Arial','',13);
        $pdf->SetXY(8, 50);
        $pdf->SetTextColor(80);
        $pdf->Cell(100,10,"From: ", 0, 0, 'L');
        $pdf->Cell(52,10,'Date: ',0,0, 'L');
        $pdf->Cell(0,10,'Date Due: ',0,1, 'L');
        $pdf->SetTextColor(0);
        $pdf->SetXY(8, 60);
        $pdf->Cell(100,10,'Nikola Jankovic, Bulevar Nikole Tesle 49/3',0,0, 'L');
        $pdf->Cell(52,10,'31. September, 2020',0,0, 'L');
        $pdf->Cell(0,10,'1. September, 2020',0,1, 'L');

        $pdf->SetDrawColor(65,105,225);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(7, 70, 203, 70);
        $pdf->SetXY(8, 73);
        $pdf->SetTextColor(80);
        $pdf->Cell(100,10,"Bill To: ", 0, 1, 'L');
        $pdf->SetTextColor(0);
        $pdf->SetXY(8, 83);
        $pdf->Cell(100,10,'Djordje Jankovic, selo Moravac bb',0,1, 'L');

        $pdf->SetTextColor(80);
        $pdf->SetXY(8, 93);
        $pdf->Cell(100,10,"Ship To: ", 0, 0, 'L');
        $pdf->Cell(52,10,'Payment Terms: ',0,1, 'L');

        $pdf->SetTextColor(0);
        $pdf->SetXY(8, 103);
        $pdf->Cell(100,10,'selo Moravac bb',0,0, 'L');
        $pdf->Cell(52,10,'Odmah da se plati',0,1, 'L');

        $pdf->SetFillColor(100,149,237);
        $pdf->SetFont('Arial','',18);
        $pdf->SetXY(8, 118);
        $pdf->SetTextColor(255);
        $pdf->Cell(45,10,'Balance Due: ',0,0, 'L', true);
        $pdf->Cell(50,10,'250 USD',0,1, 'L', true);

        $pdf->SetDrawColor(65,105,225);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(7, 135, 203, 135);

        $pdf->SetXY(8, 142);
        $pdf->SetFont('Arial','',13);
        $pdf->SetTextColor(255,255, 255);
        $pdf->SetFillColor(65,105,225);

        $pdf->Cell(100, 8, 'Item', 0,0, 'L', true);
        $pdf->Cell(30, 8, 'Quantity', 0,0, 'L', true);
        $pdf->Cell(30, 8, 'Rate', 0,0, 'L', true);
        $pdf->Cell(35, 8, 'Amount', 0,1, 'L', true);

        $pdf->SetFillColor(205,92,92);

        $Y = 150;

        $pdf->SetTextColor(0);
        $pdf->SetXY(10,$Y);
        $pdf->MultiCell(100, 8, "Lopta", 0, '');
        $H = $pdf->GetY();
        $height= $H-$Y;
        $pdf->SetXY(110,$Y);
        $pdf->Cell(30, $height, '1', 0,0, '');
        $pdf->SetXY(140,$Y);
        $pdf->Cell(30, $height, '12 '.$currency, 0,0, '' );
        $pdf->SetXY(170,$Y);
        $pdf->Cell(0, $height, "12 ".$currency, 0,1, '');

        $Y = 160;

        $pdf->SetDrawColor(135,206,250);
        $pdf->Line(10, $Y, 210-10, $Y);
        $pdf->SetXY(10,$Y + 15);
        $pdf->SetFont('Arial','',13);
        $pdf->SetTextColor(80);
        $pdf->Cell(150,10,'Subtotal:',0,0, 'R');
        $pdf->SetTextColor(0);
        $pdf->Cell(43,10,'250 USD',0,1, 'R');

        $pdf->SetTextColor(80);
        $pdf->Cell(150,10,'Tax(14%):',0,0, 'R');
        $pdf->SetTextColor(0);
        $pdf->Cell(43,10,'23222 USD',0,1, 'R');

        $pdf->SetTextColor(80);
        $pdf->Cell(151,10,'Discount(11%): ',0,0, 'R');
        $pdf->SetTextColor(0);
        $pdf->Cell(42,10,'120 USD',0,1, 'R');

        $pdf->SetTextColor(0);
        $pdf->Cell(151,10,'Total: ',0,0, 'R');
        $pdf->Cell(42,10,'500 USD',0,1, 'R');
        $pdf->Ln();
        $pdf->SetTextColor(80);
        $pdf->MultiCell(190,10, "Notes: ", 0,'L');
        $pdf->MultiCell(190,10, 'Terms: ', 0,'L');

        // Line break
        $pdf->Ln(20);
        $pdf->Output();
    }

    public function pdf3()
    {
        require_once APPPATH."/third_party/fpdf/fpdf.php";

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',24);
        $pdf->SetTextColor(0);
        $pdf->SetAutoPageBreak(false);
        $x = $pdf->GetPageHeight();
        $pdf->SetFillColor(75,0,130);
        $pdf->SetXY(0, 0);
        $pdf->Cell(6, $x, '', 0,0,'', true);
        $y = $pdf->GetPageWidth();
        $pdf->SetFillColor(230,230,250);
        $pdf->SetXY(6.1, 0);
        $pdf->Cell($y, 50, '  Racun za septembar 2020', '', '', 'L', true);
        $pdf->Image('./assets/uploads/invoice.png', 170, 6, 30);

        $pdf->SetFont('Arial','',13);
        $pdf->SetXY(11, 55);
        $pdf->SetTextColor(80);
        $pdf->Cell(100,10,"From: ", 0, 0, 'L');
        $pdf->Cell(52,10,'Date: ',0,0, 'L');
        $pdf->Cell(0,10,'Date Due: ',0,1, 'L');
        $pdf->SetTextColor(0);
        $pdf->SetXY(11, 63);
        $pdf->Cell(100,10,'Nikola Jankovic, Bulevar Nikole Tesle 49/3',0,0, 'L');
        $pdf->Cell(52,10,'31. September, 2020',0,0, 'L');
        $pdf->Cell(0,10,'1. September, 2020',0,1, 'L');

        $pdf->SetDrawColor(75,0,130);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(5, 73, 210, 73);
        $pdf->SetXY(11, 74);
        $pdf->SetTextColor(80);
        $pdf->Cell(100,10,"Bill To: ", 0, 1, 'L');
        $pdf->SetTextColor(0);
        $pdf->SetXY(11, 82);
        $pdf->Cell(100,10,'Djordje Jankovic, selo Moravac bb',0,1, 'L');

        $pdf->SetTextColor(80);
        $pdf->SetXY(11, 92);
        $pdf->Cell(100,10,"Ship To: ", 0, 0, 'L');
        $pdf->Cell(52,10,'Payment Terms: ',0,1, 'L');

        $pdf->SetTextColor(0);
        $pdf->SetXY(11, 100);
        $pdf->Cell(100,10,'selo Moravac bb',0,0, 'L');
        $pdf->Cell(52,10,'Odmah da se plati',0,1, 'L');

        $pdf->SetFillColor(230,230,250);
        $pdf->SetFont('Arial','',18);
        $pdf->SetXY(11, 115);
        $pdf->Cell(45,10,'Balance Due: ',0,0, 'L', true);
        $pdf->Cell(50,10,'250 USD',0,1, 'L', true);


        $pdf->Output();

    }

    public function pdf4()
    {
        require_once APPPATH."/third_party/fpdf/fpdf.php";

        $image = './assets/';
        $currency = 'USD';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',24);
        $pdf->SetTextColor(255,255, 255);
        $x = $pdf->GetPageWidth();
        $pdf->Image($image, 0, 0, $x, 20);

        $pdf->Output();
    }
}
