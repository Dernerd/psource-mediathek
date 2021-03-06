<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * like have_posts() alternative for comment loop
 * check if there are galleries
 * 
 * 
 * @return type
 */
function psmt_have_comments() {
	
    $the_comment_query = psourcemediathek()->the_comment_query;
    
	return $the_comment_query->have_comments();
    
}
/**
 * Fetch the current comment
 * 
 * @return type
 */
function psmt_the_comment() {
    
	$the_comment_query = psourcemediathek()->the_comment_query;
	return $the_comment_query->the_comment();
    
}




/**
 *  print comment id
 * @param type $comment
 */
function psmt_comment_id( $comment = false ) {
    
    echo psmt_get_comment_id( $comment );
}
/**
 * Get comment id
 * @param int|object $comment
 * @return int comment id
 */
function psmt_get_comment_id( $comment = false ) {
    
    $comment = psmt_get_comment( $comment ); 
    return apply_filters( 'psmt_get_comment_id', $comment->id );
}

function psmt_comment_user_id( $comment = false ) {
    
    echo psmt_get_comment_user_id( $comment );
}
/**
 * 
 * @param type $comment
 * @return int
 */
function psmt_get_comment_user_id( $comment = false ) {
    
    $comment = psmt_get_comment( $comment );
    
    return apply_filters( 'psmt_get_comment_user_id',  $comment->user_id, $comment->id ); 
    
}
function psmt_comment_user_domain( $comment = false ) {
    
    echo psmt_get_comment_user_domain( $comment );
}
/**
 * 
 * @param type $comment
 * @return string
 */
function psmt_get_comment_user_domain( $comment = false ) {
    
    $comment = psmt_get_comment( $comment );
    
    return apply_filters( 'psmt_get_comment_user_domain',  $comment->user_domain, $comment->id ); 
    
}


/**
 * Print comment description
 * 
 * @param type $comment
 */
function psmt_comment_content( $comment = false ) {
    
    echo psmt_get_comment_content( $comment );
        
}
/**
 * Get comment description
 * 
 * @param type $comment
 * @return type
 */
function psmt_get_comment_content( $comment = false ) {
        
    $comment = psmt_get_comment( $comment );

    return apply_filters( 'psmt_get_comment_content', stripslashes( $comment->content ), $comment->id );
        
}

/**
 * Print the date of creation for the comment
 * 
 * @param type $comment
 */
function psmt_comment_date( $comment = false ) {
    
	echo psmt_get_comment_date( $comment );
    
}
/**
 * Get the date this comment was created
 * @param type $comment
 * @return string 
 */
function psmt_get_comment_date( $comment = false ) {
        
        $comment = psmt_get_comment( $comment );
        
        return  apply_filters( 'psmt_get_comment_date', date_i18n( get_option( 'date_format' ), $comment->date_posted ), $comment->id );
        
}

function psmt_list_comments( $args, $comments = null ) {
    $post_id = 0;
    
    if ( ! isset( $args['post_id'] ) ) {
    
        if ( psmt_is_single_media() ) {
            $post_id = psmt_get_current_media_id ();
        } elseif ( psmt_is_single_gallery() ) {
			$post_id = psmt_get_current_gallery_id ();
        }    
        
    } else {
       $post_id = $args['post_id'];
    }
	
    if ( $post_id ){
     
        $comments = get_comments( array('post_id'=> $post_id ) );
    }
	
    wp_list_comments($args, $comments);
}
