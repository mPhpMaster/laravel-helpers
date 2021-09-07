<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Traits;

/**
 * Trait TFilenameBuilder
 *
 * @uses \MPhpMaster\LaravelHelpers\Interfaces\IFileable
 * @uses \MPhpMaster\LaravelHelpers\Interfaces\IFilenameBuilder
 *
 * @package MPhpMaster\LaravelHelpers\Traits
 */
trait TFilenameBuilder
{
//    protected ?string $fileName;
//    protected ?string $fileExtension;

    public function toFilename(): string
    {
        return call_user_func($this->getFilenameParts('get')['get']);
    }

    public function getFilename(): string
    {
        return $this->toFilename();
    }

    public function getFilenameParts(?string $part = null): array
    {
        $parts = [
            "parts" => $filename = [
                $this->makeFilename(),
                $this->makeFilenameHash(),
            ],
            "glue" => $glue = "-",
            "extension" => $ext = (($ext = $this->getFilenameExtension()) ? ".{$ext}" : ""),
            "get" => fn() => implode($glue, $filename) . str_start($ext, '.')
        ];

        return $part ? wrapWith(data_get($parts, $part, null), $part) : $parts;
    }

    public function getFilenameExtension(): string
    {
        return ltrim(
            $this->fileExtension ?? getConst('static::FilenameExtension', "xlsx"),
            '.'
        );
    }

    public function getFileUrl(): string
    {
        return \Storage::disk($this->disk)->url( $this->toFilename() );
    }

    public function getFilePath(): string
    {
        return \Storage::disk($this->disk)->path( $this->toFilename() );
    }

    /**
     * @param mixed|null $default
     *
     * @return string
     */
    public function makeFilenameHash($default = null): string
    {
        $default ??= "dmygia";
        $time_format = hasConst(static::class, 'FilenameUniqueHash') ? static::FilenameUniqueHash : $default;
        return \Carbon\Carbon::now()->format($time_format);
    }

    public function makeFilename(?string $default = null): string
    {
        return (string)snake_case($this->fileName ?? class_basename(static::class) ?? $default);
    }
}
