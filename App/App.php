<?php namespace Carbontwelve\InboundTracker;

/**
 * Class App
 * @package Carbontwelve\ButtonBoard\App
 */
Class App
{

    /**
     * The App framework version
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /** @var null|string Absolute path to App directory */
    protected $path;

    /** @var null|string URL To plugin root */
    protected $pluginUrl;

    /**
     * The View Service Provider
     * @var \Carbontwelve\ButtonBoard\View
     */
    protected $view;

    /**
     * Loaded Models
     * @var array
     */
    protected $models = array();

    /**
     * Wordpress Database Class
     * @var \wpdb
     * */
    protected $wpdb;

    /**
     * Wordpress Rewrite Service Provider
     * @var \Carbontwelve\ButtonBoard\Rewrite
     */
    protected $rewriter;


    /**
     * Setup our plugin environment
     * @param string|null $path
     * @param string|null $pluginUrl
     */
    public function __construct($path = null, $pluginUrl = null, $config = array())
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $this->wpdb      = $wpdb;
        $this->path      = $path;
        $this->pluginUrl = $pluginUrl;
        $this->view      = new View($this->path . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR);
        //$this->rewriter  = new Rewrite($this);

        foreach ($config as $key => $value)
        {
            $this[$key] = $value;
        }
    }

    /**
     * Factory method for initiating our models
     *
     * @param $className
     * @param $class
     */
    public function registerModel($className, $class)
    {
        $this->models[$className] = new $class($this->wpdb, $this);
    }

    public function getRewriter()
    {
        return $this->rewriter;
    }

    /**
     * Method for returning a model
     *
     * @param $className
     * @return mixed
     */
    public function getModel($className)
    {
        return $this->models[$className];
    }

    /**
     * Returns Plugins Absolute Path
     * @return null|string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns Plugins URL
     * @return null|string
     */
    public function getPluginUrl()
    {
        return $this->pluginUrl;
    }

    /**
     * Method run when plugin is installed/upgraded
     *
     * @param string $version
     */
    public function install($version = "1.0.0")
    {
        if (count($this->models) > 0) {
            foreach ($this->models as $model) {
                $model->install();
            }
        }

        if ($version === false) {
            add_option("carbontwelve_inboundtracker_version", $version);
        } else {
            update_option("carbontwelve_inboundtracker_version", $version);
        }
    }

    /**
     * Render a given view file and return the result
     * @param  string $template
     * @param  array $data
     * @return string
     */
    public function renderView($template = '', $data = array())
    {
        return $this->view->render($template, $data);
    }

    /**
     * Dynamically access application services.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically set application services.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }

}
