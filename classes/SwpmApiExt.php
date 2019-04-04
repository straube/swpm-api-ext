<?php

/**
 * Simple Membership API (Extended), WordPress plugin.
 *
 * Core features.
 *
 * @author  Gustavo Straube <https://github.com/straube>
 * @version 0.1.0
 * @package swpm-api-ext
 */
class SwpmApiExt
{

    /**
     * The plugin version.
     *
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     * The plugin instance.
     *
     * @var \SwpmApiExt
     */
    private static $instance;

    /**
     * Get the single plugin instance.
     *
     * @return \SwpmApiExt
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Creates a new plugin instance.
     *
     * It hooks the plugin to WordPress through actions, filters, etc.
     */
    private function __construct()
    {
        $this->hookActions();
    }

    /**
     * Hooks the required actions to WordPress.
     *
     * @return void
     */
    private function hookActions()
    {
         add_action('init', [ $this, 'handleRequest' ]);
    }

    /**
     * Handles a request to the API.
     *
     * @return void
     */
    public function handleRequest()
    {
        $action = $this->getAction();

        /*
         * This is probably not a request to the API.
         */
        if (empty($action)) {
            return;
        }

        $this->validateKey();

        if ($action === 'levels') {
            $this->processLevels();
        }
    }

    /**
     * Send the response to client and ends the request (exit script execution).
     *
     * @param  array $data
     * @param  bool $success
     * @return void
     */
    private function sendResponse($data, $success = true)
    {
        $result = $success ? 'success' : 'failure';
        $data += [
            'result' => $result,
        ];

	    ob_end_clean();

        if (!headers_sent()) {
            header('Content-type: application/json');
        }

	    echo json_encode($data);
	    exit;
    }

    /**
     * Get the API action.
     *
     * In case no action was provided in the request, it returns `null`.
     *
     * @return string|null
     */
    private function getAction()
    {
        return !empty($_REQUEST['swpm_api_ext_action']) ? $_REQUEST['swpm_api_ext_action'] : null;
    }

    /**
     * Validates the API key sent in the request.
     *
     * @return void
     */
    private function validateKey()
    {
        $settings = SwpmSettings::get_instance();

        if (empty($_REQUEST['key'])) {
            $this->sendResponse([
                'message' => __('No API key provided', 'swpm-api-ext'),
            ], false);
        }

        $key = $settings->get_value('swpm-addon-api-key');

        if ($key !== $_REQUEST['key']) {
            $this->sendResponse([
                'message' => __('Invalid API key', 'swpm-api-ext'),
            ], false);
        }
    }

    //
    // ACTIONS
    //

    /**
     * Process the `levels` action.
     *
     * @return void
     */
    private function processLevels()
    {
        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}swpm_membership_tbl WHERE id <> 1";
        $levels = $wpdb->get_results($query);

        $this->sendResponse([
            'message' => __('Levels found', 'swpm-api-ext'),
            'data' => $levels,
        ]);
    }
}
