<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Interfaces;

/**
 * Interface IFilenameBuilder
 *
 * @package mPhpMaster\Support\Interfaces
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
