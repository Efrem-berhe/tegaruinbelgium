<?php

namespace Drupal\covid\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\charts\Services\ChartsSettingsServiceInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\covid\Repository\CovidRepository;

/**
 * Creates a 'Covid' Block.
 *
 * @Block(
 * id = "covid_charts_block",
 * admin_label = @Translation("Covid Charts Block"),
 * )
 */
class CovidChartsBlock extends BlockBase implements BlockPluginInterface, ContainerFactoryPluginInterface {

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
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected $covidRepo;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ChartsSettingsServiceInterface $chartSettings, MessengerInterface $messenger, UuidInterface $uuidService, CovidRepository $covidRepo) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->chartSettings = $chartSettings->getChartsSettings();
    $this->messenger = $messenger;
    $this->uuidService = $uuidService;
    $this->covidRepo = $covidRepo;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('charts.settings'),
      $container->get('messenger'),
      $container->get('uuid'),
      $container->get('covid.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['chart_type'] = [
      '#type' => 'select',
      '#default_value' => $config['chart_type'] ?? $this->chartSettings['type'] ?? '',
      '#title' => $this->t('Choose Chart Type'),
      '#options' => [
        '' => '-Select-',
        'geo' => 'Geo',
        'line' => 'Line',
      	'bar' => 'Bar',
      	'column' => 'Column',
      	'donut' => 'Donut',
      	'pie' => 'Pie',
      ],
      '#required' => TRUE
    ];
    $countries = $this->covidRepo->getCountries('jhu');
    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Choose Country'),
      '#default_value' => $config['country'] ?? '',
      '#states' => [
        'invisible' => [
          ':input[id="edit-settings-chart-type"]' => ['value' => 'geo'],
        ],
      ],
      '#options' => $countries,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('chart_type', $form_state->getValue('chart_type'));
    $this->setConfigurationValue('country', $form_state->getValue('country'));
    parent::blockSubmit($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $library = $this->chartSettings['library'];
    if (empty($library)) {
      $this->messenger->addError($this->t('You need to first configure Charts default settings'));
      return [];
    }
    // Customize options here.
    $categories = $this->covidRepo->getCountries();
    $totalCases = $this->covidRepo->getTotalCases();
    $countries_jhu = $this->covidRepo->getCountries('jhu');
    $chartType = $config['chart_type'] ?? 'geo';
    $options = [
      'type' => $config['chart_type'] ?? 'geo',
      'title' => $this->covidRepo->getJHUAPIDataByCountry($countries_jhu[$config['country']])['country'] ?? '',
      'colors' => $this->chartSettings['colors'],
      'colorAxis' => $this->chartSettings['colors'],
      'datalessRegionColor' => '#dedede',
      'legend' => ['position' => 'right'],
      'width' => '100%',
      'height' => '500',
      'three_dimensional' => TRUE,
      'legend' => TRUE,
    ];

    if ($chartType == 'geo') {
      $seriesData[] = [
        'name' => 'Cases',
        'color' => '#0d233a',
        'data' => array_column($totalCases, 'cases'),
      ];

      $seriesData[] = [
        'name' => 'Deaths',
        'data' => array_column($totalCases, 'deaths'),
        'color' => '#910000',
      ];
    } else {
      $countries = $this->covidRepo->getCountries('jhu');
      $casesDataAll = $this->covidRepo->getJHUAPIDataByCountry($countries[$config['country']]);
      $confirmedCountry = $casesDataAll['timeline']['deaths'];
      $deaths = array_values($confirmedCountry);
      $recovered = array_values($casesDataAll['timeline']['recovered']);
      $cases = array_values($casesDataAll['timeline']['cases']);
      $seriesData[] = [
        'name' => "Deaths",
        'color' => 'red',
        'data' => ($chartType != 'pie' && $chartType != 'donut') ? $deaths : [end($deaths)],
      ];
      $seriesData[] = [
        'name' => "Recovered",
        'color' => 'green',
        'data' => ($chartType != 'pie' && $chartType != 'donut') ? $recovered : [end($recovered)],
      ];
      $seriesData[] = [
        'name' => "Cases",
        'color' => 'orange',
        'data' => ($chartType != 'pie' && $chartType != 'donut') ? $cases : [end($cases)],
      ];
      $categories = array_keys($confirmedCountry);
    }

    // Creates a UUID for the chart ID.
    $chartId = 'chart-' . $this->uuidService->generate();

    $build = [
      '#theme' => 'covid',
      '#library' => (string) $library,
      '#categories' => $categories,
      '#seriesData' => $seriesData,
      '#options' => $options,
      '#id' => $chartId,
      '#override' => [],
      '#cache' => [
        'max-age' => 3600,
      ],
    ];
    $build['#attached']['library'][] = 'covid/covid_js';
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 10;
  }
}
