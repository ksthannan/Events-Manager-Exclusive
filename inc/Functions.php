<?php 
namespace EvmeManager\Events;
/**
 * Class Functions 
 */

 class Functions{
    // define properties 
    private $dbv = '1.3';
    public function __construct()
    {
        // init functions 
        add_action('init', [$this, 'init']);

        // Event Dashboard 
        new admin\Dashboard();

    }

    public function init(){

        // register activation and deactivation hook 
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Event post type 
        $this->event_post_type();

        // Add meta boxes
        add_action('add_meta_boxes', [$this, 'add_event_date_meta']);
        add_action('save_post', [$this, 'save_event_date_meta']);

        // Event date column 
        add_filter('manage_events_posts_columns', [$this, 'add_custom_column_to_events_admin']);
        add_action('manage_events_posts_custom_column', [$this, 'populate_custom_column_in_events_admin'], 10, 2);

        // Event appearrance 
        add_filter('the_content', [$this, 'event_date_apperrance']);

    }

    // Run function on deactivattion
    function deactivate() {
        
    }

    /**
     * activation update options
     */
    public function activate()
    {

        // update version
        $this -> add_version();

        // update database version 
        $dbv = get_option('dbv');
        if ($dbv != $this->dbv) {
            update_option('dbv', $this->dbv);
        }

    }

    // add version 
    public function add_version()
    {
        $installed = get_option('evme_events_manager_installed');

        if (!$installed) {
            update_option('evme_events_manager_installed', time());
        }

        update_option('evme_version', EVME_VER);

    }

    // Event post type 
    public function event_post_type()
    {
        $labels = array(
            'name'                  => _x( 'Events', 'Post Type General Name', 'events-exclusive' ),
            'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'events-exclusive' ),
            'menu_name'             => __( 'Events', 'events-exclusive' ),
            'name_admin_bar'        => __( 'Event', 'events-exclusive' ),
            'archives'              => __( 'Event Archives', 'events-exclusive' ),
            'attributes'            => __( 'Event Attributes', 'events-exclusive' ),
            'parent_item_colon'     => __( 'Parent Event:', 'events-exclusive' ),
            'all_items'             => __( 'All Events', 'events-exclusive' ),
            'add_new_item'          => __( 'Add New Event', 'events-exclusive' ),
            'add_new'               => __( 'Add New', 'events-exclusive' ),
            'new_item'              => __( 'New Event', 'events-exclusive' ),
            'edit_item'             => __( 'Edit Event', 'events-exclusive' ),
            'update_item'           => __( 'Update Event', 'events-exclusive' ),
            'view_item'             => __( 'View Event', 'events-exclusive' ),
            'view_items'            => __( 'View Events', 'events-exclusive' ),
            'search_items'          => __( 'Search Event', 'events-exclusive' ),
            'not_found'             => __( 'Not found', 'events-exclusive' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'events-exclusive' ),
            'featured_image'        => __( 'Featured Image', 'events-exclusive' ),
            'set_featured_image'    => __( 'Set featured image', 'events-exclusive' ),
            'remove_featured_image' => __( 'Remove featured image', 'events-exclusive' ),
            'use_featured_image'    => __( 'Use as featured image', 'events-exclusive' ),
            'insert_into_item'      => __( 'Insert into event', 'events-exclusive' ),
            'uploaded_to_this_item' => __( 'Uploaded to this event', 'events-exclusive' ),
            'items_list'            => __( 'Events list', 'events-exclusive' ),
            'items_list_navigation' => __( 'Events list navigation', 'events-exclusive' ),
            'filter_items_list'     => __( 'Filter events list', 'events-exclusive' ),
        );
        $args = array(
            'label'                 => __( 'Event', 'events-exclusive' ),
            'description'           => __( 'Events', 'events-exclusive' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'menu_icon'             => 'dashicons-calendar-alt',
            'show_in_rest' => true
        );
        register_post_type( 'events', $args );
    }

    // Add custom meta box for event date
    public function add_event_date_meta() {
        add_meta_box(
            'event_date', 
            'Event Date', 
            [$this, 'display_event_date'],
            'events', 
            'normal', 
            'default'
        );
    }

    // Display meta box content
    public function display_event_date($post) {
        // Retrieve current event date
        $event_date = get_post_meta($post->ID, '_event_date', true);
        var_dump($event_date);

        // Display input field for event date
        ?>
        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($event_date); ?>">
        <?php
    }

    // Save event date when the post is saved
    function save_event_date_meta($post_id) {
        // Check if nonce is set
        // if (!isset($_POST['event_date_meta_box_nonce'])) {
        //     return;
        // }

        // Verify nonce
        // if (!wp_verify_nonce($_POST['event_date_meta_box_nonce'], 'event_date_meta_box')) {
        //     return;
        // }

        // Check if this is an autosave
        // if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        //     return;
        // }

        // Check user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update event date meta field
        if (isset($_POST['event_date'])) {
            update_post_meta($post_id, '_event_date', sanitize_text_field($_POST['event_date']));
        }
    }

    // Add custom column to the events admin page
    function add_custom_column_to_events_admin($columns) {
        // Add a new column for event date
        $columns['_event_date'] = __('Event Date', 'events-exclusive');
        return $columns;
    }

    // Populate custom column with the value of event_date meta field
    function populate_custom_column_in_events_admin($column, $post_id) {
        // Check if the column is the one we added
        if ($column == '_event_date') {
            // Get the event date from the meta field
            $event_date = get_post_meta($post_id, '_event_date', true);

            // Display the event date in the column
            echo $event_date;
        }
    }


    // Event date show on frontend post 
    public function event_date_apperrance($content){
        ob_start();
        $post_type = get_post_type(get_the_ID());
        if ($post_type == 'events') {
            $date = get_post_meta(get_the_ID(), '_event_date', true);
            $date = strtotime($date);
            $formatted_date = date("d F, Y", $date);
            ?>
                <div class="evme_appearrance_wrap">
                    <div class="evme_info">
                        <p><strong><?php _e('Event Date', 'events-exclusive');?></strong>: <?php echo esc_html($formatted_date);?></p>

                    </div>
                </div>
            <?php 
        }
        $evme_content = ob_get_clean();
        $content = $evme_content . $content;
        return $content;
    }

 }
