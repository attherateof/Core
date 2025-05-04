<?php

/**
 * Copyright © 2025 MageStack. All rights reserved.
 * See COPYING.txt for license details.
 *
 * DISCLAIMER
 *
 * Do not make any kind of changes to this file if you
 * wish to upgrade this extension to newer version in the future.
 *
 * @category  MageStack
 * @package   MageStack_Core
 * @author    Amit Biswas <amit.biswas.webdeveloper@gmail.com>
 * @copyright 2025 MageStack
 * @license   https://opensource.org/licenses/MIT  MIT License
 */

declare(strict_types=1);

namespace MageStack\Core\Plugin;

use Magento\Config\Model\Config\Structure\Element\Field as Subject;

class ConfigFieldPlugin
{
    /**
     * After plugin for getComment method.
     *
     * @param Subject $subject
     * @param string  $result
     *
     * @return string
     */
    public function afterGetComment(Subject $subject, string $result): string
    {
        if (trim($result) !== '') {
            $result .= '<br />';
        }
        $result .= '<div class="note" style="display: block; margin-top: 10px;">';
        $result .= '<strong style="color: #eb5202;">Path: </strong>' . __('<code>%1</code>', $this->getPath($subject));
        $result .= '</div>';

        return $result;
    }

    /**
     * Get the config path.
     *
     * @param  Subject $subject
     * @return string
     */
    private function getPath(Subject $subject): string
    {
        return $subject->getConfigPath() ?: $subject->getPath();
    }
}
