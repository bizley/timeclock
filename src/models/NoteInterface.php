<?php

declare(strict_types=1);

namespace app\models;

/**
 * Interface NoteInterface
 * @package app\models
 */
interface NoteInterface
{
    /**
     * @return string|null
     */
    public function getNote(): ?string;
}
