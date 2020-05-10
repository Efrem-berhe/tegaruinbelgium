<?php

namespace Drupal\covid_charts\Controller;

use Drupal\charts\Services\ChartsSettingsServiceInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Database\Connection;
use Drupal\covid\Repository\CovidRepository;

/**
 * Covid 19 Data Visualization.
 */
class CovidVisualization extends ControllerBase {

  /**
   * The charts settings.
   *
   * @var \Drupal\charts\Services\ChartsSettingsServiceInterface
   */
  protected $chartSettings;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The UUID service.
   *
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected $uuidService;

  /**
   * The UUID service.
   *
   * @var Drupal\covid\Repository\CovidRepository
   */
  protected $covidRepo;

  /**
   * Construct.
   *
   * @param \Drupal\charts\Services\ChartsSettingsServiceInterface $chartSettings
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   * @param \Drupal\Component\Uuid\UuidInterface $uuidService
   * @param \Drupal\covid\Repository\CovidRepository
   */
  public function __construct(ChartsSettingsServiceInterface $chartSettings, MessengerInterface $messenger, UuidInterface $uuidService, Connection $database, CovidRepository $covidRepo) {
    $this->chartSettings = $chartSettings->getChartsSettings();
    $this->messenger = $messenger;
    $this->uuidService = $uuidService;
    $this->dbConnection = $database;
    $this->covidRepo = $covidRepo;
  }

  /**
   * Display.
   *
   * @return array
   *   Array to render.
   */
  public function display() {
    $library = $this->chartSettings['library'];
    if (empty($library)) {
      $this->messenger->addError($this->t('You need to first configure Charts default settings'));
      return [];
    }

    $casesDataAll = $this->covidRepo->getCountries();
    $casesDataFinal = [];
    foreach($casesDataAll as $key => $casesData) {
      $casesDataFinal[$casesData->country] = [
        'confirmed' => isset($casesDataFinal[$casesData->country]['confirmed']) ? ($casesData->confirmed + $casesDataFinal[$casesData->country]['confirmed']) : ($casesData->confirmed),
        'deaths' => isset($casesDataFinal[$casesData->country]['deaths']) ? ($casesData->deaths + $casesDataFinal[$casesData->country]['deaths']) : ($casesData->deaths)
      ];
    }
    $categories = array_keys($casesDataFinal);
    $usaKey = array_search("USA", $categories);
    if ($usaKey >= 0) {
      $categories[$usaKey] = "United States";
    }
    $confirmed = array_column($casesDataFinal, 'confirmed');
    $deaths = array_column($casesDataFinal, 'deaths');
    //$categories = ["India", "United States", "United Kingdom"];
    // Customize options here.
    $options = [
      'type' => $config['chart_type'] ?? 'geo',
      'title' => 'Covid-19 Global Visualization',
      'colors' => $this->chartSettings['colors'],
      'colorAxis' => $this->chartSettings['colors'],
      'datalessRegionColor' => '#dedede',
      'legend' => TRUE,
    ];

    // Sample data format.
    $seriesData[] = [
      'name' => 'Confirmed',
      'color' => '#0d233a',
      'data' => $confirmed,
    ];
    $seriesData[] = [
      'name' => 'Deaths',
      'data' => $deaths,
      'color' => '#910000',
    ];
    // Creates a UUID for the chart ID.
    $chartId = 'chart-' . $this->uuidService->generate();

    $build = [
      '#theme' => 'covid_charts',
      '#library' => (string) $library,
      '#categories' => $categories,
      '#seriesData' => $seriesData,
      '#options' => $options,
      '#id' => $chartId,
      '#override' => [],
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('charts.settings'),
      $container->get('messenger'),
      $container->get('uuid'),
      $container->get('database'),
      $container->get('covid.repository')
    );
  }

}
