<?php

namespace Awesome\Filesystem\Services;

use Awesome\Filesystem\Exceptions;
use Awesome\Filesystem\Contracts\{FileRepository, FileService as ServiceContract};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class FileService implements ServiceContract
{
    private FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function create(File $file, string $keyPath, string $fileName = null): Model
    {
        $path = $this->getPath($keyPath);

        return DB::transaction(function () use ($file, $path, $fileName) {
            $fileModel = $this->fileRepository->create($file, $path, $fileName);
            if ($fileModel && Storage::put($fileModel->path, $file->openFile())) {
                return $fileModel;
            }
        });

        throw new Exceptions\FileNotCreatedException();
    }

    public function delete(string $id): bool
    {
        if ($model = $this->getById($id)) {
            return DB::transaction(function () use ($id, $model) {
                if ($this->fileRepository->delete($id) && Storage::delete($model->path)) {
                    return true;
                }

                throw new Exceptions\FileNotDeletedException();
            });
        }

        return false;
    }

    public function getById(string $id): ?Model
    {
        return $this->fileRepository->getById($id);
    }

    public function update(string $id, File $newFile): bool
    {
        if ($model = $this->getById($id)) {
            return DB::transaction(function () use ($id, $newFile) {
                if ($this->fileRepository->update($id, $newFile) && Storage::put($model->path, $newFile->openFile())) {
                    return true;
                }
                
                throw new Exceptions\FileNotUpdatedException();
            });
        }
        
        return false;
    }

    private function getPath(string $keyPath): string
    {
        return trim(
            (string)(config("filedirectories.paths.{$keyPath}") ?? config('filedirectories.paths.default')),
            '/'
        );
    }
}
