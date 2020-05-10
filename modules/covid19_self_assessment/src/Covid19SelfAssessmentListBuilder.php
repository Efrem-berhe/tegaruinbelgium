<?php

namespace Drupal\covid19_self_assessment;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Covid19 self assessment entities.
 *
 * @ingroup covid19_self_assessment
 */
class Covid19SelfAssessmentListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Covid19 self assessment ID');
    $header['name'] = $this->t('Name');
    $header['bundle_id'] = $this->t('Bundle');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\covid19_self_assessment\Entity\Covid19SelfAssessment $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.covid19_self_assessment.edit_form',
      ['covid19_self_assessment' => $entity->id()]
    );
    $row['bundle_id'] = $entity->bundle();
    return $row + parent::buildRow($entity);
  }

}
