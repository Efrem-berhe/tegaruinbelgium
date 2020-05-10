<?php

namespace Drupal\covid19_self_assessment\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Covid19 self assessment type entity.
 *
 * @ConfigEntityType(
 *   id = "covid19_self_assessment_type",
 *   label = @Translation("Covid19 self assessment type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\covid19_self_assessment\Covid19SelfAssessmentTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\covid19_self_assessment\Form\Covid19SelfAssessmentTypeForm",
 *       "edit" = "Drupal\covid19_self_assessment\Form\Covid19SelfAssessmentTypeForm",
 *       "delete" = "Drupal\covid19_self_assessment\Form\Covid19SelfAssessmentTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\covid19_self_assessment\Covid19SelfAssessmentTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "covid19_self_assessment_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "covid19_self_assessment",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/covid19_self_assessment_type/{covid19_self_assessment_type}",
 *     "add-form" = "/admin/structure/covid19_self_assessment_type/add",
 *     "edit-form" = "/admin/structure/covid19_self_assessment_type/{covid19_self_assessment_type}/edit",
 *     "delete-form" = "/admin/structure/covid19_self_assessment_type/{covid19_self_assessment_type}/delete",
 *     "collection" = "/admin/structure/covid19_self_assessment_type"
 *   }
 * )
 */
class Covid19SelfAssessmentType extends ConfigEntityBundleBase implements Covid19SelfAssessmentTypeInterface {

  /**
   * The Covid19 self assessment type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Covid19 self assessment type label.
   *
   * @var string
   */
  protected $label;

}
