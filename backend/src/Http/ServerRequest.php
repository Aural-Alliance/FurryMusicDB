<?php

declare(strict_types=1);

namespace App\Http;

use App\Auth\Acl;
use App\Auth\Auth;
use App\Entity\Artist;
use App\Entity\Label;
use App\Entity\User;
use InvalidArgumentException;
use Mezzio\Session\SessionInterface;

final class ServerRequest extends \Slim\Http\ServerRequest
{
    public const ATTR_USER = 'user';
    public const ATTR_ROUTER = 'router';
    public const ATTR_AUTH = 'auth';
    public const ATTR_ACL = 'acl';
    public const ATTR_SESSION = 'session';

    public const ATTR_ARTIST = 'artist';
    public const ATTR_LABEL = 'label';

    public function getRouter(): RouterInterface
    {
        return $this->getAttributeOfClass(self::ATTR_ROUTER, RouterInterface::class);
    }

    public function getUser(): User
    {
        return $this->getAttributeOfClass(self::ATTR_USER, User::class);
    }

    public function getAuth(): Auth
    {
        return $this->getAttributeOfClass(self::ATTR_AUTH, Auth::class);
    }

    public function getAcl(): Acl
    {
        return $this->getAttributeOfClass(self::ATTR_ACL, Acl::class);
    }

    public function getSession(): SessionInterface
    {
        return $this->getAttributeOfClass(self::ATTR_SESSION, SessionInterface::class);
    }

    public function getArtist(): Artist
    {
        return $this->getAttributeOfClass(self::ATTR_ARTIST, Artist::class);
    }

    public function getLabel(): Label
    {
        return $this->getAttributeOfClass(self::ATTR_LABEL, Label::class);
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
