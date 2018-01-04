<?php

namespace Drupal\apigee_edge\Entity;

use Apigee\Edge\Api\Management\Entity\Developer as EdgeDeveloper;
use Drupal\user\UserInterface;

/**
 * Defines the Developer entity class.
 *
 * @\Drupal\apigee_edge\Annotation\EdgeEntityType(
 *   id = "developer",
 *   label = @Translation("Developer"),
 *   handlers = {
 *     "storage" = "\Drupal\apigee_edge\Entity\Storage\DeveloperStorage",
 *   }
 * )
 */
class Developer extends EdgeDeveloper implements DeveloperInterface {

  use EdgeEntityBaseTrait {
    id as private traitId;
  }

  /**
   * The original email address of the developer.
   *
   * @var null|string
   */
  protected $originalEmail;

  /**
   * Constructs a Developer object.
   *
   * @param array $values
   *   An array of values to set, keyed by property name. If the entity type
   *   has bundles, the bundle key has to be specified.
   */
  public function __construct(array $values = []) {
    parent::__construct($values);
    $this->entityTypeId = 'developer';
    $this->originalEmail = isset($this->originalEmail) ? $this->originalEmail : $this->email;
  }

  /**
   * Creates developer entity from Drupal user.
   *
   * @param UserInterface $user
   *   The Drupal user account.
   *
   * @return Developer
   *   The developer entity.
   */
  public static function createFromDrupalUser(UserInterface $user) : Developer {
    $developer_data = [
      'email' => $user->getEmail(),
      'originalEmail' => isset($user->original) ? $user->original->getEmail() : $user->getEmail(),
      'userName' => $user->getAccountName(),
      'firstName' => $user->get('first_name')->value,
      'lastName' => $user->get('last_name')->value,
      'status' => $user->isActive() ? Developer::STATUS_ACTIVE : Developer::STATUS_INACTIVE,
    ];

    $developer = !isset($user->original) ? static::create($developer_data) : new static($developer_data);

    return $developer;
  }

  /**
   * {@inheritdoc}
   */
  public function uuid() {
    return parent::id();
  }

  /**
   * {@inheritdoc}
   */
  public function id() : ? string {
    return $this->originalEmail;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail(string $email) : void {
    parent::setEmail($email);
    if ($this->originalEmail === NULL) {
      $this->originalEmail = $this->email;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setOriginalEmail(string $originalEmail) {
    $this->originalEmail = $originalEmail;
  }

}