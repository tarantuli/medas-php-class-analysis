<?php

namespace Shared\Charts;

use Shared\DataControl\Str;
use Shared\Exceptions\InvalidInputException;

/**
 * (no summary)
 */
class SettingsBug1
{
    public const CHART_SIZE = 2;
    public const IMAGE_SIZE = 1;
    private const VALID_SIZE_LOCKS = [self::IMAGE_SIZE, self::CHART_SIZE];
    private const VALID_X_AXIS_TYPES = [Axes\X::NUMERIC, Axes\X::DATE, Axes\X::CATEGORIES];
    private const VALID_Y_AXIS_TYPES = [Axes\Y::NUMERIC, Axes\Y::DATE];

    /**
     * @var  Color
     */
    private $axisColor;

    /**
     * @var  Color
     */
    private $backgroundColor;

    /**
     * @var  float
     */
    private $barInnerMargin = 1;

    /**
     * @var  float
     */
    private $barMaximumWidth = 12;

    /**
     * @var  float
     */
    private $barMinimumWidth = 4;

    /**
     * @var  float
     */
    private $barOuterMargin = 7;

    /**
     * @var  int
     */
    private $barVisualisationCounter = -1;

    /**
     * @var  float
     */
    private $chartHeight = 200;

    /**
     * @var  string
     */
    private $chartTitle = '';

    /**
     * @var  Color
     */
    private $chartTitleColor;

    /**
     * @var  string
     */
    private $chartTitleFont = 'trebucbd';

    /**
     * @var  float
     */
    private $chartTitleMargin = 15;

    /**
     * @var  float
     */
    private $chartTitleSize = 10;

    /**
     * @var  float
     */
    private $chartWidth = 300;

    /**
     * @var  DatasetCollection
     */
    private $data;

    /**
     * @var  float
     */
    private $dataGridOverflow = 5;

    /**
     * @var  int
     */
    private $defaultColorScheme = Visualisation::EXCEL2007_COLORS;

    /**
     * @var  int
     */
    private $defaultDatasetColorCounter = -1;

    /**
     * @var  bool
     */
    private $defaultDrawSmoothLines = false;

    /**
     * @var  bool
     */
    private $defaultDrawSquaredLines = false;

    /**
     * @var  int
     */
    private $defaultHistogramBarAmount = 8;

    /**
     * @var  float
     */
    private $defaultLineGapLimit = 2;

    /**
     * @var  float
     */
    private $defaultLineThickness = 1;

    /**
     * @var  float
     */
    private $defaultMarkerSize = 4;

    /**
     * @var  int
     */
    private $defaultMarkerType = Visualisations\Line::CIRCLE_MARKER;

    /**
     * @var  bool
     */
    private $defaultSetMissingXValuesToZero = true;

    /**
     * @var  float
     */
    private $defaultSubcurveWidth = 0;

    /**
     * @var  int
     */
    private $defaultVisualisationType = Visualisation::LINE;

    /**
     * @var  string[]
     */
    private $errors;

    /**
     * @var  string
     */
    private $fileDir;

    /**
     * @var  string  string
     */
    private $fontDirectory;

    /**
     * @var  Color
     */
    private $gridColor;

    /**
     * @var  Image
     */
    private $im;

    /**
     * @var  float
     */
    private $imageBottomPadding = 15;

    /**
     * @var  int
     */
    private $imageHeight = 400;

    /**
     * @var  float
     */
    private $imageLeftPadding = 15;

    /**
     * @var  float
     */
    private $imageRightPadding = 15;

    /**
     * @var  float
     */
    private $imageTopPadding = 15;

    /**
     * @var  int
     */
    private $imageWidth = 600;

    /**
     * @var  string
     */
    private $isOnY2Marker = ' &#9658;';

    /**
     * @var  Legend
     */
    private $legend;

    /**
     * @var  Color
     */
    private $legendBackgroundColor;

    /**
     * @var  float
     */
    private $legendBackgroundOpacity = 80;

    /**
     * @var  float
     */
    private $legendBoxMargin = 5;

    /**
     * @var  float
     */
    private $legendBoxPadding = 3;

    /**
     * @var  float
     */
    private $legendInterlineMargin = 5;

    /**
     * @var  Color
     */
    private $legendLabelColor;

    /**
     * @var  string
     */
    private $legendLabelFont = 'arial';

    /**
     * @var  float
     */
    private $legendLabelSize = 9;

    /**
     * @var  string
     */
    private $legendMarker = '&#9632;';

    /**
     * @var  string
     */
    private $legendMarkerFont = 'arial';

    /**
     * @var  float
     */
    private $legendMarkerMargin = 3;

    /**
     * @var  float
     */
    private $legendMarkerSize = 8;

    /**
     * @var  int
     */
    private $legendPlacement = Legend::TOP_LEFT;

    /**
     * @var  string
     */
    private $locale;

    /**
     * @var  int
     */
    private $namelessDatasetCounter = -1;

    /**
     * @var  int
     */
    private $pseudoAntialiasing = 4;

    /**
     * @var  string[]
     */
    private $resolvedFonts = [];

    /**
     * @var  bool
     */
    private $showChartTitle = true;

    /**
     * @var  bool
     */
    private $showLegend = false;

    /**
     * @var  bool
     */
    private $showTicks = true;

    /**
     * @var  bool
     */
    private $showXAxisLabels = true;

    /**
     * @var  bool
     */
    private $showXAxisTitle = true;

    /**
     * @var  bool
     */
    private $showY2Axis = true;

    /**
     * @var  bool
     */
    private $showYAxisLabels = true;

    /**
     * @var  bool
     */
    private $showYAxisTitle = true;

    /**
     * @var  int
     */
    private $sizeLock = self::IMAGE_SIZE;

    /**
     * @var  Color
     */
    private $subgridColor;

    /**
     * @var  float
     */
    private $tickLength = 2;

    /**
     * @var  float
     */
    private $tickMargin = 5;

    /**
     * @var  bool
     */
    private $transparentBackground = true;

    /**
     * @var  Axes\X
     */
    private $xAxis;

    /**
     * @var
     */
    private $xAxisGivenMax;

    /**
     * @var
     */
    private $xAxisGivenMin;

    /**
     * @var  int
     */
    private $xAxisIntervalCountAim = 8;

    /**
     * @var  float
     */
    private $xAxisLabelAngle = 90;

    /**
     * @var  Color
     */
    private $xAxisLabelColor;

    /**
     * @var  string
     */
    private $xAxisLabelFont = 'calibri';

    /**
     * @var  string
     */
    private $xAxisLabelFormat;

    /**
     * @var  float
     */
    private $xAxisLabelSize = 8;

    /**
     * @var  int[]
     */
    private $xAxisNormalizationBases = [1, 2, 5];

    /**
     * @var  string
     */
    private $xAxisTitle = '';

    /**
     * @var  Color
     */
    private $xAxisTitleColor;

    /**
     * @var  string
     */
    private $xAxisTitleFont = 'trebuc';

    /**
     * @var  float
     */
    private $xAxisTitleMargin = 5;

    /**
     * @var  float
     */
    private $xAxisTitleSize = 10;

    /**
     * @var  int
     */
    private $xAxisType = Axes\X::NUMERIC;

    /**
     * @var  float
     */
    private $xm;

    /**
     * @var  float
     */
    private $xo;

    /**
     * @var  float
     */
    private $xRange;

    /**
     * @var  Axes\Y2
     */
    private $y2Axis;

    /**
     * @var  float
     */
    private $y2AxisGivenMax;

    /**
     * @var  float
     */
    private $y2AxisGivenMin;

    /**
     * @var  int
     */
    private $y2AxisIntervalCountAim = 6;

    /**
     * @var  string
     */
    private $y2AxisLabelFormat;

    /**
     * @var  int[]
     */
    private $y2AxisNormalizationBases = [1, 2, 5];

    /**
     * @var  string
     */
    private $y2AxisTitle = '';

    /**
     * @var  int
     */
    private $y2AxisType = Axes\Y::NUMERIC;

    /**
     * @var  Axes\Y
     */
    private $yAxis;

    /**
     * @var  float
     */
    private $yAxisGivenMax;

    /**
     * @var  float
     */
    private $yAxisGivenMin;

    /**
     * @var  int
     */
    private $yAxisIntervalCountAim = 6;

    /**
     * @var  float
     */
    private $yAxisLabelAngle = 0;

    /**
     * @var  Color
     */
    private $yAxisLabelColor;

    /**
     * @var  string
     */
    private $yAxisLabelFont = 'calibri';

    /**
     * @var  string
     */
    private $yAxisLabelFormat;

    /**
     * @var  float
     */
    private $yAxisLabelSize = 8;

    /**
     * @var  int[]
     */
    private $yAxisNormalizationBases = [1, 2, 5];

    /**
     * @var  string
     */
    private $yAxisTitle = '';

    /**
     * @var  Color
     */
    private $yAxisTitleColor;

    /**
     * @var  string
     */
    private $yAxisTitleFont = 'trebuc';

    /**
     * @var  float
     */
    private $yAxisTitleMargin = 5;

    /**
     * @var  float
     */
    private $yAxisTitleSize = 10;

    /**
     * @var  int
     */
    private $yAxisType = Axes\Y::NUMERIC;

    /**
     * @var  float
     */
    private $ym;

    /**
     * @var  float
     */
    private $yo;

    /**
     * @var  float
     */
    private $yRange;

    public function __construct()
    {
        $this->axisColor = Color::fromHtmlString('#000');
        $this->backgroundColor = Color::fromHtmlString('#fff');
        $this->chartTitleColor = Color::fromHtmlString('#000');
        $this->gridColor = Color::fromHtmlString('#c0c0d1');
        $this->legendLabelColor = Color::fromHtmlString('#000');
        $this->subgridColor = Color::fromHtmlString('#dfdff2');
        $this->xAxisLabelColor = Color::fromHtmlString('#000');
        $this->xAxisTitleColor = Color::fromHtmlString('#000');
        $this->yAxisLabelColor = Color::fromHtmlString('#000');
        $this->yAxisTitleColor = Color::fromHtmlString('#000');

        $this->setData(new DatasetCollection($this));
        $this->setLegend(new Legend($this));
        $this->setXAxis(new Axes\X($this));
        $this->setYAxis(new Axes\Y($this));
        $this->setY2Axis(new Axes\Y2($this));
    }

    /**
     * Changes all fonts in one go: axis labels and titles, legend labels
     *
     * @param  string  $font
     */
    public function setAllFonts(string $font): void
    {
        $this->setXAxisLabelFont($font);
        $this->setYAxisLabelFont($font);
        $this->setLegendLabelFont($font);
        $this->setXAxisTitleFont($font);
        $this->setYAxisTitleFont($font);
    }

    public function setAxisColor(Color $value): void
    {
        $this->axisColor = $value;
    }

    public function getAxisColor(): Color
    {
        return $this->axisColor;
    }

    public function setBackgroundColor(Color $value): void
    {
        $this->backgroundColor = $value;
    }

    public function getBackgroundColor(): Color
    {
        return $this->backgroundColor;
    }

    public function setBarInnerMargin(float $value): void
    {
        $this->barInnerMargin = $value;
    }

    public function getBarInnerMargin(): float
    {
        return $this->barInnerMargin;
    }

    public function setBarMaximumWidth(float $value): void
    {
        $this->barMaximumWidth = $value;
    }

    public function getBarMaximumWidth(): float
    {
        return $this->barMaximumWidth;
    }

    public function setBarMinimumWidth(float $value): void
    {
        $this->barMinimumWidth = $value;
    }

    public function getBarMinimumWidth(): float
    {
        return $this->barMinimumWidth;
    }

    public function setBarOuterMargin(float $value): void
    {
        $this->barOuterMargin = $value;
    }

    public function getBarOuterMargin(): float
    {
        return $this->barOuterMargin;
    }

    public function getBarVisualisationCounter(): int
    {
        return $this->barVisualisationCounter;
    }

    public function setChartHeight(int $value): void
    {
        $this->chartHeight = $value;
    }

    public function getChartHeight(): int
    {
        return $this->chartHeight;
    }

    public function setChartTitle(string $value): void
    {
        $this->chartTitle = $value;
    }

    public function getChartTitle(): string
    {
        return $this->chartTitle;
    }

    public function setChartTitleColor(Color $value): void
    {
        $this->chartTitleColor = $value;
    }

    public function getChartTitleColor(): Color
    {
        return $this->chartTitleColor;
    }

    public function setChartTitleFont(string $value): void
    {
        $this->chartTitleFont = $value;
    }

    public function getChartTitleFont(): string
    {
        return $this->resolveFont($this->chartTitleFont);
    }

    public function setChartTitleMargin(float $value): void
    {
        $this->chartTitleMargin = $value;
    }

    public function getChartTitleMargin(): float
    {
        return $this->chartTitleMargin;
    }

    public function setChartTitleSize(float $value): void
    {
        $this->chartTitleSize = $value;
    }

    public function getChartTitleSize(): float
    {
        return $this->chartTitleSize;
    }

    public function setChartWidth(int $value): void
    {
        $this->chartWidth = $value;
    }

    public function getChartWidth(): int
    {
        return $this->chartWidth;
    }

    public function setData(DatasetCollection $value): void
    {
        $this->data = $value;
    }

    public function getData(): DatasetCollection
    {
        return $this->data;
    }

    public function setDataGridOverflow(float $value): void
    {
        $this->dataGridOverflow = $value;
    }

    public function getDataGridOverflow(): float
    {
        return $this->dataGridOverflow;
    }

    public function setDefaultColorScheme(int $value): void
    {
        if (!in_array($value, Visualisation::VALID_COLOR_SCHEMES)) {
            throw new InvalidInputException($value, 'default color scheme');
        }

        $this->defaultColorScheme = $value;
    }

    public function getDefaultColorScheme(): int
    {
        return $this->defaultColorScheme;
    }

    public function getDefaultDatasetColorCounter(): int
    {
        return $this->defaultDatasetColorCounter;
    }

    public function setDefaultDrawSmoothLines(bool $value): void
    {
        $this->defaultDrawSmoothLines = $value;
    }

    public function getDefaultDrawSmoothLines(): bool
    {
        return $this->defaultDrawSmoothLines;
    }

    public function setDefaultDrawSquaredLines(bool $value): void
    {
        $this->defaultDrawSquaredLines = $value;
    }

    public function getDefaultDrawSquaredLines(): bool
    {
        return $this->defaultDrawSquaredLines;
    }

    public function setDefaultHistogramBarAmount(int $value): void
    {
        $this->defaultHistogramBarAmount = $value;
    }

    public function getDefaultHistogramBarAmount(): int
    {
        return $this->defaultHistogramBarAmount;
    }

    public function setDefaultLineGapLimit(float $value): void
    {
        $this->defaultLineGapLimit = $value;
    }

    public function getDefaultLineGapLimit(): float
    {
        return $this->defaultLineGapLimit;
    }

    public function setDefaultLineThickness(float $value): void
    {
        $this->defaultLineThickness = $value;
    }

    public function getDefaultLineThickness(): float
    {
        return $this->defaultLineThickness;
    }

    public function setDefaultMarkerSize(float $value): void
    {
        $this->defaultMarkerSize = $value;
    }

    public function getDefaultMarkerSize(): float
    {
        return $this->defaultMarkerSize;
    }

    public function setDefaultMarkerType(int $value): void
    {
        if (!in_array($value, Visualisations\Line::VALID_MARKER_TYPES)) {
            throw new InvalidInputException($value, 'default line marker type');
        }

        $this->defaultMarkerType = $value;
    }

    public function getDefaultMarkerType(): int
    {
        return $this->defaultMarkerType;
    }

    public function setDefaultSetMissingXValuesToZero(bool $value): void
    {
        $this->defaultSetMissingXValuesToZero = $value;
    }

    public function getDefaultSetMissingXValuesToZero(): bool
    {
        return $this->defaultSetMissingXValuesToZero;
    }

    public function setDefaultSubcurveWidth(float $value): void
    {
        $this->defaultSubcurveWidth = $value;
    }

    public function getDefaultSubcurveWidth(): float
    {
        return $this->defaultSubcurveWidth;
    }

    public function setDefaultVisualisationType(int $value): void
    {
        if (!in_array($value, Visualisation::VALID_TYPES)) {
            throw new InvalidInputException($value, 'visualisation type');
        }

        $this->defaultVisualisationType = $value;
    }

    public function getDefaultVisualisationType(): int
    {
        return $this->defaultVisualisationType;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setFileDir(string $value): void
    {
        $this->fileDir = $value;
    }

    public function getFileDir(): string
    {
        return $this->fileDir;
    }

    public function setFontDirectory(string $directory): void
    {
        if (!Str::endsWith($directory, DIRECTORY_SEPARATOR)) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        $this->fontDirectory = $directory;

        Image::setFontDirectory($directory);
    }

    public function getFontDirectory(): string
    {
        return $this->fontDirectory;
    }

    public function setGridColor(Color $value): void
    {
        $this->gridColor = $value;
    }

    public function getGridColor(): Color
    {
        return $this->gridColor;
    }

    public function setIm(Image $im): void
    {
        $this->im = $im;
    }

    public function getIm(): Image
    {
        return $this->im;
    }

    public function setImageBottomPadding(float $value): void
    {
        $this->imageBottomPadding = $value;
    }

    public function getImageBottomPadding(): float
    {
        return $this->imageBottomPadding;
    }

    public function setImageHeight(int $value): void
    {
        $this->imageHeight = $value;
    }

    public function getImageHeight(): int
    {
        return $this->imageHeight;
    }

    public function setImageLeftPadding(float $value): void
    {
        $this->imageLeftPadding = $value;
    }

    public function getImageLeftPadding(): float
    {
        return $this->imageLeftPadding;
    }

    public function setImageRightPadding(float $value): void
    {
        $this->imageRightPadding = $value;
    }

    public function getImageRightPadding(): float
    {
        return $this->imageRightPadding;
    }

    public function setImageTopPadding(float $value): void
    {
        $this->imageTopPadding = $value;
    }

    public function getImageTopPadding(): float
    {
        return $this->imageTopPadding;
    }

    public function setImageWidth(int $value): void
    {
        $this->imageWidth = $value;
    }

    public function getImageWidth(): int
    {
        return $this->imageWidth;
    }

    public function increaseBarVisualisationCounter(): void
    {
        ++$this->barVisualisationCounter;
    }

    public function increaseDefaultDatasetColorCounter(): void
    {
        ++$this->defaultDatasetColorCounter;
    }

    public function increaseNamelessDatasetCounter(): void
    {
        ++$this->namelessDatasetCounter;
    }

    public function setIsOnY2Marker(string $value): void
    {
        $this->isOnY2Marker = $value;
    }

    public function getIsOnY2Marker(): string
    {
        return $this->isOnY2Marker;
    }

    public function setLegend(Legend $legend): void
    {
        $this->legend = $legend;
    }

    public function getLegend(): Legend
    {
        return $this->legend;
    }

    public function setLegendBackgroundColor(Color $value): void
    {
        $this->legendBackgroundColor = $value;
    }

    public function getLegendBackgroundColor(): Color
    {
        return $this->legendBackgroundColor;
    }

    public function setLegendBackgroundOpacity(float $value): void
    {
        $this->legendBackgroundOpacity = $value;
    }

    public function getLegendBackgroundOpacity(): float
    {
        return $this->legendBackgroundOpacity;
    }

    public function setLegendBoxMargin(float $value): void
    {
        $this->legendBoxMargin = $value;
    }

    public function getLegendBoxMargin(): float
    {
        return $this->legendBoxMargin;
    }

    public function setLegendBoxPadding(float $value): void
    {
        $this->legendBoxPadding = $value;
    }

    public function getLegendBoxPadding(): float
    {
        return $this->legendBoxPadding;
    }

    public function setLegendInterlineMargin(float $value): void
    {
        $this->legendInterlineMargin = $value;
    }

    public function getLegendInterlineMargin(): float
    {
        return $this->legendInterlineMargin;
    }

    public function setLegendLabelColor(Color $value): void
    {
        $this->legendLabelColor = $value;
    }

    public function getLegendLabelColor(): Color
    {
        return $this->legendLabelColor;
    }

    public function setLegendLabelFont(string $value): void
    {
        $this->legendLabelFont = $value;
    }

    public function getLegendLabelFont(): string
    {
        return $this->resolveFont($this->legendLabelFont);
    }

    public function setLegendLabelSize(float $value): void
    {
        $this->legendLabelSize = $value;
    }

    public function getLegendLabelSize(): float
    {
        return $this->legendLabelSize;
    }

    public function setLegendMarker(string $value): void
    {
        $this->legendMarker = $value;
    }

    public function getLegendMarker(): string
    {
        return $this->legendMarker;
    }

    public function setLegendMarkerFont(string $value): void
    {
        $this->legendMarkerFont = $value;
    }

    public function getLegendMarkerFont(): string
    {
        return $this->resolveFont($this->legendMarkerFont);
    }

    public function setLegendMarkerMargin(float $value): void
    {
        $this->legendMarkerMargin = $value;
    }

    public function getLegendMarkerMargin(): float
    {
        return $this->legendMarkerMargin;
    }

    public function setLegendMarkerSize(float $value): void
    {
        $this->legendMarkerSize = $value;
    }

    public function getLegendMarkerSize(): float
    {
        return $this->legendMarkerSize;
    }

    public function setLegendPlacement(int $value): void
    {
        if (!in_array($value, Legend::VALID_LOCATIONS)) {
            throw new InvalidInputException($value, 'legend placement location');
        }

        $this->legendPlacement = $value;
    }

    public function getLegendPlacement(): int
    {
        return $this->legendPlacement;
    }

    public function setLocale(string|null $value): void
    {
        $this->locale = $value;
    }

    public function getLocale(): string|null
    {
        return $this->locale;
    }

    public function getNamelessDatasetCounter(): int
    {
        return $this->namelessDatasetCounter;
    }

    public function setPseudoAntialiasing(float $value): void
    {
        $this->pseudoAntialiasing = $value;
    }

    public function getPseudoAntialiasing(): float
    {
        return $this->pseudoAntialiasing;
    }

    public function resolveFont(string $name): string
    {
        if (isset($this->resolvedFonts[$name])) {
            return $this->resolvedFonts[$name];
        }

        foreach (['.ttf', ''] as $postfix) {
            foreach (['', $this->fontDirectory] as $prefix) {
                $file = $prefix . $name . $postfix;

                if (file_exists($file) && !is_dir($file)) {
                    break(2);
                }
            }
        }

        $this->resolvedFonts[$name] = $file;

        return $file;
    }

    public function setShowChartTitle(bool $value): void
    {
        $this->showChartTitle = $value;
    }

    public function getShowChartTitle(): bool
    {
        return $this->showChartTitle;
    }

    public function setShowLegend(bool $value): void
    {
        $this->showLegend = $value;
    }

    public function getShowLegend(): bool
    {
        return $this->showLegend;
    }

    public function setShowTicks(bool $value): void
    {
        $this->showTicks = $value;
    }

    public function getShowTicks(): bool
    {
        return $this->showTicks;
    }

    public function setShowXAxisLabels(bool $value): void
    {
        $this->showXAxisLabels = $value;
    }

    public function getShowXAxisLabels(): bool
    {
        return $this->showXAxisLabels;
    }

    public function setShowXAxisTitle(bool $value): void
    {
        $this->showXAxisTitle = $value;
    }

    public function getShowXAxisTitle(): bool
    {
        return $this->showXAxisTitle;
    }

    public function setShowY2Axis(bool $value): void
    {
        $this->showY2Axis = $value;
    }

    public function getShowY2Axis(): bool
    {
        return $this->showY2Axis;
    }

    public function setShowYAxisLabels(bool $value): void
    {
        $this->showYAxisLabels = $value;
    }

    public function getShowYAxisLabels(): bool
    {
        return $this->showYAxisLabels;
    }

    public function setShowYAxisTitle(bool $value): void
    {
        $this->showYAxisTitle = $value;
    }

    public function getShowYAxisTitle(): bool
    {
        return $this->showYAxisTitle;
    }

    public function setSizeLock(int $value): void
    {
        if (!in_array($value, self::VALID_SIZE_LOCKS)) {
            throw new InvalidInputException($value, 'size lock type');
        }

        $this->sizeLock = $value;
    }

    public function getSizeLock(): int
    {
        return $this->sizeLock;
    }

    public function setSubgridColor(Color $value): void
    {
        $this->subgridColor = $value;
    }

    public function getSubgridColor(): Color
    {
        return $this->subgridColor;
    }

    public function setTickLength(float $value): void
    {
        $this->tickLength = $value;
    }

    public function getTickLength(): float
    {
        return $this->tickLength;
    }

    public function setTickMargin(float $value): void
    {
        $this->tickMargin = $value;
    }

    public function getTickMargin(): float
    {
        return $this->tickMargin;
    }

    public function setTransparentBackground(bool $value): void
    {
        $this->transparentBackground = $value;
    }

    public function getTransparentBackground(): bool
    {
        return $this->transparentBackground;
    }

    public function setXAxis(Axes\X $value): void
    {
        $this->xAxis = $value;
    }

    public function getXAxis(): Axes\X
    {
        return $this->xAxis;
    }

    public function setXAxisGivenMax(float|null $value): void
    {
        $this->xAxisGivenMax = $value;
    }

    public function getXAxisGivenMax(): float|null
    {
        return $this->xAxisGivenMax;
    }

    public function setXAxisGivenMin(float|null $value): void
    {
        $this->xAxisGivenMin = $value;
    }

    public function getXAxisGivenMin(): float|null
    {
        return $this->xAxisGivenMin;
    }

    public function setXAxisIntervalCountAim(int $value): void
    {
        $this->xAxisIntervalCountAim = $value;
    }

    public function getXAxisIntervalCountAim(): int
    {
        return $this->xAxisIntervalCountAim;
    }

    public function setXAxisLabelAngle(float $value): void
    {
        $this->xAxisLabelAngle = $value;
    }

    public function getXAxisLabelAngle(): float
    {
        return $this->xAxisLabelAngle;
    }

    public function setXAxisLabelColor(Color $value): void
    {
        $this->xAxisLabelColor = $value;
    }

    public function getXAxisLabelColor(): Color
    {
        return $this->xAxisLabelColor;
    }

    public function setXAxisLabelFont(string $value): void
    {
        $this->xAxisLabelFont = $value;
    }

    public function getXAxisLabelFont(): string
    {
        return $this->resolveFont($this->xAxisLabelFont);
    }

    public function setXAxisLabelFormat(string|null $value): void
    {
        $this->xAxisLabelFormat = $value;
    }

    public function getXAxisLabelFormat(): string|null
    {
        return $this->xAxisLabelFormat;
    }

    public function setXAxisLabelSize(float $value): void
    {
        $this->xAxisLabelSize = $value;
    }

    public function getXAxisLabelSize(): float
    {
        return $this->xAxisLabelSize;
    }

    public function setXAxisNormalizationBases(array $value): void
    {
        $this->xAxisNormalizationBases = $value;
    }

    public function getXAxisNormalizationBases(): array
    {
        return $this->xAxisNormalizationBases;
    }

    public function setXAxisTitle(string $value): void
    {
        $this->xAxisTitle = $value;
    }

    public function getXAxisTitle(): string
    {
        return $this->xAxisTitle;
    }

    public function setXAxisTitleColor(Color $value): void
    {
        $this->xAxisTitleColor = $value;
    }

    public function getXAxisTitleColor(): Color
    {
        return $this->xAxisTitleColor;
    }

    public function setXAxisTitleFont(string $value): void
    {
        $this->xAxisTitleFont = $value;
    }

    public function getXAxisTitleFont(): string
    {
        return $this->resolveFont($this->xAxisTitleFont);
    }

    public function setXAxisTitleMargin(float $value): void
    {
        $this->xAxisTitleMargin = $value;
    }

    public function getXAxisTitleMargin(): float
    {
        return $this->xAxisTitleMargin;
    }

    public function setXAxisTitleSize(float $value): void
    {
        $this->xAxisTitleSize = $value;
    }

    public function getXAxisTitleSize(): float
    {
        return $this->xAxisTitleSize;
    }

    public function setXAxisType(int $value): void
    {
        if (!in_array($value, self::VALID_X_AXIS_TYPES)) {
            throw new InvalidInputException($value, 'x-axis type');
        }

        $this->xAxisType = $value;
    }

    public function getXAxisType(): int
    {
        return $this->xAxisType;
    }

    public function setXm(float $value): void
    {
        $this->xm = $value;
        $this->xRange = $this->xm - $this->xo;
    }

    public function getXm(): float
    {
        return $this->xm;
    }

    public function setXo(float $value): void
    {
        $this->xo = $value;
        $this->xRange = $this->xm - $this->xo;
    }

    public function getXo(): float
    {
        return $this->xo;
    }

    public function setXRange(float $value): void
    {
        $this->xRange = $value;
    }

    public function getXRange(): float
    {
        return $this->xRange;
    }

    public function setY2Axis(Axes\Y2 $value): void
    {
        $this->y2Axis = $value;
    }

    public function getY2Axis(): Axes\Y2
    {
        return $this->y2Axis;
    }

    public function setY2AxisGivenMax(float|null $value): void
    {
        $this->y2AxisGivenMax = $value;
    }

    public function getY2AxisGivenMax(): float|null
    {
        return $this->y2AxisGivenMax;
    }

    public function setY2AxisGivenMin(float|null $value): void
    {
        $this->y2AxisGivenMin = $value;
    }

    public function getY2AxisGivenMin(): float|null
    {
        return $this->y2AxisGivenMin;
    }

    public function setY2AxisIntervalCountAim(int $value): void
    {
        $this->y2AxisIntervalCountAim = $value;
    }

    public function getY2AxisIntervalCountAim(): int
    {
        return $this->y2AxisIntervalCountAim;
    }

    public function setY2AxisLabelFormat(string|null $value): void
    {
        $this->y2AxisLabelFormat = $value;
    }

    public function getY2AxisLabelFormat(): string|null
    {
        return $this->y2AxisLabelFormat;
    }

    public function setY2AxisNormalizationBases(array $value): void
    {
        $this->y2AxisNormalizationBases = $value;
    }

    public function getY2AxisNormalizationBases(): array
    {
        return $this->y2AxisNormalizationBases;
    }

    public function setY2AxisTitle(string $value): void
    {
        $this->y2AxisTitle = $value;
    }

    public function getY2AxisTitle(): string
    {
        return $this->y2AxisTitle;
    }

    public function setY2AxisType(int $value): void
    {
        if (!in_array($value, self::VALID_Y_AXIS_TYPES)) {
            throw new InvalidInputException($value, 'secondary y-axis type');
        }

        $this->y2AxisType = $value;
    }

    public function getY2AxisType(): int
    {
        return $this->y2AxisType;
    }

    public function setYAxis(Axes\Y $value): void
    {
        $this->yAxis = $value;
    }

    public function getYAxis(): Axes\Y
    {
        return $this->yAxis;
    }

    public function setYAxisGivenMax(float|null $value): void
    {
        $this->yAxisGivenMax = $value;
    }

    public function getYAxisGivenMax(): float|null
    {
        return $this->yAxisGivenMax;
    }

    public function setYAxisGivenMin(float|null $value): void
    {
        $this->yAxisGivenMin = $value;
    }

    public function getYAxisGivenMin(): float|null
    {
        return $this->yAxisGivenMin;
    }

    public function setYAxisIntervalCountAim(int $value): void
    {
        $this->yAxisIntervalCountAim = $value;
    }

    public function getYAxisIntervalCountAim(): int
    {
        return $this->yAxisIntervalCountAim;
    }

    public function setYAxisLabelAngle(float $value): void
    {
        $this->yAxisLabelAngle = $value;
    }

    public function getYAxisLabelAngle(): float
    {
        return $this->yAxisLabelAngle;
    }

    public function setYAxisLabelColor(Color $value): void
    {
        $this->yAxisLabelColor = $value;
    }

    public function getYAxisLabelColor(): Color
    {
        return $this->yAxisLabelColor;
    }

    public function setYAxisLabelFont(string $value): void
    {
        $this->yAxisLabelFont = $value;
    }

    public function getYAxisLabelFont(): string
    {
        return $this->resolveFont($this->yAxisLabelFont);
    }

    public function setYAxisLabelFormat(string|null $value): void
    {
        $this->yAxisLabelFormat = $value;
    }

    public function getYAxisLabelFormat(): string|null
    {
        return $this->yAxisLabelFormat;
    }

    public function setYAxisLabelSize(float $value): void
    {
        $this->yAxisLabelSize = $value;
    }

    public function getYAxisLabelSize(): float
    {
        return $this->yAxisLabelSize;
    }

    public function setYAxisNormalizationBases(array $value): void
    {
        $this->yAxisNormalizationBases = $value;
    }

    public function getYAxisNormalizationBases(): array
    {
        return $this->yAxisNormalizationBases;
    }

    public function setYAxisTitle(string $value): void
    {
        $this->yAxisTitle = $value;
    }

    public function getYAxisTitle(): string
    {
        return $this->yAxisTitle;
    }

    public function setYAxisTitleColor(Color $value): void
    {
        $this->yAxisTitleColor = $value;
    }

    public function getYAxisTitleColor(): Color
    {
        return $this->yAxisTitleColor;
    }

    public function setYAxisTitleFont(string $value): void
    {
        $this->yAxisTitleFont = $value;
    }

    public function getYAxisTitleFont(): string
    {
        return $this->resolveFont($this->yAxisTitleFont);
    }

    public function setYAxisTitleMargin(float $value): void
    {
        $this->yAxisTitleMargin = $value;
    }

    public function getYAxisTitleMargin(): float
    {
        return $this->yAxisTitleMargin;
    }

    public function setYAxisTitleSize(float $value): void
    {
        $this->yAxisTitleSize = $value;
    }

    public function getYAxisTitleSize(): float
    {
        return $this->yAxisTitleSize;
    }

    public function setYAxisType(int $value): void
    {
        if (!in_array($value, self::VALID_Y_AXIS_TYPES)) {
            throw new InvalidInputException($value, 'y-axis type');
        }

        $this->yAxisType = $value;
    }

    public function getYAxisType(): int
    {
        return $this->yAxisType;
    }

    public function setYm(float $value): void
    {
        $this->ym = $value;
        $this->yRange = $this->ym - $this->yo;
    }

    public function getYm(): float
    {
        return $this->ym;
    }

    public function setYo(float $value): void
    {
        $this->yo = $value;
        $this->yRange = $this->ym - $this->yo;
    }

    public function getYo(): float
    {
        return $this->yo;
    }

    public function setYRange(float $value): void
    {
        $this->yRange = $value;
    }

    public function getYRange(): float
    {
        return $this->yRange;
    }
}
