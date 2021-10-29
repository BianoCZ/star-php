<?php

declare(strict_types = 1);

namespace Biano\Star;

final class Version
{

    private string $version;

    private function __construct(string $version)
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public static function v1(): self
    {
        return new self('v1');
    }

}
