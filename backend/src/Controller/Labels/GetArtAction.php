<?php

declare(strict_types=1);

namespace App\Controller\Labels;

use App\Avatars;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class GetArtAction
{
    public function __construct(
        private readonly Avatars $avatars
    ) {
    }

    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $response = $response->withCacheLifetime(Response::CACHE_ONE_DAY);

        $path = $this->avatars->getLabelPath($params['label_id']);

        return $this->avatars->streamFilesystemOrDefault(
            $response,
            $path
        );
    }
}
