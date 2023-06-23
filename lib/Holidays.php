<?php

class Holidays
{
    public const POST_TYPE_DESTINATION = 'destination';

    public function __construct()
    {
        $this->registerEventListeners();
    }

    private function registerEventListeners(): void
    {
        add_action('init', [$this, 'registerPostType']);
        add_shortcode('holiday-destinations', [$this, 'renderHolidayDestinations']);
        add_action('rest_api_init', [$this, 'registerRestEndpoints']);
        add_action('init', [$this, 'enqueueStyle']);
    }

    public function registerPostType(): void
    {
        register_post_type(self::POST_TYPE_DESTINATION, [
            'labels' => [
                'name' => __('Destinations'),
                'singular_name' => __('Destination'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'revisions'],
            'rewrite' => false,
        ]);
    }

    public function renderHolidayDestinations(): string
    {
        $destinations = $this->getDestinations();
        $output = [];
        $currentLetter = '';

        foreach ($destinations as $destination) {
            $title = $destination->post_title;
            $firstLetter = strtoupper(substr($title, 0, 1));

            if ($firstLetter !== $currentLetter) {
                if (!empty($output)) {
                    $output[] = '</ul>';
                }
                $currentLetter = $firstLetter;
                $output[] = '<h2>' . $currentLetter . '</h2><ul class="holidays-destinations">';
            }

            $output[] = '<li class="title"><a href="' . get_permalink($destination->ID) . '">' . esc_html($title) . '</a></li>';
        }

        if (!empty($output)) {
            $output[] = '</ul>';
        }

        return implode('', $output);
    }

    private function getDestinations(): array
    {
        $args = [
            'post_type' => self::POST_TYPE_DESTINATION,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
        ];

        return get_posts($args);
    }

    public function registerRestEndpoints(): void
    {
        register_rest_route('/v1/holidays', '/deals', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [$this, 'getDealsEndpoint'],
        ]);

        register_rest_route('/v1/holidays', '/request_brochure', [
            'methods' => 'POST',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
            'callback' => [$this, 'createBrochureRequest'],
            'args' => $this->getBrochureRequestArgs(),
        ]);
    }

    private function getBrochureRequestArgs(): array
    {
        return [
            'name' => [
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'is_string',
            ],
            'email' => [
                'required' => true,
                'sanitize_callback' => 'sanitize_email',
                'validate_callback' => 'is_email',
            ],
            'address' => [
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'is_string',
            ],
        ];
    }

    public function getDealsEndpoint($request)
    {
        $deals = $this->getDeals();
        return $deals;
    }

    private function getDeals(): array
    {
        return [
            [
                'id' => 1872,
                'title' => 'Deal of the week'
            ],
            [
                'id' => 1578,
                'title' => 'Winter sun'
            ],
            [
                'id' => 1238,
                'title' => 'Ski unlimited'
            ],
        ];
    }

    public function createBrochureRequest(WP_REST_Request $request)
    {
        $name = $request->get_param('name');
        $email = $request->get_param('email');
        $address = $request->get_param('address');

        try {
            $this->sendBrochureApiRequest($name, $address);
            $message = "
        A user has requested a holiday brochure<BR>
        Name: $name<BR>
        Email: $email<BR>
        Address: $address<BR>";
            wp_mail('holidays@argjend.com', 'Brochure request', $message);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return new WP_Error('request_failed', __('There was an error processing your request. Please try again later.'), ['status' => 500]);
        }
    }

    private function sendBrochureApiRequest(string $name, string $address): void
    {
        $apiKey = 'EXAMPLE-API-KEY';
        $endpoint = 'https://example-api-domain.com/v1/brochure-request';
        $data = [
            'name' => $name,
            'address' => $address,
        ];
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'json' => $data,
            ]);

            $responseBody = (string) $response->getBody();
            $responseData = json_decode($responseBody, true);

            if (isset($responseData['error'])) {
                throw new Exception($responseData['error']);
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->logError($e->getMessage());
            throw new Exception(__('There was an error processing your request. Please try again later.'));
        }
    }

    function enqueueStyle()
    {
        wp_enqueue_style('holidays-style', plugin_dir_url(dirname(__FILE__)) . 'app.css', array(), '1.0.0', 'all');
    }

    /**
     * Error logging placeholder
     * You shouldn't need to change this method
     * @param $msg
     */
    private function logError($msg)
    {
        //...assume there is some error logging logic here
    }
}
