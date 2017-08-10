<?php
/**
 * @file
 * Contains \Drupal\rsvplist\Form\RSVPForm
 */
namespace Drupal\rsvplist\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RSVPForm
 * @package Drupal\rsvplist
 *
 * Provides a RSVP signup form
 */
class RSVPForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvplist_signup_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');
    //$nid = $node->nid->value;
    $nid = null;
    $form['name'] = [
      '#title' => $this->t('Full name'),
      '#type' => 'textfield',
      '#size' => '40',
      '#description' => $this->t('Your full name'),
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#title' => $this->t('Email address'),
      '#type' => 'textfield',
      '#size' => '40',
      '#description' => $this->t('Your email address'),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('The form is working'));
  }
}