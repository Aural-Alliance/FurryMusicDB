<?php

declare(strict_types=1);

namespace App\Http;

use App\Auth\Acl;
use App\Auth\CurrentUser;
use InvalidArgumentException;

final class ServerRequest extends \Slim\Http\ServerRequest
{
    public const ATTR_USER = 'user';
    public const ATTR_ROUTER = 'router';
    public const ATTR_ACL = 'acl';

    public function getRouter(): RouterInterface
    {
        return $this->getAttributeOfClass(self::ATTR_ROUTER, RouterInterface::class);
    }

    public function getUser(): CurrentUser
    {
        return $this->getAttributeOfClass(self::ATTR_USER, CurrentUser::class);
    }

    public function getAcl(): Acl
    {
        return $this->getAttributeOfClass(self::ATTR_ACL, Acl::class);
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
