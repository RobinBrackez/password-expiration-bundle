<?php

namespace RobinBrackez\PasswordExpirationBundle\User;

interface PasswordExpirableInterface
{
    public function getPasswordChangedAt(): ?\DateTimeImmutable;
    public function setPasswordChangedAt(?\DateTimeImmutable $passwordChangedAt): self;
    public function changedPasswordLongerAgoThan(int $days): bool;
}