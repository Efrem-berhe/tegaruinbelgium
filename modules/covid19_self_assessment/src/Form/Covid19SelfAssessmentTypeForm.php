<?php

namespace Drupal\covid19_self_assessment\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class Covid19SelfAssessmentTypeForm.
 */
class Covid19SelfAssessmentTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $covid19_self_assessment_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $covid19_self_assessment_type->label(),
      '#description' => $this->t("Label for the Covid19 self assessment type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $covid19_self_assessment_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\covid19_self_assessment\Entity\Covid19SelfAssessmentType::load',
      ],
      '#disabled' => !$covid19_self_assessment_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $covid19_self_assessment_type = $this->entity;
    $status = $covid19_self_assessment_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Covid19 self assessment type.', [
          '%label' => $covid19_self_assessment_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Covid19 self assessment type.', [
          '%label' => $covid19_self_assessment_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($covid19_self_assessment_type->toUrl('collection'));
  }

}
