<?php 
namespace EvmeManager\Events\admin;
class Calendar {  
     
    /**
     * Constructor
     */
    public function __construct(){     
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }
     
    /********************* PROPERTY ********************/  
    private $dayLabels = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
     
    private $currentYear=0;
     
    private $currentMonth=0;
     
    private $currentDay=0;
     
    private $currentDate=null;
     
    private $daysInMonth=0;
     
    private $naviHref= null;
     
   
        
    /**
    * print out the calendar
    */
    public function show() {
      $year = null;
      $month = null;
      if (null == $year && isset($_GET['year'])) {
        $year = $_GET['year'];
      } elseif (null == $year) {
        $year = date("Y", time());
      }
      if (null == $month && isset($_GET['month'])) {
        $month = $_GET['month'];
      } elseif (null == $month) {
        $month = date("m", time());
      }                 
         
        $this->currentYear=$year;
         
        $this->currentMonth=$month;
         
        $this->daysInMonth=$this->_daysInMonth($month,$year);  
         
        $content='<div id="calendar">'.
                        '<div class="box">'.
                        $this->_createNavi().
                        '</div>'.
                        '<div class="box-content">'.
                                '<ul class="evme_label">'.$this->_createLabels().'</ul>';   
                                $content.='<div class="clear"></div>';     
                                $content.='<ul class="dates">';    
                                 
                                $weeksInMonth = $this->_weeksInMonth($month,$year);
                                // Create weeks in a month
                                for( $i=0; $i<$weeksInMonth; $i++ ){
                                     
                                    //Create days in a week
                                    for($j=1;$j<=7;$j++){
                                        $content.=$this->_showDay($i*7+$j);
                                    }
                                }
                                 
                                $content.='</ul>';
                                 
                                $content.='<div class="clear"></div>';     
             
                        $content.='</div>';
                 
        $content.='</div>';
        return $content;   
    }
     
   
    /**
    * create the li element for ul
    */
    private function _showDay($cellNumber){
         
        if($this->currentDay==0){
             
            $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));
                     
            if(intval($cellNumber) == intval($firstDayOfTheWeek)){
                 
                $this->currentDay=1;
                 
            }
        }
         
        if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth) ){
             
            $this->currentDate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
             
            $cellContent = $this->currentDay;
             
            $this->currentDay++;   
             
        }else{
             
            $this->currentDate =null;
 
            $cellContent=null;
        }


        // Event details 
        $args = array(
            'post_type'      => 'events',
            'posts_per_page' => -1, 
            'meta_query'     => array(
                'relation'     => 'OR',
                array(
                    'key'      => '_event_date', 
                    'value'    => array( date( $this->currentYear.'-'.$this->currentMonth.'-01', strtotime( 'first day of this month' ) ), date( $this->currentYear.'-'.$this->currentMonth.'-t', strtotime( 'last day of this month' ) ) ),
                    'type'     => 'DATE',
                    'compare'  => 'BETWEEN'
                )
            )
        );
        $query = new \WP_Query( $args );

        $event_info = array();
        // $event_dates = array();
        // $event_array = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $e_date = get_post_meta( get_the_ID(), '_event_date', true );
                $event = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'link' => get_the_permalink(),
                    'description' => the_content(),
                    'date' => $e_date
                );
                $event_info[$e_date] = array();
                // array_push($event_dates, $e_date);
            }
            while ( $query->have_posts() ) {
                $query->the_post();

                $e_date = get_post_meta( get_the_ID(), '_event_date', true );
                $event = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'link' => get_the_permalink(),
                    'description' => the_content(),
                    'date' => $e_date
                );

                array_push($event_info[$e_date], $event);
            }
        }

        // echo '<pre>';
        // var_dump(date( '2024-06-01', strtotime( 'first day of this month' ) ));
        // var_dump(date( '2024-06-t', strtotime( 'last day of this month' ) ));
        // var_dump($event_info);
        // echo '</pre>';
        // exit();

        $event_cal = '';
        $event_cal .= '<li id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).
                ($cellContent==null?'mask':'').'">';

        // echo '<pre>';
        // var_dump($event_info);
        // echo '</pre>';
        // exit();
             
        foreach($event_info as $event_date_key => $event_data_value){

            // get the saved day count 
            $date = new \DateTime($event_date_key);
            $formatted_date = $date->format('d');
            $formatted_month = $date->format('m');
            $formatted_year = $date->format('Y');

            $date_current = $this->currentYear . '-' . $this->currentMonth . '-' . $cellContent;

            // check the current day 
            if($formatted_date == $cellContent && $this->currentMonth == $formatted_month && $this->currentYear == $formatted_year){

                $event_cal .= '<div class="evme_events_exist">';

                foreach ($event_data_value as $event_data) {


                    if ($event_data['date'] === $date_current) {

                        $event_cal .= '<a href="'.get_permalink($event_data['id']).'" class="evme_event" target="_blank">' . $event_data['title'] . '</a>';

                    }

                }


                $event_cal .= '</div>';

            }




        }
         
        $event_cal .= '<span>' . $cellContent . '</span>';
        $event_cal .= '</li>';

        return $event_cal;

    }
     
    /**
    * create navigation
    */
    private function _createNavi(){
         
        $nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
         
        $nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;
         
        $preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;
         
        $preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;
         
        return
            '<div class="header">'.
                '<a class="prev" href="'.$this->naviHref.'?page=evme_events_manager&month='.sprintf('%02d',$preMonth).'&year='.$preYear.'">' . __('Prev', 'events-exclusive') . '</a>'.
                    '<span class="title">'.date('M Y',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</span>'.
                '<a class="next" href="'.$this->naviHref.'?page=evme_events_manager&month='.sprintf("%02d", $nextMonth).'&year='.$nextYear.'">'.__('Next', 'events-exclusive').'</a>'.
            '</div>';
    }
         
    /**
    * create calendar week labels
    */
    private function _createLabels(){  
                 
        $content='';
         
        foreach($this->dayLabels as $index=>$label){
             
            $content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>';
 
        }
         
        return $content;
    }
     
     
     
    /**
    * calculate number of weeks in a particular month
    */
    private function _weeksInMonth($month=null,$year=null){
         
        if( null==($year) ) {
            $year =  date("Y",time()); 
        }
         
        if(null==($month)) {
            $month = date("m",time());
        }
         
        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month,$year);
         
        $numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);
         
        $monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));
         
        $monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));
         
        if($monthEndingDay<$monthStartDay){
             
            $numOfweeks++;
         
        }
         
        return $numOfweeks;
    }
 
    /**
    * calculate number of days in a particular month
    */
    private function _daysInMonth($month=null,$year=null){
         
        if(null==($year))
            $year =  date("Y",time()); 
 
        if(null==($month))
            $month = date("m",time());
             
        return date('t',strtotime($year.'-'.$month.'-01'));
    }
     
}