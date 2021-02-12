<?php

namespace Drupal\lanacion_mvpages\Services;

use DateTime;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class Activation
 *
 * @package Drupal\experiences_activation\Services
 */
class MostViewedPages {

  /** @var \Drupal\Core\Session\AccountProxyInterface  */
  protected $currentUser;

  /** @var \Drupal\Core\Database\Connection  */
  protected $connection;

  /** @var \Drupal\Core\Messenger\Messenger  */
  protected $messenger;

  /** @var \Drupal\Core\Entity\EntityTypeManager */
  protected $entityTypeManager;

  /**
   * Activation constructor.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   * @param \Drupal\Core\Database\Connection $database
   * @param \Drupal\Core\Messenger\Messenger $messenger
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   */
  public function __construct(AccountProxyInterface $current_user, Connection $database, Messenger $messenger, EntityTypeManager $entity_type_manager) {
    $this->currentUser = $current_user;
    $this->connection = $database;
    $this->messenger = $messenger;
    $this->entityTypeManager = $entity_type_manager;
  }

  public function saveNewView($nid){
    $qty = $this->getFinalQty($nid);
    $uid = $this->currentUser->getAccount()->id();
    $timestamp = time();

    $this->connection->merge('most_viewed_pages')
      ->key('nid', $nid)
      ->fields([
        'qty' => $qty,
        'uid' => $uid,
        'timestamp' => $timestamp
      ])
      ->execute();

    \Drupal::messenger()->addMessage('sasa', 'status');

  }

  public function getFourPagesMV(){

  }

  private function getPartialQty($nid){

    $query = $this->connection->select('most_viewed_pages', 'mvp')
      ->condition('mvp.nid', $nid, '=')
      ->fields('mvp', ['qty']);

    return $query->execute()->fetch();

  }

  private function getFinalQty($nid){
    $partialQty = $this->getPartialQty($nid);
    $total = 1;

    if($partialQty){
      $total = $partialQty->qty + 1;
    }

    return $total;
  }


}
