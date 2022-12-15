<?php

namespace Awesome\Filesystem\Repositories;

use Awesome\Filesystem\Contracts\FileRepository as RepositoryContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

class FileRepository implements RepositoryContract
{
    private Model $fileModel;

    public function __construct(Model $model)
    {
        $this->fileModel = $model;
    }

    public function create(File $file, string $path = '', string $fileName = null): Model
    {
        $uuid = $this->generateUuid();

        return $this->fileModel->newQuery()->create([
            ...$this->prepareFile($file), 
            ...[
                'id' => $uuid,
                'path' => "{$this->prepareFilePath($path)}{$uuid}.{$file->guessExtension()}"
            ]
        ]);
    }
    
    public function delete(string $id): bool|null
    {
        if ($model = $this->getById($id)) {
            return $model->delete();
        }
        
        return false;
    }

    public function getById(string $id): ?Model
    {
        return $this->fileModel->newQuery()->find($id);
    }
    
    public function update(string $id, File $newFile): bool
    {
        if ($oldFile = $this->getById($id)) {
            return $oldFile->update($this->prepareFile($newFile));
        }
        
        return false;
    }

    private function generateUuid()
    {
        return (string)Str::uuid();
    }
    
    private function prepareFile(File $file, string $fileName = null): array
    {
        return [
            'name' => $fileName ?: $file->getFilename(),
            'size' => $file->getSize()
        ];
    }
    
    private function prepareFilePath(string $path): string
    {
        return trim($path, '/') . '/';
    }
}
