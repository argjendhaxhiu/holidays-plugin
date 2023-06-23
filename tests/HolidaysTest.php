<?php

declare(strict_types=1);

require_once(dirname(__FILE__) . '/wp-load.php');
require_once(__DIR__ . '/../lib/Holidays.php');

use PHPUnit\Framework\TestCase;

final class HolidaysTest extends TestCase
{
  private $holidays;

  public function setUp(): void
  {
    parent::setUp();
    $this->holidays = new Holidays();
  }

  public function testItRegistersDestinationPostType(): void
  {
    $this->holidays->registerPostType();
    $this->assertTrue(post_type_exists(Holidays::POST_TYPE_DESTINATION));
  }

  public function testItRendersHolidayDestinations(): void
  {
    $destinations = [
      (object) [
        'post_title' => 'Alaska',
        'ID' => 1
      ],
      (object) [
        'post_title' => 'Antigua',
        'ID' => 2
      ],
      (object) [
        'post_title' => 'Cuba',
        'ID' => 3
      ],
      (object) [
        'post_title' => 'Cullercoats',
        'ID' => 4
      ],
      (object) [
        'post_title' => 'Kenya',
        'ID' => 5
      ],
      (object) [
        'post_title' => 'Key West',
        'ID' => 6
      ]
    ];

    $expectedOutput = '<h2>A</h2><ul class="holidays-destinations"><li class="title"><a href="' . self::getBaseURL() . '?destination=alaska">Alaska</a></li><li class="title"><a href="' . self::getBaseURL() . '?destination=antigua">Antigua</a></li></ul><h2>C</h2><ul class="holidays-destinations"><li class="title"><a href="' . self::getBaseURL() . '?destination=cuba">Cuba</a></li><li class="title"><a href="' . self::getBaseURL() . '?destination=cullercoats">Cullercoats</a></li></ul><h2>K</h2><ul class="holidays-destinations"><li class="title"><a href="' . self::getBaseURL() . '?destination=kenya">Kenya</a></li><li class="title"><a href="' . self::getBaseURL() . '?destination=key-west">Key West</a></li></ul>';

    $this->assertEquals($expectedOutput, $this->holidays->renderHolidayDestinations($destinations));
  }

  public function testItRegistersRestEndpoints(): void
  {
    $this->holidays->registerRestEndpoints();
    $server = rest_get_server();
    $routes = $server->get_routes();

    $this->assertTrue(isset($routes['/v1/holidays/deals']));
    $this->assertTrue(isset($routes['/v1/holidays/request_brochure']));
  }

  public function testItReturnsDealsEndpoint(): void
  {
    $response = $this->holidays->getDealsEndpoint(new WP_REST_Request());
    $this->assertIsArray($response);
    $this->assertNotEmpty($response);
  }

  private static function getBaseURL(): string
  {
    return 'http://localhost/fresh-wp/';
  }
}
