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

}
