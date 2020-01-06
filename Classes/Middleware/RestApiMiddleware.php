<?php

declare(strict_types=1);

namespace DMK\T3rest\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tx_T3rest_Controller_InterfaceController;
use Tx_T3rest_Utility_Config;
use Tx_T3rest_Utility_Factory;

/**
 * Class RestApiMiddleware.
 *
 * @author     Mario Seidel <mario.seidel@dmk-ebusiness.com>
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class RestApiMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // the hook is not enabled, proceed next middleware!
        if (!Tx_T3rest_Utility_Config::isRestHookEnabled()) {
            return $handler->handle($request);
        }

        $this->getController()->execute();

        return $handler->handle($request);
    }

    /**
     * Returns an instance of a api controller.
     *
     * @return Tx_T3rest_Controller_InterfaceController
     *
     * @throws \Exception
     */
    public function getController()
    {
        return Tx_T3rest_Utility_Factory::getRestApiController();
    }
}
