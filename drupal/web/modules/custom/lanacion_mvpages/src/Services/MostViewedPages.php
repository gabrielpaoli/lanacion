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

  public function saveNewView($node){
    $qty = $this->getFinalQty($node->id());
    $uid = $this->currentUser->getAccount()->id();
    $tid = $this->getTaxonomyId($node);
    $timestamp = $node->getCreatedTime();

    if($tid == null){
      die('El nodo no tiene categoria asignada');
    }

    $this->connection->merge('most_viewed_pages')
      ->key('nid', $node->id())
      ->fields([
        'qty' => $qty,
        'uid' => $uid,
        'tid' => $tid,
        'timestamp' => $timestamp
      ])
      ->execute();

    \Drupal::messenger()->addMessage('Guardado correctamente', 'status');

  }

  public function getFourPagesMV($node){

    $nids = [];
    $actualTime = new DateTime();
    $actualTime = $actualTime->getTimestamp();
    $timeCompare = $actualTime - 600;
    $tid = $this->getTaxonomyId($node);

    $query = $this->connection->select('most_viewed_pages', 'mvp')
      ->fields('mvp', ['nid'])
      //->condition('mvp.timestamp', $timeCompare, '>')
      ->condition('mvp.tid', $tid, '=')
      ->condition('mvp.nid', $node->id(), '<>')
      ->range(0,4)
      ->orderBy('qty', 'DESC');

    $result = $query->execute();
    foreach ($result as $record) {
      $nids[] = $record->nid;
    }

    return $nids;
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

  private function getTaxonomyId($node){
    $tid = (!empty($node->get('field_tags')->target_id) ? $node->get('field_tags')->target_id : null);
    return $tid;
  }


}
