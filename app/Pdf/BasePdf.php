<?php

namespace App\Pdf;

use FPDF;

class BasePdf extends FPDF
{
    // ── Colors (RGB) ──────────────────────────────────────────────────────────
    const C_RED      = [133, 26,  26];
    const C_RED_LT   = [242, 232, 232];
    const C_RED_DK   = [90,  16,  16];
    const C_TEXT     = [44,  62,  80];
    const C_GRAY     = [127, 140, 141];
    const C_ROW_ALT  = [253, 245, 245];
    const C_DOC_OK_BG = [212, 237, 218];
    const C_DOC_OK_FG = [21,  87,  36];
    const C_DOC_NO_BG = [248, 215, 218];
    const C_DOC_NO_FG = [114, 28,  36];
    const C_WARN_BG   = [255, 243, 205];
    const C_WARN_FG   = [133, 100, 4];

    protected string $orientation;
    private string $generadoPor    = '';
    /** @var string|null Temp PNG path for left logo */
    private ?string $logoLeftPath  = null;
    /** @var string|null Temp PNG path for right logo */
    private ?string $logoRightPath = null;

    public function __construct(string $orientation = 'L')
    {
        $this->orientation = $orientation;
        parent::__construct($orientation, 'mm', 'A4');
        $this->AliasNbPages();
        $this->SetAutoPageBreak(true, 18);
        $this->SetMargins(10, 40, 10);

        $this->logoLeftPath  = $this->webpToPng(public_path('assets/upea_logo.webp'));
        $this->logoRightPath = $this->webpToPng(public_path('assets/disbedc_logo.webp'));
    }

    public function __destruct()
    {
        if ($this->logoLeftPath  && file_exists($this->logoLeftPath))  @unlink($this->logoLeftPath);
        if ($this->logoRightPath && file_exists($this->logoRightPath)) @unlink($this->logoRightPath);
    }

    public function setGeneradoPor(string $nombre, string $rol = ''): void
    {
        $this->generadoPor = $rol ? "{$nombre} ({$rol})" : $nombre;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Convert WebP to a temp PNG file; returns path or null on failure. */
    private function webpToPng(string $src): ?string
    {
        if (!file_exists($src)) return null;
        $img = @imagecreatefromwebp($src);
        if (!$img) return null;
        $tmp = tempnam(sys_get_temp_dir(), 'pdf_logo_') . '.png';
        imagepng($img, $tmp);
        imagedestroy($img);
        return $tmp;
    }

    /** Encode UTF-8 string to windows-1252 for FPDF. */
    protected function enc(string $str): string
    {
        return iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $str) ?: $str;
    }

    protected function setFill(array $rgb): void
    {
        $this->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
    }

    protected function setDraw(array $rgb): void
    {
        $this->SetDrawColor($rgb[0], $rgb[1], $rgb[2]);
    }

    protected function setTextClr(array $rgb): void
    {
        $this->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
    }

    /** Usable page width (between left and right margins). */
    protected function pw(): float
    {
        return $this->GetPageWidth() - $this->lMargin - $this->rMargin;
    }

    // ── Header ────────────────────────────────────────────────────────────────

    public function Header(): void
    {
        // Red top bar
        $this->setFill(self::C_RED);
        $this->Rect(0, 0, $this->GetPageWidth(), 3, 'F');

        $pw = $this->GetPageWidth();

        // Logo dimensions
        // UPEA: 200x200 (square)  → h=25, w=25
        // DISBEDC: 200x133        → h=26, w=26*(200/133)≈39.1
        $logoLeftH  = 25;
        $logoLeftW  = 25;
        $logoRightH = 27;
        $logoRightW = round($logoRightH * (200 / 133)); // ~40mm

        if ($this->logoLeftPath) {
            $this->Image($this->logoLeftPath, 10, 5, $logoLeftW, $logoLeftH);
        }
        if ($this->logoRightPath) {
            $this->Image($this->logoRightPath, $pw - 10 - $logoRightW, 4, $logoRightW, $logoRightH);
        }

        // Center text zone
        $centerX = 10 + $logoLeftW + 4;
        $centerW  = $pw - $logoLeftW - $logoRightW - 28;

        $this->SetXY($centerX, 6);
        $this->SetFont('Arial', 'B', 11);
        $this->setTextClr(self::C_RED);
        $this->Cell($centerW, 6, $this->enc('UNIVERSIDAD PÚBLICA DE EL ALTO'), 0, 2, 'C');

        $this->SetFont('Arial', 'B', 7.5);
        $this->setTextClr(self::C_TEXT);
        $this->SetX($centerX);
        $this->Cell($centerW, 4.5, $this->enc('DIRECCIÓN DE INTERACCIÓN SOCIAL, BIENESTAR ESTUDIANTIL,'), 0, 2, 'C');
        $this->SetX($centerX);
        $this->Cell($centerW, 4.5, $this->enc('DEPORTES Y CULTURA'), 0, 2, 'C');

        $this->SetFont('Arial', '', 7);
        $this->setTextClr(self::C_GRAY);
        $this->SetX($centerX);
        $this->Cell($centerW, 4, $this->enc('El Alto - La Paz - Bolivia'), 0, 2, 'C');

        // Red bottom line
        $y = 34;
        $this->setDraw(self::C_RED);
        $this->SetLineWidth(0.5);
        $this->Line(10, $y, $pw - 10, $y);
        $this->SetLineWidth(0.2);

        $this->SetY($y + 3);
    }

    // ── Footer ────────────────────────────────────────────────────────────────

    public function Footer(): void
    {
        $this->SetY(-16);
        $this->setDraw(self::C_RED);
        $this->SetLineWidth(0.4);
        $this->Line($this->lMargin, $this->GetY(), $this->GetPageWidth() - $this->rMargin, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(1.5);

        $this->SetFont('Arial', '', 7.5);
        $this->setTextClr(self::C_GRAY);

        $fecha = date('d/m/Y H:i');
        // Línea 1: fecha (izquierda) | número de página (derecha)
        $this->Cell($this->pw() / 2, 4.5, $this->enc("Generado el: {$fecha}"), 0, 0, 'L');
        $this->Cell($this->pw() / 2, 4.5, $this->enc('Página ' . $this->PageNo() . ' de {nb}'), 0, 1, 'R');
        // Línea 2: usuario generador (izquierda) | nombre del sistema (derecha)
        $this->SetFont('Arial', 'I', 7);
        $porTexto = $this->generadoPor ? "Generado por: {$this->generadoPor}" : '';
        $this->Cell($this->pw() / 2, 4, $this->enc($porTexto), 0, 0, 'L');
        $this->Cell($this->pw() / 2, 4, $this->enc('Sistema de Reportes DISBEDC'), 0, 0, 'R');
    }

    // ── Section title bar ─────────────────────────────────────────────────────

    protected function sectionTitle(string $title): void
    {
        $this->Ln(4);
        $this->setFill(self::C_RED);
        $this->setTextClr([255, 255, 255]);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($this->pw(), 6, $this->enc(strtoupper($title)), 0, 1, 'L', true);
        $this->setTextClr(self::C_TEXT);
    }

    // ── Filters table ─────────────────────────────────────────────────────────

    /**
     * Render the "Filtros Aplicados" block.
     * Each row is an array of [label, value] pairs (1 = full-width, 2 = side by side).
     * Cells auto-expand vertically when text is too long.
     */
    protected function filtersTable(array $rows): void
    {
        $pw     = $this->pw();
        $labelW = $pw * 0.18;
        $valueW = $pw * 0.32;

        foreach ($rows as $row) {
            $this->filterRow($row, $labelW, $valueW, $pw);
        }
    }

    private function filterRow(array $pairs, float $labelW, float $valueW, float $pw): void
    {
        $lineH  = 4.5;
        $padX   = 2.0;
        $padY   = 1.5;
        $startY = $this->GetY();
        $startX = $this->lMargin;
        $isOne  = count($pairs) === 1;

        // Pass 1: calculate max row height using encoded strings (matches actual rendering)
        $maxH = $lineH + $padY * 2;
        foreach ($pairs as [$lbl, $val]) {
            $vW = $isOne ? ($pw - $labelW) : $valueW;
            $n  = $this->countFilterLines((string) $val, $vW - $padX * 2);
            // Add half-line safety buffer for FPDF internal rounding differences
            $maxH = max($maxH, $n * $lineH + $padY * 2 + ($lineH * 0.5));
        }

        // Pass 2: draw each label+value pair
        $curX = $startX;
        $this->setDraw([221, 221, 221]);

        foreach ($pairs as [$lbl, $val]) {
            $vW = $isOne ? ($pw - $labelW) : $valueW;

            // Label cell
            $this->setFill(self::C_RED_LT);
            $this->Rect($curX, $startY, $labelW, $maxH, 'FD');
            $this->setTextClr(self::C_RED_DK);
            $this->SetFont('Arial', 'B', 8.5);
            $this->SetXY($curX + $padX, $startY + $padY);
            $this->MultiCell($labelW - $padX * 2, $lineH, $this->enc($lbl), 0, 'L');

            // Value cell
            $this->setFill([255, 255, 255]);
            $this->Rect($curX + $labelW, $startY, $vW, $maxH, 'FD');
            $this->setTextClr(self::C_TEXT);
            $this->SetFont('Arial', '', 8.5);
            $this->SetXY($curX + $labelW + $padX, $startY + $padY);
            $this->MultiCell($vW - $padX * 2, $lineH, $this->enc($val), 0, 'L');

            $curX += $labelW + $vW;
        }

        $this->SetXY($startX, $startY + $maxH);
    }

    /**
     * Count lines needed by encoding the string first (windows-1252),
     * so GetStringWidth matches what FPDF actually renders.
     */
    private function countFilterLines(string $text, float $maxW): int
    {
        if ($maxW <= 0 || $text === '') return 1;
        // Encode to windows-1252 to match how FPDF renders it
        $text   = iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text) ?: $text;
        $this->SetFont('Arial', '', 8.5);
        $spaceW = $this->GetStringWidth(' ');
        $words  = array_filter(explode(' ', $text), fn($w) => $w !== '');
        $lines  = 1;
        $curW   = 0.0;

        foreach ($words as $word) {
            $wW = $this->GetStringWidth($word);
            if ($curW > 0 && $curW + $spaceW + $wW > $maxW) {
                $lines++;
                $curW = $wW;
            } else {
                $curW += ($curW > 0 ? $spaceW : 0) + $wW;
            }
        }
        return $lines;
    }

    // ── Table header row ──────────────────────────────────────────────────────

    /**
     * @param array $cols  [ [label, width, align?], ... ]
     */
    protected function tableHeader(array $cols, float $h = 6): void
    {
        $this->setFill(self::C_RED);
        $this->setTextClr([255, 255, 255]);
        $this->setDraw([107, 20, 20]);
        $this->SetFont('Arial', 'B', 7);
        foreach ($cols as [$label, $w, $align]) {
            $this->Cell($w, $h, $this->enc(strtoupper($label)), 1, 0, $align ?? 'L', true);
        }
        $this->Ln();
        $this->setTextClr(self::C_TEXT);
    }

    // ── Warning box ───────────────────────────────────────────────────────────

    protected function warningLimitado(int $total): void
    {
        $this->Ln(3);
        $this->setFill(self::C_WARN_BG);
        $this->setDraw([255, 193, 7]);
        $this->setTextClr(self::C_WARN_FG);
        $this->SetFont('Arial', 'B', 8);
        $msg = $this->enc("⚠ Aviso: Se encontraron {$total} estudiantes. Este reporte muestra los primeros 300. Aplique filtros más específicos para obtener un resultado completo.");
        $this->MultiCell($this->pw(), 5, $msg, 1, 'L', true);
        $this->setTextClr(self::C_TEXT);
        $this->Ln(2);
    }
}
