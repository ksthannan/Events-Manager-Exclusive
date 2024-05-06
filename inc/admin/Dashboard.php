<?php 
namespace EvmeManager\Events\admin;
/**
 * Class acedemy dashboard
 */
class Dashboard{
   

    public function __construct() {
       
        //create an admin page
        add_action('admin_menu', [$this, 'add_admin_menu']);

    }

    // Add mmenu page
    public function add_admin_menu() {
       add_menu_page(__('Events Manager Exclusive', 'events-exclusive'), __('Events Manager Exclusive', 'events-exclusive'), 'manage_options', 'evme_events_manager', [$this, 'admin_page'], 'dashicons-admin-users');
    }

    // admin page callback
    public function admin_page() {

        // dashboard content 
        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">'.__('Events Manager Exclusive', 'events-exclusive').'</h1>';
        echo '<a href="'.admin_url('edit.php?post_type=events').'" class="page-title-action">'.__('Add New Event', 'events-exclusive').'</a>';

        echo '<div class="evme_cal_wrapper">';
        // Event calendar 
        // $this->event_calendar_view();
        $calendar = new Calendar();
        echo $calendar->show();

        echo '</div>';

    }

    // Event calendar render 
    public function event_calendar_view(){

        // $current_month = date( 'm' );
        // $current_year = date( 'Y' );
        // $args = array(
        //     'post_type'      => 'events',
        //     'posts_per_page' => -1, 
        //     'meta_query'     => array(
        //         'relation'     => 'OR',
        //         array(
        //             'key'      => '_event_date', 
        //             'value'    => array( date( 'Y-m-01', strtotime( 'first day of this month' ) ), date( 'Y-m-t', strtotime( 'last day of this month' ) ) ),
        //             'type'     => 'DATE',
        //             'compare'  => 'BETWEEN'
        //         )
        //     )
        // );
        // $query = new \WP_Query( $args );
        // $event_info = array();
        // $event_dates = array();
        // if ( $query->have_posts() ) {
        //     while ( $query->have_posts() ) {
        //         $query->the_post();

        //         $e_date = get_post_meta( get_the_ID(), '_event_date', true );
        //         $event = array(
        //             'title' => get_the_title(),
        //             'description' => the_content(),
        //             'date' => $e_date,
        //             'id' => get_the_ID()
        //         );
        //         array_push($event_info, $event);
        //         array_push($event_dates, $e_date);

        //     }
        // }

        // // echo '<pre>';
        // // print_r($event_info);
        // // echo '</pre>';


        // // Get current month and year
        // $month = date('n');
        // $year = date('Y');

        // // Get the number of days in the current month
        // $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // // Get the first day of the month
        // $firstDay = date('N', mktime(0, 0, 0, $month, 1, $year));

        // // Create an array of month names
        // $monthNames = [
        //     1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
        //     7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        // ];

        // // Create an array of day names
        // $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        // // Start table
        // echo "<table class='evme_table'>";
        // echo "<caption>" . $monthNames[$month] . " " . $year . "</caption>";

        // // Print the day names
        // echo "<tr>";
        // foreach ($dayNames as $day) {
        //     echo "<th>$day</th>";
        // }
        // echo "</tr>";

        // // Initialize day counter
        // $dayCounter = 1;

        // // Loop through each week (rows)
        // for ($i = 0; $i < 6; $i++) {
        //     echo "<tr>";

        //     // Loop through each day of the week (columns)
        //     for ($j = 0; $j < 7; $j++) {
        //         // Check if the current cell should be blank
        //         if ($i == 0 && $j < $firstDay - 1) {
        //             echo "<td>&nbsp;</td>";
        //         } elseif ($dayCounter > $numDays) {
        //             // If all days of the month have been printed, exit loop
        //             break;
        //         } else {

        //             echo '<td>';

        //             foreach($event_dates as $event_date){

        //                 // get the saved day count 
        //                 $date = new \DateTime($event_date);
        //                 $formatted_date = $date->format('d');

        //                 // check the current day 
        //                 if($formatted_date == $dayCounter){

        //                     echo '<div class="evme_events_exist">';

        //                     foreach ($event_info as $event_data) {

        //                         if ($event_data['date'] === $event_date) {

        //                             echo '<a href="'.get_permalink($event_data['id']).'" class="evme_event" target="_blank">' . $event_data['title'] . '</a>';

        //                         }

        //                     }


        //                     echo '</div>';

        //                 }
        //             }


        //             // Print the day
        //             echo '<span>'.$dayCounter.'</span>';
        //             echo '</td>';


        //             $dayCounter++;
        //         }
        //     }

        //     echo "</tr>";

        //     // If all days of the month have been printed, exit loop
        //     if ($dayCounter > $numDays) {
        //         break;
        //     }
        // }

        // // End table
        // echo "</table>";




    }


    
        

    

}


