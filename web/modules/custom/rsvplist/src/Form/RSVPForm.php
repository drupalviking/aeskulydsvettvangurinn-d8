<?php
/**
 * @file
 * Contains \Drupal\rsvplist\Form\RSVPForm
 */
namespace Drupal\rsvplist\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Egulias\EmailValidator\EmailValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RSVPForm
 * @package Drupal\rsvplist
 *
 * Provides a RSVP signup form
 */
class RSVPForm extends FormBase {
  /**
   * The Route Match
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Email validation
   *
   * @var \Egulias\EmailValidator\EmailValidatorInterface
   */
  protected $email_validator;

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
    $node = $this->routeMatch->getParameter('node');
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

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if($value == !$this->email_validator->isValid($value)) {
      $form_state->setErrorByName('email', $this->t('The email address %mail is 
      not valid.', ['%mail' => $value]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('The form is working'));
  }

  /**
   * RSVPForm constructor.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   * @param \Egulias\EmailValidator\EmailValidatorInterface $email_validator
   */
  public function __construct(RouteMatchInterface $route_match,
                              EmailValidatorInterface $email_validator) {
    $this->routeMatch = $route_match;
    $this->email_validator = $email_validator;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('email.validator')
    );
  }
}