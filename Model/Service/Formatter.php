<?php

/**
 * Blackbird
 *
 * @copyright Copyright © 2024 Blackbird. All rights reserved.
 * @author    Lucas (Blackbird Team)
 */

declare(strict_types=1);

namespace Blackbird\Universes\Model\Service;

use Exception;

class Formatter
{
    public const AUTHORIZED_CHARACTERS_REGEX = '/[^A-Za-z0-9\-^_]/';

    /**
     * Format a universe label, typically coming from a config : removes spaces at the beginning and ending, apply
     * lowercase, replace inner spaces and special characters
     *
     * @param string $label
     * @param string $spacesReplacer
     * @return string
     */
    public function formatUniverseLabel(string $label, string $spacesReplacer = '_'): string
    {
        try {
            return \preg_replace(
                self::AUTHORIZED_CHARACTERS_REGEX,
                '',
                \str_replace(' ', $spacesReplacer, \strtolower(\trim($label)))
            );
        } catch (Exception $e) {
            return $label;
        }
    }
}
