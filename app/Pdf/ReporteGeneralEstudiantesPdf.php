<?php

namespace App\Pdf;

use Illuminate\Support\Collection;

class ReporteGeneralEstudiantesPdf extends BasePdf
{
    public function generate(
        Collection $estudiantes,
        array      $filtros,
        string     $vista,
        string     $agruparPor,
        Collection $totales,
        bool       $limitado,
        int        $totalSinLimite
    ): string {
        $this->AddPage();

        // Document title
        $this->SetFont('Arial', 'B', 11);
        $this->setTextClr(self::C_TEXT);
        $this->setDraw(self::C_RED);
        $this->SetLineWidth(0.5);
        $this->Cell($this->pw(), 7, $this->enc('REPORTE GENERAL DE ESTUDIANTES'), 0, 1, 'C');
        $this->Line($this->lMargin, $this->GetY(), $this->GetPageWidth() - $this->rMargin, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(4);

        // Filters
        $this->sectionTitle('Filtros Aplicados');
        $this->filtersTable([
            [['Sede(s)', $filtros['sedes']], ['Carrera(s)', $filtros['carreras']]],
            [['Género',  $filtros['genero']], ['Gestión',   $filtros['gestion']]],
        ]);

        if ($vista === 'listado') {
            $this->renderListado($estudiantes, $limitado, $totalSinLimite);
        } else {
            $this->renderTotales($totales, $agruparPor);
        }

        return $this->Output('S');
    }

    // ── Listado ───────────────────────────────────────────────────────────────

    private function renderListado(Collection $estudiantes, bool $limitado, int $totalSinLimite): void
    {
        if ($limitado) {
            $this->warningLimitado($totalSinLimite);
        }

        $this->sectionTitle('Listado de Estudiantes');

        if ($estudiantes->isEmpty()) {
            $this->Ln(2);
            $this->SetFont('Arial', 'I', 9);
            $this->setTextClr(self::C_GRAY);
            $this->Cell($this->pw(), 10, $this->enc('No se encontraron estudiantes con los filtros seleccionados.'), 0, 1, 'C');
            $this->setTextClr(self::C_TEXT);
            return;
        }

        // Col widths: total = 277mm (landscape 297 - 10*2 margins)
        $cols = [
            ['#',              8,  'C'],
            ['Nombre Completo', 62, 'L'],
            ['Matricula',      27, 'C'],
            ['Genero',         18, 'C'],
            ['Tipo Doc.',      17, 'C'],
            ['N Documento',    25, 'L'],
            ['Sede',           36, 'L'],
            ['Carrera',        63, 'L'],
            ['Gestion',        21, 'C'],
        ];

        $this->tableHeader($cols);

        $rowH = 5;
        $this->setDraw([221, 221, 221]);
        $this->SetFont('Arial', '', 7.5);

        foreach ($estudiantes as $i => $est) {
            $even = ($i % 2 === 1);
            if ($even) {
                $this->setFill(self::C_ROW_ALT);
            } else {
                $this->setFill([255, 255, 255]);
            }

            $this->Cell($cols[0][1], $rowH, $i + 1, 1, 0, 'C', true);
            $this->Cell($cols[1][1], $rowH, $this->enc(strtoupper($est->nombre_completo)), 1, 0, 'L', true);
            $this->Cell($cols[2][1], $rowH, $this->enc($est->matricula), 1, 0, 'C', true);
            $this->Cell($cols[3][1], $rowH, $this->enc(ucfirst($est->genero)), 1, 0, 'C', true);
            $this->Cell($cols[4][1], $rowH, $this->enc($est->tipo_documento), 1, 0, 'C', true);
            $this->Cell($cols[5][1], $rowH, $this->enc(strtoupper($est->numero_documento)), 1, 0, 'L', true);
            $this->Cell($cols[6][1], $rowH, $this->enc(strtoupper($est->sede->nombre ?? '—')), 1, 0, 'L', true);
            $this->Cell($cols[7][1], $rowH, $this->enc(strtoupper($est->carrera->nombre ?? '—')), 1, 0, 'L', true);
            $this->Cell($cols[8][1], $rowH, $this->enc($est->gestion), 1, 1, 'C', true);
        }

        // Total row
        $this->setFill(self::C_RED);
        $this->setTextClr([255, 255, 255]);
        $this->SetFont('Arial', 'B', 8);
        $totalW = array_sum(array_column($cols, 1)) - $cols[8][1];
        $this->Cell($totalW, $rowH + 1, $this->enc('Total de estudiantes:'), 1, 0, 'R', true);
        $this->Cell($cols[8][1], $rowH + 1, $estudiantes->count(), 1, 1, 'C', true);
        $this->setTextClr(self::C_TEXT);
    }

    // ── Totales ───────────────────────────────────────────────────────────────

    private function renderTotales(Collection $totales, string $agruparPor): void
    {
        $labelPrincipal  = $agruparPor === 'carrera' ? 'Carrera' : 'Sede';
        $labelSecundario = $agruparPor === 'carrera' ? 'Sede'    : 'Carrera';
        $groupKey        = $agruparPor === 'carrera' ? 'carrera_nombre' : 'sede_nombre';
        $subKey          = $agruparPor === 'carrera' ? 'sede_nombre'    : 'carrera_nombre';

        $grupos = $totales->groupBy($groupKey);

        $this->sectionTitle("Totales por {$labelPrincipal}");

        if ($grupos->isEmpty()) {
            $this->Ln(2);
            $this->SetFont('Arial', 'I', 9);
            $this->setTextClr(self::C_GRAY);
            $this->Cell($this->pw(), 10, $this->enc('No se encontraron datos con los filtros seleccionados.'), 0, 1, 'C');
            $this->setTextClr(self::C_TEXT);
            return;
        }

        // Col widths: 277mm
        $cols = [
            ['#',                  10, 'C'],
            [$labelPrincipal,      80, 'L'],
            [$labelSecundario,     75, 'L'],
            ['Masculino',          37, 'C'],
            ['Femenino',           37, 'C'],
            ['Total',              38, 'C'],
        ];

        $this->tableHeader($cols);

        $this->setDraw([221, 221, 221]);
        $rowH   = 5;
        $idx    = 1;
        $gMasc  = 0; $gFem = 0; $gTot = 0;

        foreach ($grupos as $grupoNombre => $subItems) {
            $mGrupo = $subItems->sum('masculino');
            $fGrupo = $subItems->sum('femenino');
            $tGrupo = $subItems->sum('total');
            $gMasc += $mGrupo; $gFem += $fGrupo; $gTot += $tGrupo;

            // Group header row
            $this->setFill(self::C_RED_LT);
            $this->setTextClr(self::C_RED_DK);
            $this->SetFont('Arial', 'B', 7.5);
            $this->Cell($cols[0][1], $rowH, $idx++, 1, 0, 'C', true);
            $this->Cell($cols[1][1] + $cols[2][1], $rowH, $this->enc(strtoupper($grupoNombre)), 1, 0, 'L', true);
            $this->Cell($cols[3][1], $rowH, $mGrupo, 1, 0, 'C', true);
            $this->Cell($cols[4][1], $rowH, $fGrupo, 1, 0, 'C', true);
            $this->Cell($cols[5][1], $rowH, $tGrupo, 1, 1, 'C', true);

            // Sub rows
            $this->SetFont('Arial', '', 7.5);
            foreach ($subItems as $sub) {
                $this->setFill([255, 255, 255]);
                $this->setTextClr(self::C_TEXT);
                $this->Cell($cols[0][1], $rowH, '', 1, 0, 'C', true);
                $this->Cell($cols[1][1], $rowH, '', 1, 0, 'L', true);
                $this->Cell($cols[2][1], $rowH, $this->enc(strtoupper($sub->$subKey)), 1, 0, 'L', true);
                $this->SetTextColor(26, 82, 118);
                $this->Cell($cols[3][1], $rowH, $sub->masculino, 1, 0, 'C', true);
                $this->SetTextColor(123, 36, 28);
                $this->Cell($cols[4][1], $rowH, $sub->femenino, 1, 0, 'C', true);
                $this->setTextClr(self::C_TEXT);
                $this->Cell($cols[5][1], $rowH, $sub->total, 1, 1, 'C', true);
            }
        }

        // Grand total row
        $this->setFill(self::C_RED);
        $this->setTextClr([255, 255, 255]);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($cols[0][1] + $cols[1][1] + $cols[2][1], $rowH + 1, $this->enc('TOTALES GENERALES'), 1, 0, 'L', true);
        $this->Cell($cols[3][1], $rowH + 1, $gMasc, 1, 0, 'C', true);
        $this->Cell($cols[4][1], $rowH + 1, $gFem,  1, 0, 'C', true);
        $this->Cell($cols[5][1], $rowH + 1, $gTot,  1, 1, 'C', true);
        $this->setTextClr(self::C_TEXT);
    }
}
