<?php

namespace App\Pdf;

use App\Models\Estudiante;

class ReporteIndividualEstudiantePdf extends BasePdf
{
    private const DOCUMENTOS = [
        'certificado_habilitacion' => 'Certificado de Habilitacion',
        'copia_titulo_tec_medio'   => 'Copia del Titulo - Tecnico Medio',
        'copia_titulo_tec_superior'=> 'Copia del Titulo - Tecnico Superior',
        'copia_titulo_licenciatura'=> 'Copia del Titulo - Licenciatura',
    ];

    public function __construct()
    {
        parent::__construct('P'); // portrait
    }

    public function generate(Estudiante $estudiante): string
    {
        $this->AddPage();

        // Document title
        $this->SetFont('Arial', 'B', 12);
        $this->setTextClr(self::C_TEXT);
        $this->setDraw(self::C_RED);
        $this->SetLineWidth(0.5);
        $this->Cell($this->pw(), 7, $this->enc('FICHA DE SEGUIMIENTO DE TITULACION'), 0, 1, 'C');
        $this->Line($this->lMargin, $this->GetY(), $this->GetPageWidth() - $this->rMargin, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(4);

        $this->renderDatosPersonales($estudiante);
        $this->renderDocumentos($estudiante);
        $this->renderFormularios($estudiante);
        $this->renderRequisitos($estudiante);
        $this->renderObservaciones($estudiante);

        return $this->Output('S');
    }

    // ── Datos personales ──────────────────────────────────────────────────────

    private function renderDatosPersonales(Estudiante $estudiante): void
    {
        $this->sectionTitle('Datos del Estudiante');

        $pw     = $this->pw();
        $lW     = $pw * 0.25;
        $vW     = $pw * 0.25;
        $rowH   = 5.5;

        $this->setDraw([221, 221, 221]);

        $rows = [
            [null, null], // nombre (full-width)
            ['Matricula',     $estudiante->matricula,           'Gestion',        $estudiante->gestion],
            ['Genero',        ucfirst($estudiante->genero),     'Tipo documento', $estudiante->tipo_documento],
            ['N Documento',   strtoupper($estudiante->numero_documento), 'Sede', strtoupper($estudiante->sede->nombre ?? '—')],
            [null, null],   // carrera (full-width)
        ];

        // Nombre (full width)
        $this->renderFichaRow($lW, $vW, $pw, $rowH, 'Nombre completo', strtoupper($estudiante->nombre_completo), true);

        // Middle rows
        foreach ([
            ['Matricula',   $estudiante->matricula,  'Gestion',        $estudiante->gestion],
            ['Genero',      ucfirst($estudiante->genero), 'Tipo Documento', $estudiante->tipo_documento],
            ['N Documento', strtoupper($estudiante->numero_documento), 'Sede', strtoupper($estudiante->sede->nombre ?? '—')],
        ] as [$l1, $v1, $l2, $v2]) {
            $this->renderFichaRow($lW, $vW, $pw, $rowH, $l1, $v1, false, $l2, $v2);
        }

        // Carrera (full width)
        $this->renderFichaRow($lW, $vW, $pw, $rowH, 'Carrera', strtoupper($estudiante->carrera->nombre ?? '—'), true);
    }

    private function renderFichaRow(float $lW, float $vW, float $pw, float $h, string $lbl, string $val, bool $fullWidth, string $lbl2 = '', string $val2 = ''): void
    {
        $this->setFill(self::C_RED_LT);
        $this->setTextClr(self::C_RED_DK);
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell($lW, $h, $this->enc($lbl), 1, 0, 'L', true);

        $this->setFill([255, 255, 255]);
        $this->setTextClr(self::C_TEXT);
        $this->SetFont('Arial', '', 8.5);

        if ($fullWidth) {
            $this->Cell($pw - $lW, $h, $this->enc($val), 1, 1, 'L', true);
        } else {
            $this->Cell($vW, $h, $this->enc($val), 1, 0, 'L', true);
            $this->setFill(self::C_RED_LT);
            $this->setTextClr(self::C_RED_DK);
            $this->SetFont('Arial', 'B', 8.5);
            $this->Cell($lW, $h, $this->enc($lbl2), 1, 0, 'L', true);
            $this->setFill([255, 255, 255]);
            $this->setTextClr(self::C_TEXT);
            $this->SetFont('Arial', '', 8.5);
            $this->Cell($pw - $lW * 2 - $vW, $h, $this->enc($val2), 1, 1, 'L', true);
        }
    }

    // ── Documentos ────────────────────────────────────────────────────────────

    private function renderDocumentos(Estudiante $estudiante): void
    {
        $this->sectionTitle('Estado de Documentos');

        $pw   = $this->pw();
        $namW = $pw * 0.68;
        $stW  = $pw - $namW;
        $rowH = 5.5;

        $this->setFill(self::C_RED);
        $this->setTextClr([255, 255, 255]);
        $this->setDraw([107, 20, 20]);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($namW, 6, $this->enc('DOCUMENTO'), 1, 0, 'L', true);
        $this->Cell($stW,  6, $this->enc('ESTADO'), 1, 1, 'C', true);

        $this->setDraw([221, 221, 221]);

        foreach (self::DOCUMENTOS as $campo => $etiqueta) {
            $ok = !empty($estudiante->$campo);
            $this->setFill([255, 255, 255]);
            $this->setTextClr(self::C_TEXT);
            $this->SetFont('Arial', '', 8.5);
            $this->Cell($namW, $rowH, $this->enc($etiqueta), 1, 0, 'L', true);
            if ($ok) {
                $this->setFill(self::C_DOC_OK_BG);
                $this->setTextClr(self::C_DOC_OK_FG);
                $this->SetFont('Arial', 'B', 8.5);
                $this->Cell($stW, $rowH, $this->enc('Entregado'), 1, 1, 'C', true);
            } else {
                $this->setFill(self::C_DOC_NO_BG);
                $this->setTextClr(self::C_DOC_NO_FG);
                $this->SetFont('Arial', 'B', 8.5);
                $this->Cell($stW, $rowH, $this->enc('Pendiente'), 1, 1, 'C', true);
            }
        }
        $this->setTextClr(self::C_TEXT);
    }

    // ── Formularios ───────────────────────────────────────────────────────────

    private function renderFormularios(Estudiante $estudiante): void
    {
        $this->sectionTitle('Formularios de Inscripcion');

        $pw   = $this->pw();
        $rowH = 5.5;

        if ($estudiante->formulariosInscripcion->isEmpty()) {
            $this->setFill([255, 255, 255]);
            $this->setTextClr(self::C_GRAY);
            $this->setDraw([221, 221, 221]);
            $this->SetFont('Arial', 'I', 8.5);
            $this->Cell($pw, $rowH, $this->enc('Sin formularios de inscripcion registrados.'), 1, 1, 'L', true);
            $this->setTextClr(self::C_TEXT);
            return;
        }

        $this->setFill(self::C_RED);
        $this->setTextClr([255, 255, 255]);
        $this->setDraw([107, 20, 20]);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(15, 6, '#', 1, 0, 'C', true);
        $this->Cell($pw - 15, 6, $this->enc('Fecha de Recepcion'), 1, 1, 'L', true);

        $this->setDraw([221, 221, 221]);

        foreach ($estudiante->formulariosInscripcion as $i => $formulario) {
            $even = ($i % 2 === 1);
            $this->setFill($even ? self::C_ROW_ALT : [255, 255, 255]);
            $this->setTextClr(self::C_TEXT);
            $this->SetFont('Arial', '', 8.5);
            $this->Cell(15, $rowH, $i + 1, 1, 0, 'C', true);
            $fecha = \Carbon\Carbon::parse($formulario->fecha_recepcion)->format('d/m/Y');
            $this->Cell($pw - 15, $rowH, $fecha, 1, 1, 'L', true);
        }
    }

    // ── Requisitos ────────────────────────────────────────────────────────────

    private function renderRequisitos(Estudiante $estudiante): void
    {
        $this->sectionTitle('Expediente de Titulacion');

        $pw   = $this->pw();
        $rowH = 5.5;

        $tiposLabel = [
            'tec_medio'    => 'Tecnico Medio',
            'tec_superior' => 'Tecnico Superior',
            'licenciatura' => 'Licenciatura',
        ];

        $porTipo = $estudiante->requisitosDefensa->groupBy('tipo_titulo');

        foreach ($tiposLabel as $tipo => $label) {
            $items = $porTipo->get($tipo, collect());

            // Sub-section header
            $this->Ln(2);
            $this->setFill(self::C_RED_LT);
            $this->setTextClr(self::C_RED_DK);
            $this->setDraw([221, 221, 221]);
            $this->SetFont('Arial', 'B', 8.5);
            $this->Cell($pw, 5.5, $this->enc($label), 1, 1, 'L', true);

            if ($items->isEmpty()) {
                $this->setFill([255, 255, 255]);
                $this->setTextClr(self::C_GRAY);
                $this->SetFont('Arial', 'I', 8.5);
                $this->Cell($pw, $rowH, $this->enc('Sin requisitos registrados.'), 1, 1, 'L', true);
            } else {
                $this->setFill(self::C_RED);
                $this->setTextClr([255, 255, 255]);
                $this->setDraw([107, 20, 20]);
                $this->SetFont('Arial', 'B', 7.5);
                $this->Cell(12, 5.5, '#', 1, 0, 'C', true);
                $this->Cell($pw - 12, 5.5, $this->enc('Nombre del Requisito'), 1, 1, 'L', true);

                $this->setDraw([221, 221, 221]);
                foreach ($items as $j => $req) {
                    $even = ($j % 2 === 1);
                    $this->setFill($even ? self::C_ROW_ALT : [255, 255, 255]);
                    $this->setTextClr(self::C_TEXT);
                    $this->SetFont('Arial', '', 8.5);
                    $this->Cell(12, $rowH, $j + 1, 1, 0, 'C', true);
                    $this->Cell($pw - 12, $rowH, $this->enc(ucfirst($req->nombre)), 1, 1, 'L', true);
                }
            }
        }
        $this->setTextClr(self::C_TEXT);
    }

    // ── Observaciones ─────────────────────────────────────────────────────────

    private function renderObservaciones(Estudiante $estudiante): void
    {
        $this->sectionTitle('Observaciones');

        $pw  = $this->pw();
        $obs = $estudiante->observacion ? ucfirst($estudiante->observacion) : 'Sin observaciones.';

        $this->setFill([250, 250, 250]);
        $this->setTextClr([85, 85, 85]);
        $this->setDraw([221, 221, 221]);
        $this->SetFont('Arial', '', 8.5);
        $this->MultiCell($pw, 5.5, $this->enc($obs), 1, 'L', true);
        $this->setTextClr(self::C_TEXT);
    }
}
