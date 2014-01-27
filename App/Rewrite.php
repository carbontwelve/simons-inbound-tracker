<?php namespace Carbontwelve\InboundTracker;

class Rewrite
{

    /** @var \Carbontwelve\InboundTracker\App */
    protected $app;

    /**
     * @param $app \Carbontwelve\InboundTracker\App
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function pre_get_posts( $query ){
        if ( ! is_admin() && $query->is_main_query() && $query->get( 'utm_campaign' ) ) { // check if user asked for a non-admin page and that query contains except_category_name var

            $request = array(
                'campaign' => $query->get( 'utm_campaign' ),
                'keyword'  => $query->get( 'utm_term' )
            );

            var_dump($request);

            die();

        }
    }

    /**
     * @param $public_query_vars
     * @return mixed
     */
    public function query_vars($public_query_vars){
        array_push($public_query_vars, 'utm_campaign');
        array_push($public_query_vars, 'utm_term');
        return $public_query_vars;
    }
}
