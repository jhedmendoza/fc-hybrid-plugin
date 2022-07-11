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

      $candidate_name = sanitize_post($_POST['candidate_name']);
      $name = split_name($candidate_name);


      $data = [
        'resume_id'         => sanitize_post($_POST['resume_id']),
        'first_name'        => isset($name[0]) ? strtoupper($name[0]) : '',
        'last_name'         => isset($name[1]) ? strtoupper($name[1]) : '',
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

      $mpdf = new Mpdf([
        'margin_left'  => 0,
        'margin_right' => 0,
        'margin_top'=> 0,
        'margin_bottom' => 0,
        'default_font' => 'Arial'
      ]);
      $html_header.='<table class="document-header" style="margin-left:40px">';
      $html_header.='<tr>';
      
      $html_header .= '<td width="65%;">
                        <h1 class="candidate-name">
                        <span class="c-first-name">'.$data['first_name'].'</span><br>
                        <span class="c-last-name">'.$data['last_name'].'</span></h1>
                        <hr style=" height: 1px; background-color: #000;border: none;" /> 
                        <p>'.strtoupper($data['candidate_title']).'</p>
                      </td>';
      $html_header .= '<td><img src="'.$data['candidate_photo'].'"/><td>';
      $html_header.='</tr>';
      $html_header.='</table>';

      $mpdf->setAutoTopMargin = 'stretch';
      $mpdf->WriteHTML($html_header);
                        
      $html_body.='<div class="content-body">';

        $html_body.='<div class="left-content">';
          $html_body.= '<div class="row l-container-header">

              <div class="column left">
                <h4>E-mail</h4>
                <p>'.$data['candidate_email'].'</p>
              </div>

              <div class="column middle">
                <h4>Location</h4>
                <p>'.$data['candidate_location'].'</p>
              </div>';

              if ( !empty($data['rate_min']) ) {
                $html_body.='<div class="column right">
                    <h4 style="white-space: nowrap;">Minimum rate/h (£)</h4>
                    <p>'.$data['rate_min'].'</p>
                  </div>';
              }

          $html_body.='</div>'; // end tag l-container-header

          $html_body.='<div class="left-content-body">';

          $html_body.='<p>'.$data['candidate_about'].'</p>'; 

          if ( isset($data['job_experience']) )
          {
            $html_body.='<div class="career-history" style="width:100%">';
            $html_body.= '<h2>CAREER HISTORY</h2>';
            foreach ($data['job_experience'] as $job_exp) {
              $html_body.='<p><strong>Employer: </strong>'.$job_exp['employer'].'</p>';
              $html_body.='<p><strong>Job Title: </strong>'.$job_exp['job_title'].'</p>';
              $html_body.='<p><strong>Start/End Date: </strong>'.$job_exp['date'].'</p>';
    
              if ( !empty($educ['notes']) ) {
                $html_body.='<p><strong>Notes: </strong>'.$job_exp['notes'].'</p>';
              }
    
            }

            $html_body.='</div>';
          }

          $html_body.='</div>';

         $html_body.='</div>';

        $html_body.='<div class="right-content">';

        if ( isset($data['education']) )
        {
  
          $html_body.= '<h2 style="padding-top:10px">QUALIFICATIONS/<br />ACCREDITATION</h2>';

          $html_body.='<div class="education">';

          foreach ($data['education'] as $educ) {
            $html_body.='<p>School Name:'.$educ['school_name'].'</p>';
            $html_body.='<p>Qualification:'.$educ['qualification'].'</p>';
            $html_body.='<p>Start/End Date:'.$educ['date'].'</p>';
  
            if ( !empty($educ['notes']) ) {
              $html_body.='<p>Notes: '.$educ['notes'].'</p>';
            }
  
          }

          $html_body.='</div>';

        }

        if ( isset($data['candidate_skills']) ) {
          $html_body.= '<h2>SKILLS:</h2>';
          $html_body.='<ul class="skills">';
          $skills = $data['candidate_skills'];
          $string = explode(',', $skills);

          foreach ($string as $str) 
            $html_body.= "<li>".$str."</li>";

          $html_body.='</ul>';
        }

        if (isset($data['links']) || isset($data['candidate_video']) ) {

          $html_body.= '<h2>SOCIAL:</h2>';
          $html_body.='<ul class="social">';

          if ( isset($data['links']) ) {
           
            foreach ($data['links'] as $link) {
                $html_body.='<li class="'.$link['link_name'].'">'.$link['link_url'].'</li>';
            }
          }

          if ( !empty($data['candidate_video']) ) {
            $html_body.= '<li>'.$data['candidate_video'].'</li>';
          }                

          $html_body.='</ul>';

        }

        $html_body.='</div>';

      $html_body.='</div>';

      $styles = file_get_contents(HYBRID_DIR_URL . 'assets/css/pdf-layout.css');
      $mpdf->WriteHTML($styles, 1);
      $mpdf->WriteHTML($html_body, 2);

      $mpdf->Output();
      exit;

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
