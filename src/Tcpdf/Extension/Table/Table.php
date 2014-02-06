<?php

namespace Tcpdf\Extension\Table;

/**
 * Tcpdf\Extension\Table\Table
 *
 * @author naitsirch
 */
class Table
{
    const FONT_WEIGHT_NORMAL = 'normal';
    const FONT_WEIGHT_BOLD = 'bold';

    private $pdf;
    private $cacheDir;
    private $rows;
    private $borderWidth;
    private $lineHeight = 1;
    private $fontFamily;
    private $fontSize;
    private $fontWeight;
    private $width;
    private $widthPercentage;

    /**
     * Create a table structure for TCPDF.
     *
     * @param \TCPDF $pdf
     * @param string $cacheDir If the cache directory is given, resized images could be cached.
     */
    public function __construct(\TCPDF $pdf, $cacheDir = null)
    {
        $this->pdf = $pdf;
        $this->cacheDir = $cacheDir;
        $this->setBorderWidth($pdf->GetLineWidth());
        $this->setFontFamily($pdf->getFontFamily());
        $this->setFontSize($pdf->getFontSizePt()); // FontSizePT is in points (not in user unit)
        $this->setFontWeight(strpos($pdf->getFontStyle(), 'B') !== false
            ? self::FONT_WEIGHT_BOLD
            : self::FONT_WEIGHT_NORMAL
        );
    }

    public function getBorderWidth()
    {
        return $this->borderWidth;
    }

    public function setBorderWidth($borderWidth)
    {
        $this->borderWidth = $borderWidth;
        return $this;
    }

    /**
     * Get the factor for the height of one line.
     * If the factor is 1.5 you will get a line height which is one and a half times largeer than the font size.
     *
     * @return float
     */
    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    /**
     * Set the factor for the height of one line.
     * If the factor is 1.5 you will get a line height which is one and a half times largeer than the font size.
     *
     * @param float $lineHeight in user units
     * @return \Tcpdf\Extension\Table\Table
     */
    public function setLineHeight($lineHeight)
    {
        $this->lineHeight = $lineHeight;
        return $this;
    }

    /**
     * Returns a new table row.
     * @return Row
     */
    public function newRow()
    {
        return $this->rows[] = new Row($this);
    }

    /**
     * Returns the PDF generator.
     * @return \TCPDF
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Returns all rows of this table.
     * @return Row[] array of Row
     */
    public function getRows()
    {
        return $this->rows;
    }

    public function getWidth()
    {
        if (null === $this->width) {
            return null;
        }
        if ($this->widthPercentage) {
            $maxWidth = $this->getPdf()->w - $this->getPdf()->rMargin - $this->getPdf()->x;
            return $this->width / 100 * $maxWidth;
        }
        return $this->width;
    }

    public function getWidthPercentage()
    {
        return $this->widthPercentage;
    }

    public function setWidth($width, $percentage = false)
    {
        if (!is_numeric($width)) {
            throw new \InvalidArgumentException('The width must be numeric.');
        }
        $this->width = $width;
        $this->widthPercentage = (bool) $percentage;
        return $this;
    }

    public function getFontFamily()
    {
        return $this->fontFamily;
    }

    public function setFontFamily($fontFamily)
    {
        $this->fontFamily = $fontFamily;
        return $this;
    }

    public function getFontSize()
    {
        return $this->fontSize;
    }

    public function setFontSize($fontSize)
    {
        if (!is_numeric($fontSize)) {
            throw new \InvalidArgumentException('The font size must be numeric.');
        }
        $this->fontSize = $fontSize;
        return $this;
    }

    public function getFontWeight()
    {
        return $this->fontWeight;
    }

    public function setFontWeight($fontWeight)
    {
        if (!in_array($fontWeight, array(self::FONT_WEIGHT_NORMAL, self::FONT_WEIGHT_BOLD))) {
            throw new \InvalidArgumentException("The font weight '$fontWeight' is not supported.");
        }
        $this->fontWeight = $fontWeight;
        return $this;
    }


    /**
     * Draws the table and returns the PDF generator.
     * @return \TCPDF
     */
    public function end()
    {
        new TableConverter($this, $this->cacheDir);
        return $this->getPdf();
    }
}
