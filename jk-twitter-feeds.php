<?php
/**
 * Plugin Name: JK Twitter Feeds
 * Description: Twitter Feeds.
 * Version: 1.0.0
 * Author: Jay Krishnan G
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!defined('JK_TWITTER_URL'))
    define('JK_TWITTER_URL', plugin_dir_url(__FILE__));

function autoload_jk_twitter_scripts()
{
    wp_enqueue_script( 'jk_twitter_script', JK_TWITTER_URL . 'assets/js/jk_twitter.js', array( 'jquery' ));
    wp_enqueue_style('jk_twitter-styles', JK_TWITTER_URL . 'assets/css/jk_twitter.css', array(), '1.0', 'all');
    wp_localize_script( 'jk_twitter_script', 'jkTwitterAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}

/**
 * Plugin Activation
 */
function jk_twitter_activation()
{
    
}

register_activation_hook(__FILE__, 'jk_twitter_activation');

/**
 * Plugin Deactivation
 */
function jk_twitter_deactivation()
{
    delete_option('jk_twitter_settings');
}

register_deactivation_hook(__FILE__, 'jk_twitter_deactivation');

/**
 * Add Admin Menu
 */
function jk_twitter_settings()
{
    add_menu_page('Twitter Feeds', 'Twitter Feeds', 'administrator', 'jk_twitter_settings', 'jk_twitter_show_settings');
}

add_action('admin_menu', 'jk_twitter_settings');

/**
 * Admin Page
 */
function jk_twitter_show_settings()
{
    $message = "";
    if($_POST)
    {
        $jk_twitter_settings = array();

        if($_POST['oauth_access_token'])
            $jk_twitter_settings['oauth_access_token'] = $_POST['oauth_access_token'];
        if($_POST['oauth_access_token_secret'])
            $jk_twitter_settings['oauth_access_token_secret'] = $_POST['oauth_access_token_secret'];
        if($_POST['consumer_key'])
            $jk_twitter_settings['consumer_key'] = $_POST['consumer_key'];
        if($_POST['consumer_secret'])
            $jk_twitter_settings['consumer_secret'] = $_POST['consumer_secret'];
        if($_POST['twitter_page'])
            $jk_twitter_settings['twitter_page'] = $_POST['twitter_page'];
        if($_POST['twitter_count'])
            $jk_twitter_settings['twitter_count'] = $_POST['twitter_count'];


        if(!empty($jk_twitter_settings))
        {
            $jk_twitter_settings = base64_encode(serialize($jk_twitter_settings));
            $updated = update_option('jk_twitter_settings', $jk_twitter_settings);
            $message .= "<div class='updated fade'><p><strong>Twitter feeds settings saved.</strong></p></div>";
        }
    }
    $jk_twitter_settings = unserialize(base64_decode(get_option('jk_twitter_settings')));

    $oauth_access_token =  isset($jk_twitter_settings["oauth_access_token"]) ? $jk_twitter_settings["oauth_access_token"] : "";
    $oauth_access_token_secret =  isset($jk_twitter_settings["oauth_access_token_secret"]) ? $jk_twitter_settings["oauth_access_token_secret"] : "";
    $consumer_key =  isset($jk_twitter_settings["consumer_key"]) ? $jk_twitter_settings["consumer_key"] : "";
    $consumer_secret =  isset($jk_twitter_settings["consumer_secret"]) ? $jk_twitter_settings["consumer_secret"] : "";
    $twitter_page  =  isset($jk_twitter_settings["twitter_page"]) ? $jk_twitter_settings["twitter_page"] : "";
    $twitter_count  =  isset($jk_twitter_settings["twitter_count"]) ? $jk_twitter_settings["twitter_count"] : "";
    ?>
    <style>
    .inner_wrapper {
        float: left;
        width: 98%;
        margin: 1% 1%;
    }
    </style>
    </pre>
    <div class="wrap">
        <form action="" method="post" name="options">
        <h3>Twitter Feeds Settings</h3>
        <?php
            echo $message;
        ?>
        <div class="inner_wrapper">
            <table class="form-table wp-list-table widefat striped" width="100%" cellpadding="10" size="40" id="twitter_feeds_table">
                <tbody>
                <tr>
                    <td scope="row" align="left" width="20%">
                        <label>Oauth Access Token:</label>
                    </td>
                    <td>
                        <input type="text" name="oauth_access_token" id="oauth_access_token" size="40" value="<?php echo $oauth_access_token; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td scope="row" align="left" width="20%">
                        <label>Oauth Access Token Secret:</label>
                    </td>
                    <td>
                        <input type="text" name="oauth_access_token_secret" id="oauth_access_token_secret"  size="40" value="<?php echo $oauth_access_token_secret; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td scope="row" align="left" width="20%">
                        <label>Consumer Key:</label>
                    </td>
                    <td>
                        <input type="text" name="consumer_key" id="consumer_key"  size="40" value="<?php echo $consumer_key; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td scope="row" align="left" width="20%">
                        <label>Consumer Key Secret:</label>
                    </td>
                    <td>
                        <input type="text" name="consumer_secret" id="consumer_secret" size="40" value="<?php echo $consumer_secret; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td scope="row" align="left" width="20%">
                        <label>Twitter Page</label>
                    </td>
                    <td>
                        <input type="text" name="twitter_page" id="twitter_page" size="40" value="<?php echo $twitter_page; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td scope="row" align="left" width="20%">
                        <label>Twitter Feed Count</label>
                    </td>
                    <td>
                        <input type="number" name="twitter_count" id="twitter_count" size="5" min="1" value="<?php echo $twitter_count; ?>"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="inner_wrapper">
            <input type="submit" name="Submit" value="Update" class="button-primary"/>
        </div>
        </form>
    </div>
    <pre>
    <?php
}

/**
 * Get Twitter Feeds
 */
function get_jk_twitter_feeds()
{
    $social_media = array();

    require_once(  __DIR__.'/twitter.php' );

    $twitter_settings = unserialize(base64_decode(get_option('jk_twitter_settings')));

    if (! empty ( $twitter_settings ))
    {
        $oauth_access_token = isset ( $twitter_settings ["oauth_access_token"] ) ? $twitter_settings ["oauth_access_token"] : "";
        $oauth_access_token_secret = isset ( $twitter_settings ["oauth_access_token_secret"] ) ? $twitter_settings ["oauth_access_token_secret"] : "";
        $consumer_key = isset ( $twitter_settings ["consumer_key"] ) ? $twitter_settings ["consumer_key"] : "";
        $consumer_secret = isset ( $twitter_settings ["consumer_secret"] ) ? $twitter_settings ["consumer_secret"] : "";
        $twitter_page = isset ( $twitter_settings ["twitter_page"] ) ? $twitter_settings ["twitter_page"] : "";
        $twitter_count = isset ( $twitter_settings ["twitter_count"] ) ? $twitter_settings ["twitter_count"] : "";

        $twitter_page = preg_replace('{/$}', '', $twitter_page);
        $twitter_page = explode('/', $twitter_page);
        $twitter_page = trim(end($twitter_page));

        if (! defined ( 'TWEET_LIMIT' ))
            define ( 'TWEET_LIMIT', $twitter_count );
        if (! defined ( 'CONSUMER_KEY' ))
            define ( 'CONSUMER_KEY', $consumer_key );
        if (! defined ( 'CONSUMER_SECRET' ))
            define ( 'CONSUMER_SECRET', $consumer_secret );
        if (! defined ( 'ACCESS_TOKEN' ))
            define ( 'ACCESS_TOKEN', $oauth_access_token );
        if (! defined ( 'ACCESS_TOKEN_SECRET' ))
            define ( 'ACCESS_TOKEN_SECRET', $oauth_access_token_secret );

        $twitter = new TwitterOAuth ( CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET );

        $twitter->ssl_verifypeer = true;

        $tweets = $twitter->get ( 'statuses/user_timeline', array (
                'screen_name' => $twitter_page,
                'exclude_replies' => 'true',
                'include_rts' => 'true',
                'count' => TWEET_LIMIT
        ) );

        $source = 'twitter';

        if (! empty ( $tweets )) {
            $twitter_data = array ();

            foreach ( $tweets as $data ) {
                $twitter_id = isset ( $data ['id_str'] ) ? $data ['id_str'] : 0;

                if ($twitter_id) {
                    $twitter_html = $twitter->get ( 'statuses/oembed', array (
                            'id' => $twitter_id
                    ) );

                    $twitter_data ['twitter_id'] = $twitter_id;
                    $created_at = isset ( $data ['created_at'] ) ? $data ['created_at'] : '';
                    $created_at = strtotime ( $created_at );
                    $created_time = date ( 'Y-m-d H:i:s', $created_at );
                    $twitter_data ['created_time'] = $created_time;

                    $social_media [] ['type'] = 'twitter';
                    $social_media [] ['id'] = $twitter_id;
                    $social_media [] ['page'] = $twitter_page;
                    $social_media [] ['html'] = $twitter_html ['html'];
                    $social_media [] ['data'] = $twitter_data;
                    $social_media [] ['created_time'] = $created_time;
                    $social_media [] ['timestamp'] = $created_at;
                }
            }
        }

        foreach ($social_media as $key => $row) {
            $timestamp[$key]  = isset($row['timestamp']) ? $row['timestamp'] : '';
        }

        array_multisort($timestamp, SORT_DESC, $social_media);

        echo json_encode($social_media);
        exit;
    }
}

add_action('wp_ajax_get_jk_twitter_feeds', 'get_jk_twitter_feeds');
add_action('wp_ajax_nopriv_get_jk_twitter_feeds', 'get_jk_twitter_feeds' );

/**
 * Display Twitter Feeds
 */
function show_jk_twitter_feeds()
{
    autoload_jk_twitter_scripts();
    ?>
    <div id="social-loader">
        <img src="<?php echo JK_TWITTER_URL.'assets/images/loader.gif'?>" />
    </div>
    <div id="social-media-container">
    </div>
    <?php
}
/**
 * Widget Class For JK Twitter Feeds.
 *
 */
class JK_Twitter_Widget extends WP_Widget 
{
    /**
     * constructor
     */
    function __construct() {
        parent::__construct(
                'jk_twitter_widget',
                __( 'JK Twitter Widget' , 'jk_twitter'),
                array( 'description' => __( 'Display Twitter Feeds' , 'jk_twitter') )
                );
        
        if ( is_active_widget( false, false, $this->id_base ) ) {
            add_action( 'wp_head', array( $this, 'css' ) );
        }
    }
    /**
     * Widget
     */    
    function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $args['before_widget'];
        echo $args['before_title'] .$title.  $args['after_title'];
        echo show_jk_twitter_feeds();
        echo $args['after_widget'];
    }
    /**
     * Widget Title Update
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }
    /**
     * Widget Title Form
     */
    function form( $instance ) {
        $defaults = array( 'title' => 'JK Twitter Feeds' );
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
            <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>"
            name="<?php echo $this->get_field_name( 'title' ); ?>"
            value="<?php echo $instance['title']; ?>" />
            </p>
       <?php
   }
}

/**
 * Register Widget
 */
function jk_twitter_register_widgets() {
    register_widget( 'JK_Twitter_Widget' );
}
add_action( 'widgets_init', 'jk_twitter_register_widgets' );