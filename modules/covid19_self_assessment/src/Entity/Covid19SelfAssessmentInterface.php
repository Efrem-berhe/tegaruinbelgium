<?php

namespace Drupal\covid19_self_assessment\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Covid19 self assessment entities.
 *
 * @ingroup covid19_self_assessment
 */
interface Covid19SelfAssessmentInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Covid19 self assessment name.
   *
   * @return string
   *   Name of the Covid19 self assessment.
   */
  public function getName();

  /**
   * Sets the Covid19 self assessment name.
   *
   * @param string $name
   *   The Covid19 self assessment name.
   *
   * @return \Drupal\covid19_self_assessment\Entity\Covid19SelfAssessmentInterface
   *   The called Covid19 self assessment entity.
   */
  public function setName($name);

  /**
   * Gets the Covid19 self assessment creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Covid19 self assessment.
   */
  public function getCreatedTime();

  /**
   * Sets the Covid19 self assessment creation timestamp.
   *
   * @param int $timestamp
   *   The Covid19 self assessment creation timestamp.
   *
   * @return \Drupal\covid19_self_assessment\Entity\Covid19SelfAssessmentInterface
   *   The called Covid19 self assessment entity.
   */
  public function setCreatedTime($timestamp);

}
