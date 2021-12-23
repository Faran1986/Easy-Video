<?php

add_action('wp_ajax_nopriv_inert_video', 'inert_video');
add_action('wp_ajax_inert_video', 'inert_video');

function inert_video()
{
    $API_key    = "AIzaSyBYlUw77iAcNGhiqHmOUzd_vASqPKCXuLM";
    $channelID  = "UCBi2mrWuNuyYy4gbM6fU18Q";
    $maxResults = 10;
    $videoList = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=' . $channelID . '&maxResults=' . $maxResults . '&key=' . $API_key . ''));
    //echo '<pre>', print_r($videoList), '</pre>';

    $vides_cat = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&regionCode=US&key=' . $API_key . ''));
    $taxonomyName = 'vcategory';
    foreach ($vides_cat->items as $item) {

        $cat_id =  wp_insert_term($item->snippet->title, $taxonomyName);

        if (is_wp_error($cat_id)) {
            // echo $cat_id->get_error_message();
        } else {
            $term_id = $cat_id['term_id'];
            update_term_meta($term_id, 'vedioCat_id', $item->id, true);
        }
    }

    foreach ($videoList->items as $item) {

        if (isset($item->id->videoId)) {
            // create post object
            global $user_ID;
            $catID = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $item->id->videoId . '&key=' . $API_key . ''));
            // echo '<pre>';
            // print_r($catID->items[0]->snippet->categoryId);
            // echo '</pre>';

            $categoryId = $catID->items[0]->snippet->categoryId;

            $content = "<div class='youtube-video'><iframe width='500' height='300' src='https://www.youtube.com/embed/" . $item->id->videoId . "' frameborder='0' allowfullscreen></iframe></div>";
            $videos = array(
                'post_title'  => __($item->snippet->title),
                'post_content' =>  $content,
                'post_status' => 'publish',
                'post_author' => $user_ID,
                'post_type'   => 'videos',
            );

            // insert the post into the database
            global $wpdb;
            $return = $wpdb->get_row("SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '" . $item->snippet->title . "' && post_status = 'publish' && post_type = 'videos' ", 'ARRAY_N');
            if (empty($return)) {
                $id =  wp_insert_post($videos);
                update_post_meta($id, 'video_id', $item->id->videoId);

                $catId = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}termmeta WHERE meta_value = '" . $categoryId  . "'", 'ARRAY_N');

                $taxonomy = 'vcategory';
                wp_set_object_terms($id, intval($catId[1]), $taxonomy);
            }
        }
    }
    die;
}
