<?php
declare(strict_types = 1);

namespace DMK\T3rest\Request;

/**
 * Class JsonBodyParser
 *
 * @package    TYPO3
 * @subpackage DMK\T3rest
 * @author     Mario Seidel <mario.seidel@dmk-ebusiness.com>
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class JsonBodyParser implements BodyParserInterface
{
    /**
     * Parse a json formatted body into an array.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return array
     */
    public function parseBody(\Psr\Http\Message\ServerRequestInterface $request): array
    {
        $content = $request->getBody()->getContents();

        if (empty($content)) {
            return [];
        }

        return \GuzzleHttp\json_decode($content, true) ?: [];
    }
}
