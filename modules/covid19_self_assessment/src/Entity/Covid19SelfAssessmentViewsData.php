<?php

namespace Drupal\covid19_self_assessment\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Covid19 self assessment entities.
 */
class Covid19SelfAssessmentViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
