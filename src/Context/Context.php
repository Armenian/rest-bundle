<?php

declare(strict_types=1);

namespace DMP\RestBundle\Context;


final class Context
{
    private array $attributes = [];
    private ?string $version = null;
    private ?array $groups = null;
    private ?bool $isMaxDepthEnabled = null;
    private ?bool $serializeNull = null;

    public function setAttribute(string $key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function getAttribute(string $key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function addGroup(string $group): self
    {
        if (null === $this->groups) {
            $this->groups = [];
        }
        if (!in_array($group, $this->groups)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function addGroups(array $groups): self
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        return $this;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function setGroups(array $groups = null): self
    {
        $this->groups = $groups;
        return $this;
    }

    public function enableMaxDepth(): self
    {
        $this->isMaxDepthEnabled = true;
        return $this;
    }

    public function disableMaxDepth(): self
    {
        $this->isMaxDepthEnabled = false;
        return $this;
    }

    public function isMaxDepthEnabled(): ?bool
    {
        return $this->isMaxDepthEnabled;
    }

    public function setSerializeNull(?bool $serializeNull): self
    {
        $this->serializeNull = $serializeNull;
        return $this;
    }

    public function getSerializeNull(): ?bool
    {
        return $this->serializeNull;
    }
}
