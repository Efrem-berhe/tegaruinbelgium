<?php

namespace Drupal\covid19_self_assessment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\PrivateTempStoreFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class Covid19SAForm.
 */
class Covid19SAForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  protected $tempStore;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'covid19_s_a_form';
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->tempStore = $temp_store_factory->get('covid19_SA');
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $symptoms_entity = $this->entityTypeManager->getStorage('covid19_self_assessment')->loadByProperties(['type' => 'symptoms']);
    foreach ($symptoms_entity as $key => $value) {
      $symptoms_options['none'] = 'None of these';
      $symptoms_options[$key] = $value->load($key)->getName();
    }
    $additional_symptoms_entity = $this->entityTypeManager->getStorage('covid19_self_assessment')->loadByProperties(['type' => 'additional_symptoms']);
    foreach ($additional_symptoms_entity as $key => $value) {
      $additional_symptoms_options['none'] = 'None of these';
      $additional_symptoms_options[$key] = $value->load($key)->getName();
    }
    $pre_existing_diseases_entity = $this->entityTypeManager->getStorage('covid19_self_assessment')->loadByProperties(['type' => 'pre_existing_diseases']);
    foreach ($pre_existing_diseases_entity as $key => $value) {
      $pre_existing_diseases_options['none'] = 'None of these';
      $pre_existing_diseases_options[$key] = $value->load($key)->getName();
    }
    $form['age'] = [
      '#type' => 'number',
      '#title' => $this->t('Enter Age'),
      '#required' => TRUE,
    ];
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select Gender'),
      '#options' => [
        'male' => $this->t('Male'),
        'female' => $this->t('Female'),
        'other' => $this->t('Other'),
      ],
      '#required' => TRUE,
    ];
    $form['body_temperature'] = [
      '#type' => 'radios',
      '#title' => $this->t('Body Temperature'),
      '#options' => [
        'normal' => $this->t('Normal (96F-98.6F)'),
        'fever' => $this->t('Fever (98.6F-102F)'),
        'high_fever' => $this->t('High Fever (>102F)'),
        'do_not_know' => $this->t("Don't know"),
      ],
      '#required' => TRUE,
    ];
    $form['symptoms'] = [
      '#type' => 'checkboxes',
      '#multiple' => 'TRUE',
      '#title' => $this->t('Symptoms'),
      '#options' => $symptoms_options,
      '#required' => TRUE,
    ];
    $form['additional_symptoms'] = [
      '#type' => 'checkboxes',
      '#multiple' => 'TRUE',
      '#title' => $this->t('Additional symptoms'),
      '#options' => $additional_symptoms_options,
      '#required' => TRUE,
    ];
    $form['travel_exposure'] = [
      '#type' => 'radios',
      '#title' => $this->t('Travel &amp; exposure'),
      '#options' => [
        'no_travel_history' => $this->t('No travel history'),
        'no_contact_with_anyone_with_symptoms' => $this->t('No contact with anyone with symptoms'),
        'travel_history' => $this->t('History of travel or meeting in affected geographical area in last few days'),
        'close_contact' => $this->t('Close Contact with confirmed COVID in last 14 days'),
      ],
      '#required' => TRUE,
    ];
    $form['pre_existing_diseases'] = [
      '#type' => 'checkboxes',
      '#multiple' => 'TRUE',
      '#title' => $this->t('Pre-existing disease(s)'),
      '#options' => $pre_existing_diseases_options,
      '#required' => TRUE,
    ];
    $form['progress_of_symptoms'] = [
      '#type' => 'radios',
      '#title' => $this->t('Progress of symptoms in last 48 hrs'),
      '#options' => [
        'improved' => $this->t('Improved'),
        'no_change' => $this->t('No change'),
        'worsened' => $this->t('Worsened'),
        'worsened_considerably' => $this->t('Worsened considerably'),
      ],
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $body_temperature = $form_state->getValues()['body_temperature'];
    $symptoms = $form_state->getValues()['symptoms'];
    $additional_symptoms = $form_state->getValues()['additional_symptoms'];
    $travel_exposure = $form_state->getValues()['travel_exposure'];
    $pre_existing_diseases = $form_state->getValues()['pre_existing_diseases'];
    $progress_of_symptoms = $form_state->getValues()['progress_of_symptoms'];
    if (in_array('none', $symptoms, TRUE)) {
      $symptoms_count = 0;
    }
    else {
      $symptoms_count = count(array_filter($symptoms));
    }
    if (in_array('none', $additional_symptoms, TRUE)) {
      $additional_symptoms_count = 0;
    }
    else {
      $additional_symptoms_count = count(array_filter($additional_symptoms));
    }
    if (in_array('none', $pre_existing_diseases, TRUE)) {
      $pre_existing_diseases_count = 0;
    }
    else {
      $pre_existing_diseases_count = count(array_filter($pre_existing_diseases));
    }
    $covid19config = $this->config('covid19_self_assessment.settings');
    if (($additional_symptoms_count >= 1) || ((($body_temperature == 'fever' || $body_temperature == 'high_fever') && ($symptoms >= 2)) && ($progress_of_symptoms == 'worsened' || $progress_of_symptoms == 'worsened_considerably')) || $travel_exposure == 'travel_history' || $travel_exposure == 'close_contact') {
      $risk_message = $covid19config->get('high_risk');
      $risk = 'high';
    }
    elseif (($body_temperature == 'normal' || $body_temperature == 'do_not_know') && in_array('none', $symptoms, TRUE) && in_array('none', $additional_symptoms, TRUE) && ($travel_exposure == 'no_travel_history' || $travel_exposure == 'no_contact_with_anyone_with_symptoms') && in_array('none', $pre_existing_diseases, TRUE) && $progress_of_symptoms == 'no_change') {
      $risk_message = $covid19config->get('low_risk');
      $risk = 'low';
    }
    else {
      $risk_message = $covid19config->get('medium_risk');
      $risk = 'medium';
    }
    $this->tempStore->set('risk', $risk);
    $this->tempStore->set('risk_message', $risk_message);
    $form_state->setRedirectUrl(Url::fromRoute('covid19_self_assessment.covid19_s_a_controller_result'));
  }

}
