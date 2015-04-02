<?php
/*
 Plugin Name: Blu Contact Form
 Plugin URI: http://aeonblu.com
 Description: A plugin to process and store form submissions.
 Version: 1.1.1
 Author: Aeon Blu
 Author URI: http://aeonblu.com
 License: GPL2
*/


/**
 * BluContact Class
 *
*/
class BluContact 
{
	public $className;
	
	public $pluginName = 'Blu Contact Form';
	public $pluginShortName = 'Contact Form';
	
	public $debug = 0;
	
	public $useContactPostType = 0;
	
	private $postType = 'contact';
	
	public $ra = 0;
	
	public $rewrite = null;
	
	public $adminNotice = '';
	public $adminError = '';

	public $error = 0;
	public $errorCode = '';
	public $errorMessage = '';
	
	/*----------------------------------------------------------------------
	CONSTRUCTOR: __construct()
	----------------------------------------------------------------------*/
	/**
     * BluContact Class Constructor
     *
     * Initializes class attributes
     *
     */
	function __construct()
	{
		$this->className = get_class();
		
		// Define Constants
		define( 'BLU_CONTACT_PLUGIN_PATH', trailingslashit( WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__ ),"",plugin_basename( __FILE__ ) ) ) );
		
		$this->setRa();
                
        if( is_admin() )
        {
			if ($this->debug)
			{
				if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
				{
					
				}
				else
				{
					echo '<script>' . 'console.log("' . $this->className . ' is_admin' . '");' . ' </script>';
				}			
			}
			
			if ( function_exists('register_activation_hook') ) 
			{
				// Register WordPress Activation Hook
				register_activation_hook( __FILE__, array( $this, 'activate' ) );
			}
		
			if ( function_exists('register_deactivation_hook') ) 
			{
				// Register WordPress Deactivation Hook
				register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
			}
			
			if ( function_exists('add_action') ) 
			{			
				add_action( 'admin_notices', array($this,'displayAdminNotice') );
				
				// Add WordPress action for the admin menu
				add_action( 'admin_menu', array($this,'adminMenu') );
       		}
        }
 				
		if ( function_exists('add_action') ) 
		{
			if ( $this->useContactPostType ) 
			{
				// Add WordPress action to register post type
				add_action( 'init', array($this,'registerPostType') );
			}
		}
		
		$this->reqisterAcfFieldGroup();
		
		if ( function_exists('add_filter') ) 
		{
			add_filter( 'query_vars', array($this,'addQueryVars'));
		}
        
        if( !(is_admin()) )
        {
        	
        }
		
	}
	
	/*----------------------------------------------------------------------
	METHOD: setDebug(debug)
	----------------------------------------------------------------------*/
	/**
	 *  Sets value of debug class variable.
     *
	 * @param boolean $debug
     *
     */
	public function setDebug($debug=false)
	{
		if ( $debug ) 
		{
			$this->debug = true;
		}
		else
		{
			$this->debug = false;
		}
	}
	
	/*----------------------------------------------------------------------
	METHOD: setRa()
	----------------------------------------------------------------------*/
	/**
	 *  Sets value of ra (random) class variable.
     *
	 * @return int
     */
	public function setRa()
	{
		$this->ra = rand('11111111', '99999999');
		
		return $this->ra;
	}
	
	/*----------------------------------------------------------------------
	METHOD: getRa()
	----------------------------------------------------------------------*/
	/**
	 *  Gets value of ra (random) class variable.
     *
	 * @return int
     */
	public function getRa()
	{
		if ( empty($this->ra) ) 
		{
		    $this->setRa();
		} 
		
		return $this->ra;
	}
	
	/*----------------------------------------------------------------------
	METHOD: getOptionsList()
	----------------------------------------------------------------------*/
	/**
	 *  Gets array of options
     *
	 * @return array
     *
     */
	public function getOptionsList()
	{
		if ($this->debug)
		{
			echo $this->className." getOptionsList<br />";
		}
		
		$options = array(
			array(
				'fieldType' => 'checkbox',
				'name'    => 'blucontact_debug',
				'label'    => 'Debug',
				'defaultValue'    => false
			),
			array(
				'fieldType' => 'text',
				'name'    => 'blucontact_formSuccessMessage',
				'label'    => 'Successful Submission Message',
				'defaultValue'    => 'Success. Your info is submitted.'
			),
			array(
				'fieldType' => 'checkbox',
				'name'    => 'blucontact_sendEmailNotification',
				'label'    => 'Send Email Notification',
				'defaultValue'    => true
			),
			array(
				'fieldType' => 'textarea',
				'name'    => 'blucontact_sendNotificationTo',
				'label'    => 'Send Notification To',
				'defaultValue'    => ''
			),
			array(
				'fieldType' => 'text',
				'name'    => 'blucontact_emailNotificationSubject',
				'label'    => 'Email Notification Subject',
				'defaultValue'    => 'Contact Form Notification'
			)
		);
		
		if ($this->debug)
		{
			echo "options: <pre>".print_r($options, true)."</pre>".PHP_EOL;
		}
		
		return $options;
	}
	
	/*----------------------------------------------------------------------
	METHOD: setOption(name,value)
	----------------------------------------------------------------------*/
	/**
	 *  Sets option.
     *
	 * @param string $name
	 * @param mixed $value
	 * @return boolean
     *
     */
	public function setOption($name=null, $value=null)
	{
		if ($this->debug)
		{
			echo $this->className." setOption<br />".PHP_EOL;
			echo "name: ".$name."<br />".PHP_EOL;
			echo "value: ".$value."<br />".PHP_EOL;
		}
		
		$valid = 0;
		$result = 0;
		
		$options = $this->getOptionsList();
		
		foreach ($options as $option)
		{
		    if ( $name == $option['name'] ) 
		    {
				$valid = 1;
				
				break;
		    } 
		}
		
		if ($this->debug)
		{
			echo "valid: ".$valid."<br />".PHP_EOL;
			echo "option: <pre>".print_r($option, true)."</pre>".PHP_EOL;
		}
				
		if ( $valid && function_exists('update_option') ) 
		{
			switch ( $option['fieldType'] )
			{
			    case 'checkbox':
			        
					if ( $value ) 
					{
						if ( $result = update_option($name,true) ) 
						{
							$this->{$name} = true;
						} 
					}
					else
					{
						if ( $result = update_option($name,false) ) 
						{
							$this->{$name} = false;
						} 
					}
			        
			        break;
			        
			    default:
			        
			        $result = update_option($name,$value);
			        
			        break;
			}
    	}
		
		if ($this->debug)
		{
			echo "result: ".$result."<br />".PHP_EOL;
		}
		
		return $result;
	}
	
	/*----------------------------------------------------------------------
	METHOD: getOption(name)
	----------------------------------------------------------------------*/
	/**
	 *  Get option value.
     *
	 * @param string $name
	 * @param string $output
	 * @return mixed
     *
     */
	public function getOption($name=null, $output=null)
	{
		if ($this->debug)
		{
			echo $this->className." getOption<br />".PHP_EOL;
			echo "name: ".$name."<br />".PHP_EOL;
		}
		
		$valid = 0;
		$value = null;
		
		$options = $this->getOptionsList();
		
		foreach ($options as $option)
		{
		    if ( $name == $option['name'] ) 
		    {
				$valid = 1;
				
				break;
		    } 
		}
		
		if ($this->debug)
		{
			echo "valid: ".$valid."<br />".PHP_EOL;
			echo "option: <pre>".print_r($option, true)."</pre>".PHP_EOL;
		}
		
		if ( $valid ) 
		{
		    $value = get_option($name);
		    
		    if ( $output == 'ARRAY' ) 
		    {
		        $valueArrayIn = explode(PHP_EOL, $value);
		        $valueArrayOut = array();
		        
		        if ( is_array($valueArrayIn) && count($valueArrayIn) ) 
		        {
		            for ($i = 0; $i < count($valueArrayIn); $i++)
		            {
		                $v = trim($valueArrayIn[$i]);
		                
		                if ( strlen($v) ) 
		                {
		                    array_push($valueArrayOut, $v);
		                } 
		            }
		        }
		        
		        $value = $valueArrayOut;
		    } 
		} 
		
		if ($this->debug)
		{
			echo "value: ".$value."<br />".PHP_EOL;
		}
		
		return $value;
	}
	
	/*----------------------------------------------------------------------
	METHOD: activate()
	----------------------------------------------------------------------*/
	/**
     * Activates Plugin
     *
     * Called by WordPress Register Activation Hook
     *
     */
	public function activate()
	{
		if ( function_exists('update_option') ) 
		{
			// Set WordPress options
			update_option('blucontact_debug',true);
			update_option('blucontact_debug',false);
			
			$options = $this->getOptionsList();
			
			if ( is_array($options) && count($options) ) 
			{
				foreach ($options as $option)
				{
					$this->setOption($option['name'], $option['defaultValue']);
				}
			} 
      	}
		
		$this->createDb();
		
		if ( $this->useContactPostType ) 
		{
			$this->registerPostType();
						
			if ( function_exists('flush_rewrite_rules') ) 
			{
				flush_rewrite_rules();
			}
		} 
		
	}
	
	/*----------------------------------------------------------------------
	METHOD: deactivate()
	----------------------------------------------------------------------*/
	/**
     * Deactivates Plugin
     *
     * Called by WordPress Register Deactivation Hook
     *
     */
	public function deactivate()
	{
		if ( function_exists('delete_option') ) 
		{
			// Remove WordPress options
			
			$options = $this->getOptionsList();
			
			if ( is_array($options) && count($options) ) 
			{
				foreach ($options as $option)
				{
					delete_option($option['name']);
				}
			} 
  		}
    	
		if ( $this->useContactPostType ) 
		{
			if ( function_exists('flush_rewrite_rules') ) 
			{
				flush_rewrite_rules();
			}
		}
	}
  	
	/*----------------------------------------------------------------------
	METHOD: createDb()
	----------------------------------------------------------------------*/
	/**
     * Creates database tables
     *
     * 
	 * @return mixed
     */
	private function createDb()
	{
		if ($this->debug)
		{
			echo $this->className." createDb<br />".PHP_EOL;
		}
		
		global $wpdb;
		
		$prefix = $wpdb->prefix;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$contactTableName = $prefix.'blu_contact';
		
		if ($this->debug)
		{
			echo "charset_collate: ".$charset_collate."<br />".PHP_EOL;
			echo "contactTableName: ".$contactTableName."<br />".PHP_EOL;
		}
	
		$sqlQuery = <<<END
CREATE TABLE IF NOT EXISTS $contactTableName (
  contact_ID bigint(20) unsigned NOT NULL auto_increment,
  contact_post_ID bigint(20) unsigned NOT NULL default '0',
  contact_name varchar(200) NOT NULL default '',
  contact_firstName varchar(100) NOT NULL default '',
  contact_lastName varchar(100) NOT NULL default '',
  contact_email varchar(100) NOT NULL default '',
  contact_url varchar(200) NOT NULL default '',
  contact_phone varchar(30) NOT NULL default '',
  contact_country varchar(100) NOT NULL default '',
  contact_age varchar(100) NOT NULL default '',
  contact_note text NOT NULL,
  contact_remoteAddress varchar(100) NOT NULL default '',
  contact_requestUri varchar(254) NOT NULL default '',
  contact_date datetime NOT NULL default '0000-00-00 00:00:00',
  contact_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  contact_export_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (contact_ID),
  KEY contact_post_ID (contact_post_ID),
  KEY contact_date (contact_date),
  KEY contact_date_gmt (contact_date_gmt),
  KEY contact_name (contact_name),
  KEY contact_email (contact_email)
) $charset_collate;
END;
		
		if ($this->debug)
		{
			echo "sqlQuery: ".$sqlQuery."<br />".PHP_EOL;
		}
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		$result = dbDelta( $sqlQuery );
		
		if ($this->debug)
		{
			echo "result: <pre>".print_r($result, true)."</pre>".PHP_EOL;
		}
		
		return $result;
	}
  	
	/*----------------------------------------------------------------------
	METHOD: insertContact(fieldValues)
	----------------------------------------------------------------------*/
	/**
     * Insert contact record
     *
     * @param $fieldValues
	 * @return mixed
     */
	public function insertContact($fieldValues=array())
	{
		if ($this->debug)
		{
			echo $this->className." insertContact<br />".PHP_EOL;
			echo "fieldValues: <pre>".print_r($fieldValues, true)."</pre>".PHP_EOL;
		}
		
		$result = null;
		
		if ( !(is_array($fieldValues) && count($fieldValues)) ) 
		{
		    return $result;
		} 
		
		global $wpdb;
		
		$fields = array('contact_post_ID', 'contact_name', 'contact_firstName', 'contact_lastName', 'contact_email', 'contact_url', 'contact_phone', 'contact_country', 'contact_age', 'contact_note', 'contact_remoteAddress', 'contact_requestUri');
		
		if ($this->debug)
		{
			echo "fields: <pre>".print_r($fields, true)."</pre>".PHP_EOL;
		}
		
		$data = array();
		
		foreach ($fieldValues as $key=>$value)
		{
		    if ( in_array($key, $fields) ) 
		    {
		        $data[$key] = $value;
		    } 
		}
		
		$data['contact_date'] = current_time( 'mysql' );
		$data['contact_date_gmt'] = current_time( 'mysql', 1 );
		
		if ($this->debug)
		{
			echo "data: <pre>".print_r($data, true)."</pre>".PHP_EOL;
		}
		
		$result = $wpdb->insert( 
			$wpdb->prefix.'blu_contact', 
			$data
		);
		
		if ($this->debug)
		{
			echo "result: <pre>".print_r($result, true)."</pre>".PHP_EOL;
		}
		
		return $result;
	}
	
	/*----------------------------------------------------------------------
	METHOD: adminMenu()
	----------------------------------------------------------------------*/
	/**
     * Add an options page to the setting menu.
     *
     * 
     *
     */
	public function adminMenu()
	{
		if ( function_exists('add_options_page') ) 
		{
			$page = add_options_page($this->pluginName.' Settings', $this->pluginShortName, 'manage_options', dirname(__FILE__), array($this,'optionsPage'));
		}
	}
	
 	/*----------------------------------------------------------------------
	METHOD: optionsPage()
	----------------------------------------------------------------------*/
	/**
     * Options Page
     *
     * 
     *
     */
	public function optionsPage()
	{
		if ($this->debug)
		{
			//echo $this->className." optionsPage<br />";
		}
		
		//echo BLU_CONTACT_PLUGIN_PATH;
		
        include(BLU_CONTACT_PLUGIN_PATH . 'includes/admin/optionsPage.php');
		
		
	}
	
 	/*----------------------------------------------------------------------
	METHOD: registerPostType()
	----------------------------------------------------------------------*/
	/**
     * Register WordPress Post Type
     *
     * 
     *
     */
	public function registerPostType() {
		
		if ($this->debug)
		{
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			{
				
			}
			else
			{
				echo '<script>' . 'console.log("' . $this->className . ' registerPostType' . '");' . ' </script>';
			}			
		}
		
		$labels = array(
			'name'               => _x( 'Contacts', 'post type general name' ),
			'singular_name'      => _x( 'Contact', 'post type singular name' ),
			'add_new'            => _x( 'Add New', 'contact' ),
			'add_new_item'       => __( 'Add New Contact' ),
			'edit_item'          => __( 'Edit Contact' ),
			'new_item'           => __( 'New Contact' ),
			'all_items'          => __( 'All Contacts' ),
			'view_item'          => __( 'View Contact' ),
			'search_items'       => __( 'Search Contacts' ),
			'not_found'          => __( 'No contacts found' ),
			'not_found_in_trash' => __( 'No contacts found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Contacts'
		);
		
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our contacts and contact specific data',
			'public'        => true,
			'menu_position' => 100,
			'supports'      => array( 'title', 'editor' ),
			//'supports'      => array( 'title', 'editor', 'custom-fields' ),
			'has_archive'   => false
		);
		
		if ( function_exists('register_post_type') ) 
		{
			register_post_type( $this->postType, $args ); 
		}
	}
	
	/*----------------------------------------------------------------------
	METHOD: addQueryVars(vars)
	----------------------------------------------------------------------*/
	/**
	 *  Add query vars method for query_vars filter.
     *
     */
	function addQueryVars($vars){
		
		array_push($vars, 'bluDebugThis');
				
		return $vars;
	}
	
	/*----------------------------------------------------------------------
	METHOD: displayAdminNotice()
	----------------------------------------------------------------------*/
	/**
	 *  Display WordPress admin notice. Called by 'admin_notices' action.
     *
     */
	function displayAdminNotice()
	{
		if ( !empty($this->adminError) ) 
		{
			echo '<div class="error"><p>'.$this->adminError.'</p></div>';
		} 
		
		if ( !empty($this->adminNotice) ) 
		{
			echo '<div class="updated"><p>'.$this->adminNotice.'</p></div>';
		} 
	}
	
	/*----------------------------------------------------------------------
	METHOD: reqisterAcfFieldGroup()
	----------------------------------------------------------------------*/
	/**
	 *  Add custom fields using Advanced Custom Fields (ACF) plugin
     *
     */
	function reqisterAcfFieldGroup()
	{
		//$this->adminNotice .= $this->className." reqisterAcfFieldGroup<br />".PHP_EOL;
		
		if ( function_exists('register_field_group') )
		{
			
			
			
		}	
	}
	
	/*----------------------------------------------------------------------
	METHOD: getDefaultFromName()
	----------------------------------------------------------------------*/
	/**
	 *  Gets default name to use with from email address.
     *
	 * @return string
     *
     */
	public function getDefaultFromName()
	{
		$defaultFromName = '';
		
		if ( function_exists('get_bloginfo') ) 
		{
			$defaultFromName = get_bloginfo('name');
		}
		
		return $defaultFromName;
	}
	
	/*----------------------------------------------------------------------
	METHOD: getDefaultFromAddress()
	----------------------------------------------------------------------*/
	/**
	 *  Gets default name to use as from email address.
     *
	 * @return string
     *
     */
	public function getDefaultFromAddress()
	{
		$defaultFromAddress = 'noreply@';
		
		if ( function_exists('get_bloginfo') ) 
		{
			$urlArray = parse_url(get_bloginfo('url'));
			
			//echo "urlArray: <pre>".print_r($urlArray, true)."</pre>\n";
			
			if ( strlen($urlArray['host']) ) 
			{
				$defaultFromAddress .= $urlArray['host'];
			} 
		}
		
		return $defaultFromAddress;
	}
 	
    /*----------------------------------------------------------------------
    METHOD: sendEmail(toName,toAddress,fromName,fromAddress,subject,body,format)
    ----------------------------------------------------------------------*/
	/**
	* Sends email message using PHP mail function.
	*
	* @param string $toName
	* @param string $toAddress
	* @param string $fromName
	* @param string $fromAddress
	* @param string $subject
	* @param string $body
	* @param string $format
	* @return int|bool
	*
	*/
    function sendEmail($toName='',$toAddress='',$fromName='',$fromAddress='',$subject='',$body='',$format='HTML')
    {
        if ($this->debug)
        {
        	echo $this->className . " send\n";
			echo "toName: ".$toName."\n";
			echo "toAddress: ".$toAddress."\n";
			echo "fromName: ".$fromName."\n";
			echo "fromAddress: ".$fromAddress."\n";
			echo "subject: ".$subject."\n";
			echo "body: ".$body."\n";
        }
        
		$result = 0;
		
		if ( empty($toAddress) || empty($subject) )
		{
			$this->error = 1;
			$this->errorCode = "INPUT:REQUIRED";
			$this->errorMessage = "To Address, Subject required.";
			
			return $result;
		}
		
		if ( !empty($toName) )
		{
		    $to = $toName.' <'.$toAddress.'>';
		}
		else
		{
		    $to = $toAddress;
		}
		
		if ( empty($fromAddress) )
		{
			$from = $this->getDefaultFromName().' <'.$this->getDefaultFromAddress().'>';
		}
		else
		{
			if ( !empty($fromName) )
			{
				$from = '"'.$fromName.'" <'.$fromAddress.'>';
			}
			else
			{
				$from = $fromAddress;
			}
		}
		
		$headers  = "From: ".$from." \r\n";
		$headers .= "Reply-To: ".$from." \r\n";
		//$headers .= "X-Mailer: PHP/" . phpversion();
		
		if ( $format == 'HTML' )
		{
			$headers .= 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n";
		}
		else
		{
			$headers .= 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
			
			$body = strip_tags($body);
		}
		
  		$phpself = $_SERVER['PHP_SELF'];
  		$remoteaddr = $_SERVER['REMOTE_ADDR'];
  		$_SERVER['PHP_SELF'] = "/";
  		$_SERVER['REMOTE_ADDR'] = $_SERVER['SERVER_ADDR'];
		
		if ( $result = mail($to, $subject, $body, $headers) )
		{
			if ($this->debug)
			{
				echo "message sent. \n\n";
			}
			
			$this->processMessage = "Message sent. ";
		}
		else
		{
			if ($this->debug)
			{
				echo "message not sent. \n\n";
			}
			
			$this->error = 1;
			$this->errorCode = "MAIL:ERROR";
			$this->errorMessage = "Error sending message. ";
		}
		
  		$_SERVER['PHP_SELF'] = $phpself;
   		$_SERVER['REMOTE_ADDR'] = $remoteaddr;
       
        return ($result);
    }
		
	/*----------------------------------------------------------------------
	DESTRUCTOR: __destruct()
	----------------------------------------------------------------------*/
	/**
	 * BluContact Class Destructor
	 *
	 */
    function __destruct()
    {
		
    }
}

$bluContact = new BluContact();

//$bluContact->setDebug(1);

/**
 * BluContactStatusTaxonomy Class
 *
*/
class BluContactStatusTaxonomy 
{
	public $className;
	
	public $debug = 0;
	
	public $taxonomy = 'contact_status';
	
	public $name_plural = 'Contact Status';
	public $name_singular = 'Contact Status';
	public $item_plural = 'Status';
	public $item_singular = 'Status';
	
	public $post_types = array( 'contact' );
	
	/*----------------------------------------------------------------------
	CONSTRUCTOR: __construct()
	----------------------------------------------------------------------*/
	/**
     * BluContactStatusTaxonomy Class Constructor
     *
     * Initializes class attributes
     *
     */
	function __construct()
	{
		$this->className = get_class();
		
		// Define Constants
		define( strtoupper($this->taxonomy).'_TAXONOMY_PLUGIN_PATH', trailingslashit( WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__ ),"",plugin_basename( __FILE__ ) ) ) );
        
        if( is_admin() )
        {
			if ( function_exists('register_activation_hook') ) 
			{
				// Register WordPress Activation Hook
				register_activation_hook( __FILE__, array( $this, 'activate' ) );
			}
		
			if ( function_exists('register_deactivation_hook') ) 
			{
				// Register WordPress Deactivation Hook
				register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
			}
       }
 				
		if ( function_exists('add_action') ) 
		{
			// Add WordPress action to register taxonomy
			add_action( 'init', array($this,'registerTaxonomy') );
		}
		
	}
	
	/*----------------------------------------------------------------------
	METHOD: setDebug(debug)
	----------------------------------------------------------------------*/
	/**
	 *  Sets value of debug class variable.
     *
	 *  Returns result.
     *
     */
	public function setDebug($debug=0)
	{
		$this->debug = $debug;
		
		return $this->debug;
	}
	
	/*----------------------------------------------------------------------
	METHOD: activate()
	----------------------------------------------------------------------*/
	/**
     * Activates Plugin
     *
     * Called by WordPress Register Activation Hook
     *
     */
	public function activate()
	{
		if ($this->debug)
		{
			echo $this->className." activate<br />";
		}
    	
    	$this->registerTaxonomy();
    	
 		if ( function_exists('flush_rewrite_rules') ) 
		{
   			flush_rewrite_rules();
		}
		
		$this->insertTerms();
	}
	
	/*----------------------------------------------------------------------
	METHOD: deactivate()
	----------------------------------------------------------------------*/
	/**
     * Deactivates Plugin
     *
     * Called by WordPress Register Deactivation Hook
     *
     */
	public function deactivate()
	{
		if ($this->debug)
		{
			echo $this->className." deactivate<br />";
		}
    	
 		if ( function_exists('flush_rewrite_rules') ) 
		{
   			flush_rewrite_rules();
		}
	}
	
 	/*----------------------------------------------------------------------
	METHOD: registerTaxonomy()
	----------------------------------------------------------------------*/
	/**
     * Register WordPress Taxonomy
     *
     * 
     *
     */
	public function registerTaxonomy() {

		$labels = array(
			'name'                       => _x( $this->name_plural, 'Taxonomy General Name', 'interactive_status_domain' ),
			'singular_name'              => _x( $this->name_singular, 'Taxonomy Singular Name', 'interactive_status_domain' ),
			'menu_name'                  => __( $this->name_singular, 'interactive_status_domain' ),
			'all_items'                  => __( 'All '.$this->item_plural, 'interactive_status_domain' ),
			'parent_item'                => __( 'Parent '.$this->item_singular, 'interactive_status_domain' ),
			'parent_item_colon'          => __( 'Parent '.$this->item_singular.':', 'interactive_status_domain' ),
			'new_item_name'              => __( 'New '.$this->item_singular.' Name', 'interactive_status_domain' ),
			'add_new_item'               => __( 'Add New '.$this->item_singular, 'interactive_status_domain' ),
			'edit_item'                  => __( 'Edit '.$this->item_singular, 'interactive_status_domain' ),
			'update_item'                => __( 'Update '.$this->item_singular, 'interactive_status_domain' ),
			'separate_items_with_commas' => __( 'Separate '.strtolower($this->item_plural).' with commas', 'interactive_status_domain' ),
			'search_items'               => __( 'Search '.$this->item_plural, 'interactive_status_domain' ),
			'add_or_remove_items'        => __( 'Add or remove '.strtolower($this->item_plural), 'interactive_status_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used '.strtolower($this->item_plural), 'interactive_status_domain' ),
			'not_found'                  => __( $this->item_singular.' Not Found', 'interactive_status_domain' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => false,
			'update_count_callback'      => '_update_post_term_count',	
			);
			
		if ( register_taxonomy( $this->taxonomy, $this->post_types, $args ) ) 
		{
		    
		} 
		
	}
	
 	/*----------------------------------------------------------------------
	METHOD: insertTerms()
	----------------------------------------------------------------------*/
	/**
     * Insert terms.
     *
     * 
     *
     */
	public function insertTerms() {
		
		$parent_term_id = 0;
		
		$t = wp_insert_term(
		  'Approved', // the term 
		  $this->taxonomy, // the taxonomy
		  array(
			'description'=> 'Approved',
			'slug' => 'approved',
			'parent'=> $parent_term_id
		  )
		);
		
		$t = wp_insert_term(
		  'Disapproved', // the term 
		  $this->taxonomy, // the taxonomy
		  array(
			'description'=> 'Disapproved',
			'slug' => 'disapproved',
			'parent'=> $parent_term_id
		  )
		);
		
		$t = wp_insert_term(
		  'No, Absolutely Not', // the term 
		  $this->taxonomy, // the taxonomy
		  array(
			'description'=> 'No, Absolutely Not',
			'slug' => 'no_absolutely_not',
			'parent'=> $parent_term_id
		  )
		);
		
		$t = wp_insert_term(
		  'More Review Required', // the term 
		  $this->taxonomy, // the taxonomy
		  array(
			'description'=> 'More Review Required',
			'slug' => 'more_review_required',
			'parent'=> $parent_term_id
		  )
		);
		
		$t = wp_insert_term(
		  'Editing Required', // the term 
		  $this->taxonomy, // the taxonomy
		  array(
			'description'=> 'Editing Required',
			'slug' => 'editing_required',
			'parent'=> $parent_term_id
		  )
		);
		
		$t = wp_insert_term(
		  'Notification Sent', // the term 
		  $this->taxonomy, // the taxonomy
		  array(
			'description'=> 'Notification Sent',
			'slug' => 'notification_sent',
			'parent'=> $parent_term_id
		  )
		);
		
		$t = wp_insert_term(
		  'Posted', // the term 
		  $this->taxonomy, // the taxonomy
		  array(
			'description'=> 'Posted',
			'slug' => 'posted',
			'parent'=> $parent_term_id
		  )
		);
		
		$t = wp_insert_term(
		  'Test', // the term 
		  $this->taxonomy, // the taxonomy
		  array(
			'description'=> 'Test',
			'slug' => 'test',
			'parent'=> $parent_term_id
		  )
		);
		
	}
	
	/*----------------------------------------------------------------------
	DESTRUCTOR: __destruct()
	----------------------------------------------------------------------*/
	/**
	 * BluContactStatusTaxonomy Class Destructor
	 *
	 */
    function __destruct()
    {
		
    }
}

$bluContactStatusTaxonomy = new BluContactStatusTaxonomy();

?>
