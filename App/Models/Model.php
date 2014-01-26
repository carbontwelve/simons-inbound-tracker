<?php namespace Carbontwelve\InboundTracker\Models;

/**
 * Class Model
 * @package Carbontwelve\InboundTracker\Models
 */
abstract class Model
{

    /**
     * Array of allowed fields to insert/update on the table
     * @var array
     */
    protected $allowed = array();

    /**
     * Local Instance of the App class
     * @var \Carbontwelve\InboundTracker\App  */
    protected $app;

    /**
     *Local Instance of the wordpress DB Class
     * @var \wpdb */
    protected $wpdb;

    /**
     * Table Name
     * @var string|null $table */
    protected $table = null;

    /**
     * Version of this table
     * Used for updating the database
     * @var string $tableVersion
     */
    protected $tableVersion = "1.0.0";

    /**
     * Pagination Details, required where pagination is used
     * @var array */
    protected $pagination = array(
        'count'       => 0,
        'currentPage' => 1,
        'lastPage'    => 1,
        'perPage'     => 10,
        'from'        => 0,
        'to'          => 10
    );

    /**
     * Initiate the Model
     * @param \wpdb $wpdb
     * @param \Carbontwelve\InboundTracker\App $app
     */
    public function __construct(\wpdb $wpdb, \Carbontwelve\InboundTracker\App $app)
    {
        $this->wpdb = $wpdb;
        $this->app = $app;
    }

    /**
     * Pagination getter
     * @return array
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Filters the input array by what is set in $this->allowed
     *
     * @param array $data
     * @return array
     */
    protected function filterDataByAllowed( array $data )
    {
        if (count($this->allowed) > 0)
        {
            return array_intersect_key($data, array_flip($this->allowed));
        }else{
            return $data;
        }
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

    /**
     * Count records by given type
     * @param string $type
     * @return null|string
     */
    public function count($type = 'all')
    {
        $query = 'SELECT COUNT(`id`) as `c` FROM ' . $this->getQueryEnd($type);
        return $this->wpdb->get_var($query);
    }

    /**
     * Update record with given $id
     * @param null $id
     * @param array $data
     * @return false|int
     */
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

    /**
     * Insert given data into the table
     * @param array $data
     * @return false|int
     */
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

    abstract protected function getQueryEnd($type = 'all');

}
