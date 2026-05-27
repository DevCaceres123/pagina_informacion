<?php

namespace App\Pdf;

use Illuminate\Support\Collection;

class ReportePendientesEstudiantesPdf extends BasePdf
{
    private const ETIQUETAS = [
        'certificado_habilitacion' => 'Cert. Hab.',
        'copia_titulo_tec_medio'   => 'Cop. T.M.',
        'copia_titulo_tec_superior'=> 'Cop. T.S.',
        'copia_titulo_licenciatura'=> 'Cop. Lic.',
    ];

    private const ETIQUETAS_RESUMEN = [
        'certificado_habilitacion' => 'Cert. Habilitacion faltante',
        'copia_titulo_tec_medio'   => 'Cop. Tit. Tec. Medio faltante',
        'copia_titulo_tec_superior'=> 'Cop. Tit. Tec. Superior faltante',
        'copia_titulo_licenciatura'=> 'Cop. Tit. Licenciatura faltante',
    ];

    public function generate(
        Collection $estudiantes,
        array      $filtros,
        array      $resumen,
        array      $modalidades,
        array      $camposModalidad,
        bool       $limitado,
        int        $totalSinLimite
    ): string {
        $this->AddPage();

        // Document title
        $this->SetFont('Arial', 'B', 11);
        $this->setTextClr(self::C_TEXT);
        $this->setDraw(self::C_RED);
        $this->SetLineWidth(0.5);
        $this->Cell($this->pw(), 7, $this->enc('REPORTE DE ESTUDIANTES CON DOCUMENTOS PENDIENTES'), 0, 1, 'C');
        $this->Line($this->lMargin, $this->GetY(), $this->GetPageWidth() - $this->rMargin, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(4);

        // Filters
        $this->sectionTitle('Filtros Aplicados');
        $this->filtersTable([
            [['Sede(s)',       $filtros['sedes']],     ['Carrera(s)',  $filtros['carreras']]],
            [['Genero',        $filtros['genero']],    ['Gestion',     $filtros['gestion']]],
            [['Modalidad(es)', $filtros['modalidades']], ['Mostrar',   $filtros['estado']]],
        ]);

        // Summary
        $this->renderResumen($resumen, $camposModalidad, $estudiantes->count());

        // Warning
        if ($limitado) {
            $this->warningLimitado($totalSinLimite);
        }

        // Listing
        $sectionLabel = $this->enc("Listado de Estudiantes — {$filtros['modalidades']}");
        $this->sectionTitle($sectionLabel);

        if ($estudiantes->isEmpty()) {
            $this->Ln(2);
            $this->SetFont('Arial', 'I', 9);
            $this->setTextClr(self::C_GRAY);
            $this->Cell($this->pw(), 10, $this->enc('No se encontraron estudiantes con documentos pendientes.'), 0, 1, 'C');
            $this->setTextClr(self::C_TEXT);
        } else {
            $this->renderListado($estudiantes, $camposModalidad);
        }

        return $this->Output('S');
    }

    // ── Resumen ───────────────────────────────────────────────────────────────

    private function renderResumen(array $resumen, array $camposModalidad, int $conPendiente): void
    {
        $this->sectionTitle('Resumen de Pendientes');

        $pw      = $this->pw();
        $labelW  = $pw * 0.30;
        $valW    = $pw * 0.05;
        $rowH    = 5.5;

        $this->setDraw([221, 221, 221]);
        $this->SetFont('Arial', 'B', 8);

        // First row: total + first 2 resumen items
        $resumenItems = array_merge(
            ['certificado_habilitacion' => $resumen['certificado'] ?? 0],
            array_intersect_key($resumen, array_flip($camposModalidad))
        );

        $chunks = array_chunk(array_keys($resumenItems), 2, true);

        foreach ($chunks as $ci => $chunk) {
            if ($ci === 0) {
                // "Total con al menos un pendiente"
                $this->setFill(self::C_RED_LT);
                $this->setTextClr(self::C_RED_DK);
                $this->Cell($labelW, $rowH, $this->enc('Total con al menos un pendiente'), 1, 0, 'L', true);
                $this->setFill([255, 243, 243]);
                $this->SetTextColor(133, 26, 26);
                $this->Cell($valW, $rowH, $conPendiente, 1, 0, 'C', true);
            } else {
                // Padding cells
                $this->setFill([255, 255, 255]);
                $this->Cell($labelW + $valW, $rowH, '', 1, 0, 'L', true);
            }
            foreach ($chunk as $campo) {
                $val = $resumenItems[$campo];
                $this->setFill(self::C_RED_LT);
                $this->setTextClr(self::C_RED_DK);
                $chunkLabelW = ($pw - $labelW - $valW) / count($chunk) - $valW;
                $this->Cell($chunkLabelW, $rowH, $this->enc(self::ETIQUETAS_RESUMEN[$campo] ?? $campo), 1, 0, 'L', true);
                $this->setFill([255, 243, 243]);
                $this->SetTextColor(133, 26, 26);
                $this->Cell($valW, $rowH, $val, 1, 0, 'C', true);
            }
            $this->Ln();
        }
        $this->setTextClr(self::C_TEXT);
    }

    // ── Listado ───────────────────────────────────────────────────────────────

    private function renderListado(Collection $estudiantes, array $camposModalidad): void
    {
        $docCols  = array_merge(['certificado_habilitacion'], $camposModalidad);
        $numDocs  = count($docCols);
        $pw       = $this->pw();

        // Base columns + doc columns
        if ($numDocs <= 3) {
            $base = [8, 54, 22, 11, 33, 46, 12];
            $docW = ($pw - array_sum($base)) / $numDocs;
        } else {
            $base = [7, 44, 19, 10, 27, 37, 11];
            $docW = ($pw - array_sum($base)) / $numDocs;
        }

        $cols = [
            ['#',       $base[0], 'C'],
            ['Nombre',  $base[1], 'L'],
            ['Matricula', $base[2], 'C'],
            ['Gen.',    $base[3], 'C'],
            ['Sede',    $base[4], 'L'],
            ['Carrera', $base[5], 'L'],
            ['Gest.',   $base[6], 'C'],
        ];
        foreach ($docCols as $campo) {
            $cols[] = [self::ETIQUETAS[$campo] ?? $campo, round($docW, 1), 'C'];
        }

        $this->tableHeader($cols, 5.5);

        $rowH = 4.5;
        $this->setDraw([221, 221, 221]);
        $this->SetFont('Arial', '', 7);

        foreach ($estudiantes as $i => $est) {
            $even = ($i % 2 === 1);
            $this->setFill($even ? self::C_ROW_ALT : [255, 255, 255]);
            $this->setTextClr(self::C_TEXT);

            $this->Cell($cols[0][1], $rowH, $i + 1, 1, 0, 'C', true);
            $this->Cell($cols[1][1], $rowH, $this->enc(strtoupper($est->nombre_completo)), 1, 0, 'L', true);
            $this->Cell($cols[2][1], $rowH, $this->enc($est->matricula), 1, 0, 'C', true);
            $this->Cell($cols[3][1], $rowH, $this->enc(mb_substr(ucfirst($est->genero), 0, 1)), 1, 0, 'C', true);
            $this->Cell($cols[4][1], $rowH, $this->enc(strtoupper($est->sede->nombre ?? '—')), 1, 0, 'L', true);
            $this->Cell($cols[5][1], $rowH, $this->enc(strtoupper($est->carrera->nombre ?? '—')), 1, 0, 'L', true);
            $this->Cell($cols[6][1], $rowH, $this->enc($est->gestion), 1, 0, 'C', true);

            foreach ($docCols as $campo) {
                $ok = !empty($est->$campo);
                if ($ok) {
                    $this->setFill(self::C_DOC_OK_BG);
                    $this->setTextClr(self::C_DOC_OK_FG);
                    $this->Cell($docW, $rowH, chr(251), 1, 0, 'C', true); // checkmark (Wingdings in Arial approx)
                } else {
                    $this->setFill(self::C_DOC_NO_BG);
                    $this->setTextClr(self::C_DOC_NO_FG);
                    $this->Cell($docW, $rowH, 'X', 1, 0, 'C', true);
                }
            }
            $this->Ln();
        }

        // Total row
        $this->setFill(self::C_RED);
        $this->setTextClr([255, 255, 255]);
        $this->SetFont('Arial', 'B', 7.5);
        $totalBaseW = array_sum(array_column(array_slice($cols, 0, count($cols) - $numDocs), 1));
        $this->Cell($totalBaseW, $rowH + 1, $this->enc('Total de estudiantes con pendientes:'), 1, 0, 'R', true);
        $this->Cell($docW * $numDocs, $rowH + 1, $estudiantes->count(), 1, 1, 'C', true);
        $this->setTextClr(self::C_TEXT);
    }

    // ── Aprobados para titularse ──────────────────────────────────────────────

    public function generateAprobados(Collection $estudiantes, array $filtros, array $modalidades): string
    {
        $this->AddPage();

        $labels = [
            'tec_medio'    => 'TECNICO MEDIO',
            'tec_superior' => 'TECNICO SUPERIOR',
            'licenciatura' => 'LICENCIATURA',
        ];
        $colLabels = [
            'tec_medio'    => 'T. Medio',
            'tec_superior' => 'T. Superior',
            'licenciatura' => 'Licenc.',
        ];

        $modCount = count($modalidades);
        $modalidadesTitle = $modCount === 3
            ? 'TODAS LAS MODALIDADES'
            : implode(' / ', array_map(fn($m) => $labels[$m] ?? strtoupper($m), $modalidades));

        // Document title
        $this->SetFont('Arial', 'B', 11);
        $this->setTextClr(self::C_TEXT);
        $this->setDraw(self::C_RED);
        $this->SetLineWidth(0.5);
        $this->Cell($this->pw(), 7, $this->enc('REPORTE DE ESTUDIANTES APROBADOS PARA TITULARSE'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(133, 26, 26);
        $this->Cell($this->pw(), 5, $this->enc('Modalidad(es): ' . $modalidadesTitle), 0, 1, 'C');
        $this->setTextClr(self::C_TEXT);
        $this->Line($this->lMargin, $this->GetY(), $this->GetPageWidth() - $this->rMargin, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(4);

        // Filters
        $this->sectionTitle('Filtros Aplicados');
        $this->filtersTable([
            [['Sede(s)',       $filtros['sedes']],      ['Carrera(s)',  $filtros['carreras']]],
            [['Genero',        $filtros['genero']],     ['Gestion',     $filtros['gestion']]],
            [['Modalidad(es)', $filtros['modalidades']], ['Mostrar',    $filtros['estado']]],
        ]);

        // Summary badge
        $this->Ln(3);
        $this->setFill([212, 237, 218]);
        $this->setTextClr([21, 87, 36]);
        $this->setDraw([21, 87, 36]);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($this->pw(), 7,
            $this->enc('Total de estudiantes en el listado: ' . $estudiantes->count()),
            1, 1, 'C', true
        );
        $this->setTextClr(self::C_TEXT);
        $this->Ln(2);

        // Section
        $this->sectionTitle('Listado de Estudiantes Aprobados — ' . $modalidadesTitle);

        if ($estudiantes->isEmpty()) {
            $this->Ln(2);
            $this->SetFont('Arial', 'I', 9);
            $this->setTextClr(self::C_GRAY);
            $this->Cell($this->pw(), 10,
                $this->enc('No se encontraron estudiantes para los filtros seleccionados.'),
                0, 1, 'C'
            );
            $this->setTextClr(self::C_TEXT);
            return $this->Output('S');
        }

        // Column widths based on number of selected modalidades
        $pw = $this->pw();
        if ($modCount === 1) {
            $baseWidths = [8, 65, 27, 17, 44, 76, 18];
        } elseif ($modCount === 2) {
            $baseWidths = [7, 60, 25, 16, 42, 70, 17];
        } else {
            $baseWidths = [7, 55, 23, 15, 39, 64, 16];
        }
        $modW = round(($pw - array_sum($baseWidths)) / $modCount, 2);

        $cols = [
            ['#',              $baseWidths[0], 'C'],
            ['Nombre Completo', $baseWidths[1], 'L'],
            ['Matricula',      $baseWidths[2], 'C'],
            ['Genero',         $baseWidths[3], 'C'],
            ['Sede',           $baseWidths[4], 'L'],
            ['Carrera',        $baseWidths[5], 'L'],
            ['Gestion',        $baseWidths[6], 'C'],
        ];
        foreach ($modalidades as $mod) {
            $cols[] = [$colLabels[$mod] ?? $mod, $modW, 'C'];
        }

        $this->tableHeader($cols);

        $rowH = 5;
        $this->setDraw([221, 221, 221]);
        $this->SetFont('Arial', '', 7.5);

        foreach ($estudiantes as $i => $est) {
            $even = ($i % 2 === 1);
            $this->setFill($even ? self::C_ROW_ALT : [255, 255, 255]);
            $this->setTextClr(self::C_TEXT);

            $this->Cell($cols[0][1], $rowH, $i + 1,                                               1, 0, 'C', true);
            $this->Cell($cols[1][1], $rowH, $this->enc(strtoupper($est->nombre_completo)),        1, 0, 'L', true);
            $this->Cell($cols[2][1], $rowH, $this->enc($est->matricula),                          1, 0, 'C', true);
            $this->Cell($cols[3][1], $rowH, $this->enc(ucfirst($est->genero)),                    1, 0, 'C', true);
            $this->Cell($cols[4][1], $rowH, $this->enc(strtoupper($est->sede->nombre ?? '—')),    1, 0, 'L', true);
            $this->Cell($cols[5][1], $rowH, $this->enc(strtoupper($est->carrera->nombre ?? '—')), 1, 0, 'L', true);
            $this->Cell($cols[6][1], $rowH, $this->enc($est->gestion),                            1, 0, 'C', true);

            foreach ($modalidades as $mod) {
                $campo    = 'aprobado_' . $mod;
                $aprobado = !empty($est->$campo);
                if ($aprobado) {
                    $this->setFill(self::C_DOC_OK_BG);
                    $this->setTextClr(self::C_DOC_OK_FG);
                    $this->Cell($modW, $rowH, 'SI', 1, 0, 'C', true);
                } else {
                    $this->setFill(self::C_DOC_NO_BG);
                    $this->setTextClr(self::C_DOC_NO_FG);
                    $this->Cell($modW, $rowH, 'NO', 1, 0, 'C', true);
                }
            }
            $this->Ln();
        }

        // Total row
        $this->setFill(self::C_DOC_OK_BG);
        $this->setTextClr(self::C_DOC_OK_FG);
        $this->SetFont('Arial', 'B', 8);
        $totalBaseW = array_sum(array_column($cols, 1)) - ($modW * $modCount);
        $this->Cell($totalBaseW,       $rowH + 1, $this->enc('Total en el listado:'), 1, 0, 'R', true);
        $this->Cell($modW * $modCount, $rowH + 1, $estudiantes->count(),              1, 1, 'C', true);
        $this->setTextClr(self::C_TEXT);

        return $this->Output('S');
    }
}
