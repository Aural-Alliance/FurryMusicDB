<?php

declare(strict_types=1);

namespace App\Http;

use App\Entity\User;
use InvalidArgumentException;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

final class ServerRequest extends \Slim\Http\ServerRequest
{
    public const ATTR_USER = 'user';

    public function getRouter(): RouteParserInterface
    {
        return $this->getAttributeOfClass(RouteContext::ROUTE_PARSER, RouteParserInterface::class);
    }

    public function getUser(): User
    {
        return $this->getAttributeOfClass(self::ATTR_USER, User::class);
    }

    /**
     * @param string $attr
     * @param string $class_name
     *
     * @throws InvalidArgumentException
     */
    private function getAttributeOfClass(string $attr, string $class_name): mixed
    {
        $object = $this->serverRequest->getAttribute($attr);

        if (empty($object)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Attribute "%s" is required and is empty in this request',
                    $attr
                )
            );
        }

        if (!($object instanceof $class_name)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Attribute "%s" must be of type "%s".',
                    $attr,
                    $class_name
                )
            );
        }

        return $object;
    }
}
