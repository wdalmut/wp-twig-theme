<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/TwigProxy.php';

add_filter("home_template", function(){ return "home.twig"; });
add_filter("single_template", function(){ return "single.twig"; });

/**
 *
 */
add_action("template_include", function ($filename) {
    $wp = new TwigProxy();

    $loader = new Twig_Loader_Filesystem(dirname(__FILE__));
    $twig = new Twig_Environment($loader, array(
                'cache' => false
            ));
    $twig->addFilter("more", new Twig_Filter_Function("twig_more"));
    $template = $twig->loadTemplate($filename);

    $data = get_template_data($filename);

    $template->display(
        array_merge(array('wp' => $wp), get_template_data($filename))
    );
});

function twig_more($string)
{
    return (strpos($string, "<!--more-->")) ?
        substr($string, 0, strpos($string, "<!--more-->")) :
        $string;
}

function get_template_data($filename)
{
    $data = array();
    switch ($filename) {
        case 'home.twig':
            $data['posts'] = get_posts();
            break;
        case 'single.twig':
            global $post;
            $data['post'] = $post;
            break;
    }

    return $data;
}

// Sidebar register
if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Sidebar Widgets',
        'id'   => 'sidebar-widgets',
        'description'   => 'These are widgets for the sidebar.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>'
    ));
}
