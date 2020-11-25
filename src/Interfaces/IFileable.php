<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Interfaces;

/**
 * Interface IFileable
 *
 * @package mPhpMaster\Support\Interfaces
 */
interface IFileable
{
    const FilenameUniqueHash = "dmygia";
    const FilenameExtension = "xlsx";

    public function toFilename(): string;

    /**
     * @param mixed|null $default
     *
     * @return string
     */
    public function makeFilenameHash($default = null): string;

    public function getFilenameExtension(): string;

    public function getFilenameParts(?string $part = null): array;

    public function makeFilename(?string $default = null): string;
}
