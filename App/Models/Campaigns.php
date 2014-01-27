<?php namespace Carbontwelve\InboundTracker\Models;

class Campaigns extends Model implements ModelInterface
{

    /**
     * Table Name
     * @var string|null $table */
    protected $table = 'campaigns';

    /**
     * Mass assignable values
     * @var array
     */
    protected $allowed = array(
        'name',
        'slug'
    );

    /**
     * Install/Update the buttons table
     */
    public function install()
    {
        $tableName = $this->wpdb->prefix . $this->table;
        $installedVersion = get_option("carbontwelve_inboundtracker_campaigns_db_version");

        if ($installedVersion === false) {

            // Install our table
            $sql = "" .
                "CREATE TABLE `$tableName`
			(
	  			`id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
	  			`created_at` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  			`updated_at` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  			`deleted_at` DATETIME DEFAULT NULL NULL,
	  			`created_by` MEDIUMINT(9) NOT NULL,
	  			`updated_by` MEDIUMINT(9) NOT NULL,
	  			`deleted_by` MEDIUMINT(9) NOT NULL,
	  			`name` VARCHAR(250) NOT NULL,
	  			`slug` VARCHAR(250) NOT NULL,
	  			`keywords` MEDIUMINT(9) NOT NULL DEFAULT '0',
	  			`clicks` MEDIUMINT(9) NOT NULL DEFAULT '0',
	  			`trend` INT(3)  NOT NULL  DEFAULT '0',
	  			`stared` TINYINT(1) DEFAULT FALSE NOT NULL,
	  			`enabled` TINYINT(1) DEFAULT TRUE NOT NULL,
	  			`default_campaign` TINYINT(1) DEFAULT FALSE NOT NULL,
	  			UNIQUE KEY `id` (id)
	    	);";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            add_option("carbontwelve_inboundtracker_campaigns_db_version", $this->tableVersion);

            // Insert default first banner :)

            $sql = "INSERT INTO `$tableName`
				(
					`created_at`,
					`name`,
					`slug`,
					`enabled`,
					`default_campaign`
				)
				VALUES(
					'" . date('Y-m-d H:i:s') . "',
					'Default',
					'default',
					TRUE,
					TRUE
				)";
            $this->wpdb->query($sql);

        } elseif ($installedVersion != $this->tableVersion) {
            // Upgrade our table

            // Upgrade sql will be here
            // update_option( "carbontwelve_inboundtracker_campaigns_db_version", $this->version );
        }
    }

    /**
     * Gets the end of the query
     * @param string $type
     * @return string
     */
    protected function getQueryEnd($type = 'all')
    {
        switch ($type) {
            case 'deleted':
                $query = "`" . $this->wpdb->prefix . $this->table . "` WHERE `deleted_at` IS NOT NULL";
                break;

            case 'stared':
                $query = "`" . $this->wpdb->prefix . $this->table . "` WHERE `stared` = TRUE AND `deleted_at` IS NULL";
                break;

            default:
            case 'all':
                $query = "`" . $this->wpdb->prefix . $this->table . "` WHERE `deleted_at` IS NULL";
                break;
        }
        return $query;
    }

    /**
     * Check if this record can be deleted...
     * @param $recordID
     * @return bool
     */
    public function canDelete( $recordID )
    {
        $query = "SELECT `id`, `default_campaign` FROM `" . $this->wpdb->prefix . $this->table . "` WHERE `id` = %d";
        $query = $this->wpdb->prepare($query, $recordID);
        $result = $this->wpdb->get_row($query);

        if ($result->default_campaign > 0)
        {
            return false;
        }else{
            return true;
        }
    }

    /**
     * Get a campaign by its slug
     * @param null $slug
     * @return mixed
     */
    public function getBySlug( $slug = null )
    {
        $query = "SELECT * FROM `" . $this->wpdb->prefix . $this->table . "` WHERE `slug` = %s AND `deleted_at` IS NULL AND `enabled` = TRUE";
        $query = $this->wpdb->prepare($query, $slug);
        return $this->wpdb->get_row($query);
    }

    /**
     * Get Default campaign
     * @return mixed
     */
    public function getDefault()
    {
        $query = "SELECT * FROM `" . $this->wpdb->prefix . $this->table . "` WHERE `default_campaign` = TRUE";
        return $this->wpdb->get_row($query);
    }
}
