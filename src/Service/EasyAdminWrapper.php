<?php

namespace RobinBrackez\PasswordExpirationBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * EasyAdmin is optional. This is a wrapper class to make sure we don't get errors when EasyAdmin is not installed.
 */
class EasyAdminWrapper
{
    private mixed $adminUrlGenerator = null;

    public function __construct(
        private ContainerInterface $container,
        private ?string $changePasswordControllerName = null,
        private ?string $changePasswordCrudActionName = null,
    ) {
        if (!$this->isEasyAdminBundleAvailable()) {
            return;
        }
        $this->adminUrlGenerator = $this->container->get('EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator');
        if ($this->adminUrlGenerator !== null) {
            throw new \LogicException("Couldn't find AdminUrlGenerator in EasyAdminBundle. Do you have the latest version of EasyAdminBundle installed? Version 2 not supported.");
        }
        if (!$this->changePasswordControllerName) {
            throw new \LogicException('You need to set the changePasswordControllerName when using EasyAdmin');
        }
        if (!$this->changePasswordCrudActionName) {
            throw new \LogicException('You need to set the changePasswordCrudActionName when using EasyAdmin');
        }
    }

    public function isEasyAdminBundleAvailable(): bool
    {
        return array_key_exists('EasyAdminBundle', $this->container->getParameter('kernel.bundles'));
    }

    public function changePasswordIsCurrentRoute($queryString): bool
    {
        if (!$this->isEasyAdminBundleAvailable()) {
            return false;
        }
        return str_contains($queryString, 'crudAction=' . $this->changePasswordCrudActionName);
    }

    public function generateUrl(): string
    {
        if (!$this->isEasyAdminBundleAvailable()) {
            throw new \LogicException('You can only generate an EasyAdmin URL when EasyAdminBundle is installed');
        }
        return $this->adminUrlGenerator->setController($this->changePasswordControllerName)
            ->setAction($this->changePasswordCrudActionName)
            ->generateUrl();
    }
}