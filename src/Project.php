<?php

declare(strict_types = 1);

namespace Biano\Star;

final class Project
{

    private string $project;

    private function __construct(string $project)
    {
        $this->project = $project;
    }

    public function getProject(): string
    {
        return $this->project;
    }

    public static function cz(): self
    {
        return new self('biano.cz');
    }

    public static function sk(): self
    {
        return new self('biano.sk');
    }

    public static function nl(): self
    {
        return new self('biano.nl');
    }

    public static function ro(): self
    {
        return new self('biano.ro');
    }

    public static function br(): self
    {
        return new self('biano.com.br');
    }

    public static function hu(): self
    {
        return new self('biano.hu');
    }

    public static function pt(): self
    {
        return new self('biano.pt');
    }

    public static function gr(): self
    {
        return new self('biano.gr');
    }

}
