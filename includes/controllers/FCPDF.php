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

      $candidate_name = sanitize_text_field($_POST['candidate_name']);
      $name = split_name($candidate_name);


      $data = [
        'resume_id'         => sanitize_text_field($_POST['resume_id']),
        'first_name'        => isset($name[0]) ? strtoupper($name[0]) : '',
        'last_name'         => isset($name[1]) ? strtoupper($name[1]) : '',
        'candidate_email'   => sanitize_text_field($_POST['candidate_email']),
        'candidate_title'   => sanitize_text_field($_POST['candidate_title']),
        'candidate_location'=> sanitize_text_field($_POST['candidate_location']),
        'candidate_photo'   => sanitize_text_field($_POST['current_candidate_photo']),
        'candidate_skills'  => sanitize_text_field($_POST['resume_skills']),
        'candidate_video'   => sanitize_text_field($_POST['candidate_video']),
        'rate_min'          => sanitize_text_field($_POST['rate_min']),
        'candidate_about'   => sanitize_text_field($_POST['resume_content']),
      ];

      if ( isset($_POST['repeated-row-links']) )
      {
          foreach($_POST['repeated-row-links'] as $link_key) {
            $links[] = array(
              'link_name' => sanitize_text_field($_POST['links_name_'.$link_key]),
              'link_url'  => sanitize_text_field($_POST['links_url_'.$link_key])
            );
          }
          $data['links'] = $links;
      }

      if ( isset($_POST['repeated-row-candidate_education']) )
      {
          foreach($_POST['repeated-row-candidate_education'] as $education_key) {
            $education[] = array(
              'school_name'  => sanitize_text_field($_POST['candidate_education_location_'.$education_key]),
              'qualification'=> sanitize_text_field($_POST['candidate_education_qualification_'.$education_key]),
              'date'         => sanitize_text_field($_POST['candidate_education_date_'.$education_key]),
              'notes'        => sanitize_text_field($_POST['candidate_education_notes_'.$education_key])
            );
          }
          $data['education'] = $education;
      }

      if ( isset($_POST['repeated-row-candidate_experience']) )
      {
          foreach($_POST['repeated-row-candidate_experience'] as $link_key) {
            $job_experience[] = array(
              'employer'  => sanitize_text_field($_POST['candidate_experience_employer_'.$link_key]),
              'job_title' => sanitize_text_field($_POST['candidate_experience_job_title_'.$link_key]),
              'date'      => sanitize_text_field($_POST['candidate_experience_date_'.$link_key]),
              'notes'     => sanitize_text_field($_POST['candidate_experience_notes_'.$link_key]),
            );
          }
          $data['job_experience'] = $job_experience;
      }
      

      if ( isset($_POST['submit_resume']) ) {
         $this->build_pdf($data);
      }
    }

    public function build_pdf($data) {
   
      $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
      $fontDirs = $defaultConfig['fontDir'];
      
      $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
      $fontData = $defaultFontConfig['fontdata'];

      $mpdf = new Mpdf([
        'fontDir' => array_merge($fontDirs, [
          __DIR__ . '/fonts',
        ]),
        'fontdata' => $fontData + [
          'vanguard' => [
              'R' => 'vanguard-cf-regular.ttf',
          ],
          'vanguard-thin' => [
              'R' => 'vanguard-cf-thin.ttf',
          ],
          'vanguard-medium' => [
              'R' => 'vanguard-cf-medium.ttf',
          ]
      ],
        'margin_left'  => 0,
        'margin_right' => 0,
        'margin_top'=> 0,
        'margin_bottom' => 0,
        'default_font' => 'Arial'
      ]);

      $html_header.='<table class="document-header" style="margin-left:40px;">';
      $html_header.='<tr>';
      
      $html_header .= '<td width="65%;">
                        <h1 class="candidate-name" style="font-size:60px;margin:0;padding:0;line-height:0">
                        <span style="line-height:0" class="c-first-name" style="font-family:vanguard-thin;">'.$data['first_name'].'</span><br />
                        <span style="line-height:0" class="c-last-name" style="font-family:vanguard">'.$data['last_name'].'</span></h1>
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

          $html_body.= '<h2>ABOUT ME</h2>';
          $html_body.='<p>'.$data['candidate_about'].'</p>'; 

          if ( isset($data['job_experience']) )
          {
            $html_body.='<div class="career-history" style="width:100%;">';
            $html_body.= '<h2>CAREER HISTORY</h2>';
            foreach ($data['job_experience'] as $job_exp) {
              $html_body.='<div style="padding-bottom:20px">';
              $html_body.='<p>Employer: '.$job_exp['employer'].'</p>';
              $html_body.='<p>Job Title: '.$job_exp['job_title'].'</p>';
              $html_body.='<p>Start/End Date: '.$job_exp['date'].'</p>';
    
              if ( !empty($educ['notes']) ) {
                $html_body.='<p>Notes: '.$job_exp['notes'].'</p>';
              }

              $html_body.='</div>';
    
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
            $html_body.= "<li><img width='15' src=".HYBRID_DIR_URL.'/assets/images/arrow.png'." />&nbsp;&nbsp;&nbsp;".$str."</li>";

          $html_body.='</ul>';
        }

        if (isset($data['links']) || isset($data['candidate_video']) ) {

          $html_body.= '<h2>SOCIAL:</h2>';
          $html_body.='<ul class="social">';

          if ( isset($data['links']) ) {
           
            foreach ($data['links'] as $link) {

              $img_width = '15';
              
              $link_name = isset($link['link_name']) ? strtolower($link['link_name']) : '';
             
              if ( isset($link['link_url'])) {
                $link_url = explode('//',$link['link_url']);
              }

              if ($link_name == 'linkedin') 
                $img_width = '20';
              
              $html_body.='<li>';

              $html_body.='<img width="'.$img_width.'" src='.HYBRID_DIR_URL.'/assets/images/'.$link_name.'.png'.' />&nbsp;&nbsp;&nbsp;';

              $html_body.= $link_url[1].'</li>';
            }
          }


          if ( !empty($data['candidate_video']) ) {

              $video = explode('//',$data['candidate_video']);
          
            $html_body.= '<li><img style="margin-left:-10px" width="40" src='.HYBRID_DIR_URL.'/assets/images/video.png'.' />'.$video[1].'</li>';
          }                

          $html_body.='</ul>';

        }

        $html_body.='</div>';

      $html_body.='</div>';

      $mpdf->SetHTMLFooter("<img width='160' style='margin-left:550px;margin-bottom:50px' src=".HYBRID_DIR_URL.'/assets/images/pdf-logo.png'." />");

      $styles = file_get_contents(HYBRID_DIR_URL . 'assets/css/pdf-layout.css');
      $mpdf->WriteHTML($styles, 1);
      $mpdf->WriteHTML($html_body, 2);

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
