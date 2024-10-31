<?php

function odg_mail( $to, $subject = ull, $message = null, $from ) {
    $headers[] = $from;
    $headers[] = 'Content-type: text/html';

    $subject = str_replace( "\'", "'", $subject );
    $subject = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );
//        $template=  get_newsletter_template();
    $message.='<p style="font-style: italic;font-size:12px;">' . get_option( "oo-notification-footer-texte" ) . '</p>';
    $message = str_replace( "\'", "'", $message );
    $message = str_replace( '\"', "&quot;", $message );
    
    $output = $message;
    $output = nl2br( $output );
    $result = wp_mail( $to, $subject, $output, $headers );


    return $result;
}

function odg_interval_to_minutes($interval)
{
    //$result=$interval->y*24*365 + $interval->m*24*30 + $interval->d*24 + $interval->h;
    $seconds = $interval->days*86400 + $interval->h*3600 + $interval->i*60 + $interval->s;

    return $seconds/60;
}

function odg_remove_dir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") 
            odg_remove_dir($dir."/".$object); else unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($dir); 
   } 
}
