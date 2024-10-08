<?php
function ciccc_register_rest_routes() {
    register_rest_route('ciccc-connect/v1', '/events', array(
        'methods' => 'GET',
        'callback' => 'ciccc_get_events',
        'permission_callback' => '__return_true',
    ));
}

function ciccc_get_events($request) {
    // Debugging: Log to WordPress debug.log
    error_log('ciccc_get_events function called');

    // Get parameters from the request
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 10;
    $orderby = $request->get_param('orderby') ? $request->get_param('orderby') : 'date';
    $order = $request->get_param('order') ? strtoupper($request->get_param('order')) : 'DESC';
    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;

    $api_url = $request->get_param('api_url');
    if (!$api_url) {
        $api_url = defined('CICCC_DEFAULT_API_URL') ? CICCC_DEFAULT_API_URL : '';
        if (!$api_url) {
            return new WP_Error('invalid_api_url', 'API URL is required', array('status' => 400));
        }
    }

    // Add query parameters to the API URL
    $api_url = add_query_arg(array(
        'per_page' => $per_page,
        'orderby' => $orderby,
        'order' => $order,
        'page' => $page,
    ), $api_url);

    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        error_log('API request failed: ' . $response->get_error_message());
        return new WP_Error('api_error', 'Failed to fetch events', array('status' => 500));
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data['events']) || !is_array($data['events'])) {
        error_log('Invalid API response: ' . print_r($data, true));
        return new WP_Error('api_error', 'Invalid API response', array('status' => 500));
    }

    // Limit the number of events to $per_page
    $events = array_slice($data['events'], 0, $per_page);

    error_log('API request successful, returning ' . count($events) . ' events');
    return new WP_REST_Response($events, 200);
}