<?php

declare(strict_types=1);

namespace App;

use App\Http\Response;
use Intervention\Image\ImageManager;
use League\Flysystem\Filesystem;

final class Avatars
{
    public function __construct(
        private readonly Environment $environment,
        private readonly Filesystem $filesystem,
        private readonly ImageManager $imageManager
    ) {
    }

    public function getLabelPath(string $labelId): string
    {
        return '/labels/' . $labelId . '.jpg';
    }

    public function getArtistPath(string $artistId): string
    {
        return '/artists/' . $artistId . '.jpg';
    }

    public function getDefaultPath(): string
    {
        return $this->environment->getParentDirectory() . '/frontend/public/avatar.jpg';
    }

    public function upload(
        string $original,
        string $path
    ): void {
        $image = $this->imageManager->read($original);
        $image = $image->cover(512, 512);

        $newImageData = $image->toJpeg();

        $this->filesystem->write(
            $path,
            (string)$newImageData
        );
    }

    public function streamFilesystemOrDefault(
        Response $response,
        string $path,
    ): Response {
        if ($this->filesystem->has($path)) {
            return $response->withFile(
                $this->filesystem->readStream($path),
                $this->filesystem->mimeType($path)
            );
        }

        return $response->withFile(
            file_get_contents($this->getDefaultPath()) ?: '',
            'image/jpeg'
        );
    }
}
