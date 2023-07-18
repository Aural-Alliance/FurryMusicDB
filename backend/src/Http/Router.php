<?php

declare(strict_types=1);

namespace App\Http;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

final class Router implements RouterInterface
{
    private ?UriInterface $baseUrl = null;

    private RouteInterface $currentRoute;

    private RouteParserInterface $routeParser;

    public function __construct(
        private readonly ServerRequestInterface $request
    ) {
        $routeParser = $request->getAttribute(RouteContext::ROUTE_PARSER);
        $currentRoute = RouteContext::fromRequest($request)->getRoute();

        if (null === $routeParser || null === $currentRoute) {
            throw new \RuntimeException('Current route and/or router not available on this request.');
        }

        $this->routeParser = $routeParser;
        $this->currentRoute = $currentRoute;
    }

    public function getBaseUrl(): UriInterface
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->buildBaseUrl();
        }

        return $this->baseUrl;
    }

    public function buildBaseUrl(): UriInterface
    {
        $currentUri = $this->request->getUri();

        $useHttps = ('https' === $currentUri->getScheme());

        $baseUrl = (new Uri())
            ->withScheme($currentUri->getScheme())
            ->withHost($currentUri->getHost())
            ->withPort($currentUri->getPort());

        if ($useHttps && $baseUrl->getScheme() !== '') {
            $baseUrl = $baseUrl->withScheme('https');
        }

        // Avoid double-trailing slashes in various URLs
        if ('/' === $baseUrl->getPath()) {
            $baseUrl = $baseUrl->withPath('');
        }

        // Filter the base URL so it doesn't say http://site:80 or https://site:443
        if (Uri::isDefaultPort($baseUrl)) {
            return $baseUrl->withPort(null);
        }

        return $baseUrl;
    }

    public function fromHereWithQueryAsUri(
        ?string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        bool $absolute = false
    ): UriInterface {
        if ($this->request instanceof ServerRequestInterface) {
            $queryParams = array_merge($this->request->getQueryParams(), $queryParams);
        }

        return $this->fromHereAsUri($routeName, $routeParams, $queryParams, $absolute);
    }

    public function fromHereWithQuery(
        ?string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        bool $absolute = false
    ): string {
        if ($this->request instanceof ServerRequestInterface) {
            $queryParams = array_merge($this->request->getQueryParams(), $queryParams);
        }

        return $this->fromHere($routeName, $routeParams, $queryParams, $absolute);
    }

    public function fromHereAsUri(
        ?string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        bool $absolute = false
    ): UriInterface {
        if (null === $routeName) {
            $routeName = $this->currentRoute->getName();
        }

        $routeParams = array_merge($this->currentRoute->getArguments(), $routeParams);

        if (null === $routeName) {
            throw new InvalidArgumentException(
                'Cannot specify a null route name if no existing route is configured.'
            );
        }

        return $this->namedAsUri($routeName, $routeParams, $queryParams, $absolute);
    }

    public function fromHere(
        ?string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        bool $absolute = false
    ): string {
        if (null === $routeName) {
            $routeName = $this->currentRoute->getName();
        }

        $routeParams = array_merge($this->currentRoute->getArguments(), $routeParams);

        if (null === $routeName) {
            throw new InvalidArgumentException(
                'Cannot specify a null route name if no existing route is configured.'
            );
        }

        return $this->named($routeName, $routeParams, $queryParams, $absolute);
    }

    public function namedAsUri(
        string $routeName,
        array $routeParams = [],
        array $queryParams = [],
        bool $absolute = false
    ): UriInterface {
        $relativeUri = $this->routeParser->relativeUrlFor($routeName, $routeParams, $queryParams);

        return ($absolute)
            ? self::resolveUri($this->getBaseUrl(), $relativeUri, true)
            : self::createUri($relativeUri);
    }

    public function named(
        string $routeName,
        array $routeParams = [],
        array $queryParams = [],
        bool $absolute = false
    ): string {
        $relativeUri = $this->routeParser->relativeUrlFor($routeName, $routeParams, $queryParams);

        return ($absolute)
            ? (string)self::resolveUri($this->getBaseUrl(), $relativeUri, true)
            : $relativeUri;
    }

    /**
     * Compose a URL, returning an absolute URL (including base URL) if the current settings or
     * this function's parameters indicate an absolute URL is necessary
     *
     * @param UriInterface $base
     * @param string|UriInterface $rel
     * @param bool $absolute
     */
    public static function resolveUri(
        UriInterface $base,
        UriInterface|string $rel,
        bool $absolute = false
    ): UriInterface {
        if (!$rel instanceof UriInterface) {
            $rel = self::createUri($rel);
        }

        if (!$absolute) {
            return $rel;
        }

        // URI has an authority solely because of its port.
        if ($rel->getAuthority() !== '' && $rel->getHost() === '' && $rel->getPort()) {
            // Strip the authority from the URI, then reapply the port after the merge.
            $originalPort = $rel->getPort();

            $newUri = UriResolver::resolve($base, $rel->withScheme('')->withHost('')->withPort(null));
            return $newUri->withPort($originalPort);
        }

        return UriResolver::resolve($base, $rel);
    }

    public static function createUri(string $uri): UriInterface
    {
        return new Uri($uri);
    }
}
