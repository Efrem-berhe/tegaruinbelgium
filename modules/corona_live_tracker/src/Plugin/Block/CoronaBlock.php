<?php

namespace Drupal\corona_live_tracker\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Block of Cat Facts... you can't make this stuff up.
 *
 * @Block(
 *   id = "corona_tracker_block",
 *   admin_label = @Translation("Corona Live Tracker Block")
 * )
 */
class CoronaBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\cat_facts\CatFactsClient
   */
  protected $coronaservice;

  /**
   * CatFacts constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param $corona_api \Drupal\corona_live_tracker\CoronaApi
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $corona_api) 
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->coronaservice = $corona_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('corona_api')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $countries = $this->coronaservice->countries();
    $states = $this->coronaservice->states();
    $travel = $this->coronaservice->travel();
    return [
      '#theme' => 'corona_live_tracker',
      '#countriesdata' => $countries,
      '#statedata' => $states,
      '#traveldata' => $travel,
    ];
  }

}
