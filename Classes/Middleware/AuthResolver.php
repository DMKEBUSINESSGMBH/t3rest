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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Security\RequestToken;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Tx_T3rest_Middleware_AuthController.
 *
 * @author     Mario Seidel <mario.seidel@dmk-ebusiness.com>
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class AuthResolver extends AbstractMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     *
     * @SuppressWarnings("PHPMD.Superglobals")
     * @SuppressWarnings("PHPMD.ElseExpression")
     */
    protected function processRestRequest(
        ServerRequestInterface $request,
        \Psr\Http\Server\RequestHandlerInterface $handler,
    ): \Psr\Http\Message\ResponseInterface {
        // auth nach redirect herstellen
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            [$_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']] =
                explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)), 2);
        }

        // there are user and pwd, so we have to reauth by this data
        if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
            $_POST['user'] = $_SERVER['PHP_AUTH_USER'];
            $_POST['pass'] = $_SERVER['PHP_AUTH_PW'];
            $_POST['logintype'] = 'login';
            $request = $request->withParsedBody(
                array_merge(
                    $request->getParsedBody() ?: [],
                    $_POST
                )
            );
        } else {
            $requestBody = $this->getParsedBody($request);

            // TODO: If T3 would use the request object in fe user auth,
            //      we would not need to change POST here.
            $_POST['user'] = $requestBody['user'] ?? '';
            $_POST['pass'] = $requestBody['pass'] ?? '';
            $_POST['logintype'] = $requestBody['logintype'] ?? '';
        }

        GeneralUtility::makeInstance(Context::class)->getAspect('security')->setReceivedRequestToken(
            new RequestToken(
                'core/user-auth/fe',
                null,
                ['pid' => \Tx_T3rest_Utility_Config::getAuthUserStoragePid()]
            )
        );

        return $handler->handle($this->addFeUserPid($request));
    }

    /**
     * Check if API URI is first occurrence in request URI path.
     */
    protected function isApiCall(ServerRequestInterface $request): bool
    {
        $requestUri = ltrim($request->getUri()->getPath(), '/');
        $apiSegment = ltrim(\Tx_T3rest_Utility_Config::getRestApiUriPath(), '/');

        return str_starts_with($requestUri, $apiSegment);
    }

    /**
     * Add the storage pid for fe users configured in the extension configuration.
     */
    protected function addFeUserPid(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withParsedBody(
            array_merge($request->getParsedBody() ?: [], [
                'pid' => \Tx_T3rest_Utility_Config::getSignedAuthUserStoragePid(),
            ])
        );
    }
}
