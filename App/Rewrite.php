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
                'campaign' => strtolower( sanitize_title_with_dashes($query->get( 'utm_campaign' ), '', 'save') ),
                'keyword'  => strtolower( sanitize_title_with_dashes($query->get( 'utm_term' ), '', 'save') )
            );

            if ($request['campaign'] === ''){ $request['campaign'] = 'default'; }
            if ($request['keyword'] === ''){  $request['keyword']  = 'default'; }

            /** @var \Carbontwelve\InboundTracker\Models\Campaigns $model */
            $model    = $this->app->getModel('campaigns');
            $campaign = $model->getBySlug( $request['campaign'] );

            // If the campaign cant be found by slug then we should get the default campaign
            if ( is_null($campaign) ){ $campaign = $model->getDefault(); }

            // If a default campaign can not be found then there has been an error.
            if ( ! is_null($campaign) )
            {
                /** @var \Carbontwelve\InboundTracker\Models\Keywords $model */
                $model    = $this->app->getModel('keywords');
                $data     = $model->getByCampaignIDAndKeywordSlug( $campaign->id, $request['keyword'] );

                // If keyword doesn't exist then create it.
                if ( is_null( $data ))
                {
                    $model->insert(array(
                            'campaign_id' => $campaign->id,
                            'name'        => $request['keyword'],
                            'slug'        => $request['keyword'],
                            'enabled'     => 1,
                            'clicks'      => 1,
                        ));
                    $data = $model->getByCampaignIDAndKeywordSlug( $campaign->id, $request['keyword'] );
                }else{

                    $model->update($data->id, array( 'clicks' => ( $data->clicks + 1 ) ));

                }
            }
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

