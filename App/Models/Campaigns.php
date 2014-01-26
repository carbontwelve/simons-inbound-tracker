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
	  			`id` mediumint(9) NOT NULL AUTO_INCREMENT,
	  			`created_at` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  			`updated_at` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  			`deleted_at` datetime DEFAULT NULL NULL,
	  			`created_by` mediumint(9) NOT NULL,
	  			`updated_by` mediumint(9) NOT NULL,
	  			`deleted_by` mediumint(9) NOT NULL,
	  			`name` VARCHAR(250) NOT NULL,
	  			`keywords` mediumint(9) NOT NULL DEFAULT 0,
	  			`clicks` mediumint(9) NOT NULL DEFAULT 0,
	  			`trend` INT(3)  NOT NULL  DEFAULT '100',
	  			`stared` tinyint(1) DEFAULT FALSE NOT NULL,
	  			`enabled` tinyint(1) DEFAULT FALSE NOT NULL,
	  			`default_campaign` tinyint(1) DEFAULT FALSE NOT NULL,
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
					`enabled`,
					`default_campaign`
				)
				VALUES(
					'" . date('Y-m-d H:i:s') . "',
					'Default',
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

    public function getPaginated($type = 'all')
    {
        if (isset($_GET['paged']) && is_numeric( $_GET['paged']) && $_GET['paged'] > 0 )
        {
            $this->pagination['currentPage'] = intval( $_GET['paged'] );
        }else{
            $this->pagination['currentPage'] = 1;
        }

        $this->pagination['count']       = $this->count($type);
        $this->pagination['lastPage']    = ceil( $this->pagination['count'] / $this->pagination['perPage'] );

        // Current Page cant be greater than Last Page
        if ($this->pagination['currentPage'] > $this->pagination['lastPage'])
        {
            $this->pagination['currentPage'] = $this->pagination['lastPage'];
        }

        // Current Page cant be less than 1
        if ($this->pagination['currentPage'] < 1 ){
            $this->pagination['currentPage'] = 1;
        }

        // Offset
        $this->pagination['from'] = ($this->pagination['currentPage'] - 1) * $this->pagination['perPage'];
        $this->pagination['to']   = $this->pagination['perPage'];

        $query = 'SELECT * FROM ' . $this->getQueryEnd($type) . ' ' . $this->wpdb->prepare('LIMIT %d, %d ', array( $this->pagination['from'], $this->pagination['to'] ));
        return $this->wpdb->get_results($query);
    }

    public function getAll($type = 'all')
    {
        $query = 'SELECT * FROM ' . $this->getQueryEnd($type);
        return $this->wpdb->get_results($query);
    }

    public function get( $recordID = null )
    {
        $query = "SELECT * FROM `" . $this->wpdb->prefix . $this->table . "` WHERE `id` = %d AND `deleted_at` IS NULL AND `enabled` = TRUE";
        $query = $this->wpdb->prepare($query, $recordID);
        return $this->wpdb->get_row($query);
    }

    public function count($type = 'all')
    {
        $query = 'SELECT COUNT(`id`) as `c` FROM ' . $this->getQueryEnd($type);
        return $this->wpdb->get_var($query);
    }

    public function update($id = null, Array $data)
    {
        $data = $this->filterDataByAllowed($data);

        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $sqlParts = array();
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $format = '%s';
            } else {
                $format = '%d';
            }
            if (is_null($value)) {
                $format = 'NULL';
            }

            $sqlParts[] = '`' . $key . '` = ' . $format;

            if ($format == 'NULL') {
                unset($data[$key]);
            }
        }

        $sqlParts  = implode(',', $sqlParts);
        $sqlValues = array_values($data);
        $sql       = $this->wpdb->prepare(
            "UPDATE `" . $this->wpdb->prefix . $this->table . "` SET " . $sqlParts . " WHERE `id` = " . intval($id),
            $sqlValues
        );
        return $this->wpdb->query($sql);
    }

    public function insert(Array $data)
    {
        $data = $this->filterDataByAllowed($data);

        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        $sqlParts = array();
        foreach ($data as $value) {
            if (is_string($value)) {
                $format = '%s';
            } else {
                $format = '%d';
            }
            if (is_null($value)) {
                $format = 'NULL';
            }

            $sqlParts[] = $format;
        }

        $sqlKeys = array_keys($data);
        foreach ($sqlKeys as &$value) {
            $value = '`' . $value . '`';
        }
        unset($value);

        $sqlKeys = implode(',', $sqlKeys);
        $sqlParts = implode(',', $sqlParts);
        $sqlValues = array_values($data);

        $sql = $this->wpdb->prepare(
            "INSERT INTO `" . $this->wpdb->prefix . $this->table . "` (" . $sqlKeys . ") VALUES (" . $sqlParts . ")",
            $sqlValues
        );

        return $this->wpdb->query($sql);
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
}
