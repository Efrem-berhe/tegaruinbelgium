<?php

namespace Drupal\covid19_self_assessment;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Covid19 self assessment entity.
 *
 * @see \Drupal\covid19_self_assessment\Entity\Covid19SelfAssessment.
 */
class Covid19SelfAssessmentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\covid19_self_assessment\Entity\Covid19SelfAssessmentInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished covid19 self assessment entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published covid19 self assessment entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit covid19 self assessment entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete covid19 self assessment entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add covid19 self assessment entities');
  }

}
