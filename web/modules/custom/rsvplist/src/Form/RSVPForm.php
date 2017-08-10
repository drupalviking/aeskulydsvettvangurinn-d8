<?php
/**
 * @file
 * Contains \Drupal\rsvplist\Form\RSVPForm
 */
namespace Drupal\rsvplist\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
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
   * DB connection.
   *
   * @var Connection
   */
  protected $database;

  /**
   * Current Route Match
   *
   * @var RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Email validation service
   *
   * @var EmailValidatorInterface
   */
  protected $email_validator;

  /**
   * The current user.
   *
   * @var AccountProxyInterface
   */
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
    /** @var \Drupal\node\Entity\Node $node */
    $node = $this->routeMatch->getParameter('node');

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
      '#value' => $node ? $node->id() : null,
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
    $this->database->insert('rsvplist')
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
   * @param Connection $database
   *  The database connection service
   * @param RouteMatchInterface $route_match
   *  The Current Route Match
   * @param EmailValidatorInterface $email_validator
   *  The Email validation service
   * @param AccountProxyInterface $current_user
   *  The current user
   */
  public function __construct(Connection $database,
                              RouteMatchInterface $route_match,
                              EmailValidatorInterface $email_validator,
                              AccountProxyInterface $current_user) {
    $this->database = $database;
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
      $container->get('database'),
      $container->get('current_route_match'),
      $container->get('email.validator'),
      $container->get('current_user')
    );
  }
}