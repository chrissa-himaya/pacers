<?php
namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentHistory;
use App\Models\Award;
use App\Models\AwardHistory;
use App\Models\PftHistory;
use App\Models\QRSProfile;
use App\Models\Schooling;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use App\Models\Officer;
use App\Models\Type;
use App\Models\Sourcedata;
use Illuminate\Support\Collection;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Storage;

class QRSProfilesController extends Controller
{
    protected $config_data;

    // ── Shared constants ──────────────────────────────────────────────────────
    private const RANKS       = ['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL'];
    private const RANK_ID_MAP = ['2LT' => 1, '1LT' => 2, 'CPT' => 3, 'MAJ' => 4, 'LTC' => 5, 'COL' => 6];
    private const RANK_COL_MAP = [1 => 'seclt', 2 => 'firstlt', 3 => 'cpt', 4 => 'maj', 5 => 'ltc', 6 => 'col'];

    public function __construct(QRSProfile $qrsprofile)
    {
        $columnHidden = array_merge($qrsprofile->getDates(), ['id']);
        $this->config_data = (object) [
            "module_name"       => "QRS Profiles",
            "module_perm_name"  => "qrsprofile",
            "module_route"      => "qrsprofiles",
            "module_view_folder"=> "officerdata.qrsprofile",
            "columnHidden"      => $columnHidden,
            "columnLabels"      => [''],
            "optionalFields"    => ['name', 'email'],
        ];
        view()->share('config_data', $this->config_data);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  SHARED DATA BUILDER  (used by both profile() and exportExcel())
    // ══════════════════════════════════════════════════════════════════════════
    /** @return array<string, mixed> */
    private function buildProfileData(Officer $officer): array
    {
        $data = $officer->load([
            'assignmenthistories',
            'assignmenthistories.assignments',
            'assignmenthistories.assignments.types',
            'schoolings',
            'schoolings.assignments',
            'awards',
            'awards.awards',
            'awards.dateranks',
            'pfts',
            'designations',
        ]);

        $types           = Type::whereIn('id', [1, 2, 3, 4])->with('assignments')->get();
        $sourcedataMap   = $this->buildSourcedataMap();
        $schoolingCriteria = Assignment::where('type_id', 5)->orderBy('id')->get();

        $totals = $this->buildTotals($data, 'year_earned');
        $computedTotals = $this->buildTotals($data, 'computed_points');

        $schoolingMap    = $this->buildSchoolingMap($data);
        $schoolingPoints = $this->buildSchoolingPoints($schoolingCriteria, $schoolingMap, $sourcedataMap);

        $awardsMap    = $this->buildAwardsMap($data);
        $awardsPoints = $this->buildAwardsPoints($data, $sourcedataMap);

        $pftMap    = $this->buildPftMap($data);
        $pftPoints = $this->buildPftPoints($data, $sourcedataMap);

        $qrsScores = [];
        foreach (self::RANK_ID_MAP as $rank => $rankId) {
            $qrsScores[$rank] = $this->computeQrsScore(
                $data, $rank, $rankId,
                $sourcedataMap, $computedTotals,
                $schoolingPoints, $schoolingCriteria,
                $awardsPoints, $pftPoints
            );
        }

        return compact(
            'data', 'types', 'totals', 'qrsScores',
            'sourcedataMap', 'schoolingCriteria', 'schoolingMap',
            'awardsMap', 'pftMap', 'schoolingPoints',
            'awardsPoints', 'pftPoints', 'computedTotals'
        );
    }

    // ── Helper builders ───────────────────────────────────────────────────────

    /** @return array<int, array<int, \App\Models\Sourcedata>> */
    private function buildSourcedataMap(): array
    {
        $map = [];
        foreach (Sourcedata::all() as $sd) {
            $map[$sd->assignment_id][$sd->rank_id] = $sd;
        }
        return $map;
    }

    /** @return \Illuminate\Support\Collection */
    private function buildTotals(Officer $data, string $field): Collection
    {
        return $data->assignmenthistories
            ->filter(fn($h) => $h->pri_sec_spec === 'primary'
                && !empty($h->assignment_id)
                && !empty($h->rank_during_completion))
            ->groupBy(fn($h) => $h->assignment_id)
            ->map(fn($rows) => $rows
                ->groupBy('rank_during_completion')
                ->map(fn($r2) => $r2->sum($field)));
    }

    /** @return \Illuminate\Support\Collection */
    private function buildSchoolingMap(Officer $data): Collection
    {
        return $data->schoolings
            ->sortByDesc('date_completed')
            ->groupBy('assignment_id')
            ->map(fn($records, $id) => $id == 44 ? $records : $records->first());
    }

    /** @return array<int, array<int, array{max: float|null, actual: float|null}>> */
    private function buildSchoolingPoints(
        Collection $criteria,
        Collection $schoolingMap,
        array $sourcedataMap
    ): array {
        $points = [];
        foreach ($criteria as $c) {
            $entry  = $schoolingMap->get($c->id);
            $record = ($entry instanceof Collection) ? $entry->first() : $entry;

            foreach (self::RANK_COL_MAP as $rankId => $col) {
                $max    = $sourcedataMap[$c->id][$rankId]->max_month ?? null;
                $actual = $record ? (float)($record->$col ?? 0) : null;
                $actual = ($actual > 0) ? $actual : null;
                if (!is_null($actual) && !is_null($max)) {
                    $actual = min($actual, (float)$max);
                }
                $points[$c->id][$rankId] = ['max' => $max, 'actual' => $actual];
            }
        }
        return $points;
    }

    /** @return \Illuminate\Support\Collection */
    private function buildAwardsMap(Officer $data): Collection
    {
        return $data->awards
            ->groupBy('date_rank_id')
            ->map(fn($awards) => $awards
                ->groupBy('award_id')
                ->map(fn($g) => [
                    'name'  => $g->first()->awards->code ?? 'Unknown',
                    'count' => $g->count(),
                ])->values());
    }

    /** @return array<string, array{current_max: float|null, prev_min: float|null, current_actual: float|null, prev_actual: float|null}> */
    private function buildAwardsPoints(Officer $data, array $sourcedataMap): array
    {
        $points = [];
        foreach (self::RANK_ID_MAP as $rankLabel => $rankId) {
            $sd         = $sourcedataMap[45][$rankId] ?? null;
            $prevRankId = $rankId > 1 ? $rankId - 1 : null;
            $sdPrev     = $prevRankId ? ($sourcedataMap[45][$prevRankId] ?? null) : null;

            $currentMax       = $sd ? (float)$sd->max_point : null;
            $currentActualRaw = (float)$data->awards->where('date_rank_id', $rankId)->sum('points');
            $currentActual    = $currentMax !== null
                ? min($currentActualRaw, $currentMax)
                : $currentActualRaw;

            $prevMax       = $sdPrev ? (float)$sdPrev->min_point : null;
            $prevActualRaw = $prevRankId
                ? (float)$data->awards->where('date_rank_id', $prevRankId)->sum('points')
                : 0;
            $prevActual = $prevMax !== null
                ? min($prevActualRaw, $prevMax)
                : $prevActualRaw;

            $points[$rankLabel] = [
                'current_max'    => $currentMax,
                'prev_min'       => $prevMax,
                'current_actual' => $currentActual > 0 ? $currentActual : null,
                'prev_actual'    => $prevActual > 0 ? $prevActual : null,
            ];
        }
        return $points;
    }

    /** @return \Illuminate\Support\Collection */
    private function buildPftMap(Officer $data): Collection
    {
        return $data->pfts
            ->sortByDesc('date_taken')
            ->groupBy('rank')
            ->map(fn($records) => $records->first());
    }

    /** @return array<string, array{max: float|null, actual: float|null}> */
    private function buildPftPoints(Officer $data, array $sourcedataMap): array
    {
        $points = [];
        foreach (self::RANK_ID_MAP as $rankLabel => $rankId) {
            $sd  = $sourcedataMap[46][$rankId] ?? null;
            $pft = $data->pfts->firstWhere('rank', $rankLabel);
            $points[$rankLabel] = [
                'max'    => $sd ? (float)$sd->max_point : null,
                'actual' => $pft && $pft->points > 0 ? (float)$pft->points : null,
            ];
        }
        return $points;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  QRS SCORE CALCULATOR
    // ══════════════════════════════════════════════════════════════════════════
    private function computeQrsScore(
        Officer $data,
        string  $rank,
        int     $rankId,
        array   $sourcedataMap,
        Collection $computedTotals,
        array   $schoolingPoints,
        Collection $schoolingCriteria,
        array   $awardsPoints,
        array   $pftPoints
    ): float {
        // 1. Assignments
        $assignmentTotal = 0.0;
        foreach ($computedTotals as $assignmentId => $rankTotals) {
            $gained  = (float)($rankTotals[$rank] ?? 0);
            $sd      = $sourcedataMap[$assignmentId][$rankId] ?? null;
            $maxPt   = $sd ? (float)$sd->max_point : null;
            $assignmentTotal += $maxPt !== null ? min($gained, $maxPt) : $gained;
        }

        // 2. Schooling
        $schoolingTotal = 0.0;
        foreach ($schoolingCriteria as $criteria) {
            $pt     = $schoolingPoints[$criteria->id][$rankId] ?? ['max' => null, 'actual' => null];
            $actual = (float)($pt['actual'] ?? 0);
            $max    = $pt['max'] !== null ? (float)$pt['max'] : null;
            $schoolingTotal += $max !== null ? min($actual, $max) : $actual;
        }

        // 3. Awards
        $ap          = $awardsPoints[$rank] ?? [];
        $awardsTotal = (float)($ap['current_actual'] ?? 0) + (float)($ap['prev_actual'] ?? 0);

        // 4. PFT
        $pp        = $pftPoints[$rank] ?? [];
        $pftActual = (float)($pp['actual'] ?? 0);
        $pftMax    = $pp['max'] !== null ? (float)$pp['max'] : null;
        $pftCapped = $pftMax !== null ? min($pftActual, $pftMax) : $pftActual;

        return round($assignmentTotal + $schoolingTotal + $awardsTotal + $pftCapped, 2);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ROUTES
    // ══════════════════════════════════════════════════════════════════════════
    public function profile(Officer $officer)
    {
        $ranks      = self::RANKS;
        $rankIdMap  = self::RANK_ID_MAP;
        $rankColumnMap = self::RANK_COL_MAP;

        $profileData = $this->buildProfileData($officer);

        return view(
            $this->config_data->module_view_folder . '.profile',
            array_merge($profileData, compact('ranks', 'rankIdMap', 'rankColumnMap'))
        );
    }

    public function index()
    {
        abort_if(
            Gate::denies($this->config_data->module_perm_name . '_access'),
            Response::HTTP_FORBIDDEN, '403 Forbidden'
        );
        return view($this->config_data->module_view_folder . '.index');
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  EXCEL EXPORT  (refactored — builds data via shared helper)
    // ══════════════════════════════════════════════════════════════════════════
    public function exportExcel(Officer $officer)
    {
        $ranks         = self::RANKS;
        $rankIdMap     = self::RANK_ID_MAP;
        $rankColumnMap = self::RANK_COL_MAP;

        $profileData = $this->buildProfileData($officer);
        extract($profileData);   // brings $data, $types, $totals, $qrsScores, etc. into scope

        // ─── Build spreadsheet ───────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('QRS Sheet');

        $yearsToYrM = static function (mixed $decimalYears): string {
            if (!$decimalYears || $decimalYears == 0) return '-';
            $totalMonths = (int)round((float)$decimalYears * 12);
            $yrs = intdiv($totalMonths, 12);
            $mos = $totalMonths % 12;
            if ($yrs > 0 && $mos > 0) return "{$yrs}yr{$mos}m";
            if ($yrs > 0)             return "{$yrs}yr";
            return "{$mos}m";
        };

        // ── Style presets ────────────────────────────────────────────────────
        $headerFill    = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A3A1F']];
        $subHeaderFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E6B36']];
        $lightFill     = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']];
        $whiteFont     = ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9];
        $boldFont      = ['bold' => true, 'size' => 9];
        $centerAlign   = ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER];
        $leftAlign     = ['horizontal' => Alignment::HORIZONTAL_LEFT,   'vertical' => Alignment::VERTICAL_CENTER];
        $thinBorder    = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'A5D6A7']];
        $allBorders    = ['allBorders' => $thinBorder];

        // ── Column widths ─────────────────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(22);
        foreach (range('C', 'H') as $c) $sheet->getColumnDimension($c)->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(2);
        foreach (['J','K','L','M','N','O','P','Q','R','S','T','U'] as $c) {
            $sheet->getColumnDimension($c)->setWidth(10);
        }

        $row = 1;

        // ── Letterhead ────────────────────────────────────────────────────────
        foreach ([
            ['PHILIPPINE ARMY',                    ['bold'=>true,'size'=>14,'color'=>['rgb'=>'1A3A1F']]],
            ['PERSONNEL MANAGEMENT CENTER',        ['bold'=>true,'size'=>11,'color'=>['rgb'=>'2E6B36']]],
            ['Fort Andres Bonifacio, Taguig City', ['size'=>9]],
        ] as [$text, $font]) {
            $sheet->mergeCells("A{$row}:U{$row}");
            $sheet->setCellValue("A{$row}", $text);
            $sheet->getStyle("A{$row}")->applyFromArray(['font' => $font, 'alignment' => $centerAlign]);
            $row++;
        }

        $sheet->mergeCells("A{$row}:U{$row}");
        $sheet->setCellValue("A{$row}", 'QUANTITATIVE RATING SYSTEM (QRS) SHEET');
        $sheet->getStyle("A{$row}:U{$row}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>12,'color'=>['rgb'=>'FFFFFF']],
            'fill'      => $headerFill,
            'alignment' => $centerAlign,
        ]);
        $row += 2;

        // Officer info
        $this->xlsOfficerInfo($sheet, $row, $data, $boldFont);
        $row += 4;

        // ── QRS rank columns (3 ranks × 2 cols) ──────────────────────────────
        $qrsRanks  = ['CPT', 'MAJ', 'LTC'];
        $qrsCols   = $this->buildQrsCols($sheet, $row, $qrsRanks, $subHeaderFill, $lightFill, $whiteFont, $allBorders);

        // ── Career Summary section ────────────────────────────────────────────
        $this->xlsCareerSummary(
            $sheet, $row, $types, $ranks, $totals, $computedTotals,
            $sourcedataMap, $rankIdMap, $qrsRanks, $qrsCols,
            $headerFill, $subHeaderFill, $lightFill, $whiteFont, $boldFont,
            $centerAlign, $leftAlign, $allBorders, $yearsToYrM
        );
        $row += 2;

        // ── Professional Preparation section ─────────────────────────────────
        $this->xlsSchooling(
            $sheet, $row, $schoolingCriteria, $schoolingMap, $schoolingPoints,
            $rankIdMap, $qrsRanks, $qrsCols,
            $headerFill, $subHeaderFill, $lightFill, $whiteFont, $boldFont,
            $centerAlign, $allBorders
        );
        $row += 2;

        // ── Awards section ────────────────────────────────────────────────────
        $this->xlsAwards(
            $sheet, $row, $awardsMap, $awardsPoints,
            $rankIdMap, $qrsRanks, $qrsCols,
            $headerFill, $subHeaderFill, $lightFill, $whiteFont, $boldFont,
            $centerAlign, $allBorders
        );
        $row += 3;

        // ── PFT section ───────────────────────────────────────────────────────
        $this->xlsPft(
            $sheet, $row, $pftMap, $pftPoints,
            $rankIdMap, $qrsRanks, $qrsCols,
            $headerFill, $subHeaderFill, $lightFill, $whiteFont, $boldFont,
            $centerAlign, $allBorders
        );
        $row += 2;

        // ── QRS Scores ────────────────────────────────────────────────────────
        foreach ($qrsRanks as $qr) {
            [$c1, $c2] = $qrsCols[$qr];
            $sheet->setCellValue("{$c1}{$row}", 'QRS SCORE:');
            $sheet->setCellValue("{$c2}{$row}", number_format((float)($qrsScores[$qr] ?? 0), 2));
            $sheet->getStyle("{$c1}{$row}")->applyFromArray([
                'font'      => ['bold'=>true,'size'=>10,'color'=>['rgb'=>'FFFFFF']],
                'fill'      => $headerFill,
                'alignment' => ['horizontal'=>Alignment::HORIZONTAL_RIGHT,'vertical'=>Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("{$c2}{$row}")->applyFromArray([
                'font'      => ['bold'=>true,'size'=>12,'color'=>['rgb'=>'FFFFFF']],
                'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'2E6B36']],
                'alignment' => $centerAlign,
            ]);
        }
        $row += 2;

        // Footer
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'QRS/QRS_v1.xlsx — DTG printed: '.now()->format('m/d/Y, h:i A'));
        $sheet->getStyle("A{$row}")->applyFromArray(['font' => ['size'=>8,'color'=>['rgb'=>'999999']]]);

        // Print setup
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.3)->setBottom(0.3)->setLeft(0.3)->setRight(0.3);

        // Write file
        $filename = 'QRS_'.str_replace(' ', '_', $data->NAME).'_'.date('Ymd').'.xlsx';
        $tempPath = storage_path('app/temp/'.$filename);
        if (!is_dir(storage_path('app/temp'))) mkdir(storage_path('app/temp'), 0755, true);
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  EXCEL SECTION HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    /** Write officer info rows and return next row */
    private function xlsOfficerInfo(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int &$row,
        Officer $data,
        array $boldFont
    ): void {
        $sheet->setCellValue("A{$row}", 'Name:');
        $sheet->getStyle("A{$row}")->applyFromArray(['font' => $boldFont]);
        $sheet->mergeCells("B{$row}:D{$row}");
        $sheet->setCellValue("B{$row}", $data->RANK.' '.$data->NAME);
        $sheet->getStyle("B{$row}")->applyFromArray(['font' => ['bold'=>true,'size'=>11]]);
        $sheet->setCellValue("F{$row}", 'Date of Commissioning:');
        $sheet->getStyle("F{$row}")->applyFromArray(['font' => $boldFont]);
        $sheet->setCellValue("G{$row}", $data->DOC);
        $row++;

        $sheet->setCellValue("A{$row}", 'Designation:');
        $sheet->getStyle("A{$row}")->applyFromArray(['font' => $boldFont]);
        $sheet->setCellValue("B{$row}", $data->designations->name ?? '');
        $sheet->setCellValue("F{$row}", 'Date of Rank:');
        $sheet->getStyle("F{$row}")->applyFromArray(['font' => $boldFont]);
        $sheet->setCellValue("G{$row}", $data->DOR);
        $row++;

        $sheet->setCellValue("A{$row}", 'PM Code:');
        $sheet->getStyle("A{$row}")->applyFromArray(['font' => $boldFont]);
        $sheet->setCellValue("B{$row}", $data->PM_CODE);
        $row++;
    }

    /**
     * Build QRS column map and write rank sub-headers.
     * Returns ['CPT' => ['E','F'], 'MAJ' => ['G','H'], 'LTC' => ['I','J']] etc.
     * @return array<string, string[]>
     */
    private function buildQrsCols(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int $row,
        array $qrsRanks,
        array $subHeaderFill,
        array $lightFill,
        array $whiteFont,
        array $allBorders
    ): array {
        $centerAlign = ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER];
        $qrsCols = [];
        $colIdx  = 9; // J = index 10 (1-based)
        foreach ($qrsRanks as $qr) {
            $c1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $c2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 2);
            $sheet->mergeCells("{$c1}{$row}:{$c2}{$row}");
            $sheet->setCellValue("{$c1}{$row}", $qr);
            $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray([
                'font'      => $whiteFont,
                'fill'      => $subHeaderFill,
                'alignment' => $centerAlign,
                'borders'   => $allBorders,
            ]);
            $qrsCols[$qr] = [$c1, $c2];
            $colIdx += 2;
        }

        // Sub-sub-headers (next row)
        $nextRow = $row + 1;
        foreach ($qrsRanks as $qr) {
            [$c1, $c2] = $qrsCols[$qr];
            $sheet->setCellValue("{$c1}{$nextRow}", 'max points');
            $sheet->setCellValue("{$c2}{$nextRow}", 'actual points');
            $sheet->getStyle("{$c1}{$nextRow}:{$c2}{$nextRow}")->applyFromArray([
                'font'      => ['bold'=>true,'size'=>7],
                'fill'      => $lightFill,
                'alignment' => $centerAlign,
                'borders'   => $allBorders,
            ]);
        }
        return $qrsCols;
    }

    private function xlsCareerSummary(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int &$row,
        Collection $types,
        array $ranks,
        Collection $totals,
        Collection $computedTotals,
        array $sourcedataMap,
        array $rankIdMap,
        array $qrsRanks,
        array $qrsCols,
        array $headerFill,
        array $subHeaderFill,
        array $lightFill,
        array $whiteFont,
        array $boldFont,
        array $centerAlign,
        array $leftAlign,
        array $allBorders,
        callable $yearsToYrM
    ): void {
        // Section header
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'Career Summary');
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray(['font'=>$whiteFont,'fill'=>$headerFill,'alignment'=>$centerAlign]);

        $sheet->mergeCells("J{$row}:U{$row}");
        $sheet->setCellValue("J{$row}", 'QRS Requirements');
        $sheet->getStyle("J{$row}:U{$row}")->applyFromArray(['font'=>$whiteFont,'fill'=>$subHeaderFill,'alignment'=>$centerAlign]);
        $row++;

        // Column headers
        $sheet->setCellValue("A{$row}", 'CATEGORY');
        $sheet->setCellValue("B{$row}", 'CRITERIA');
        foreach (['C'=>'2LT','D'=>'1LT','E'=>'CPT','F'=>'MAJ','G'=>'LTC','H'=>'COL'] as $col => $r) {
            $sheet->setCellValue("{$col}{$row}", $r);
        }
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>8,'color'=>['rgb'=>'1A3A1F']],
            'fill'      => $lightFill,
            'alignment' => $centerAlign,
            'borders'   => $allBorders,
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        foreach ($qrsRanks as $qr) {
            [$c1, $c2] = $qrsCols[$qr];
            $sheet->setCellValue("{$c1}{$row}", 'max points');
            $sheet->setCellValue("{$c2}{$row}", 'actual points');
            $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray([
                'font'    => ['bold'=>true,'size'=>7],
                'fill'    => $lightFill,
                'alignment' => $centerAlign,
                'borders' => $allBorders,
            ]);
        }
        $row++;

        // Data rows
        $colLetters = ['C','D','E','F','G','H'];
        foreach ($types as $type) {
            $criteria = $type->assignments;
            $rowspan  = max(1, $criteria->count());

            foreach ($criteria as $i => $assignment) {
                if ($i === 0 && $rowspan > 1) {
                    $sheet->mergeCells("A{$row}:A".($row + $rowspan - 1));
                }
                if ($i === 0) {
                    $sheet->setCellValue("A{$row}", $type->name);
                    $sheet->getStyle("A{$row}")->applyFromArray(['font'=>$boldFont,'alignment'=>$leftAlign]);
                }
                $sheet->setCellValue("B{$row}", $assignment->name);

                foreach ($ranks as $ri => $rank) {
                    $val = (float)data_get($totals, $assignment->id.'.'.$rank, 0);
                    $fmt = $yearsToYrM($val);
                    $sheet->setCellValue($colLetters[$ri].$row, $fmt);
                    $sheet->getStyle($colLetters[$ri].$row)->applyFromArray(['alignment'=>$centerAlign]);
                    if ($fmt !== '-') {
                        $sheet->getStyle($colLetters[$ri].$row)->applyFromArray([
                            'font' => ['bold'=>true,'size'=>9,'color'=>['rgb'=>'1A3A1F']],
                            'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'E8F5E9']],
                        ]);
                    }
                }

                foreach ($qrsRanks as $qr) {
                    [$c1, $c2] = $qrsCols[$qr];
                    $qrId = $rankIdMap[$qr];
                    $sd   = $sourcedataMap[$assignment->id][$qrId] ?? null;
                    $mP   = $sd ? number_format((float)$sd->max_point, 2) : '-';
                    $g    = (float)data_get($computedTotals, $assignment->id.'.'.$qr, 0);
                    $mPt  = $sd ? (float)$sd->max_point : null;
                    $gc   = $mPt !== null ? min($g, $mPt) : $g;
                    $gd   = $gc > 0 ? number_format($gc, 2) : '-';

                    $sheet->setCellValue("{$c1}{$row}", $mP);
                    $sheet->setCellValue("{$c2}{$row}", $gd);
                    $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray(['alignment'=>$centerAlign,'borders'=>$allBorders]);
                    if ($gd !== '-') {
                        $sheet->getStyle("{$c2}{$row}")->applyFromArray([
                            'font' => ['bold'=>true,'color'=>['rgb'=>'1A3A1F']],
                            'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'C8E6CB']],
                        ]);
                    }
                }

                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray(['borders'=>$allBorders]);
                $row++;
            }

            if ($criteria->isEmpty()) {
                $sheet->setCellValue("A{$row}", $type->name);
                $sheet->setCellValue("B{$row}", '—');
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray(['borders'=>$allBorders]);
                $row++;
            }
        }
    }

    private function xlsSchooling(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int &$row,
        Collection $schoolingCriteria,
        Collection $schoolingMap,
        array $schoolingPoints,
        array $rankIdMap,
        array $qrsRanks,
        array $qrsCols,
        array $headerFill,
        array $subHeaderFill,
        array $lightFill,
        array $whiteFont,
        array $boldFont,
        array $centerAlign,
        array $allBorders
    ): void {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'Professional Preparation & Development');
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray(['font'=>$whiteFont,'fill'=>$headerFill,'alignment'=>$centerAlign]);

        $sheet->mergeCells("J{$row}:U{$row}");
        $sheet->setCellValue("J{$row}", 'Schooling Points');
        $sheet->getStyle("J{$row}:U{$row}")->applyFromArray(['font'=>$whiteFont,'fill'=>$subHeaderFill,'alignment'=>$centerAlign]);
        $row++;

        // Column headers
        $sheet->setCellValue("A{$row}", 'CATEGORY');
        $sheet->setCellValue("B{$row}", 'CRITERIA');
        $sheet->mergeCells("C{$row}:F{$row}");
        $sheet->setCellValue("C{$row}", 'COURSE');
        $sheet->setCellValue("G{$row}", 'RATING');
        $sheet->setCellValue("H{$row}", 'STANDING');
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>8,'color'=>['rgb'=>'1A3A1F']],
            'fill'      => $lightFill,
            'alignment' => $centerAlign,
            'borders'   => $allBorders,
        ]);

        foreach ($qrsRanks as $qr) {
            [$c1, $c2] = $qrsCols[$qr];
            $sheet->setCellValue("{$c1}{$row}", 'max points');
            $sheet->setCellValue("{$c2}{$row}", 'actual points');
            $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray([
                'font'    => ['bold'=>true,'size'=>7],
                'fill'    => $lightFill,
                'alignment' => $centerAlign,
                'borders' => $allBorders,
            ]);
        }
        $row++;

        $firstRow = true;
        foreach ($schoolingCriteria as $criteria) {
            $entry   = $schoolingMap->get($criteria->id);
            $isMulti = $criteria->id == 44 && $entry;
            $record  = ($entry instanceof Collection) ? $entry->first() : $entry;

            if ($firstRow) {
                $sheet->setCellValue("A{$row}", 'Professional Preparation and Development');
                if ($schoolingCriteria->count() > 1) {
                    $sheet->mergeCells("A{$row}:A".($row + $schoolingCriteria->count() - 1));
                }
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => $boldFont,
                    'alignment' => ['vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],
                ]);
                $firstRow = false;
            }

            $sheet->setCellValue("B{$row}", $criteria->name);

            $courseName = '-';
            if ($isMulti && $entry instanceof Collection && $entry->isNotEmpty()) {
                $courseName = $entry->map(fn($s) => trim(($s->schoolingnames->name ?? '').' '.($s->classname ?? '')))->filter()->implode(', ');
            } elseif ($record && $record->date_completed) {
                $courseName = ($record->schoolingnames->name ?? '').' '.($record->classname ?? '');
            }
            $sheet->mergeCells("C{$row}:F{$row}");
            $sheet->setCellValue("C{$row}", $courseName);
            $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);

            // Enhancement #3: rating rounded to 2 decimals
            $ratingVal = $record && $record->rating ? round((float)$record->rating, 2) : '-';
            $sheet->setCellValue("G{$row}", $ratingVal);
            $sheet->setCellValue("H{$row}", ($record->standing ?? '-').' / '.($record->total_student ?? '-'));
            $sheet->getStyle("G{$row}:H{$row}")->applyFromArray(['alignment'=>$centerAlign]);
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray(['borders'=>$allBorders]);

            foreach ($qrsRanks as $qr) {
                [$c1, $c2] = $qrsCols[$qr];
                $qrId = $rankIdMap[$qr];
                $pt   = $schoolingPoints[$criteria->id][$qrId] ?? ['max'=>null,'actual'=>null];
                $sheet->setCellValue("{$c1}{$row}", !is_null($pt['max']) ? number_format((float)$pt['max'], 2) : '-');
                $aVal = (!is_null($pt['actual']) && $pt['actual'] > 0) ? number_format((float)$pt['actual'], 2) : '-';
                $sheet->setCellValue("{$c2}{$row}", $aVal);
                $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray(['alignment'=>$centerAlign,'borders'=>$allBorders]);
                if ($aVal !== '-') {
                    $sheet->getStyle("{$c2}{$row}")->applyFromArray([
                        'font' => ['bold'=>true,'color'=>['rgb'=>'1A3A1F']],
                        'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'C8E6CB']],
                    ]);
                }
            }
            $row++;
        }
    }

    private function xlsAwards(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int &$row,
        Collection $awardsMap,
        array $awardsPoints,
        array $rankIdMap,
        array $qrsRanks,
        array $qrsCols,
        array $headerFill,
        array $subHeaderFill,
        array $lightFill,
        array $whiteFont,
        array $boldFont,
        array $centerAlign,
        array $allBorders
    ): void {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'Awards and Decorations');
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray(['font'=>$whiteFont,'fill'=>$headerFill,'alignment'=>$centerAlign]);

        $sheet->mergeCells("J{$row}:U{$row}");
        $sheet->setCellValue("J{$row}", 'Awards Points');
        $sheet->getStyle("J{$row}:U{$row}")->applyFromArray(['font'=>$whiteFont,'fill'=>$subHeaderFill,'alignment'=>$centerAlign]);
        $row++;

        // Column headers — Enhancement #4: add Category column label
        $sheet->setCellValue("A{$row}", 'CATEGORY');
        foreach (['B'=>'2LT','C'=>'1LT','D'=>'CPT','E'=>'MAJ','F'=>'LTC','G'=>'COL'] as $col => $rank) {
            $sheet->setCellValue("{$col}{$row}", $rank);
        }
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>8,'color'=>['rgb'=>'1A3A1F']],
            'fill'      => $lightFill,
            'alignment' => $centerAlign,
            'borders'   => $allBorders,
        ]);

        foreach ($qrsRanks as $qr) {
            [$c1, $c2] = $qrsCols[$qr];
            $sheet->setCellValue("{$c1}{$row}", 'max points');
            $sheet->setCellValue("{$c2}{$row}", 'actual points');
            $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray([
                'font'    => ['bold'=>true,'size'=>7],
                'fill'    => $lightFill,
                'alignment' => $centerAlign,
                'borders' => $allBorders,
            ]);
        }
        $row++;

        // Awards data — Enhancement #4: 2 rows: current rank + prev rank
        $sheet->setCellValue("A{$row}", 'Awards & Decorations');
        $sheet->mergeCells("A{$row}:A".($row + 1));
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => $boldFont,
            'alignment' => ['vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],
        ]);

        // Row 1: current rank label
        $sheet->setCellValue("B{$row}", 'Current Rank');
        foreach ([1=>'C',2=>'D',3=>'E',4=>'F',5=>'G',6=>'H'] as $rankId => $col) {
            // not applicable for current rank row — leave awards display in original row below
        }
        // Actually write awards in one merged row as before, but add category sub-labels
        // Revert: keep original single data row + add label cells
        foreach ([1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G'] as $rankId => $col) {
            $rankAwards = $awardsMap->get($rankId, collect());
            $text = $rankAwards->map(fn($a) => $a['name'].'-'.$a['count'])->implode(', ');
            $sheet->setCellValue("{$col}{$row}", $text ?: '-');
            $sheet->getStyle("{$col}{$row}")->applyFromArray(['alignment'=>$centerAlign]);
        }
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray(['borders'=>$allBorders]);

        // Awards QRS points — current rank row
        foreach ($qrsRanks as $qr) {
            [$c1, $c2] = $qrsCols[$qr];
            $ap   = $awardsPoints[$qr] ?? [];
            $cMax = $ap['current_max'] ?? null;
            $cAct = $ap['current_actual'] ?? null;
            $sheet->setCellValue("{$c1}{$row}", !is_null($cMax) ? number_format((float)$cMax, 1) : '-');
            $sheet->setCellValue("{$c2}{$row}", !is_null($cAct) ? number_format((float)$cAct, 2) : '-');
            $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray(['alignment'=>$centerAlign,'borders'=>$allBorders]);
        }
        $row++;

        // Row 2: previous rank — Enhancement #4: show category label + prev points
        $sheet->setCellValue("A{$row}", ''); // merged from above
        foreach ([1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G'] as $rankId => $col) {
            // show nothing (prev rank breakdown not per-rank in original logic)
            $sheet->setCellValue("{$col}{$row}", '');
        }
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray(['borders'=>$allBorders]);

        foreach ($qrsRanks as $qr) {
            [$c1, $c2] = $qrsCols[$qr];
            $ap   = $awardsPoints[$qr] ?? [];
            $pMin = $ap['prev_min'] ?? null;
            $pAct = $ap['prev_actual'] ?? null;
            $sheet->setCellValue("{$c1}{$row}", !is_null($pMin) ? number_format((float)$pMin, 1) : '-');
            $sheet->setCellValue("{$c2}{$row}", !is_null($pAct) ? number_format((float)$pAct, 2) : '-');
            $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray(['alignment'=>$centerAlign,'borders'=>$allBorders]);
        }
        $row++;
    }

    private function xlsPft(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int &$row,
        Collection $pftMap,
        array $pftPoints,
        array $rankIdMap,
        array $qrsRanks,
        array $qrsCols,
        array $headerFill,
        array $subHeaderFill,
        array $lightFill,
        array $whiteFont,
        array $boldFont,
        array $centerAlign,
        array $allBorders
    ): void {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'Physical Fitness Test');
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray(['font'=>$whiteFont,'fill'=>$headerFill,'alignment'=>$centerAlign]);

        $sheet->mergeCells("J{$row}:U{$row}");
        $sheet->setCellValue("J{$row}", 'PFT Points');
        $sheet->getStyle("J{$row}:U{$row}")->applyFromArray(['font'=>$whiteFont,'fill'=>$subHeaderFill,'alignment'=>$centerAlign]);
        $row++;

        $sheet->setCellValue("A{$row}", 'DATA');
        foreach (['B'=>'2LT','C'=>'1LT','D'=>'CPT','E'=>'MAJ','F'=>'LTC','G'=>'COL'] as $col => $rank) {
            $sheet->setCellValue("{$col}{$row}", $rank);
        }
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>8,'color'=>['rgb'=>'1A3A1F']],
            'fill'      => $lightFill,
            'alignment' => $centerAlign,
            'borders'   => $allBorders,
        ]);

        foreach ($qrsRanks as $qr) {
            [$c1, $c2] = $qrsCols[$qr];
            $sheet->setCellValue("{$c1}{$row}", 'max points');
            $sheet->setCellValue("{$c2}{$row}", 'actual points');
            $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray([
                'font'    => ['bold'=>true,'size'=>7],
                'fill'    => $lightFill,
                'alignment' => $centerAlign,
                'borders' => $allBorders,
            ]);
        }
        $row++;

        $pftRows = [
            ['Rating',          fn($pft) => $pft ? number_format((float)$pft->rating, 2) : ''],
            ['Date Taken',      fn($pft) => $pft ? Carbon::parse($pft->date_taken)->format('d/M/Y') : ''],
            ['Supervising Unit',fn($pft) => $pft->supervising_unit ?? ''],
        ];

        $rankCols = ['2LT'=>'B','1LT'=>'C','CPT'=>'D','MAJ'=>'E','LTC'=>'F','COL'=>'G'];
        foreach ($pftRows as $idx => [$label, $getter]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->getStyle("A{$row}")->applyFromArray(['font'=>$boldFont]);
            foreach ($rankCols as $rank => $col) {
                $pft = $pftMap->get($rank);
                $sheet->setCellValue("{$col}{$row}", $getter($pft));
                $sheet->getStyle("{$col}{$row}")->applyFromArray(['alignment'=>$centerAlign]);
            }
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray(['borders'=>$allBorders]);

            // PFT points only on Rating row
            if ($idx === 0) {
                foreach ($qrsRanks as $qr) {
                    [$c1, $c2] = $qrsCols[$qr];
                    $pp = $pftPoints[$qr] ?? ['max'=>null,'actual'=>null];
                    $sheet->setCellValue("{$c1}{$row}", !is_null($pp['max']) ? number_format((float)$pp['max'], 1) : '-');
                    $sheet->setCellValue("{$c2}{$row}", !is_null($pp['actual']) ? number_format((float)$pp['actual'], 2) : '-');
                    $sheet->getStyle("{$c1}{$row}:{$c2}{$row}")->applyFromArray(['alignment'=>$centerAlign,'borders'=>$allBorders]);
                }
            }
            $row++;
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PHOTO UPLOAD
    // ══════════════════════════════════════════════════════════════════════════
    public function uploadPhoto(Request $request, Officer $officer)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,webp|max:1024',
        ]);

        try {
            if ($officer->photo_path && Storage::disk('public')->exists($officer->photo_path)) {
                Storage::disk('public')->delete($officer->photo_path);
            }
            $path = $request->file('photo')->store('officer-photos', 'public');
            $officer->update(['photo_path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully.',
                'path'    => asset('storage/'.$path),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo: '.$e->getMessage(),
            ], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  STUB METHODS (resource controller)
    // ══════════════════════════════════════════════════════════════════════════
    public function create()  {}
    public function store(Request $request) {}
    public function show(QRSProfile $qRSProfile) {}
    public function edit(QRSProfile $qRSProfile) {}
    public function update(Request $request, QRSProfile $qRSProfile) {}
    public function destroy(QRSProfile $qRSProfile) {}
}