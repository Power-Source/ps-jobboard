<?php

/**
 * @author:DerN3rd
 */
class JE_Router
{
    public function __construct()
    {
        add_filter('comments_open', array(&$this, 'disable_jobboard_comments'), 99, 2);
        add_filter('pings_open', array(&$this, 'disable_jobboard_comments'), 99, 2);
        add_filter('get_comments_number', array(&$this, 'clear_jobboard_comment_count'), 99, 2);
        add_filter('comments_number', array(&$this, 'clear_jobboard_comment_text'), 99, 2);
        add_filter('body_class', array(&$this, 'jobboard_body_class'));
        add_action('wp_head', array(&$this, 'hide_jobboard_comment_elements'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_jobboard_assets'), 100);

        if (apply_filters('jbp_use_core_front_request', true)) {
            add_action('template_include', array(&$this, 'determine_page'));
            add_filter('the_content', array(&$this, 'je_single_content'));
            add_filter('the_title', array(&$this, 'je_single_title'));
            add_filter('get_edit_post_link', array(&$this, 'hide_edit_post_link'));
        }
    }

    function enqueue_jobboard_assets()
    {
        if (is_singular('jbp_job')) {
            je()->load_script('buttons');
            je()->load_script('job');
        } elseif (is_post_type_archive('jbp_job') || is_tax('jbp_category')) {
            je()->load_script('buttons');
            je()->load_script('jobs');
        } elseif (is_singular('jbp_pro')) {
            je()->load_script('buttons');
            je()->load_script('expert');
        } elseif (is_post_type_archive('jbp_pro') || is_tax('jbp_skills_tag')) {
            je()->load_script('buttons');
            je()->load_script('experts');
        } elseif (is_page()) {
            $post = get_post();
            if (!$post || !$this->is_jobboard_page($post->ID)) {
                return;
            }

            je()->load_script('buttons');

            $scenarios = array(
                'landing' => array('jbp-landing-page', 'jbp-profile-panel'),
                'jobs' => array('jbp-job-archive-page', 'jbp-my-job-page'),
                'experts' => array('jbp-expert-archive-page', 'jbp-my-expert-page'),
                'job-form' => array('jbp-job-update-page'),
                'expert-form' => array('jbp-expert-update-page'),
                'contact' => array('jbp-job-contact-page', 'jbp-expert-contact-page'),
                'job' => array('jbp-job-single-page'),
                'expert' => array('jbp-job-pro-page'),
            );

            foreach ($scenarios as $scenario => $shortcodes) {
                foreach ($shortcodes as $shortcode) {
                    if (has_shortcode($post->post_content, $shortcode)) {
                        je()->load_script($scenario);
                        break;
                    }
                }
            }
        }
    }

    function is_jobboard_page($post_id = 0)
    {
        $post = get_post($post_id ?: get_the_ID());
        if (!$post) {
            return false;
        }

        if (in_array($post->post_type, array('jbp_job', 'jbp_pro'), true)) {
            return true;
        }

        if (JE_Page_Factory::is_core_page($post->ID)) {
            return true;
        }

        $shortcodes = array(
            'jbp-landing-page',
            'jbp-job-update-page',
            'jbp-job-archive-page',
            'jbp-job-contact-page',
            'jbp-my-job-page',
            'jbp-expert-update-page',
            'jbp-expert-archive-page',
            'jbp-expert-contact-page',
            'jbp-my-expert-page',
            'jbp-job-single-page',
            'jbp-job-pro-page',
            'jbp-profile-panel'
        );

        foreach ($shortcodes as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                return true;
            }
        }

        return false;
    }

    function disable_jobboard_comments($open, $post_id = 0)
    {
        return $this->is_jobboard_page($post_id) ? false : $open;
    }

    function clear_jobboard_comment_count($count, $post_id = 0)
    {
        return $this->is_jobboard_page($post_id) ? 0 : $count;
    }

    function clear_jobboard_comment_text($output, $number)
    {
        return $this->is_jobboard_page() ? '' : $output;
    }

    function jobboard_body_class($classes)
    {
        if ($this->is_jobboard_page()) {
            $classes[] = 'je-jobboard-page';
        }

        return $classes;
    }

    function hide_jobboard_comment_elements()
    {
        if (!$this->is_jobboard_page()) {
            return;
        }
        ?>
        <style id="je-jobboard-comments">
            .je-jobboard-page .upostdata-part.comment_count,
            .je-jobboard-page .uposts-part.comment_count,
            .je-jobboard-page .upost-data-object-comments,
            .je-jobboard-page .comments-area,
            .je-jobboard-page #comments {
                display: none !important;
            }
        </style>
        <?php
    }

    function je_single_content($content)
    {
        if (in_the_loop() && is_main_query()) {
            if (is_singular('jbp_job') && !JE_Page_Factory::is_core_page(get_the_ID()) && !is_404()) {
                return do_shortcode('[jbp-job-single-page]');
            } elseif (is_singular('jbp_pro') && !JE_Page_Factory::is_core_page(get_the_ID()) && !is_404()) {
                return do_shortcode('[jbp-job-pro-page]');
            }
        }
        return $content;
    }

    function je_single_title($title)
    {
        if (in_the_loop()) {
            $shortcodes = apply_filters('je_buttons_on_single_page', '[jbp-job-browse-btn][jbp-expert-browse-btn][jbp-job-post-btn][jbp-expert-post-btn][jbp-my-job-btn][jbp-expert-profile-btn]');
            if (is_tax('jbp_category')) {
                $term = get_term_by('slug', get_query_var('jbp_category'), 'jbp_category');

                return __('Job Kategorie: ', 'psjb') . ' ' . $term->name;
            } elseif (is_singular('jbp_job') && in_the_loop() && !JE_Page_Factory::is_core_page(get_the_ID())) {
                global $wp_query;
                if ($wp_query->is_main_query()) {
                    $title = do_shortcode('<p style="text-align: center">' . $shortcodes . '</p>') . esc_html($title);
                    remove_filter('the_title', array(&$this, 'je_single_title'));
                }
            } elseif (is_singular('jbp_pro') && in_the_loop() && !JE_Page_Factory::is_core_page(get_the_ID())) {
                global $wp_query;
                if ($wp_query->is_main_query()) {
                    $title = do_shortcode('<p style="text-align: center">' . $shortcodes . '</p>');
                    remove_filter('the_title', array(&$this, 'je_single_title'));
                }
            }
        }
        return $title;
    }

    function hide_edit_post_link($link)
    {
        global $post;
        if ($post->post_type == 'jbp_job' || $post->post_type == 'jbp_pro') {
            return null;
        }
        return $link;
    }

    function determine_page($template)
    {
        global $wp_query;
        //this is for jobs section
        if (get_query_var('post_type') == 'jbp_job' && !is_404()) {
            global $wp_query;
            $template = array('single-jbp_job.php', 'page.php', 'index.php');
            if (is_archive('jbp_job')) {
                $vpost = get_post(je()->pages->page(JE_Page_Factory::JOB_LISTING));
                $wp_query->posts = array($vpost);
                $wp_query->post_count = 1;
                $template = array_merge(array($vpost->post_name . '-page.php'), $template);
            }
            $template = locate_template($template);
        }
        //yah, experts time
        if (get_query_var('post_type') == 'jbp_pro') {
            $template = locate_template(array('page.php', 'index.php'));
            $template = array('single-jbp_pro.php', 'page.php', 'index.php');
            if (is_archive('jbp_pro')) {
                $vpost = get_post(je()->pages->page(JE_Page_Factory::EXPERT_LISTING));
                global $wp_query;
                $wp_query->posts = array($vpost);
                $wp_query->post_count = 1;
                $template = array_merge(array($vpost->post_name . '-page.php'), $template);
            }
            $template = locate_template($template);
        }

        if (is_tax(array('jbp_category', 'jbp_skills_tag'))) {
            global $wp_query;
            $template = array('page.php', 'index.php');
            if (is_archive('jbp_job')) {
                $vpost = get_post(je()->pages->page(JE_Page_Factory::JOB_LISTING));
                $wp_query->posts = array($vpost);
                $wp_query->post_count = 1;
                $template = array_merge(array($vpost->post_name . '-page.php'), $template);

            }
            $template = locate_template($template);
        }

        return $template;
    }
}