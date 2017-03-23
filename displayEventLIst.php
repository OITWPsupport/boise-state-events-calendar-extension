<?php 
/*
Plugin Name: BSU Events Calendar extension     
Plugin URI: wpsupport.boisestate.edu/david
Description: Provides modifying template search path		
Version: 1.0
Author: David Ferro
*/
function widget_output( $args) {
        global $wp_query, $tribe_ecp, $post;
        $old_post = $post;
        
       extract( $args);
       if(!isset($limit)){
           $limit=5;
       }
       //print_r($args);
       // extract( $instance, EXTR_SKIP );
        // extracting $instance provides $title, $limit
        //$title = apply_filters('widget_title', $title );
        if ( tribe_get_option('viewOption') == 'upcoming') {
                $event_url = tribe_get_listview_link($category != -1 ? intval($category) : null);
        } else {
                $event_url = tribe_get_gridview_link($category != -1 ? intval($category) : null);
        }

        if( function_exists( 'tribe_get_events' ) ) {
                
                $posts = tribe_get_events( 'eventDisplay=upcoming&numResults=' . $limit .'&eventCat=' . $category );
                $template = TribeEventsTemplates::getTemplateHierarchy( $template_name );
        }

        // if no posts, and the don't show if no posts checked, let's bail
        if ( ! $posts && $no_upcoming_events ) {
                return;
        }

//        /* Before widget (defined by themes). */
//        echo $before_widget;
//
//        /* Title of widget (before and after defined by themes). */
//        echo ( $title ) ? $before_title . $title . $after_title : '';

        if ( $posts ) {
                /* Display list of events. */
                $result= "<div class='upcoming'>";
                foreach( $posts as $post ) : 
                        setup_postdata($post);
                        $result .= displayEvents($post);
                endforeach;
                $result .= "</div>";

                $wp_query->set('eventDisplay', $old_display);

                /* Display link to all events */
                $result .=  '<div class="dig-in"><a href="' . $event_url . '">' . 'View All Events' . '</a></div>';
        } 
        else {
                _e('There are no upcoming events at this time.', 'tribe-events-calendar');
        }

        /* After widget (defined by themes). */
        $result .= $after_widget;
        $post = $old_post;
        
        return $result;
}	

function displayEvents($post){

$event = array();
$tribe_ecp = TribeEvents::instance();
reset($tribe_ecp->metaTags); // Move pointer to beginning of array.
foreach($tribe_ecp->metaTags as $tag){
	$var_name = str_replace('_Event','',$tag);
	$event[$var_name] = tribe_get_event_meta( $post->ID, $tag, true );
}
//print_r($event);
$event = (object) $event; //Easier to work with.
//print_r($event);
//ob_start();
//if ( !isset($alt_text) ) { $alt_text = ''; }
//post_class($alt_text,$post->ID);
//$class = ob_get_contents();
//
//ob_end_clean();
//var_dump($class);
//print_r($post);
//echo "<p $class >";
$result = "	<div class='event' style='font-size:14px; font-weight:bold;'>";
$result .=  "	<a href='" . get_permalink($post->ID) ."'>". $post->post_title ." </a>";
$result .=  "	</div>";
$result .=  "	<div class='when' style='font-size:10px !important;'>";
		
			$space = false;
			$output = '';
			$result .=  tribe_get_start_date( $post->ID ); 

         if( tribe_is_multiday( $post->ID ) || !$event->AllDay ) {
            $result .=  ' - ' . tribe_get_end_date($post->ID);
         }

			if($event->AllDay) {
				$result .=  ' <small>('.__('All Day','tribe-events-calendar').')</small>';
         }
       
$result .=  "	</div> <hr />";
 $alt_text = ( empty( $alt_text ) ) ? 'alt' : ''; 
 
 return $result;
}


add_shortcode('EventListDisplay', 'widget_output');

?>