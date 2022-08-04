<?php
namespace TIG\PostNL\Block\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Layout\Generic;

class Back extends Generic implements ButtonProviderInterface
{
    /** @var Action  */
    private $action;

    /**
     * @param UiComponentFactory    $uiComponentFactory
     * @param Action                $action
     * @param array                 $data
     */
    public function __construct(
        UiComponentFactory $uiComponentFactory,
        Action $action,
        $data = []
    )
    {
        $this->action = $action;
        parent::__construct($uiComponentFactory, $data);
    }


    /**
     * Create Button
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10,
        ];
    }

    /**
     * Get back URL
     *
     * @return mixed
     */
    public function getBackUrl()
    {
        return $this->action->getUrl('*/*/');
    }
}
