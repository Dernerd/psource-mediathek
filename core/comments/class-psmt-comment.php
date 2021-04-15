<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class PSMT_Comment{
    
    public $id;
    
    public $content;
    
    public $user_id;
    
    public $post_id;
    
    public $user_domain;
    
    public $date_posted;
    
    public $parent_id;
    
}
/**
 * 
 * @param type $comment
 * @return PSMT_Comment
 */
function psmt_comment_migrate( $comment ) {
    
    $psmt_comment= new PSMT_Comment;
    
    $psmt_comment->id = $comment->comment_ID;
    
    $psmt_comment->content = $comment->comment_content;
    
    $psmt_comment->user_id = $comment->user_id;
    
    $psmt_comment->post_id = $comment->comment_post_ID;
    
    $psmt_comment->user_domain = $comment->comment_author_url;
    
    $psmt_comment->date_posted = $comment->comment_date;
    
    $psmt_comment->parent_id = $comment->comment_parent;
    
    return $psmt_comment;
    
}


