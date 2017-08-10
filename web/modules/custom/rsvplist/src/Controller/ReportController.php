<?php
/**
 * @file
 * Contains \Drupal\rsvplist\Controller\ReportController
 */
namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReportController extends ControllerBase {
  /**
   * DB connection.
   *
   * @var Connection
   */
  protected $database;

  /**
   * Gets all RSVPs for all nodes.
   *
   * @return array
   */
  protected function load() {
    $select = $this->database->select('rsvplist', 'r');
    // Join the users table so we can get the entry creator's username.
    $select->join('node_field_data', 'n', 'r.nid = n.nid');
    // Select these specific fields for the output
    $select->addfield('n', 'title');
    $select->addField('r', 'name');
    $select->addField('r', 'mail');
    $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);

    return $entries;
  }

  /**
   * Creates the report page.
   *
   * @return array
   *  Render array for report output.
   */
  public function report() {
    $content = [];
    $content['message'] = [
      '#markup' => $this->t('Below is a list of all Event RSVPs including the 
      registered name, email address and the name of the event they will be 
      attending.'),
    ];
    $headers = [
      $this->t('Event'),
      $this->t('Name'),
      $this->t('Email'),
    ];

    $rows = [];
    foreach($entries = $this->load() as $entry) {
      // Sanitize each entry
      $rows[] = array_map('Drupal\Component\Utility\SafeMarkup::checkPlain',
        $entry);
    }
    $content['table'] = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => $this->t('No entries available.'),
    ];
    // Don't cache this page.
    $content['#cache']['max-age'] = 0;

    return $content;
  }

  public function __construct(Connection $database) {
    $this->$database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }
}