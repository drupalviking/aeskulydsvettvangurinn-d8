<?php
/**
 * @file
 * Contains \Drupal\rsvplist\EnablerService
 */
namespace Drupal\rsvplist;

use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;

class EnablerService {
  /**
   * DB connection.
   *
   * @var Connection
   */
  protected $database;

  public function setEnabled(Node $node) {
    if(!$this->isEnabled($node)) {
      $insert = $this->database->insert('rsvplist_enabled');
      $insert->fields(['nid'], [$node->id()]);
      $insert->execute();
    }
  }

  public function isEnabled(Node $node) {
    if($node->isNew()){
      return FALSE;
    }
    $select = $this->database->select('rsvplist_enabled', 're');
    $select->fields('re', ['nid']);
    $select->condition('nid', $node->id());
    $results = $select->execute();
    return !empty($results->fetchCol());
  }

  public function delEnabled(Node $node) {
    $delete = $this->database->delete('rsvp_enabled');
    $delete->condition('nid', $node->id());
    $delete->execute();
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