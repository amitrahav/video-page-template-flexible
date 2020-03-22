<?php
/*
 * Template Name: Video Content
 * Description: A Page Template To Display Video.
 */

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://the-two.co/
 * @since      1.0.0
 *
 * @package    Videopagetemplate
 * @subpackage Videopagetemplate/public/partials
 */
?>
<?php get_header();?>

<!-- Open loop for content with gutenberg -->
<?php  if ( have_posts() ) : while ( have_posts() ) :the_post();?>

<?php // Some vars
    $unauthorized = false;
    $wrapper_classes = sanitize_html_class(apply_filters('wrapper_classes', ''));

    $headline_open_wrapper = apply_filters('headline_open_wrapper', '<h2>');
    $headline_close_wrapper = apply_filters('headline_close_wrapper', '</h2>');

    $after_wrapper_begins_html = apply_filters('after_wrapper_begins_html', '');
    $after_video_content = get_field('content_after_video');
    $before_wrapper_ends_html = apply_filters('before_wrapper_ends_html', '');
    $comments_wrapper_class = apply_filters('comments_wrapper_class','comments-wrapper');

    // $video_id = esc_url('nX4vzKH4les');
    $is_vimeo   =  get_field('video_provider');
    $video_id = $is_vimeo? get_field('vimeo_video_id'): get_field('youtube_video_id');
    $poster = $is_vimeo? vpt_vimeo_thumbnail($video_id) : vpt_youtube_thumbnail($video_id, 'hqdefault'); 
    $src = $is_vimeo? vpt_vimeo_id_to_url($video_id) : vpt_youtube_id_to_url($video_id);
    
    // Video build for wp shortcode
    $shortcode_attrs = array(
        'src'=> $src,
        'height'=> 200,
        'width'=> 500,
        'poster'=> $poster,
        'loop'=> 'off',
        'autoplay'=> 'on',
        'preload'=> 'auto',
        'class'=> 'd-block',
    );


    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;
    $restrict_text = _( apply_filters('role_restrict_text', 'This page is restricted, you need to get ' . get_option('user_role_access'). ' role if you want to see the content') , 'video_page_template');

    // Security 
    $allowed_html = [
        'a'      => [
            'href'  => [],
            'title' => [],
        ],
        'br'     => [],
        'p'      => [],
        'strong' => [],
        'h1'     => [
            'class' => [],
            'id'    => []
        ],
        'h2'     => [
            'class' => [],
            'id'    => []
        ],
        'h3'     => [
            'class' => [],
            'id'    => []
        ],
        'h4'     => [
            'class' => [],
            'id'    => []
        ],
        'h5'     => [
            'class' => [],
            'id'    => []
        ],
        'h6'     => [
            'class' => [],
            'id'    => []
        ],
        'div'    => [
            'class' => [],
            'id'    => []
        ],
    ];
?>

<?php 
    if(get_option('restrict_one_session')): // Handle other session if restrict
        if(get_current_user_id()){
            $sessions  = WP_Session_Tokens::get_instance( get_current_user_id() );
            // get current session
            $token = wp_get_session_token();
            // destroy everything since we'll be logging in shortly
            $sessions->destroy_others( $token  );    
        }else{
            $unauthorized = true;
        }
    endif;
?>

<div class="<?= $wrapper_classes ?>">

    <?= wp_kses( $headline_open_wrapper, $allowed_html );?>
        <?= the_title();?>
    <?= wp_kses($headline_close_wrapper, $allowed_html);?>

    <?= wp_kses($after_wrapper_begins_html, $allowed_html);?>

    
    <?php if( !$unauthorized  && get_option('user_role_access') == 'None' || ( get_option('user_role_access') != 'None' && in_array( get_option('user_role_access'), $roles))){?>

        <?= the_content();?>

        <!-- Open video short code -->
        <?php echo wp_video_shortcode($shortcode_attrs); ?>
        <!-- End of Video -->

        <?= apply_filters( 'the_content', $after_video_content );?>

        <!-- Comments loop -->
        <?php if ( ( comments_open() || get_comments_number() ) && ! post_password_required() ) :?>

            <div class="<?= $comments_wrapper_class?>">

                <?php comments_template(); ?>

            </div>

        <?php endif; ?>

    <?php } else{?>
        <div class="error">
            <?= wp_kses($restrict_text, $allowed_html);?>
        </div>
    <?php }?>
        
    <?= wp_kses($before_wrapper_ends_html, $allowed_html);?>
</div>

<!-- End loop -->
<?php endwhile; endif;?>

<!-- Start footer -->
<?php get_footer();?>