<?php

namespace Drupal\covid19_self_assessment\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class Covid19ConfigForm.
 */
class Covid19ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['covid19_self_assessment.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'covid19_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('covid19_self_assessment.settings');
    $form['high_risk'] = [
      '#type' => 'textarea',
      '#title' => $this->t('High Risk'),
      '#description' => $this->t('Enter high risk message here.'),
      '#default_value' => $config->get('high_risk'),
    ];
    $form['medium_risk'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Medium Risk'),
      '#description' => $this->t('Enter medium risk message here.'),
      '#default_value' => $config->get('medium_risk'),
    ];
    $form['low_risk'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Low Risk'),
      '#description' => $this->t('Enter low risk message here.'),
      '#default_value' => $config->get('low_risk'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('covid19_self_assessment.settings')
      ->set('high_risk', $form_state->getValue('high_risk'))
      ->set('medium_risk', $form_state->getValue('medium_risk'))
      ->set('low_risk', $form_state->getValue('low_risk'))
      ->save();
  }

}
