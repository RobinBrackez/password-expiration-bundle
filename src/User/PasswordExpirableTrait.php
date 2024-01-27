<?php

namespace RobinBrackez\PasswordExpirationBundle\User;

use Doctrine\ORM\Mapping as ORM;

trait PasswordExpirableTrait 
{
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $passwordChangedAt = null;

    public function getPasswordChangedAt(): ?\DateTimeImmutable
    {
        return $this->passwordChangedAt;
    }

    public function setPasswordChangedAt(?\DateTimeImmutable $passwordChangedAt): self
    {
        $this->passwordChangedAt = $passwordChangedAt;

        return $this;
    }

    public function changedPasswordLongerAgoThan(int $days): bool
    {
        if (is_null($this->passwordChangedAt)) {
            return true;
        }

        $now = new \DateTime();
        $interval = $now->diff($this->passwordChangedAt);

        return $interval->days > $days;
    }
}