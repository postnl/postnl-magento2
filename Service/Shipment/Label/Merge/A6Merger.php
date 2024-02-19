<?php

namespace TIG\PostNL\Service\Shipment\Label\Merge;

use TIG\PostNL\Service\Pdf\Fpdi;

class A6Merger extends AbstractMerger implements MergeInterface
{
    /**
     * @param Fpdi[] $labels
     * @codingStandardsIgnoreStart
     * @param bool  $createNewPdf Sometimes you want to generate a new Label PDF, for example when printing packingslips
     *                            This parameter indicates whether to reuse the existing label PDF
     *                            @TODO Refactor to a cleaner way rather than chaining all the way to \TIG\PostNL\Service\Shipment\Label\Merge\AbstractMerger
     * @codingStandardsIgnoreEnd
     *
     * @return Fpdi
     */
    public function files(array $labels, $createNewPdf = false)
    {
        $this->pdf = $this->createPdf(false, $createNewPdf);
        foreach ($labels as $label) {
            // @codingStandardsIgnoreLine
            $filename = $this->file->save($label->Output('S'));

            $this->pdf->addPage('P', Fpdi::PAGE_SIZE_A6);
            $this->pdf->setSourceFile($filename);

            $pageId = $this->pdf->importPage(1);
            $this->pdf->useTemplate($pageId, 0, 0);
        }

        $this->file->cleanup();

        return $this->pdf;
    }
}
