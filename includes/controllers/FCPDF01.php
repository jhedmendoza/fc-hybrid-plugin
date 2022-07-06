<?php

if (!defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class FCPDF extends TCPDF {

    public function __construct(){
      add_action( 'init', [$this, 'create_pdf'] );
    }


    public function create_pdf() {

      $data = [
        'candidate_name'    => sanitize_post($_POST['candidate_name']),
        'candidate_email'   => sanitize_post($_POST['candidate_email']),
        'candidate_title'   => sanitize_post($_POST['candidate_title']),
        'candidate_location'=> sanitize_post($_POST['candidate_location']),
        'candidate_photo'   => sanitize_post($_POST['current_candidate_photo']),
        'candidate_skills'  => sanitize_post($_POST['resume_skills']),
        'candidate_about'   => sanitize_post($_POST['resume_content']),
      ];

      if ( isset($_POST['submit_resume']) ) {
         $this->build_pdf($data);
      }
    }

    public function build_pdf($data) {

      $logo = hybrid_get_path().'assets/images/logo.png';

      $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
      $candidate_info = "Tel 1234567896 Fax 987654321\n"
                        . "E abc@gmail.com\n"
                        . "www.abc.com";
      $pdf->SetHeaderData($logo, $header_logo_width, $data['candidate_name'], $candidate_info);
      $pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);

      $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
      $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

      $pdf->SetFont('helvetica', '', 12);
      $pdf->AddPage();
      $html = '<html>
      <head></head>
      <body>
        <h1>'.$data['candidate_name'].'</h1>
      </body>
      </html>';
      $pdf->writeHTML($html, true, 0, true, 0);
      $pdf->lastPage();
      $pdf->Output('htmlout.pdf', 'I');
    }
}

$pdf = new FCPDF();
?>
