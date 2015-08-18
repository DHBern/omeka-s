<?php
namespace Omeka\BlockLayout;

use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\ErrorStore;
use Zend\Form\Element\Textarea;
use Zend\View\Renderer\PhpRenderer;

class Html extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'HTML';
    }

    public function prepareForm(PhpRenderer $view)
    {
        $view->headscript()->appendFile($view->assetUrl('js/ckeditor/ckeditor.js', 'Omeka'));
        $view->headscript()->appendFile($view->assetUrl('js/ckeditor/adapters/jquery.js', 'Omeka'));
    }

    public function ingest(SitePageBlock $block, ErrorStore $errorStore)
    {
        $htmlPurifier = $this->getServiceLocator()->get('Omeka\HtmlPurifier');
        $data = $block->getData();
        $data['html'] = $htmlPurifier->purify($data['html']);
        $block->setData($data);
    }

    public function form(PhpRenderer $view, $index, SitePageBlockRepresentation $block = null)
    {
        $textarea = new Textarea("o:block[$index][o:data][html]");
        $textarea->setAttribute('class', 'block-html');
        if ($block) {
            $textarea->setAttribute('value', $block->data()['html']);
        }
        $script = '<script type="text/javascript">
            $(".block-html").ckeditor({customConfig: "' . $view->assetUrl('js/ckeditor_config.js', 'Omeka') . '"});
        </script>';
        return $view->formField($textarea) . $script;
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {}
}
