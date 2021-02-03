<?php

namespace Drupal\lanacion_mvpages\Services;

use DateTime;
use Drupal\Core\Mail\MailManager;
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

  public function saveNewView(){
    $nid = 4;
    $qty = 1;
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
  }

  /**
   * SaveNewCode() Save new code
   * @void
   */
  private function saveNewCode(){
    $uid = $this->currentUser->getAccount()->id();
    $timestamp = time();
    $code = $this->createRandomCode(1000, 9999);

    $this->connection->merge('activation_codes')
      ->key('uid', $uid)
      ->fields([
        'code' => $code,
        'timestamp' => $timestamp
      ])
      ->execute();

    return $code;
  }

  /**
   * deleteCode() Delete code
   *
   * @void
   */
  private function deleteCode(){
    $uid = $this->currentUser->getAccount()->id();
    $this->connection->delete('activation_codes')->condition('uid', $uid)->execute();
  }

  /**
   * createRandomCode() Create new random code
   *
   * @param $min
   * @param $max
   *
   * @return int
   */
  private function createRandomCode($min, $max){
    return rand($min, $max);
  }

  /**
   * checkCode() Check code
   *
   * @param $formCode
   *
   * @return bool
   */
  public function checkCode($formCode){
    $code = $this->getActivationPerUid()[0]->code;
    if($formCode == $code){
      return true;
    }

    $this->messenger->addStatus('Incorrect code');
    return false;
  }

  /**
   * checkTimePerUid() Check if it's elapsed time
   *
   * @return bool
   */
  public function checkTimePerUid(){
    $timestamp = (!empty($this->getActivationPerUid()[0]->timestamp)) ? $this->getActivationPerUid()[0]->timestamp : 0;
    $actualTime = new DateTime();
    $actualTime = $actualTime->getTimestamp();
    if(($actualTime - 300) > $timestamp){
      return true;
    }
    return false;
  }

  /**
   * updateUser() Update user to activated
   *
   * @void
   */
  public function updateUser(){
    $this->activateUser();
    $this->deleteCode();
    $this->messenger->addStatus('User activated');
  }

  /**
   * getActivationPerUid() get activation code per uid
   *
   * @return mixed
   */
  private function getActivationPerUid(){
    $uid = $this->currentUser->getAccount()->id();

    $query = $this->connection->select('activation_codes', 'ac');
    $query->condition('ac.uid', $uid, '=');
    $query->fields('ac', ['code', 'timestamp']);

    $result = $query->execute()->fetchAll();

    return $result;
  }

  /**
   * activateUser() change field_active to true in database
   *
   * @void
   */
  private function activateUser(){
    $uid = $this->currentUser->getAccount()->id();
    $user = $this->entityTypeManager->getStorage('user')->load($uid);
    $user->set('field_active', 1);
    //TODO: PROBABILITY SET NEW ROL
    $user->save();
  }

  /**
   * isActiveUser() Check if user is active or not
   *
   * @return bool
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function isActiveUser(){
    $uid = $this->currentUser->getAccount()->id();
    $user = $this->entityTypeManager->getStorage('user')->load($uid);

    if(isset($user->get('field_active')->value) && $user->get('field_active')->value == 0){
      return false;
    }
    return true;
  }

}
