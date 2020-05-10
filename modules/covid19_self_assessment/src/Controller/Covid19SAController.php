<?php

namespace Drupal\covid19_self_assessment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\PrivateTempStoreFactory;

/**
 * Class Covid19SAController.
 */
class Covid19SAController extends ControllerBase {
  /**
   * {@inheritdoc}
   */
  protected $tempStore;

  /**
   * {@inheritdoc}
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory) {
    $this->tempStore = $temp_store_factory->get('covid19_SA');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore')
    );
  }

  /**
   * Covid19SAResult.
   *
   * @return risk
   *   Return results.
   */
  public function covid19SaResult() {
    $temp_value = $this->tempStore->get('risk');
    $temp_risk_message = $this->tempStore->get('risk_message');
    return [
      '#theme' => 'covid19_self_assess',
      '#risk' => $temp_value,
      '#risk_message' => $temp_risk_message,
      '#attached' => [
        'library' => [
          'covid19_self_assessment/covid19_self_assessment.css',
        ],
      ],
    ];
  }

}
