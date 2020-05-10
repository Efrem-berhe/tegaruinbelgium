<?php

namespace Drupal\covid\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;
use Drupal\Component\Datetime\TimeInterface;

class CovidRepository {

  const COVID_JHU_API_URL = 'https://corona.lmao.ninja/v2/historical';
  const COVID_API_URL = 'http://corona.lmao.ninja/countries?sort=country';

  public $connection;

  public $logger;

  protected $http_client;

  protected $dateTime;

  public function __construct(Connection $connection, Client $http_client, LoggerChannelFactory $logger, TimeInterface $dateTime) {
    $this->connection = $connection;
    $this->httpClient = $http_client;
    $this->logger = $logger->get('covid');
    $this->dateTime = $dateTime;
  }

  public function getCountries($api = NULL) {
    try {
      $apiData = ($api == 'jhu') ? $this->getJHUTotalCases() : $this->getTotalCases();
      $countries = array_unique(array_keys($apiData));
      $usaKey = array_search("USA", $countries);
      if ($usaKey !== NULL) {
        $countries[$usaKey] = "United States";
      }
    } catch(\Exception $e) {
      $this->logger->error($e->getMessage());
      drupal_set_message($e->getMessage());
    }
    return $countries ?? [];
  }

  public function getJHUTotalCases() {
    try {
      $casesDatas = $this->getJHUAPIData();
      $countryCases = [];
      foreach($casesDatas as $cases) {
        if (isset($countryCases[$cases['country']])) {
          $countryCases[$cases['country']]['cases'] += end($cases['timeline']['cases']);
          $countryCases[$cases['country']]['deaths'] += end($cases['timeline']['deaths']);
          $countryCases[$cases['country']]['recovered'] += end($cases['timeline']['recovered']);
        } else {
          $countryCases[$cases['country']] = [
            'cases' => end($cases['timeline']['cases']),
            'deaths' => end($cases['timeline']['deaths']),
            'recovered' => end($cases['timeline']['recovered'])
          ];
        }
      }
    } catch(\Exception $e) {
      $this->logger->error($e->getMessage());
      drupal_set_message($e->getMessage());
    }
    return $countryCases ?? [];
  }

  public function getTotalCases() {
    try {
      $casesDatas = $this->getDefaultAPIData();
      $countryCases = [];
      foreach($casesDatas as $cases) {
        $countryCases[$cases['country']]['cases'] = $cases['cases'];
        $countryCases[$cases['country']]['deaths'] = $cases['deaths'];
        $countryCases[$cases['country']]['recovered'] = $cases['recovered'];
      }
    } catch(\Exception $e) {
      $this->logger->error($e->getMessage());
      drupal_set_message($e->getMessage());
    }
    return $countryCases ?? [];
  }

  // not in use for now as approch is changed
  public function getDefaultAPIData() {
    $cache = \Drupal::cache();
    if (($apiCacheData = $cache->get('covid_default_data'))) {
      $apiData = $apiCacheData->data;
    } else {
      $request = $this->httpClient->get(self::COVID_API_URL);
      $body = $request->getBody();
      $apiData = Json::decode($body);
      $time = $this->dateTime->getRequestTime();
      $resync_time = \Drupal::config('covid.settings')->get('resync_time') ?? 0;
      $cache->set('covid_default_data', $apiData, $time + ($resync_time * 60), ['covid:default']);
    }
    return $apiData;
  }

  // not in use for now as approch is changed
  public function getJHUAPIData() {
    $cache = \Drupal::cache();
    if (($apiCacheData = $cache->get('covid_jhucsse_data'))) {
      $apiData = $apiCacheData->data;
    } else {
      $request = $this->httpClient->get(self::COVID_JHU_API_URL);
      $body = $request->getBody();
      $apiData = Json::decode($body);
      $time = $this->dateTime->getRequestTime();
      $resync_time = \Drupal::config('covid.settings')->get('resync_time') ?? 0;
      $cache->set('covid_jhucsse_data', $apiData, $time + ($resync_time * 60), ['covid:jhucsse']);
    }
    return $apiData;
  }

  public function getJHUAPIDataByCountry(string $country) {
    $request = $this->httpClient->get(self::COVID_JHU_API_URL . '/' .$country .'?lastdays=7');
    $body = $request->getBody();
    $apiData = Json::decode($body);
    return $apiData;
  }

}
