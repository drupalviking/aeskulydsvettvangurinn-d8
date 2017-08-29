<?php
/**
 * @file
 * Contains \Drupal\rsvplist\EnablerService
 */
namespace Drupal\rsvplist;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class EnablerService {
  public function __construct() {
  }

  public function setEnabled(Node $node) {
    if(!$this->isEnabled($node)) {
      $insert = Database::getConnection()->insert('rsvplist_enabled');
      $insert->fields(['nid'], [$node->id()]);
      $insert->execute();
    }
  }

  public function isEnabled(Node $node) {
    if($node->isNew()){
      return FALSE;
    }
    $select = Database::getConnection()->select('rsvplist_enabled', 're');
    $select->fields('re', ['nid']);
    $select->condition('nid', $node->id());
    $results = $select->execute();
    return !empty($results->fetchCol());
  }

  public function delEnabled(Node $node) {
    $delete = Database::getConnection()->delete('rsvplist_enabled');
    $delete->condition('nid', $node->id());
    $delete->execute();
  }
}