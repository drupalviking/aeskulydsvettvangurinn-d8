<?php
/**
 * @file
 * Contains \Drupal\rsvplist\Form\RSVPForm
 */
namespace Drupal\rsvplist\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxy;
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

  protected $current_user;

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
    $user_id = $this->current_user->id();
    db_insert('rsvplist')
      ->fields([
        'name' => $form_state->getValue('name'),
        'mail' => $form_state->getValue('email'),
        'nid' => $form_state->getValue('nid'),
        'uid' => $user_id,
        'created' => time(),
      ])
      ->execute();
    drupal_set_message($this->t('Thank you for your RSVP,
     you are on the list for the event.'));
  }

  /**
   * RSVPForm constructor.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   * @param \Egulias\EmailValidator\EmailValidatorInterface $email_validator
   * @param \Drupal\Core\Session\AccountProxy $current_user
   */
  public function __construct(RouteMatchInterface $route_match,
                              EmailValidatorInterface $email_validator,
                              AccountProxy $current_user) {
    $this->routeMatch = $route_match;
    $this->email_validator = $email_validator;
    $this->current_user = $current_user;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('email.validator'),
      $container->get('current_user')
    );
  }
}