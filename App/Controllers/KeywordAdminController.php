<?php namespace Carbontwelve\InboundTracker\Controllers;

class KeywordAdminController extends Controller
{

    /**
     * @inherit
     */
    public function __construct( \Carbontwelve\InboundTracker\App $app )
    {
        parent::__construct( $app );

        // Double check that we are an admin
        // @todo: figure out how to make this do a nice "wordpress" not authorised page
        if (! is_admin() ){ exit('You are not authorised to view this area.'); }

        add_action('admin_menu', array($this, 'registerAdminMenu'));
        $this->flashMessages['inputs'] = $this->sanitizeInputs();
    }

    public function registerAdminMenu()
    {
        add_submenu_page(
            null,
            'Keywords',
            'Keywords',
            'manage_options',
            'inbound_links_keywords',
            array($this, 'keyword_router')
        );
    }

    public function keyword_router()
    {

        $allowedActions = array('index', 'trash', 'untrash', 'star', 'unstar');
        $action         = ( isset($_GET['action']) ) ? $_GET['action'] : $allowedActions[0];
        if (!in_array($action, $allowedActions)) {
            $action = $allowedActions[0];
        }

        // Get the record ID :)
        $id = $this->getInput('id', null);

        switch ($action) {

            case 'index':
            default:
                echo $this->index($id);
                break;

        }

    }

    private function index($id = null)
    {
        // Which record types are we after?
        $allowedTypes = array('all', 'stared', 'deleted');
        $type         = ( isset($_GET['type']) ) ? $_GET['type'] : $allowedTypes[0];
        if (!in_array($type, $allowedTypes)) {
            $type     = $allowedTypes[0];
        }


        /** @var \Carbontwelve\InboundTracker\Models\Keywords $model */
        $model = $this->app->getModel('keywords');
        $data  = $model->getByCampaignID($id, $type);

        return $this->app->renderView(
            'keywords.index',
            array(
                'data'          => $data,
                'flashMessages' => $this->flashMessages
            )
        );

    }

}
