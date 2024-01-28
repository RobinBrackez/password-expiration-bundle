<?php

namespace RobinBrackez\PasswordExpirationBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use RobinBrackez\PasswordExpirationBundle\User\PasswordExpirableInterface;

class PasswordChangeListener
{
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getEntity();

        // Check if the entity is an instance of your User entity
        if (!$entity instanceof PasswordExpirableInterface) {
            return;
        }

        // Check if the 'password' field is changed
        if ($args->hasChangedField('password')) {
            // Update the passwordChangedAt property
            $entity->setPasswordChangedAt(new \DateTimeImmutable());
        }
    }
}