<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Interfaces;

/**
 * Interface IFilenameBuilder
 *
 * @package MPhpMaster\LaravelHelpers\Interfaces
 */
interface IFilenameBuilder
{
    const FilenameUniqueHash = "dmygia";
    const FilenameExtension = "xlsx";

    public function getFilename(): string;

    public function getFilenameExtension(): string;

    public function getFilenameParts(?string $part = null): array;

    /**
     * @param mixed|null $default
     *
     * @return string
     */
    public function makeFilenameHash($default = null): string;

    public function makeFilename(?string $default = null): string;
}
