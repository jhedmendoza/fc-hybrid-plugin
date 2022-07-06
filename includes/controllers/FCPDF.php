<?php

if (!defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use Mpdf\Mpdf;

class FCPDF {

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

      $mpdf = new Mpdf();
      $html_header.='<table>';
      $html_header.='<tr>';
      $html_header .= '<td><img style="width:170px;border:2px solid #000" src="' . $data['candidate_photo'].'"/><td>';
      $html_header.=  '<td style="vertical-align:top">
                        <h1 style="font-size:40px;font-family:Helvetica">'.$data['candidate_name'].'</h1><br />
                        <p><strong>E-mail: </strong>'.$data['candidate_email'].'</p><br />
                        <p><strong>Skills: </strong>'.$data['candidate_skills'].'</p><br />
                        <p><strong>Location: </strong>'.$data['candidate_location'].'</p><br />
                      </td>';
      $html_header.='</tr>';
      $html_header.='</table><hr style="border-top: 4px double #999;" />';

      $mpdf->setAutoTopMargin='stretch';
      $mpdf->SetHTMLHeader($html_header);
      $mpdf->WriteHTML('<p>'.$data['candidate_about'].'</p>');
      $mpdf->Output();
      exit;
    }
}

$pdf = new FCPDF();
?>
