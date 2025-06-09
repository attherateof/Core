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
 * @link      https://github.com/attherateof/Core
 */

declare(strict_types=1);

namespace MageStack\Core\Block\Adminhtml\System\Config\Form\Field\Frontend;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\App\State;
use Magento\Backend\Block\Template\Context;

class DebugMode extends Field
{
    public function __construct(
        private readonly State $appState,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        try {
            $mode = $this->appState->getMode();
        } catch (\Exception $e) {
            // log error reason
            // fallback in case mode cannot be read
            $mode = 'default';
        }

        if ($mode === State::MODE_DEVELOPER) {
            return parent::_getElementHtml($element);
        }

        return '';
    }
}
