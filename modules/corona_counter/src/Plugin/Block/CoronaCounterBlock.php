<?php

namespace Drupal\corona_counter\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'CoronaCounterBlock' block.
 *
 * @Block(
 *  id = "corona_counter_block",
 *  admin_label = @Translation("Corona Counter"),
 * )
 */
class CoronaCounterBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * Guzzle\Client instance.
   *
   * @var \Guzzle\Client
   */
  protected $httpClient;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Guzzle\Client $httpClient
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $httpClient) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $httpClient;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 300;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $request = $this->httpClient->request('GET', 'https://api.covid19api.com/countries');

    if ($request->getStatusCode() != 200) {
      return $form;
    }
    $countries = json_decode($request->getBody()->getContents());
    $options = [];
    foreach ($countries as $country) {
      $options[$country->Slug] = $country->Country;
    }

    $form['countries'] = [
      '#type' => 'select',
      '#title' => $this->t('Countries'),
      '#description' => $this->t('Choose the counrties you want to show thier stat'),
      '#options' => $options,
      '#default_value' => $this->configuration['countries'],
      '#multiple' => TRUE,
      '#required' => TRUE,
    ];

    $form['show_all_countries'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show all countries statistics'),
      '#default_value' => $this->configuration['show_all_countries'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['countries'] = $form_state->getValue('countries');
    $this->configuration['show_all_countries'] = $form_state->getValue('show_all_countries');
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   A render array used to show country and cases
   */
  public function build() {
    try {
      if ($this->configuration['show_all_countries']) {
        $request = $this->httpClient->request('GET', 'https://corona.lmao.ninja/all');
        $countries_stat = json_decode($request->getBody()->getContents());

        $data['all_countries'] = [
          'name' => $this->t('Coronavirus Cases'),
          'cases' => $countries_stat->cases,
          'deaths' => $countries_stat->deaths,
          'recovered' => $countries_stat->recovered,
        ];
      }

      $countries = $this->configuration['countries'];
      foreach ($countries as $country) {
        $request = $this->httpClient->request('GET', 'https://corona.lmao.ninja/countries/' . $country . '');
        $country_stat = json_decode($request->getBody()->getContents());

        $data[$country] = [
          'name' => $country_stat->country,
          'cases' => $country_stat->cases,
          'deaths' => $country_stat->deaths,
          'recovered' => $country_stat->recovered,
        ];
      }
      return [
        '#theme' => 'corona_counter_block',
        '#content' => $data,
        '#attached' => [
          'library' => [
            'corona_counter/block_style',
          ],
        ],
      ];
    } catch (GuzzleException $e) {
      return [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => t("Something unexpected happened, please contact the administrators if the problem presists"),
      ];
    }
  }
}
