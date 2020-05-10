<?php

namespace Drupal\corona_live_tracker;

use Drupal\Component\Serialization\Json;

class CoronaApi {

  /**
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * CatFactsClient constructor.
   *
   * @param $http_client_factory \Drupal\Core\Http\ClientFactory
   */
  public function __construct($http_client_factory) {
    $this->client = $http_client_factory->fromOptions([
      'base_uri' => 'https://www.trackcorona.live/api/',
    ]);
  }

  /**
   * Get some random api facts.
   *
   *
   * @return array
   */
  public function countries() {
    $response = $this->client->get('countries', [
      'query' => [
      ]
    ]);

    return Json::decode($response->getBody());
  }
  
    /**
   * Get some random api facts.
   *
   *
   * @return array
   */
  public function states() {
    $response = $this->client->get('cities', [
      'query' => [
      ]
    ]);

    return Json::decode($response->getBody());
    
  }
  
      /**
   * Get some random api facts.
   *
   *
   * @return array
   */
  public function travel() {
    $response = $this->client->get('travel', [
      'query' => [
      ]
    ]);

    return Json::decode($response->getBody());
  }

}

