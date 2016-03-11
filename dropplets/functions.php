<?php

/*-----------------------------------------------------------------------------------*/
/* Include 3rd Party Functions
/*-----------------------------------------------------------------------------------*/

include('./dropplets/includes/feedwriter.php');
include('./dropplets/includes/markdown.php');
include('./dropplets/includes/phpass.php');
include('./dropplets/includes/actions.php');

function var_log($var) {
	error_log(var_export($var));
}

/*-----------------------------------------------------------------------------------*/
/* Get All Posts Function
/*-----------------------------------------------------------------------------------*/

function get_all_posts($options = array()) {
    global $dropplets;

    if($handle = opendir(POSTS_DIR)) {

        $files = array();
        $filetimes = array();

        while (false !== ($entry = readdir($handle))) {
            if(substr(strrchr($entry,'.'),1)==ltrim(FILE_EXT, '.')) {

                // Define the post file.
                $fcontents = file(POSTS_DIR.$entry);

                // Define the post title.
                $post_title = Markdown($fcontents[0]);

                // Define the post author.
                $post_author = str_replace(array("\n", '-'), '', $fcontents[1]);

                // Define the post author Twitter account.
                $post_author_twitter = str_replace(array("\n", '- '), '', $fcontents[2]);

                // Define the published date.
                $post_date = str_replace('-', '', $fcontents[3]);

                // Define the post category.
                $post_category = str_replace(array("\n", '-'), '', $fcontents[4]);

                // Early return if we only want posts from a certain category
                if(isset($options["category"]) && $options["category"] != trim(strtolower($post_category))) {
                    continue;
                }

                // Define the post status.
                $post_status = str_replace(array("\n", '- '), '', $fcontents[5]);

                // Define the post intro.
                $post_intro = Markdown($fcontents[7]);

                // Define the post content
                $post_content = Markdown(join('', array_slice($fcontents, 6, sizeof($fcontents) -1)));

                // Pull everything together for the loop.
                $files[] = array('fname' => $entry, 'post_title' => $post_title, 'post_author' => $post_author, 'post_author_twitter' => $post_author_twitter, 'post_date' => $post_date, 'post_category' => $post_category, 'post_status' => $post_status, 'post_intro' => $post_intro, 'post_content' => $post_content);
                $post_dates[] = $post_date;
                $post_titles[] = $post_title;
                $post_authors[] = $post_author;
                $post_authors_twitter[] = $post_author_twitter;
                $post_categories[] = $post_category;
                $post_statuses[] = $post_status;
                $post_intros[] = $post_intro;
                $post_contents[] = $post_content;
            }
        }
        array_multisort($post_dates, SORT_DESC, $files);
        return $files;

    } else {
        return false;
    }
}

/*-----------------------------------------------------------------------------------*/
/* Get Posts for Selected Category
/*-----------------------------------------------------------------------------------*/

function get_posts_for_category($category) {
    $category = trim(strtolower($category));
    return get_all_posts(array("category" => $category));
}

/*-----------------------------------------------------------------------------------*/
/* Post Pagination
/*-----------------------------------------------------------------------------------*/

function get_pagination($page,$total) {

    $string = '';
    $string .= "<ul style=\"list-style:none; width:400px; margin:15px auto;\">";

    for ($i = 1; $i<=$total;$i++) {
        if ($i == $page) {
            $string .= "<li style='display: inline-block; margin:5px;' class=\"active\"><a class=\"button\" href='#'>".$i."</a></li>";
        } else {
            $string .=  "<li style='display: inline-block; margin:5px;'><a class=\"button\" href=\"?page=".$i."\">".$i."</a></li>";
        }
    }
    
    $string .= "</ul>";
    return $string;
}

/*-----------------------------------------------------------------------------------*/
/* If is Home (Could use "is_single", "is_category" as well.)
/*-----------------------------------------------------------------------------------*/

$homepage = BLOG_URL;

// Get the current page.    
$currentpage  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] : 'https://'.$_SERVER["SERVER_NAME"];
$currentpage .= $_SERVER["REQUEST_URI"];

// If is home.
$is_home = ($homepage==$currentpage);
define('IS_HOME', $is_home);

/*-----------------------------------------------------------------------------------*/
/* Get Profile Image (This Needs to be Cached)
/*-----------------------------------------------------------------------------------*/

function get_twitter_profile_img($username) {
    
    // Temporary fix for profile images.
    $image_url = 'http://dropplets.com/profiles/?id='.$username.'';
	return $image_url;
}

/*-----------------------------------------------------------------------------------*/
/* Include All Plugins in Plugins Directory
/*-----------------------------------------------------------------------------------*/

foreach(glob('./plugins/' . '*.php') as $plugin){
    include_once $plugin;
}

/*-----------------------------------------------------------------------------------*/
/* Dropplets Header
/*-----------------------------------------------------------------------------------*/

function get_header() { ?>
    <!-- RSS Feed Links -->
    <link rel="alternate" type="application/rss+xml" title="Subscribe using RSS" href="<?php echo BLOG_URL; ?>rss" />
    <link rel="alternate" type="application/atom+xml" title="Subscribe using Atom" href="<?php echo BLOG_URL; ?>atom" />

    <!-- User Header Injection -->
    <?php echo HEADER_INJECT; ?>
    
    <!-- Plugin Header Injection -->
    <?php action::run('dp_header'); ?>
<?php 

} 

/*-----------------------------------------------------------------------------------*/
/* Dropplets Footer
/*-----------------------------------------------------------------------------------*/

function get_footer() { ?>
    
    <?php if ( PAGINATION_ON_OFF !== "on" && IS_SINGLE !== true ) { ?>
    <!-- Post Pagination -->
    <script>
        var infinite = true;
        var next_page = 2;
        var loading = false;
        var no_more_posts = false;
        $(function() {
            function load_next_page() {
                $.ajax({
                    url: "index.php?page=" + next_page,
                    success: function (res) {
                        next_page++;
                        var result = $.parseHTML(res);
                        var articles = $(result).filter(function() {
                            return $(this).is('article');
                        });
                        if (articles.length < 2) {  //There's always one default article, so we should check if  < 2
                            no_more_posts = true;
                        }  else {
                            $('article').last().after(articles.slice(1));
                        }
                        loading = false;
                    }
                });
            }

            $(window).scroll(function() {
                var when_to_load = $(window).scrollTop() * 0.32;
                if (infinite && (loading != true && !no_more_posts) && $(window).scrollTop() + when_to_load > ($(document).height()- $(window).height() ) ) {
                    // Sometimes the scroll function may be called several times until the loading is set to true.
                    // So we need to set it as soon as possible
                    loading = true;
                    setTimeout(load_next_page,500);
                }
            });
        });
    </script>
    <?php } ?>
    
    <!-- User Footer Injection -->
    <?php echo FOOTER_INJECT; ?>
    
    <!-- Plugin Footer Injection -->
    <?php action::run('dp_footer'); ?>
<?php 

}
