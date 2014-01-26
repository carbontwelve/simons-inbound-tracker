<?php namespace Carbontwelve\InboundTracker\Controllers;

class AdminPages extends Controller
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
        add_menu_page(
            'Inbound Links', // The text to be displayed in the title tags of the page when the menu is selected
            'Inbound Links', // The on-screen name text for the menu
            'manage_options', // The capability required for this menu to be displayed to the user.
            'inbound_links_index', // The slug name to refer to this menu by (should be unique for this menu).
            array($this, 'index_router'), // The function that displays the page content for the menu page.
            plugins_url('simons-inbound-tracker/public/img/icon.png'), // The icon for this menu.
            91.2 // The position in the menu order this menu should appear.
        );


        add_submenu_page(
            'inbound_links_index', // The slug name for the parent menu
            'Add New Campaign', // The text to be displayed in the title tags of the page when the menu is selected
            'Add New Campaign', // The text to be used for the menu
            'manage_options', // The capability required for this menu to be displayed to the user.
            'inbound_links_add', // The slug name to refer to this menu by (should be unique for this menu).
            array($this, 'record_router')
        );

        /**

        add_submenu_page(
            'button_board_index', // The slug name for the parent menu
            'Settings', // The text to be displayed in the title tags of the page when the menu is selected
            'Settings', // The text to be used for the menu
            'manage_options', // The capability required for this menu to be displayed to the user.
            'button_board_settings', // The slug name to refer to this menu by (should be unique for this menu).
            array($this, 'settings_router')
        );
        */
    }

    public function index_router()
    {
        $allowedActions = array('index', 'trash', 'untrash', 'star', 'unstar');
        $action         = ( isset($_GET['action']) ) ? $_GET['action'] : $allowedActions[0];
        if (!in_array($action, $allowedActions)) {
            $action = $allowedActions[0];
        }

        // Get the record ID :)
        $id = $this->getInput('id', null);

        switch ($action) {

            case 'trash':
                echo $this->setTrash($id, 'trash');
                break;

            case 'untrash':
                echo $this->setTrash($id, 'untrash');
                break;

            case 'star':
                echo $this->setStar($id, 'star');
                break;

            case 'unstar':
                echo $this->setStar($id, 'unstar');
                break;


            case 'index':
            default:
                echo $this->index();
                break;
        }
    }

    public function record_router()
    {

        $allowedActions = array('add', 'create', 'edit', 'update');
        $action         = ( isset($_GET['action']) ) ? $_GET['action'] : $allowedActions[0];
        if (!in_array($action, $allowedActions)) {
            $action = $allowedActions[0];
        }

        // Get the record ID :)
        $id = $this->getInput('id', null);

        switch ($action) {

            case 'create':
                echo $this->create();
                break;

            case 'edit':
                echo $this->edit();
                break;

            case 'update':
                echo $this->update();
                break;

            case 'add':
            default:
                echo $this->add();

        }
    }

    /**
     * Displays the index view for campaigns
     * @return string
     */
    private function index()
    {

        // Which record types are we after?
        $allowedTypes = array('all', 'stared', 'deleted');
        $type         = ( isset($_GET['type']) ) ? $_GET['type'] : $allowedTypes[0];
        if (!in_array($type, $allowedTypes)) {
            $type     = $allowedTypes[0];
        }

        // Get Data
        /** @var \Carbontwelve\InboundTracker\Models\Campaigns $model */
        $model = $this->app->getModel('campaigns');
        $data  = $model->getPaginated($type);

        // Get Counts
        $count = array(
            'all'      => $model->count('all'),
            'stared'   => $model->count('stared'),
            'trash'    => $model->count('deleted')
        );

        return $this->app->renderView(
            'campaigns.index',
            array(
                'pagination'    => $model->getPagination(),
                'data'          => $data,
                'type'          => $type,
                'count'         => $count,
                'flashMessages' => $this->flashMessages
            )
        );
    }

    private function add()
    {
        return $this->app->renderView(
            'campaigns.add',
            array(
                'flashMessages' => $this->flashMessages
            )
        );
    }

    private function create()
    {
        var_dump($this->flashMessages['inputs']);
    }

    private function setTrash( $id = null, $direction = 'trash')
    {
        /** @var \Carbontwelve\InboundTracker\Models\Campaigns $model */
        $model = $this->app->getModel('campaigns');

        // Check that the record can be deleted, as default ones should not be deletable!

        if ($model->canDelete($id) === true)
        {
            switch ($direction)
            {
                case 'trash':
                    $result = $model->update($id, array('deleted_at' => date('Y-m-d H:i:s')));

                    if ($result === false) {
                        $this->flashMessages['error'] = 'There was an error moving that campaign to the trash.';
                    } else {
                        $this->flashMessages['success'] = '1 campaign moved to the Trash. <a href="' . admin_url(
                            ) . 'admin.php?page=inbound_links_index&amp;action=untrash&amp;id=' . $id . '">Undo</a>';
                    }
                    break;

                case 'untrash':
                    $result = $model->update($id, array('deleted_at' => null));

                    if ($result === false) {
                        $this->flashMessages['error'] = 'There was an error moving that campaign to the trash.';
                    } else {
                        $this->flashMessages['success'] = '1 campaign moved to the Trash. <a href="' . admin_url(
                            ) . 'admin.php?page=inbound_links_index&amp;action=untrash&amp;id=' . $id . '">Undo</a>';
                    }
                    break;

                default:
                    $this->flashMessages['error'] = 'There was an error moving that campaign to the trash.';
            }
        }else{
            $this->flashMessages['error'] = 'You can\'t delete a default record.';
        }
        return $this->index();

    }

    private function setStar( $id = null, $direction = 'star')
    {
        /** @var \Carbontwelve\InboundTracker\Models\Campaigns $model */
        $model = $this->app->getModel('campaigns');

        switch ($direction)
        {
            case 'star':
                $result = $model->update($id, array('stared' => '1'));

                if ($result === false) {
                    $this->flashMessages['error'] = 'There was an error staring that campaign';
                } else {
                    $this->flashMessages['success'] = '1 campaign stared. <a href="' . admin_url(
                        ) . 'admin.php?page=inbound_links_index&amp;action=unstar&amp;id=' . $id . '">Undo</a>';
                }
                break;

            case 'unstar':
                $result = $model->update($id, array('stared' => '0'));

                if ($result === false) {
                    $this->flashMessages['error'] = 'There was an error unstaring that campaign.';
                } else {
                    $this->flashMessages['success'] = '1 campaigned unstared <a href="' . admin_url(
                        ) . 'admin.php?page=inbound_links_index&amp;action=star&amp;id=' . $id . '">Undo</a>';
                }
                break;

            default:
                $this->flashMessages['error'] = 'There was an error changing the star status of that campaign.';
        }

        return $this->index();
    }
}
