<?php

namespace Awesome\Filesystem\Contracts;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\File;

interface FileService
{
    public function create(File $file, string $keyPath, string $fileName = null): Model;

    public function delete(string $id): bool;

    public function getById(string $id): ?Model;

    public function update(string $id, File $newFile): bool;
}