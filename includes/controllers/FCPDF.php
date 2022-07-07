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
        'resume_id'         => sanitize_post($_POST['resume_id']),
        'candidate_name'    => sanitize_post($_POST['candidate_name']),
        'candidate_email'   => sanitize_post($_POST['candidate_email']),
        'candidate_title'   => sanitize_post($_POST['candidate_title']),
        'candidate_location'=> sanitize_post($_POST['candidate_location']),
        'candidate_photo'   => sanitize_post($_POST['current_candidate_photo']),
        'candidate_skills'  => sanitize_post($_POST['resume_skills']),
        'candidate_video'   => sanitize_post($_POST['candidate_video']),
        'rate_min'          => sanitize_post($_POST['rate_min']),
        'candidate_about'   => sanitize_post($_POST['resume_content']),
      ];

      if ( isset($_POST['repeated-row-links']) )
      {
          foreach($_POST['repeated-row-links'] as $link_key) {
            $links[] = array(
              'link_name' => sanitize_post($_POST['links_name_'.$link_key]),
              'link_url'  => sanitize_post($_POST['links_url_'.$link_key])
            );
          }
          $data['links'] = $links;
      }

      if ( isset($_POST['repeated-row-candidate_education']) )
      {
          foreach($_POST['repeated-row-candidate_education'] as $education_key) {
            $education[] = array(
              'school_name'  => sanitize_post($_POST['candidate_education_location_'.$education_key]),
              'qualification'=> sanitize_post($_POST['candidate_education_qualification_'.$education_key]),
              'date'         => sanitize_post($_POST['candidate_education_date_'.$education_key]),
              'notes'        => sanitize_post($_POST['candidate_education_notes_'.$education_key])
            );
          }
          $data['education'] = $education;
      }

      if ( isset($_POST['repeated-row-candidate_experience']) )
      {
          foreach($_POST['repeated-row-candidate_experience'] as $link_key) {
            $job_experience[] = array(
              'employer'  => sanitize_post($_POST['candidate_experience_employer_'.$link_key]),
              'job_title' => sanitize_post($_POST['candidate_experience_job_title_'.$link_key]),
              'date'      => sanitize_post($_POST['candidate_experience_date_'.$link_key]),
              'notes'     => sanitize_post($_POST['candidate_experience_notes_'.$link_key]),
            );
          }
          $data['job_experience'] = $job_experience;
      }
      

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
                        <p><strong>Professional Title: </strong>'.$data['candidate_title'].'</p>
                        <p><strong>E-mail: </strong>'.$data['candidate_email'].'</p>
                        <p><strong>Skills: </strong>'.$data['candidate_skills'].'</p>
                        <p><strong>Location: </strong>'.$data['candidate_location'].'</p>';

                        if ( !empty($data['candidate_video']) )
                          $html_header.= '<p><strong>Video: </strong>'.$data['candidate_video'].'</p>';

                        if ( !empty($data['rate_min']) )
                          $html_header.= '<p><strong>Minimum rate/h (£): </strong>'.$data['rate_min'].'</p>';

                          if ( isset($data['links']) ) {
                            foreach ($data['links'] as $link) {
                                $html_header.="<p><strong>".$link['link_name']."</strong>: ".$link['link_url']." </p>";
                            }
                          }

      $html_header.= '</td>';
      $html_header.='</tr>';
      $html_header.='</table><hr style="border-top: 4px double #999;" />';

      $mpdf->setAutoTopMargin = 'stretch';
      $mpdf->WriteHTML($html_header);
      $mpdf->WriteHTML('<p>'.$data['candidate_about'].'</p>');

      if ( isset($data['education']) )
      {

        $education.= '<hr />';
        $education.= '<h2>Qualifications/Accreditation</h2>';

        foreach ($data['education'] as $educ) {
          $education.='<p><strong>School Name: </strong>'.$educ['school_name'].'</p>';
          $education.='<p><strong>Qualification: </strong>'.$educ['qualification'].'</p>';
          $education.='<p><strong>Start/End Date: </strong>'.$educ['date'].'</p>';

          if ( !empty($educ['notes']) ) {
            $education.='<p><strong>Notes: </strong>'.$educ['notes'].'</p>';
          }

        }
        $mpdf->WriteHTML($education);
      }

      if ( isset($data['job_experience']) )
      {

        $job_experience.= '<hr />';
        $job_experience.= '<h2>Career History</h2>';

        foreach ($data['job_experience'] as $job_exp) {
          $job_experience.='<p><strong>Employer: </strong>'.$job_exp['employer'].'</p>';
          $job_experience.='<p><strong>Job Title: </strong>'.$job_exp['job_title'].'</p>';
          $job_experience.='<p><strong>Start/End Date: </strong>'.$job_exp['date'].'</p>';

          if ( !empty($educ['notes']) ) {
            $job_experience.='<p><strong>Notes: </strong>'.$job_exp['notes'].'</p>';
          }

        }
        $mpdf->WriteHTML($job_experience);
      }

      $upload = wp_upload_dir();
      $upload_dir = $upload['basedir'];
      $upload_dir = $upload_dir . '/fc_hybrid_plugin';

      if (! is_dir($upload_dir))
         mkdir( $upload_dir, 0700 );

      $mpdf->Output($upload_dir.'/'.$data['resume_id'].'.pdf','F');
    }
}

$pdf = new FCPDF();
?>
