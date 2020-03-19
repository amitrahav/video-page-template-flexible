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
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php // Some vars
    $wrapper_classes = sanitize_html_class(apply_filters('wrapper_classes', ''));

    $headline_open_wrapper = apply_filters('headline_open_wrapper', '<h2>');
    $headline_close_wrapper = apply_filters('headline_close_wrapper', '</h2>');

    $after_wrapper_begins_html = apply_filters('after_wrapper_begins_html', '');
    $after_video_content = get_field('content_after_video');
    $before_wrapper_ends_html = apply_filters('before_wrapper_ends_html', '');

    // $video_id = esc_url('nX4vzKH4les');
    $is_vimeo   =  get_field('video_provider');
    $video_id = $is_vimeo? get_field('vimeo_video_id'): get_field('youtube_video_id');
    $poster = $is_vimeo? vpt_vimeo_thumbnail($video_id) :vpt_youtube_thumbnail($video_id, ''); 
    $src = $is_vimeo? vpt_vimeo_id_to_url($video_id) :vpt_youtube_id_to_url($video_id);

    // Video build for wp shortcode
    $shortcode_attrs = array(
        'src'=> $src,
        'height'=>'',
        'width'=>'',
        'poster'=> $poster,
        'loop'=> 0,
        'autoplay'=> 1,
        'preload'=> 1,
        'class'=> '',
    );


    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;
    $restrict_text = _( apply_filters('role_restrict_text', 'This page is restricted, you need to get' . get_option('user_role_access'). ' if you want to see the content') , 'video_page_template');
?>

<div class="<?= $wrapper_classes ?>">

    <?= wp_kses_data($headline_open_wrapper);?>
        <?= the_title();?>
    <?= wp_kses_data($headline_close_wrapper);?>

    <?= wp_kses_data($after_wrapper_begins_html);?>

    
    <?php if(get_option('user_role_access') !== 'None' && in_array(get_option('user_role_access'), $roles)){?>
        <?php the_content();?>
        <!-- Open video short code -->
        <?= wp_video_shortcode($shortcode_attrs) ?>
        <!-- End of Video -->

        <?= esc_html($after_video_content);?>
    <?php } else{?>
        <div class="error">
            <?= esc_html($restrict_text);?>
        </div>
    <?php }?>
        
    <?= wp_kses_data($before_wrapper_ends_html);?>
</div>


<?php get_footer();?>