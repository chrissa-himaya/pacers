@extends('layouts.app')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;500;600;700&family=Source+Code+Pro:wght@400;500;600&display=swap');

:root {
    --pa-dark:      #1a3a1f;
    --pa-mid:       #2e6b36;
    --pa-accent:    #3d8a47;
    --pa-light:     #7ab87f;
    --pa-pale:      #c8e6cb;
    --pa-wash:      #e8f5e9;
    --pa-ice:       #f4faf4;
    --border-mid:   #a5d6a7;
    --border-light: #dceede;
    --border-cell:  #e0ede0;
    --text-dark:    #0d1f10;
    --text-mid:     #1a3a1f;
    --text-muted:   #4a7050;
    --text-value:   #163019;
    --ok:  #1b5e20; --warn: #e65100; --bad: #b71c1c;
    --radius-sm: 3px; --radius: 6px;
    --font-head: 'Libre Baskerville', Georgia, serif;
    --font-body: 'Source Sans 3', Arial, sans-serif;
    --font-mono:  Arial, 'Times New Roman', var(--font-body);
    --shadow-sm: 0 1px 3px rgba(30,80,30,.10);
    --shadow:    0 2px 8px rgba(30,80,30,.12);
    --base-font: 14px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: var(--font-body);
    font-size: var(--base-font);
    background: #eef3ee;
    color: var(--text-dark);
    -webkit-font-smoothing: antialiased;
    line-height: 1.4;
}

/* ── Shell ── */
.qrs-shell {
    max-width: 1600px;
    margin: 0 auto;
    padding: 12px 14px 24px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* ── Action bar ── */
.action-bar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 14px;
    background: var(--pa-dark);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}
.action-bar-left, .action-bar-right { display: flex; align-items: center; gap: 8px; }
.action-label { font-size: 13px; font-weight: 700; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .1em; }

.btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 14px; border-radius: var(--radius-sm);
    font-family: var(--font-body); font-size: 13px; font-weight: 600;
    cursor: pointer; border: none; transition: all .18s ease;
    text-transform: uppercase; letter-spacing: .04em;
}
.btn-back  { background: rgba(255,255,255,.12); color: rgba(255,255,255,.9); border: 1px solid rgba(255,255,255,.2); }
.btn-back:hover  { background: rgba(255,255,255,.22); }
.btn-print { background: var(--pa-accent); color: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.25); }
.btn-print:hover { background: var(--pa-mid); transform: translateY(-1px); }
.btn-excel { background: #1d6f42; color: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.25); }
.btn-excel:hover { background: #155a34; transform: translateY(-1px); }
.btn-excel svg { width: 16px; height: 16px; fill: #fff; }

/* ── Letterhead ── */
.letterhead {
    background: #fff;
    border: 2px solid var(--pa-mid);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}
.letterhead-stripe { height: 5px; background: linear-gradient(90deg,var(--pa-dark),var(--pa-mid) 40%,var(--pa-light) 70%,var(--pa-pale)); }
.letterhead-body {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: start;
    gap: 16px;
    padding: 14px 20px;
}
.org-crest { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.crest-logo {
    height: 110px;   /* adjust as needed */
    width: auto;
    object-fit: contain;
}
/* .crest-circle {
    width: 56px; height: 56px; border-radius: 50%;
    border: 2px solid var(--pa-mid); background: var(--pa-wash);
    display: grid; place-items: center; font-size: 22px; color: var(--pa-mid);
} */
.org-center { text-align: center; }
.org-name { font-family: var(--font-head); font-size: 15px; font-weight: 700; color: var(--pa-dark); letter-spacing: .25em; text-transform: uppercase; }
.org-sub  { font-size: 13px; color: var(--text-muted); font-weight: 600; letter-spacing: .08em; margin: 2px 0; }
.doc-title {
    display: inline-block; margin-top: 6px; padding: 4px 18px;
    background: var(--pa-dark); color: #fff;
    font-size: 13px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; border-radius: 999px;
}
.officer-card { display: flex; align-items: flex-start; gap: 12px; }
.officer-photo-wrap {
    width: 90px; height: 110px; border: 2px solid var(--pa-mid); border-radius: var(--radius-sm);
    overflow: hidden; flex-shrink: 0; background: var(--pa-wash);
    display: grid; place-items: center; color: var(--pa-light); font-size: 28px;
    position: relative; cursor: pointer;
}
.officer-photo-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.officer-photo-wrap .photo-placeholder { font-size: 28px; color: var(--pa-light); }
.officer-photo-wrap .photo-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,.45);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .2s ease; color: #fff; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .04em; text-align: center; line-height: 1.3;
}
.officer-photo-wrap:hover .photo-overlay { opacity: 1; }
.officer-photo-wrap input[type="file"] { display: none; }
.officer-name { font-family: var(--font-head); font-size: 18px; font-weight: 700; color: var(--pa-dark); margin-bottom: 5px; }
.officer-meta { display: grid; grid-template-columns: auto 1fr; gap: 2px 10px; font-size: 13px; }
.meta-lbl { color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
.meta-val { color: var(--text-dark); font-weight: 500; }

/* ══════════════════════════════════════════════
   PAIRED-ROW LAYOUT
══════════════════════════════════════════════ */
.body-grid { display: flex; flex-direction: column; gap: 8px; }

.pair-row {
    display: grid;
    grid-template-columns: 57% 1fr;
    gap: 8px;
    align-items: stretch;
}

.section-panel, .pts-panel {
    background: #fff;
    border: 1px solid var(--border-mid);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.section-panel:hover, .pts-panel:hover { box-shadow: var(--shadow); }
.section-panel, .pts-panel { overflow-x: auto; overflow-y: hidden; }

.section-head {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 12px;
    background: linear-gradient(135deg, var(--pa-dark), var(--pa-mid));
    color: #fff; flex-shrink: 0;
}
.section-head-icon {
    width: 22px; height: 22px; border-radius: 4px;
    background: rgba(255,255,255,.15); display: grid; place-items: center;
    font-size: 12px; flex-shrink: 0;
}
.section-head h2 {
    font-family: var(--font-body); font-size: 13px; font-weight: 700;
    letter-spacing: .10em; text-transform: uppercase; margin: 0; line-height: 1;
}
.pts-head {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 12px;
    background: linear-gradient(135deg, var(--pa-mid), var(--pa-accent));
    color: #fff; flex-shrink: 0;
}
.pts-head h2 {
    font-family: var(--font-body); font-size: 13px; font-weight: 700;
    letter-spacing: .10em; text-transform: uppercase; margin: 0; line-height: 1;
}

.rank-tabs {
    display: flex; gap: 3px;
    padding: 6px 8px 0;
    background: #fff;
    flex-shrink: 0;
}
.rank-tab {
    flex: 1; padding: 5px 4px;
    border-radius: var(--radius-sm) var(--radius-sm) 0 0;
    background: var(--pa-ice); border: 1px solid var(--border-light); border-bottom: none;
    text-align: center; cursor: pointer;
    font-family: var(--font-body); font-size: 12px; font-weight: 700;
    color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em;
    transition: all .15s ease; user-select: none;
}
.rank-tab:hover { background: var(--pa-wash); color: var(--pa-dark); }
.rank-tab.active { background: var(--pa-dark); color: #fff; border-color: var(--pa-dark); box-shadow: 0 -2px 6px rgba(30,80,30,.15); }

.pts-tab-body {
    flex: 1;
    border-top: 3px solid var(--pa-dark);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.pts-tab-content { display: none; flex-direction: column; flex: 1; }
.pts-tab-content.active { display: flex; }

/* ── Tables ── */
.tbl {
    width: 100%; border-collapse: collapse;
    font-size: var(--base-font); font-family: var(--font-body); table-layout: fixed;
}
.tbl thead th {
    background: var(--pa-wash); color: var(--pa-dark);
    font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    padding: 5px 8px; border: 1px solid var(--border-cell); white-space: nowrap;
    position: sticky; top: 0; z-index: 2; text-align: center;
}
.tbl td {
    padding: 4px 8px; border: 1px solid var(--border-cell);
    color: var(--text-mid); vertical-align: middle;
    overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
    font-size: var(--base-font);
}
.tbl tbody tr:nth-child(even) { background: var(--pa-ice); }
.tbl tbody tr:hover { background: var(--pa-wash); }
.tbl .cat-cell  { font-weight: 700; color: var(--pa-dark); white-space: normal; word-break: break-word; vertical-align: middle; font-size: 13px; background: rgba(200,230,203,.25); }
.tbl .crit-cell { white-space: normal; word-break: break-word; font-size: 13px; color: var(--text-mid); }
.tbl .rank-col  { text-align: center; font-family: var(--font-mono); font-size: 13px; font-weight: 600; color: var(--text-value); white-space: nowrap; }
.tbl .rank-col.has-value { color: var(--pa-dark); font-weight: 700; background: rgba(122,184,127,.15); }
.tbl .null-val { color: #bbb; font-size: 12px; }
.tbl .greyed   { background: #f8f8f8; color: #ccc; }

.career-tbl { min-width: 700px; }
.career-tbl col.col-cat  { width: 100px; }
.career-tbl col.col-crit { width: 160px; }
.career-tbl col.col-rank { width: 75px; }

.school-tbl { min-width: 700px; }
.school-tbl col.col-cat    { width: 120px; }
.school-tbl col.col-crit   { width: 140px; }
.school-tbl col.col-course { width: 260px; }
.school-tbl col.col-rating { width: 80px; }
.school-tbl col.col-stand  { width: 80px; }

.awards-tbl { min-width: 700px; }
.awards-tbl col.col-cat  { width: 120px; }
.awards-tbl col.col-rank { width: 96px; }
.awards-tbl td.award-cell { vertical-align: top; white-space: normal; line-height: 1.5; font-size: 13px; color: var(--text-mid); min-height: 36px; }
.award-badge { display: inline-block; padding: 1px 5px; border-radius: 3px; background: var(--pa-pale); color: var(--pa-dark); font-size: 12px; font-weight: 700; margin: 1px 2px; white-space: nowrap; }

.pft-tbl { min-width: 700px; }
.pft-tbl col.col-label { width: 100px; }
.pft-tbl col.col-rank  { width: 100px; }

.pts-tbl { font-size: 13px; }
.pts-tbl col.col-max    { width: 36%; }
.pts-tbl col.col-maxpt  { width: 32%; }
.pts-tbl col.col-gained { width: 32%; }
.pts-tbl col.col-half   { width: 50%; }
.pts-tbl thead th { font-size: 11px; padding: 4px 8px; background: var(--pa-wash); color: var(--pa-dark); }
.pts-tbl td { font-family: var(--font-mono); font-size: 13px; text-align: center; padding: 3px 8px; color: var(--text-value); border: 1px solid var(--border-cell); }
.pts-tbl tbody tr:nth-child(even) { background: var(--pa-ice); }
.pts-tbl td.has-val { font-weight: 700; color: var(--pa-dark); background: rgba(122,184,127,.12); }
.pts-tbl td.null-val { color: #ccc; font-weight: 400; }
.pts-tbl td.pts-row-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; background: rgba(200,230,203,.18); text-align: left; padding: 3px 6px; }

.qrs-score-box {
    display: grid; grid-template-columns: 1fr auto; align-items: center;
    background: linear-gradient(135deg, var(--pa-dark), var(--pa-mid));
    border-radius: var(--radius-sm); margin: 6px 8px; overflow: hidden;
    box-shadow: var(--shadow); flex-shrink: 0;
}
.qrs-score-label { padding: 8px 12px; color: rgba(255,255,255,.85); font-size: 12px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
.qrs-score-label span { display: block; font-size: 10px; color: rgba(255,255,255,.5); margin-top: 1px; text-transform: none; letter-spacing: .03em; }
.qrs-score-value { padding: 8px 18px; background: rgba(255,255,255,.12); border-left: 1px solid rgba(255,255,255,.15); font-family: var(--font-mono); font-size: 22px; font-weight: 700; color: #fff; text-align: center; min-width: 80px; }

.photo-toast {
    position: fixed; bottom: 20px; right: 20px;
    padding: 10px 20px; border-radius: 6px;
    color: #fff; font-weight: 600; font-size: 13px;
    z-index: 9999; opacity: 0; transition: opacity .3s ease; pointer-events: none;
}
.photo-toast.show { opacity: 1; }
.photo-toast.success { background: var(--pa-mid); }
.photo-toast.error { background: var(--bad); }

/* ══════════════════════════════════════════════
   PRINT — Replicates the on-screen layout faithfully
   Matches profile-layout.jpg: two-column grid, all ranks visible
══════════════════════════════════════════════ */
@media print {
    @page {
        size: A4 landscape;
        margin: 7mm 6mm;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Hide non-print elements */
    .action-bar,
    .rank-tabs,
    .photo-overlay,
    .photo-toast { display: none !important; }

    /*
     * Key technique: zoom the entire shell so it fits A4 landscape.
     * A4 landscape printable width ≈ 267mm - 12mm margins = 255mm ≈ 964px @96dpi.
     * Typical screen render width ~1480px → scale ≈ 0.65.
     * We use zoom which scales layout & fonts proportionally.
     */
    .qrs-shell {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        gap: 5px !important;
        zoom: 0.645;
        transform-origin: top left;
    }

    /* Letterhead — keep same structure, just tighten spacing */
    .letterhead {
        border: 1.5px solid var(--pa-mid) !important;
        border-radius: 4px !important;
        box-shadow: none !important;
    }
    .letterhead-stripe { height: 4px !important; }
    .letterhead-body   { padding: 8px 14px !important; gap: 12px !important; }
    .officer-photo-wrap { width: 90px !important; height: 110px !important; }
    .photo-placeholder  { font-size: 22px !important; }
    .officer-name { font-size: 17px !important; margin-bottom: 4px !important; }
    .officer-meta { font-size: 12px !important; gap: 2px 8px !important; }
    .meta-lbl, .meta-val { font-size: 12px !important; }
    .org-name  { font-size: 14px !important; }
    .org-sub   { font-size: 12px !important; }
    .doc-title { font-size: 12px !important; padding: 4px 14px !important; margin-top: 4px !important; }

    /* Grid layout preserved exactly as on-screen */
    .body-grid { gap: 5px !important; }
    .pair-row {
        display: grid !important;
        grid-template-columns: 57% 1fr !important;
        gap: 5px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    /* Panels */
    .section-panel, .pts-panel {
        border-radius: 3px !important;
        border: 1px solid var(--border-mid) !important;
        box-shadow: none !important;
        overflow: visible !important;
    }
    .section-head, .pts-head { padding: 5px 9px !important; }
    .section-head h2, .pts-head h2 { font-size: 11px !important; letter-spacing: .07em !important; }
    .section-head-icon { width: 17px !important; height: 17px !important; font-size: 10px !important; }

    /* Tables — readable sizes */
    .tbl { font-size: 10px !important; min-width: 0 !important; }
    .tbl thead th {
        font-size: 9px !important;
        padding: 3px 5px !important;
        letter-spacing: 0 !important;
        position: static !important;
        white-space: nowrap !important;
    }
    .tbl td {
        padding: 3px 5px !important;
        font-size: 10px !important;
        line-height: 1.3 !important;
    }
    .tbl .cat-cell  { font-size: 9.5px !important; padding: 3px 5px !important; white-space: normal !important; }
    .tbl .crit-cell { font-size: 10px !important; white-space: normal !important; }
    .tbl .rank-col  { font-size: 9.5px !important; padding: 2px 4px !important; }
    .tbl .null-val  { font-size: 9px !important; }

    /* Career table column widths */
    .career-tbl { min-width: 0 !important; }
    .career-tbl col.col-cat  { width: 13% !important; }
    .career-tbl col.col-crit { width: 21% !important; }
    .career-tbl col.col-rank { width: calc(66% / 6) !important; }

    /* Schooling table */
    .school-tbl { min-width: 0 !important; }
    .school-tbl col.col-cat    { width: 17% !important; }
    .school-tbl col.col-crit   { width: 18% !important; }
    .school-tbl col.col-course { width: 37% !important; }
    .school-tbl col.col-rating { width: 14% !important; }
    .school-tbl col.col-stand  { width: 14% !important; }

    /* Awards table */
    .awards-tbl { min-width: 0 !important; }
    .awards-tbl col.col-cat  { width: 15% !important; }
    .awards-tbl col.col-rank { width: calc(85% / 6) !important; }
    .award-badge { font-size: 8.5px !important; padding: 1px 3px !important; margin: 1px !important; }
    .award-cell  { font-size: 9.5px !important; line-height: 1.35 !important; }

    /* PFT table */
    .pft-tbl { min-width: 0 !important; }
    .pft-tbl col.col-label { width: 17% !important; }
    .pft-tbl col.col-rank  { width: calc(83% / 6) !important; }

    /* Points table */
    .pts-tbl { font-size: 10px !important; min-width: 0 !important; }
    .pts-tbl thead th {
        font-size: 9px !important;
        padding: 3px 4px !important;
        position: static !important;
        white-space: normal !important;
        word-break: break-word !important;
    }
    .pts-tbl td {
        font-size: 10px !important;
        padding: 3px 4px !important;
    }
    .pts-tbl td.pts-row-label { font-size: 9px !important; padding: 2px 4px !important; }

    /* ── CRITICAL: show ALL 6 rank panels side by side ── */
    .rank-tabs { display: none !important; }

    .pts-tab-body {
        display: grid !important;
        grid-template-columns: repeat(6, 1fr) !important;
        gap: 0 !important;
        border-top: 2px solid var(--pa-dark) !important;
        overflow: visible !important;
        flex: none !important;
        align-items: start !important;
    }

    .pts-tab-content {
        display: flex !important;
        flex-direction: column !important;
        border-right: 1px solid var(--border-mid) !important;
        min-width: 0 !important;
        overflow: visible !important;
    }
    .pts-tab-content:last-child { border-right: none !important; }

    /* Rank banner replacing tabs */
    .pts-tab-content::before {
        content: attr(data-rank);
        display: block !important;
        font-size: 8px !important;
        font-weight: 800 !important;
        color: #fff !important;
        text-transform: uppercase !important;
        letter-spacing: .05em !important;
        padding: 2px 4px !important;
        background: var(--pa-mid) !important;
        text-align: center !important;
        flex-shrink: 0 !important;
    }

    /* QRS score box */
    .qrs-score-box {
        margin: 3px 5px !important;
        border-radius: 2px !important;
        box-shadow: none !important;
    }
    .qrs-score-label {
        padding: 5px 7px !important;
        font-size: 8px !important;
    }
    .qrs-score-label span { font-size: 7px !important; }
    .qrs-score-value {
        font-size: 14px !important;
        padding: 5px 8px !important;
        min-width: 50px !important;
    }

    /* Print footer */
    .print-footer {
        display: block !important;
        font-size: 8px !important;
        color: #555 !important;
        padding: 4px 0 !important;
        border-top: 1px solid #ccc !important;
        margin-top: 4px !important;
    }
}

.print-footer { display: none; }

@media (max-width: 1000px) {
    .pair-row { grid-template-columns: 1fr; }
}
</style>

{{-- ═══════════════ PAGE ═══════════════ --}}
<div class="qrs-shell" id="qrsShell">

    {{-- ACTION BAR --}}
    <div class="action-bar">
        <div class="action-bar-left">
            <span class="action-label">⬢ Philippine Army — Personnel Management Center</span>
        </div>
        <div class="action-bar-right">
            <button class="btn btn-back" onclick="history.back()">&#8592; Back</button>
            <button class="btn btn-excel" id="excelBtn">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7v5h5v11H6zm2-7l2.5 3.5L13 13h1.5l-3.25 4.5L14.5 22H13l-2.5-3.5L8 22H6.5l3.25-4.5L6.5 13H8z"/></svg>
                Download Excel
            </button>
            <button class="btn btn-print" id="printBtn">&#9113; Print OCAR</button>
        </div>
    </div>

    {{-- LETTERHEAD --}}
    <div class="letterhead">
        <div class="letterhead-stripe"></div>
        <div class="letterhead-body">
            <div class="org-crest">
                <img src="{{ asset('images/pmc-logo-header.png') }}" 
                    alt="PMC Logo" 
                    class="crest-logo">
            </div>
            <div class="org-center">
                <div class="org-name">Philippine Army</div>
                <div class="org-sub">Personnel Management Center</div>
                <div class="org-sub" style="font-size:12px;font-weight:400;letter-spacing:.04em;color:var(--text-muted)">Fort Andres Bonifacio, Taguig City</div>
                <div class="doc-title">Quantitative Rating System (QRS) Sheet</div>
            </div>
            <div class="officer-card">
                <div class="officer-info">
                    <div class="officer-name">{{ $data->RANK }} {{ $data->NAME }}</div>
                    <div class="officer-meta">
                        <span class="meta-lbl">Designation</span><span class="meta-val">{{ $data->designations->name ?? 'N/A' }}</span>
                        <span class="meta-lbl">PM Code</span><span class="meta-val" style="font-family:var(--font-mono);font-weight:600">{{ $data->PM_CODE }}</span>
                        <span class="meta-lbl">DOC</span><span class="meta-val">{{ $data->DOC }}</span>
                        <span class="meta-lbl">DOR</span><span class="meta-val">{{ $data->DOR }}</span>
                    </div>
                </div>
                <div class="officer-photo-wrap" id="photoWrap" title="Click to upload photo (max 1MB)">
                    @if($data->photo_path)
                        <img src="{{ asset('storage/' . $data->photo_path) }}" alt="Profile photo" id="officerPhoto">
                    @else
                        <img src="img/sample-pic.jpg" alt="Profile photo" id="officerPhoto"
                             onerror="this.style.display='none'; document.getElementById('photoPlaceholder').style.display='block';">
                        <span class="photo-placeholder" id="photoPlaceholder" style="display:none;">&#128100;</span>
                    @endif
                    <div class="photo-overlay">&#128247;<br>Upload<br>Photo</div>
                    <input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp">
                </div>
            </div>
        </div>
    </div>

    @php
        function yearsToYrM($decimalYears): string {
            if (!$decimalYears || $decimalYears == 0) return '-';
            $totalMonths = round($decimalYears * 12);
            $yrs = intdiv($totalMonths, 12);
            $mos = $totalMonths % 12;
            if ($yrs > 0 && $mos > 0) return "{$yrs}yr{$mos}m";
            if ($yrs > 0) return "{$yrs}yr";
            return "{$mos}m";
        }
    @endphp

    <div class="body-grid">

        {{-- ══ ROW 1: Career Summary ↔ Assignment Points ══ --}}
        <div class="pair-row">
            <div class="section-panel">
                <div class="section-head">
                    <div class="section-head-icon">&#9783;</div>
                    <h2>Career Summary</h2>
                </div>
                <table class="tbl career-tbl">
                    <colgroup>
                        <col class="col-cat"><col class="col-crit">
                        @foreach ($ranks as $r)<col class="col-rank">@endforeach
                    </colgroup>
                    <thead style="height:63px">
                        <tr>
                            <th style="text-align:left">Category</th>
                            <th style="text-align:left">Criteria</th>
                            <th>2LT</th><th>1LT</th><th>CPT</th><th>MAJ</th><th>LTC</th><th>COL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($types as $type)
                            @php $criteria = $type->assignments; $rowspan = max(1, $criteria->count()); @endphp
                            @forelse ($criteria as $i => $assignment)
                                <tr>
                                    @if ($i === 0)
                                        <td rowspan="{{ $rowspan }}" class="cat-cell">{{ $type->name }}</td>
                                    @endif
                                    <td class="crit-cell">{{ $assignment->name }}</td>
                                    @foreach ($ranks as $rank)
                                        @php $val = data_get($totals, $assignment->id.'.'.$rank, 0); $fmt = yearsToYrM($val); @endphp
                                        <td class="rank-col {{ $fmt !== '-' ? 'has-value' : 'null-val' }}">{{ $fmt }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td class="cat-cell">{{ $type->name }}</td>
                                    <td class="crit-cell">—</td>
                                    @foreach ($ranks as $r)<td class="rank-col null-val">-</td>@endforeach
                                </tr>
                            @endforelse
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pts-panel">
                <div class="pts-head">
                    <div class="section-head-icon">&#9654;</div>
                    <h2>QRS — Assignment Points</h2>
                </div>
                <div class="rank-tabs" id="rankTabsMain">
                    @foreach ($rankIdMap as $rankLabel => $rankId)
                        <div class="rank-tab {{ $rankLabel == $data->RANK ? 'active' : '' }}" 
                            data-rank="{{ $rankLabel }}">
                            {{ $rankLabel }}
                        </div>
                    @endforeach
                </div>
                <div class="pts-tab-body" id="assignPtsBody">
                    @foreach ($rankIdMap as $rankLabel => $rankId)
                    <div class="pts-tab-content {{ $rankLabel == $data->RANK ? 'active' : '' }}" data-rank="{{ $rankLabel }}">
                            <table class="tbl pts-tbl" style="flex:1">
                                <colgroup>
                                    <col class="col-max"><col class="col-maxpt"><col class="col-gained">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>{{ $rankLabel }}: Max Yr</th>
                                        <th>{{ $rankLabel }}: Max Pts</th>
                                        <th>{{ $rankLabel }}: Gained</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($types as $type)
                                        @foreach ($type->assignments as $assignment)
                                            @php
                                                $sd  = $sourcedataMap[$assignment->id][$rankId] ?? null;
                                                $mY  = $sd ? number_format($sd->max_month / 12, 2) : '-';
                                                $mP  = $sd ? number_format($sd->max_point, 2) : '-';
                                                $g   = data_get($computedTotals, $assignment->id.'.'.$rankLabel, 0);
                                                $mPt = $sd ? (float)$sd->max_point : null;
                                                $gc  = $mPt !== null ? min((float)$g, $mPt) : (float)$g;
                                                $gd  = $gc > 0 ? number_format($gc, 2) : '-';
                                            @endphp
                                            <tr>
                                                <td class="{{ $mY!=='-' ? '' : 'null-val' }}">{{ $mY }}</td>
                                                <td class="{{ $mP!=='-' ? '' : 'null-val' }}">{{ $mP }}</td>
                                                <td class="{{ $gd!=='-' ? 'has-val' : 'null-val' }}">{{ $gd }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>{{-- end ROW 1 --}}

        {{-- ══ ROW 2: Professional Prep ↔ Schooling Points ══ --}}
        <div class="pair-row">
            <div class="section-panel">
                <div class="section-head">
                    <div class="section-head-icon">&#127979;</div>
                    <h2>Professional Preparation &amp; Development</h2>
                </div>
                <table class="tbl school-tbl">
                    <colgroup>
                        <col class="col-cat"><col class="col-crit">
                        <col class="col-course"><col class="col-rating"><col class="col-stand">
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="text-align:left">Category</th>
                            <th style="text-align:left">Criteria</th>
                            <th style="text-align:center">Course</th>
                            <th>Rating</th><th>Standing</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schoolingCriteria as $index => $criteria)
                            @php
                                $schoolingEntry = $schoolingMap->get($criteria->id);
                                $isMultiple = $criteria->id == 44 && $schoolingEntry;
                                $schooling  = $isMultiple ? $schoolingEntry->first() : $schoolingEntry;
                                $ratingDisplay = ($schooling && $schooling->rating)
                                    ? number_format((float)$schooling->rating, 2)
                                    : '-';
                            @endphp
                            <tr>
                                @if ($index === 0)
                                    <td rowspan="{{ $schoolingCriteria->count() }}" class="cat-cell">Professional Preparation and Development</td>
                                @endif
                                <td class="crit-cell">{{ $criteria->name }}</td>
                                <td class="crit-cell" style="white-space:normal;word-break:break-word;">
                                    @if ($isMultiple && $schoolingEntry->isNotEmpty())
                                        {{ $schoolingEntry->map(fn($s) => trim(($s->schoolingnames->name ?? '').' '.($s->classname ?? '')))->filter()->implode(', ') }}
                                    @elseif ($schooling && $schooling->date_completed)
                                        {{ $schooling->schoolingnames->name ?? '' }} {{ $schooling->classname ?? '' }}
                                    @else
                                        <span class="null-val">-</span>
                                    @endif
                                </td>
                                <td class="rank-col {{ $ratingDisplay === '-' ? 'null-val' : 'has-value' }}">{{ $ratingDisplay }}</td>
                                <td class="rank-col {{ !$schooling||!$schooling->standing ? 'null-val' : '' }}">{{ $schooling->standing ?? '-' }} / {{ $schooling->total_student ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pts-panel">
                <div class="pts-head">
                    <div class="section-head-icon">&#127979;</div>
                    <h2>Schooling Points</h2>
                </div>
                <div class="pts-tab-body" id="schoolPtsBody">
                    @foreach ($rankIdMap as $rankLabel => $rankId)
                        <div class="pts-tab-content @if($loop->first) active @endif" data-rank="{{ $rankLabel }}">
                            <table class="tbl pts-tbl" style="flex:1">
                                <colgroup><col class="col-half"><col class="col-half"></colgroup>
                                <thead><tr><th>{{ $rankLabel }}: Max Points</th><th>{{ $rankLabel }}: Actual Points</th></tr></thead>
                                <tbody>
                                    @foreach ($schoolingCriteria as $criteria)
                                        @php
                                            $pt = $schoolingPoints[$criteria->id][$rankId] ?? ['max'=>null,'actual'=>null];
                                            $hM = !is_null($pt['max']);
                                            $hA = !is_null($pt['actual']) && $pt['actual'] > 0;
                                        @endphp
                                        <tr>
                                            <td class="{{ !$hM ? 'null-val' : '' }}">{{ $hM ? number_format($pt['max'],2) : '-' }}</td>
                                            <td class="{{ $hA ? 'has-val' : 'null-val' }}">{{ $hA ? number_format($pt['actual'],2) : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>{{-- end ROW 2 --}}

        {{-- ══ ROW 3: Awards & Decorations ↔ Awards Points ══ --}}
        <div class="pair-row">
            <div class="section-panel">
                <div class="section-head">
                    <div class="section-head-icon">&#11088;</div>
                    <h2>Awards and Decorations</h2>
                </div>
                <table class="tbl awards-tbl" id="awards-deco-table">
                    <colgroup>
                        <col class="col-cat">
                        @for($i=0;$i<6;$i++)<col class="col-rank">@endfor
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="text-align:left">Category</th>
                            <th>2LT</th><th>1LT</th><th>CPT</th><th>MAJ</th><th>LTC</th><th>COL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="cat-cell">Awards &amp; Decorations</td>
                            @foreach ([1,2,3,4,5,6] as $rankId)
                                @php $rankAwards = $awardsMap->get($rankId, collect()); @endphp
                                <td class="award-cell {{ $rankAwards->isEmpty() ? 'greyed' : '' }}">
                                    @forelse ($rankAwards as $award)
                                        <span class="award-badge">{{ $award['name'] }}-{{ $award['count'] }}</span>
                                    @empty
                                        <span class="null-val">-</span>
                                    @endforelse
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pts-panel">
                <div class="pts-head">
                    <div class="section-head-icon">&#11088;</div>
                    <h2>Awards Points</h2>
                </div>
                <div class="pts-tab-body" id="awardPtsBody">
                    @foreach ($rankIdMap as $rankLabel => $rankId)
                        @php
                            $ap   = $awardsPoints[$rankLabel] ?? [];
                            $cMax = $ap['current_max'] ?? null;
                            $pMin = $ap['prev_min'] ?? null;
                            $cAct = $ap['current_actual'] ?? null;
                            $pAct = $ap['prev_actual'] ?? null;
                        @endphp
                        <div class="pts-tab-content @if($loop->first) active @endif"
                             data-rank="{{ $rankLabel }}" id="award-pts-{{ strtolower($rankLabel) }}">
                            <table class="tbl pts-tbl" style="flex:1">
                                <colgroup>
                                    <col style="width:34%"><col style="width:33%"><col style="width:33%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th style="text-align:left">Category</th>
                                        <th>{{ $rankLabel }}: Max Points</th>
                                        <th>{{ $rankLabel }}: Actual Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="awards-points-row">
                                        <td class="pts-row-label">Current Rank</td>
                                        <td class="{{ is_null($cMax) ? 'null-val' : '' }}">{{ !is_null($cMax) ? number_format($cMax,1) : '-' }}</td>
                                        <td class="{{ is_null($cAct) ? 'null-val' : 'has-val' }}">{{ !is_null($cAct) ? number_format($cAct,2) : '-' }}</td>
                                    </tr>
                                    <tr class="awards-points-row">
                                        <td class="pts-row-label">Prev Rank</td>
                                        <td class="{{ is_null($pMin) ? 'null-val' : '' }}">{{ !is_null($pMin) ? number_format($pMin,1) : '-' }}</td>
                                        <td class="{{ is_null($pAct) ? 'null-val' : 'has-val' }}">{{ !is_null($pAct) ? number_format($pAct,2) : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>{{-- end ROW 3 --}}

        {{-- ══ ROW 4: Physical Fitness Test ↔ PFT Points + QRS Score ══ --}}
        <div class="pair-row">
            <div class="section-panel">
                <div class="section-head">
                    <div class="section-head-icon">&#127939;</div>
                    <h2>Physical Fitness Test</h2>
                </div>
                <table class="tbl pft-tbl" id="pft-main-table">
                    <colgroup>
                        <col class="col-label">
                        @for($i=0;$i<6;$i++)<col class="col-rank">@endfor
                    </colgroup>
                    <thead>
                        <tr>
                            <th>PFT</th>
                            <th>2LT</th><th>1LT</th><th>CPT</th><th>MAJ</th><th>LTC</th><th>COL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="cat-cell">Rating</td>
                            @foreach (['2LT','1LT','CPT','MAJ','LTC','COL'] as $rank)
                                @php $pft = $pftMap->get($rank); @endphp
                                <td class="rank-col {{ $pft ? 'has-value' : 'null-val' }}">{{ $pft ? number_format($pft->rating,2) : '' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="cat-cell">Date Taken</td>
                            @foreach (['2LT','1LT','CPT','MAJ','LTC','COL'] as $rank)
                                @php $pft = $pftMap->get($rank); @endphp
                                <td class="rank-col {{ $pft ? '' : 'null-val' }}">{{ $pft ? \Carbon\Carbon::parse($pft->date_taken)->format('d/M/Y') : '' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="cat-cell">Supervising Unit</td>
                            @foreach (['2LT','1LT','CPT','MAJ','LTC','COL'] as $rank)
                                @php $pft = $pftMap->get($rank); @endphp
                                <td class="rank-col {{ $pft ? '' : 'null-val' }}">{{ $pft->supervising_unit ?? '' }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pts-panel">
                <div class="pts-head">
                    <div class="section-head-icon">&#127939;</div>
                    <h2>PFT Points &amp; QRS Score</h2>
                </div>
                <div class="pts-tab-body" id="pftPtsBody">
                    @foreach ($rankIdMap as $rankLabel => $rankId)
                        @php
                            $pp = $pftPoints[$rankLabel] ?? ['max'=>null,'actual'=>null];
                            $hM = !is_null($pp['max']);
                            $hA = !is_null($pp['actual']);
                        @endphp
                        <div class="pts-tab-content @if($loop->first) active @endif"
                             data-rank="{{ $rankLabel }}" id="pft-pts-{{ strtolower($rankLabel) }}">
                            <table class="tbl pts-tbl">
                                <colgroup><col class="col-half"><col class="col-half"></colgroup>
                                <thead><tr>
                                    <th>{{ $rankLabel }}: Max Points</th>
                                    <th>{{ $rankLabel }}: Actual Points</th>
                                </tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="{{ !$hM ? 'null-val' : '' }}">{{ $hM ? number_format($pp['max'],1) : '-' }}</td>
                                        <td class="{{ $hA ? 'has-val' : 'null-val' }}">{{ $hA ? number_format($pp['actual'],2) : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="qrs-score-box" style="margin-top:6px">
                                <div class="qrs-score-label">
                                    QRS Score
                                    <span>{{ $rankLabel }} — Total Points</span>
                                </div>
                                <div class="qrs-score-value">
                                    {{ number_format($qrsScores[$rankLabel] ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>{{-- end ROW 4 --}}

    </div>{{-- end body-grid --}}

    <div class="print-footer">
        QRS/QRS_v1 &bull; DTG printed: {{ now()->format('m/d/Y, h:i A') }}
    </div>
</div>{{-- end qrs-shell --}}

<div class="photo-toast" id="photoToast"></div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tabBodies = ['assignPtsBody','schoolPtsBody','awardPtsBody','pftPtsBody'];
    const tabs = document.querySelectorAll('#rankTabsMain .rank-tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const rank = this.dataset.rank;
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            tabBodies.forEach(bodyId => {
                const body = document.getElementById(bodyId);
                if (!body) return;
                body.querySelectorAll('.pts-tab-content').forEach(panel => {
                    panel.classList.toggle('active', panel.dataset.rank === rank);
                });
            });
            setTimeout(syncHeights, 20);
        });
    });

    function syncAwardsHeight(activeRank) {
        const decoTable = document.getElementById('awards-deco-table');
        const ptsPanel  = document.querySelector(`#award-pts-${activeRank.toLowerCase()} table`);
        if (!decoTable || !ptsPanel) return;
        const decoRows = decoTable.querySelectorAll('tbody tr');
        const ptsRows  = ptsPanel.querySelectorAll('tbody tr.awards-points-row');
        if (!decoRows.length || !ptsRows.length) return;
        const totalH = Array.from(decoRows).reduce((s, r) => s + r.offsetHeight, 0);
        const rowH   = Math.floor(totalH / ptsRows.length);
        ptsRows.forEach(r => r.style.height = rowH + 'px');
    }

    function syncPftHeight(activeRank) {
        const pftMain = document.getElementById('pft-main-table');
        const pftPts  = document.querySelector(`#pft-pts-${activeRank.toLowerCase()} table`);
        if (!pftMain || !pftPts) return;
        const mainH   = pftMain.offsetHeight;
        const theadH  = pftPts.querySelector('thead')?.offsetHeight ?? 0;
        const bodyRow = pftPts.querySelector('tbody tr');
        if (bodyRow) bodyRow.style.height = Math.max(0, mainH - theadH - 10) + 'px';
    }

    function syncHeights() {
        const activeTab = document.querySelector('#rankTabsMain .rank-tab.active');
        if (!activeTab) return;
        syncAwardsHeight(activeTab.dataset.rank);
        syncPftHeight(activeTab.dataset.rank);
    }

    setTimeout(syncHeights, 30);
    window.addEventListener('resize', syncHeights);

    /* ── Print: show all panels → print → restore ── */
    function showAllPanels() {
        tabBodies.forEach(id => {
            const b = document.getElementById(id);
            if (b) b.querySelectorAll('.pts-tab-content').forEach(p => p.classList.add('active'));
        });
    }

    function restorePanels() {
        const at   = document.querySelector('#rankTabsMain .rank-tab.active');
        const rank = at ? at.dataset.rank : null;
        tabBodies.forEach(id => {
            const b = document.getElementById(id);
            if (!b) return;
            b.querySelectorAll('.pts-tab-content').forEach(p => {
                p.classList.toggle('active', p.dataset.rank === rank);
            });
        });
    }

    /* Print button — no dialog, just print directly */
    document.getElementById('printBtn').addEventListener('click', function (e) {
        e.preventDefault();
        showAllPanels();
        requestAnimationFrame(() => requestAnimationFrame(() => {
            setTimeout(() => window.print(), 60);
        }));
    });

    window.addEventListener('afterprint', restorePanels);

    /* ── Excel download ── */
    document.getElementById('excelBtn').addEventListener('click', function (e) {
        e.preventDefault();
        const officerPmCode = @json($data->PM_CODE);
        window.location.href = `/qrsprofiles/${encodeURIComponent(officerPmCode)}/export-excel`;
    });

    /* ── Photo upload ── */
    const photoWrap  = document.getElementById('photoWrap');
    const photoInput = document.getElementById('photoInput');
    const photoImg   = document.getElementById('officerPhoto');
    const toast      = document.getElementById('photoToast');

    function showToast(msg, type) {
        toast.textContent = msg;
        toast.className = 'photo-toast show ' + type;
        setTimeout(() => { toast.className = 'photo-toast'; }, 3000);
    }

    photoWrap.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 1024 * 1024) { showToast('File too large. Maximum size is 1 MB.', 'error'); this.value = ''; return; }
        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) { showToast('Invalid file type. Use JPG, PNG, or WebP.', 'error'); this.value = ''; return; }

        const reader = new FileReader();
        reader.onload = ev => {
            if (photoImg) { photoImg.src = ev.target.result; photoImg.style.display = 'block'; }
            const ph = document.getElementById('photoPlaceholder');
            if (ph) ph.style.display = 'none';
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('photo', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch(`/qrsprofiles/{{ urlencode($data->PM_CODE) }}/upload-photo`, {
            method: 'POST', body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => showToast(d.success ? 'Photo uploaded successfully!' : (d.message || 'Upload failed.'), d.success ? 'success' : 'error'))
        .catch(() => showToast('Network error. Please try again.', 'error'));
    });

});
</script>
@endsection