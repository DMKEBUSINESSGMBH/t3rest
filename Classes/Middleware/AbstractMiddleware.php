<?php

declare(strict_types=1);

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "t3rest" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

namespace DMK\T3rest\Middleware;

use DMK\T3rest\Request\BodyParserInterface;
use DMK\T3rest\Request\JsonBodyParser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Tx_T3rest_Middleware_Abstract.
 *
 * @author     Michael Wagner
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractMiddleware implements MiddlewareInterface
{
    abstract protected function processRestRequest(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;

    /**
     * @throws \Exception
     *
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        // if hook is not enabled or uri is not an api call, proceed with next handler!
        if (\Tx_T3rest_Utility_Config::isRestHookEnabled()) {
            $GLOBALS['TYPO3_REQUEST'] ??= $request;
            if ($this->isApiCall($request)) {
                return $this->processRestRequest($request, $handler);
            }
        }

        return $handler->handle($request);
    }

    /**
     * Check if API URI is first occurrence in request URI path.
     *
     * @return bool
     */
    protected function isApiCall(ServerRequestInterface $request)
    {
        $requestUri = ltrim($request->getUri()->getPath(), '/');
        $apiSegment = ltrim(\Tx_T3rest_Utility_Config::getRestApiUriPathForSiteLanguage(), '/');

        return str_starts_with($requestUri, $apiSegment);
    }

    /**
     * Return the parsed body.
     *
     * @return array
     */
    protected function getParsedBody(ServerRequestInterface $request)
    {
        $result = [];
        try {
            $result = $this->getBodyParser()->parseBody($request);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            \Sys25\RnBase\Utility\Logger::warn(
                sprintf(
                    '%s: could not parse body as JSON: %s',
                    self::class,
                    $invalidArgumentException->getMessage()
                ),
                't3rest',
                [
                    'exception' => $invalidArgumentException,
                ]
            );
        }

        return $result;
    }

    /**
     * We expect JSON here so we use a JSON-BodyParser.
     */
    protected function getBodyParser(): BodyParserInterface
    {
        return GeneralUtility::makeInstance(JsonBodyParser::class);
    }
}
