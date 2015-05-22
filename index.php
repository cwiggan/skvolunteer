<?php
/*
Plugin Name: Skip Volunteer Plugin
Plugin URI: http://cwiggan.com/
Description: Contains all the core functionality for skip volunteer plugin
Author: Carlita
Author URI: http://cwiggan.com/
Version: 1.0
*/


if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class SkipVolunteer {
 /**
     * Construct the plugin object
     */
    public function __construct()
    {
 		// when the plugin is activated
		//add_action( 'init', array( $this, 'add_hours') );
        add_action('wp_head', array( $this, 'skip_js'));
        add_action("wp_ajax_add_hours", array($this, 'add_hours'));
        add_action("wp_ajax_nopriv_add_hours", array($this, 'add_hours'));
		add_action('admin_menu', array($this,'skip_volunteer_menu'));
		add_shortcode( 'skip_form', array( $this, 'form_shortcode' ) );
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

    } // END public function __construct

    /**
     * Activate the plugin
     */
    public static function activate()
    {
		global $wpdb;
		$table_name = $wpdb->prefix . 'skip_volunteer';
	    $sql = "CREATE TABLE $table_name (
	        id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
	        project_date DATE NOT NULL,
	        club varchar(150) NOT NULL,
	        project varchar(150) NOT NULL,
	        description longtext NOT NULL,
	        adult int(9) NOT NULL,
	        children int(9) NOT NULL,
	        families int(9) NOT NULL,
	        hours int(9) NOT NULL,
	        PRIMARY KEY  (id)
	        );";
	 
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql );
    } // END public static function activate

    public function skip_volunteer_menu()
    {
    	 add_menu_page( 'Skip Volunteer Hours', 'Skip Volunteer', 'manage_options', 'skipvolunteer', array($this,'skip_admin_init') );
    }

    public static function skip_js(){
        echo '<script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery( "#hoursv" ).on( "submit", function( event ){
          event.preventDefault();
               var str = jQuery(this).serialize();
               //alert(str);
                var vdata = {
                    action: "add_hours",
                    project: "value"
                };
               jQuery.ajax({
                    type: "post",
                    data: str+ "&action=add_hours",
                    url: "'.admin_url('admin-ajax.php').'",
                    success: function(response) {
                        //alert(response);
                        jQuery("#confirm").html("<p>Thank you for your participation!</p>");
                    }
                });
        }); });</script>';
    }
	public static function skip_admin_init()
	{ 

		   //Create an instance of our package class...
		    $testListTable = new Hours_List_Table();
		    //Fetch, prepare, sort, and filter our data...
		    $testListTable->prepare_items();
		    
		    ?>
		    <div class="wrap">
		        
		        <div id="icon-users" class="icon32"><br/></div>
		        <h2>Volunteer Hours</h2>

		        
		        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		        <form id="volunteers-filter" method="get">
		            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
		            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		            <!-- Now we can render the completed list table -->
		            <?php $testListTable->display() ?>
		        </form>
		        
		    </div>
	<?php 
	}

    public function add_hours(){
		global $wpdb;
	    // if the submit button is clicked, send the email
	    if ($_POST)
	    {
	 
	        // sanitize form values
	        $club = sanitize_text_field( $_POST["club"] );
	        $project_date = sanitize_text_field( $_POST["project_date"] );
	        $project = sanitize_text_field( $_POST["project"] );
	        $description = esc_textarea( $_POST["description"] );
	        $adult = sanitize_text_field( $_POST["adult"] );
	        $children = sanitize_text_field( $_POST["children"] );
	        $families = sanitize_text_field( $_POST["families"] );
	        $hours = sanitize_text_field( $_POST["hours"] );
			
			$wpdb->insert(
				$wpdb->prefix . 'skip_volunteer',
				array(
					'club' => $club,
					'project_date' => $project_date,
					'project' => $project,
					'description' => $description,
					'adult' => $adult,
					'children' => $children,
					'families' => $families,
					'hours' => $hours,
				)
			);
 
           echo var_dump($_POST);
	    }
         
         die();
    }
    /**
     * Deactivate the plugin
     */     
    public static function deactivate()
    {

        // Do nothing
    } // END public static function deactivate

	public function form_shortcode( $atts, $content = null ){
       $thanks =  "Thank you for your participation!";
	   $form = '<form class="contact-form" id="hoursv" method="post" action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '">
	    <div class="row"><div class="large-12 columns">
	        <label for="club"> Club Name:</label>
		<select class="form-control" style="margin-bottom: 20px;" name="club" id="club"><option value="">---</option><option value="Bon Air ES">Bon Air ES</option><option value="Crestwood ES">Crestwood ES</option><option value="Greenfield ES">Greenfield ES</option><option value="Community">Community</option>
</select>  
	    </div></div>
	    <div class="row"><div class="large-12 columns">
	        <label for="project">Project:</label>
	        <input type="text" name="project" id="project" size="50" maxlength="50" value="" />
	    </div></div>
	    <div class="row"><div class="large-12 columns">
	        <label for="project_date">Project Date:</label>
	        <input type="text" class="MyDate" name="project_date" id="project_date" size="50" maxlength="50" value="" />
	    </div></div>
	    <div class="row"><div class="large-12 columns">
	        <label for="description">Project Description:</label>
	        <textarea name="description" id="description" cols="50" rows="15"></textarea>
	    </div></div>
	    <div class="row"><div class="large-12 columns">
	        <label for="adult">Adults:</label>
	        <input type="text" name="adult" id="adult" size="50" maxlength="50" value="" />
	    </div></div>
	    <div class="row"><div class="large-12 columns">
	        <label for="children">Children:</label>
	        <input type="text" name="children" id="children" size="50" maxlength="50" value="" />
	    </div></div>
	    <div class="row"><div class="large-12 columns">
	        <label for="families">Families:</label>
	        <input type="text" name="families" id="families" size="50" maxlength="50" value="" />
	    </div></div>
	    <div class="row"><div class="large-12 columns">
	        <label for="hours">Hours:</label>
	        <input type="text" name="hours" id="hours" size="50" maxlength="50" value="" />
	    </div></div>
	    <div class="row"><div class="large-12 columns">
	        <input type="submit" value="Submit" name="skip_send" id="send" />
            <div id="confirm"></div>
	    </div></div>
	</form>
	<script type="text/javascript">

jQuery(document).ready(function() {
    jQuery(".MyDate").datepicker({
        dateFormat : "yy-mm-dd"
    });
});

</script>';
	   // put code here to add the form HTML to $form
	   return $form;
	}

	public static function deliver_mail() {

	}

    public static function uninstall()
    {
        global $wpdb;
        $table = $wpdb->prefix."skip_volunteer";

    	$wpdb->query("DROP TABLE IF EXISTS $table");
    } // END public static function deactivate
}

class Hours_List_Table extends WP_List_Table {
    
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'volunteer',     //singular name of the listed records
            'plural'    => 'volunteers',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'hours':
	    case 'club':
            case 'project':
            case 'project_date':
            case 'adult':
            case 'children':
            case 'families':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     **************************************************************************/
    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&movie=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&movie=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']), 
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['project'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
	    'project'     => 'Project',
	    'club'     => 'Club',
            'project_date'  => 'Date',
            'adult'    => 'Adults',
            'children'    => 'Children',
            'families'    => 'Families',
            'hours'    => 'Hours'
        );
        return $columns;
    }


    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'project'     => array('project',false),     //true means it's already sorted
            'club'    => array('club',false),
            'hours'    => array('hours',false),
            'project_date'  => array('project_date',false)
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete',
            'export'    => 'Export'
        );
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
         global $wpdb;
         $table_name = $wpdb->prefix . 'skip_volunteer';
         $entry_id = ( is_array( $_REQUEST['volunteer'] ) ) ? $_REQUEST['volunteer'] : array( $_REQUEST['volunteer'] );
        //Detect when a bulk action is being triggered...
         //var_dump($$_REQUEST);
        if( 'delete'===$this->current_action() ) {
               
                foreach ( $entry_id as $id ) {
                    $id = absint( $id );
                    echo $id;
                   // $wpdb->query( "DELETE FROM $this->entries_table_name WHERE entries_id = $id" );
                }
        }
        if('export'===$this->current_action() ) {
               $selected = implode(",", $entry_id);
               //var_dump($entry_id);
               $query =  "SELECT * FROM $table_name WHERE id IN($selected)" ;
               //echo $query;
               $row = $wpdb->get_results($query);
               $export = '';
               $export .= '<table><tr>';
               $export .= '<td>Project</td>';
               $export .= '<td>Club Name</td>';
               $export .= '<td>Project Date</td>';
               $export .= '<td>Adult</td>';
               $export .= '<td>Children</td>';
               $export .= '<td>Families</td>';
               $export .= '<td>Hours</td></tr>';
               foreach ($row as $value) {
              // var_dump($value->project);
                    $export .= '<tr>'; 
                    $export .= '<td>'.$value->project.'</td>';
                    $export .= '<td>'.$value->club.'</td>';
                    $export .= '<td>'.$value->project_date.'</td>';
                    $export .= '<td>'.$value->adult.'</td>';
                    $export .= '<td>'.$value->children.'</td>';
                    $export .= '<td>'.$value->families.'</td>';
                    $export .= '<td>'.$value->hours.'</td>';
                    $export .= '</tr>'; 
               }
               $export .='</table>';
               $upload_dir = wp_upload_dir();

               $filename = "skip_volunteer";
               $handle = fopen($upload_dir['baseurl']."/export/$filename.xls", "w");
               fwrite($handle, $export);
             //  echo '<a href="'.$upload_dir['baseurl'].'/export/'.$filename.'xls">';
                
        }
        
    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        

        $table_name = $wpdb->prefix . 'skip_volunteer';
        $example_data = $wpdb->get_results( "SELECT * FROM $table_name",'ARRAY_A' );
        $data = $example_data;
      //  var_dump($data);       
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
       
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }


}
register_activation_hook(__FILE__,array('SkipVolunteer','activate'));
register_deactivation_hook( __FILE__, array( 'SkipVolunteer', 'deactivate' ) );
register_uninstall_hook(    __FILE__, array( 'SkipVolunteer', 'uninstall' ) );

$skip_vol = new SkipVolunteer;
