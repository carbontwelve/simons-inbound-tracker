<?php namespace Carbontwelve\InboundTracker;

class Start
{

    /**
     * Local Instance of the App class
     * @var \Carbontwelve\InboundTracker\App  */
    protected $app;

    /**
     * Plugin Version
     * @var string */
    protected $version = "1.0.0";

    /**
     * Check to see if plugin is loaded, and if so fire $this->loaded
     * This is pretty much a bootstrap file as it is only fired by wordpress on the plugins_loaded action.
     */
    public function __construct()
    {
        $this->app = new App(
            __DIR__,
            plugins_url('simons-button-board/')
        );

        // Register Models
        $this->app->registerModel('campaigns', '\\Carbontwelve\\InboundTracker\\Models\\Campaigns');
        $this->app->registerModel('keywords', '\\Carbontwelve\\InboundTracker\\Models\\Keywords');

        // If our plugin is loaded and activated then we need to start it up
        add_action('plugins_loaded', array($this, 'loaded'));

        // Actions to be added for rewriting
        //add_action( 'generate_rewrite_rules', array($this->app->getRewriter(), 'add_rewrite_rules') );
        add_action( 'pre_get_posts',          array($this->app->getRewriter(), 'pre_get_posts') );
        add_filter( 'query_vars',             array($this->app->getRewriter(), 'query_vars') );
    }

    /**
     * Actions to do on plugin activation:
     * + Here we flush the rewrite rules to force a call to generate_rewrite_rules()
     *
     * @return void
     */
    public function activated()
    {
        /** @var \WP_Rewrite $wp_rewrite */
        //global $wp_rewrite; $wp_rewrite->flush_rules();
    }

    /**
     * Actions to do on plugin deactivation
     */
    public function deactivated()
    {
        //remove_action( 'generate_rewrite_rules', array($this->rewriter, 'add_rewrite_rules') );
        /** @var \WP_Rewrite $wp_rewrite */
        //global $wp_rewrite; $wp_rewrite->flush_rules();
    }

    /**
     * Load plugin classes and initiate everything
     */
    public function loaded()
    {

        // Run install/upgrade if not already installed/upgraded
        // This has to be loaded after the models have been registered...
        if (get_site_option('carbontwelve_inboundtracker_version') != $this->version) {
            $this->app->install($this->version);
        }

        // Add Pages to administration
        if (is_admin())
        {
            new \Carbontwelve\InboundTracker\Controllers\CampaignAdminController($this->app);
            new \Carbontwelve\InboundTracker\Controllers\KeywordAdminController($this->app);
        }

        // Register short codes
        //new ShortCode($this->app);
    }
}
