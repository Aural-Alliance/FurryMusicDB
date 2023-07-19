<?php

declare(strict_types=1);

namespace App\Controller\Users;

use App\Http\Response;
use App\Http\ServerRequest;
use App\Serializer\ApiSerializerInterface;
use Psr\Http\Message\ResponseInterface;

final class GetMeAction
{
    public function __construct(
        private readonly ApiSerializerInterface $apiSerializer
    ) {
    }

    public function __invoke(ServerRequest $request, Response $response): ResponseInterface
    {
        $currentUser = $request->getUser();

        return $response->withJson(
            $this->apiSerializer->toArray($currentUser->getLocalUser())
        );
    }
}
