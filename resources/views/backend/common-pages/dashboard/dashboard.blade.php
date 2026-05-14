@extends('backend.master')

@section('title', 'Dashboard')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
<style>
/* ── Variables ── */
:root {
    --db-bg:          #eef2f8;
    --db-surface:     #ffffff;
    --db-border:      rgba(15,23,60,0.08);
    --db-shadow-sm:   0 1px 3px rgba(15,23,60,0.06), 0 4px 16px rgba(15,23,60,0.04);
    --db-shadow-md:   0 2px 8px rgba(15,23,60,0.08), 0 8px 32px rgba(15,23,60,0.06);
    --db-shadow-lg:   0 4px 16px rgba(15,23,60,0.10), 0 16px 48px rgba(15,23,60,0.08);

    --c-blue:    #1a56db;
    --c-indigo:  #5145cd;
    --c-purple:  #7e3af2;
    --c-teal:    #0694a2;
    --c-amber:   #d97706;
    --c-red:     #e02424;
    --c-green:   #057a55;

    --c-blue-bg:   #eff6ff;
    --c-indigo-bg: #f0effe;
    --c-purple-bg: #f5f3ff;
    --c-teal-bg:   #ecfeff;
    --c-amber-bg:  #fffbeb;
    --c-red-bg:    #fef2f2;
    --c-green-bg:  #f0fdf4;

    --font-head: 'Sora', sans-serif;
    --font-body: 'DM Sans', sans-serif;
}

/* ── Layout ── */
.db-page {
    background: var(--db-bg);
    min-height: 100vh;
    padding: 1.5rem;
    font-family: var(--font-body);
}

/* ── Hero Banner ── */
.db-hero {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    background: linear-gradient(135deg, #0c1445 0%, #1a237e 35%, #1565c0 65%, #0288d1 100%);
    padding: 2rem 2.25rem;
    margin-bottom: 1.5rem;
    isolation: isolate;
}

.db-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 80% at 90% 50%, rgba(2,136,209,0.35) 0%, transparent 60%),
        radial-gradient(ellipse 40% 60% at 10% 20%, rgba(123,97,255,0.28) 0%, transparent 55%);
    z-index: -1;
}

.db-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 1px 1px, rgba(255,255,255,0.06) 1px, transparent 0);
    background-size: 28px 28px;
    z-index: -1;
}

.db-hero__body { flex: 1; }

.db-hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.65);
    margin-bottom: 0.6rem;
}

.db-hero__eyebrow-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 0 3px rgba(74,222,128,0.25);
    animation: pulse-green 2s ease-in-out infinite;
}

@keyframes pulse-green {
    0%,100% { box-shadow: 0 0 0 3px rgba(74,222,128,0.25); }
    50%      { box-shadow: 0 0 0 6px rgba(74,222,128,0.1); }
}

.db-hero__title {
    font-family: var(--font-head);
    font-size: clamp(1.35rem, 2.5vw, 2rem);
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.35rem;
    letter-spacing: -0.025em;
    line-height: 1.2;
}

.db-hero__sub {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.68);
    max-width: 560px;
    line-height: 1.6;
}

.db-hero__meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.75rem;
}

.db-hero__clock {
    font-family: var(--font-head);
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.04em;
    line-height: 1;
}

.db-hero__date {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.65);
}

.db-hero__user-pill {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 50px;
    padding: 0.5rem 1rem 0.5rem 0.5rem;
}

.db-hero__avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #06b6d4);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-head);
    font-weight: 700;
    font-size: 0.85rem;
    color: #fff;
    flex-shrink: 0;
}

.db-hero__user-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #fff;
}

.db-hero__user-role {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.6);
}

/* ── Section Header ── */
.db-section-label {
    font-family: var(--font-head);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.db-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--db-border);
}

/* ── KPI Cards ── */
.db-kpi {
    background: var(--db-surface);
    border-radius: 16px;
    border: 1px solid var(--db-border);
    box-shadow: var(--db-shadow-sm);
    padding: 1.25rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}

.db-kpi:hover {
    transform: translateY(-3px);
    box-shadow: var(--db-shadow-md);
}

.db-kpi__accent {
    position: absolute;
    top: 0; left: 0;
    width: 4px;
    height: 100%;
    border-radius: 0 4px 4px 0;
}

.db-kpi__orb {
    position: absolute;
    top: -16px; right: -16px;
    width: 80px; height: 80px;
    border-radius: 50%;
    opacity: 0.08;
}

.db-kpi__icon-wrap {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    margin-bottom: 0.15rem;
}

.db-kpi__icon-wrap svg { width: 20px; height: 20px; }

.db-kpi__label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
}

.db-kpi__value {
    font-family: var(--font-head);
    font-size: 2.1rem;
    font-weight: 700;
    line-height: 1;
    color: #0f172a;
    letter-spacing: -0.03em;
}

.db-kpi__meta {
    font-size: 0.78rem;
    color: #94a3b8;
    line-height: 1.4;
}

.db-kpi__progress {
    margin-top: auto;
    padding-top: 0.6rem;
}

.db-kpi__progress-bar-bg {
    height: 4px;
    border-radius: 99px;
    overflow: hidden;
    background: #f1f5f9;
    margin-bottom: 0.3rem;
}

.db-kpi__progress-bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 1s cubic-bezier(0.4,0,0.2,1);
}

.db-kpi__progress-text {
    font-size: 0.7rem;
    color: #94a3b8;
    display: flex;
    justify-content: space-between;
}

/* Color variants */
.db-kpi--blue  .db-kpi__accent, .db-kpi--blue  .db-kpi__orb      { background: var(--c-blue); }
.db-kpi--blue  .db-kpi__icon-wrap                                  { background: var(--c-blue-bg); color: var(--c-blue); }
.db-kpi--blue  .db-kpi__value, .db-kpi--blue .db-kpi__icon-wrap svg { color: var(--c-blue); }
.db-kpi--blue  .db-kpi__progress-bar-fill                          { background: var(--c-blue); }

.db-kpi--indigo .db-kpi__accent, .db-kpi--indigo .db-kpi__orb     { background: var(--c-indigo); }
.db-kpi--indigo .db-kpi__icon-wrap                                 { background: var(--c-indigo-bg); color: var(--c-indigo); }
.db-kpi--indigo .db-kpi__value, .db-kpi--indigo .db-kpi__icon-wrap svg { color: var(--c-indigo); }
.db-kpi--indigo .db-kpi__progress-bar-fill                         { background: var(--c-indigo); }

.db-kpi--purple .db-kpi__accent, .db-kpi--purple .db-kpi__orb     { background: var(--c-purple); }
.db-kpi--purple .db-kpi__icon-wrap                                 { background: var(--c-purple-bg); color: var(--c-purple); }
.db-kpi--purple .db-kpi__value, .db-kpi--purple .db-kpi__icon-wrap svg { color: var(--c-purple); }
.db-kpi--purple .db-kpi__progress-bar-fill                         { background: var(--c-purple); }

.db-kpi--teal .db-kpi__accent, .db-kpi--teal .db-kpi__orb         { background: var(--c-teal); }
.db-kpi--teal .db-kpi__icon-wrap                                   { background: var(--c-teal-bg); color: var(--c-teal); }
.db-kpi--teal .db-kpi__value, .db-kpi--teal .db-kpi__icon-wrap svg { color: var(--c-teal); }
.db-kpi--teal .db-kpi__progress-bar-fill                           { background: var(--c-teal); }

.db-kpi--amber .db-kpi__accent, .db-kpi--amber .db-kpi__orb       { background: var(--c-amber); }
.db-kpi--amber .db-kpi__icon-wrap                                  { background: var(--c-amber-bg); color: var(--c-amber); }
.db-kpi--amber .db-kpi__value, .db-kpi--amber .db-kpi__icon-wrap svg { color: var(--c-amber); }
.db-kpi--amber .db-kpi__progress-bar-fill                          { background: var(--c-amber); }

.db-kpi--red .db-kpi__accent, .db-kpi--red .db-kpi__orb           { background: var(--c-red); }
.db-kpi--red .db-kpi__icon-wrap                                    { background: var(--c-red-bg); color: var(--c-red); }
.db-kpi--red .db-kpi__value, .db-kpi--red .db-kpi__icon-wrap svg   { color: var(--c-red); }
.db-kpi--red .db-kpi__progress-bar-fill                            { background: var(--c-red); }

/* ── Chart Panels ── */
.db-panel {
    background: var(--db-surface);
    border-radius: 16px;
    border: 1px solid var(--db-border);
    box-shadow: var(--db-shadow-sm);
    padding: 1.4rem;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.db-panel__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-shrink: 0;
}

.db-panel__title {
    font-family: var(--font-head);
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.15rem;
}

.db-panel__sub {
    font-size: 0.78rem;
    color: #94a3b8;
}

.db-panel__badge {
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 0.25rem 0.65rem;
    border-radius: 99px;
    white-space: nowrap;
    flex-shrink: 0;
}

.db-chart {
    flex: 1;
    min-height: 0;
}

.db-chart--300 { height: 300px; flex: none; }
.db-chart--260 { height: 260px; flex: none; }
.db-chart--200 { height: 200px; flex: none; }

/* ── Operational Health ── */
.db-health-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.db-health-tile {
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.db-health-tile--blue   { background: var(--c-blue-bg); }
.db-health-tile--teal   { background: var(--c-teal-bg); }
.db-health-tile--purple { background: var(--c-purple-bg); }
.db-health-tile--green  { background: var(--c-green-bg); }

.db-health-tile__label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
}

.db-health-tile__value {
    font-family: var(--font-head);
    font-size: 1.6rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1;
}

.db-health-tile--blue   .db-health-tile__value { color: var(--c-blue); }
.db-health-tile--teal   .db-health-tile__value { color: var(--c-teal); }
.db-health-tile--purple .db-health-tile__value { color: var(--c-purple); }
.db-health-tile--green  .db-health-tile__value { color: var(--c-green); }

/* ── Tables ── */
.db-table { width: 100%; font-size: 0.85rem; }
.db-table thead th {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 0 0.75rem 0.65rem;
    border-bottom: 1px solid var(--db-border);
    white-space: nowrap;
}
.db-table tbody td {
    padding: 0.7rem 0.75rem;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
    color: #334155;
}
.db-table tbody tr:last-child td { border-bottom: none; }
.db-table tbody tr:hover td { background: #f8fafc; }

.db-badge {
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    padding: 0.28rem 0.7rem;
    border-radius: 99px;
    display: inline-block;
}
.db-badge--field   { background: #ecfeff; color: #0e7490; }
.db-badge--corp    { background: #eff6ff; color: #1d4ed8; }
.db-badge--default { background: #f1f5f9; color: #64748b; }

.db-user-cell__name {
    font-weight: 600;
    color: #0f172a;
    font-size: 0.85rem;
    line-height: 1.3;
}
.db-user-cell__email {
    font-size: 0.72rem;
    color: #94a3b8;
}

/* ── Divider ── */
.db-divider {
    height: 1px;
    background: var(--db-border);
    margin: 1rem 0;
}

/* ── Scroll fade-in animation ── */
.db-fadein {
    opacity: 0;
    transform: translateY(16px);
    animation: db-fadein 0.5s ease forwards;
}

@keyframes db-fadein {
    to { opacity: 1; transform: translateY(0); }
}

.db-fadein:nth-child(1) { animation-delay: 0.05s; }
.db-fadein:nth-child(2) { animation-delay: 0.10s; }
.db-fadein:nth-child(3) { animation-delay: 0.15s; }
.db-fadein:nth-child(4) { animation-delay: 0.20s; }
.db-fadein:nth-child(5) { animation-delay: 0.25s; }
.db-fadein:nth-child(6) { animation-delay: 0.30s; }

/* ── Responsive ── */
@media (max-width: 991.98px) {
    .db-hero { flex-direction: column; align-items: flex-start; }
    .db-hero__meta { align-items: flex-start; flex-direction: row; flex-wrap: wrap; gap: 0.5rem; }
}
@media (max-width: 575.98px) {
    .db-page { padding: 1rem; }
    .db-hero { padding: 1.25rem; }
    .db-hero__clock { font-size: 1.4rem; }
    .db-health-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('body')
@php
    $kpis              = $kpis ?? [];
    $assetMix          = collect($assetMix ?? []);
    $topStores         = collect($topStores ?? []);
    $userSectorMix     = collect($userSectorMix ?? []);
    $brandCoverage     = collect($brandCoverage ?? []);
    $monthlyAssets     = collect($monthlyAssets ?? []);
    $monthlyUsers      = collect($monthlyUsers ?? []);
    $monthlyVmIssues   = collect($monthlyVmIssues ?? []);
    $planogramActivity = collect($planogramActivity ?? []);
    $operationalHealth = $operationalHealth ?? [];
    $recentUsers       = collect($recentUsers ?? []);
    $recentPlanograms  = collect($recentPlanograms ?? []);

    $userName  = auth()->user()?->name ?? 'Admin';
    $userInitials = collect(explode(' ', $userName))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
@endphp

<div class="db-page">

    {{-- ── Hero Banner ── --}}
    <div class="db-hero d-flex align-items-start justify-content-between gap-3 mb-4">
        <div class="db-hero__body">
            <div class="db-hero__eyebrow">
                <span class="db-hero__eyebrow-dot"></span>
                Operations Overview &bull; Live
            </div>
            <h1 class="db-hero__title">Retail Asset Intelligence</h1>
            <p class="db-hero__sub mb-0">
                Central visibility across stores, assets, planograms, brand assignments, and visual merchandising.
            </p>
        </div>
        <div class="db-hero__meta">
            <div class="db-hero__clock" id="db-clock">--:--:--</div>
            <div class="db-hero__date" id="db-date"></div>
            <div class="db-hero__user-pill">
                <div class="db-hero__avatar">{{ $userInitials }}</div>
                <div>
                    <div class="db-hero__user-name">{{ $userName }}</div>
                    <div class="db-hero__user-role">Administrator</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <p class="db-section-label">Key Performance Indicators</p>
    <div class="row g-3 mb-4">
        @php
        $kpiIcons = [
            'store'  => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/></svg>',
            'layers' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polygon stroke-linecap="round" stroke-linejoin="round" points="12 2 2 7 12 12 22 7 12 2"/><polyline stroke-linecap="round" stroke-linejoin="round" points="2 17 12 22 22 17"/><polyline stroke-linecap="round" stroke-linejoin="round" points="2 12 12 17 22 12"/></svg>',
            'tag'    => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>',
            'link'   => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>',
            'clock'  => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline stroke-linecap="round" stroke-linejoin="round" points="12 6 12 12 16 14"/></svg>',
            'alert'  => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line stroke-linecap="round" x1="12" y1="9" x2="12" y2="13"/><line stroke-linecap="round" x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        ];
        @endphp

        @foreach($kpis as $key => $kpi)
        @php
            $color   = $kpi['color'] ?? 'blue';
            $value   = (int)($kpi['value'] ?? 0);
            $total   = (int)($kpi['total'] ?? 0);
            $pct     = $total > 0 ? min(100, round($value / $total * 100)) : 0;
            $iconKey = $kpi['icon'] ?? 'layers';
        @endphp
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="db-kpi db-kpi--{{ $color }} db-fadein">
                <div class="db-kpi__accent"></div>
                <div class="db-kpi__orb"></div>
                <div class="db-kpi__icon-wrap">
                    {!! $kpiIcons[$iconKey] ?? $kpiIcons['layers'] !!}
                </div>
                <div class="db-kpi__label">{{ $kpi['label'] ?? '' }}</div>
                <div class="db-kpi__value" data-counter="{{ $value }}">0</div>
                <div class="db-kpi__meta">{{ $kpi['meta'] ?? '' }}</div>
                @if($total > 0)
                <div class="db-kpi__progress">
                    <div class="db-kpi__progress-bar-bg">
                        <div class="db-kpi__progress-bar-fill" style="width: 0%" data-target-width="{{ $pct }}%"></div>
                    </div>
                    <div class="db-kpi__progress-text">
                        <span>{{ $value }} active</span>
                        <span>{{ $pct }}% of {{ number_format($total) }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Monthly Growth ── --}}
    <p class="db-section-label">Trend Analysis</p>
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <div class="db-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Monthly Growth Trends</div>
                        <div class="db-panel__sub">Assets, users, and VM issue creation over time</div>
                    </div>
                    <span class="db-panel__badge bg-primary-subtle text-primary">Spline</span>
                </div>
                <div id="chart-monthly" class="db-chart--300"></div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="db-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">User Sector Split</div>
                        <div class="db-panel__sub">Field vs corporate distribution</div>
                    </div>
                    <span class="db-panel__badge bg-info-subtle text-info">Donut</span>
                </div>
                <div id="chart-sector" class="db-chart--300"></div>
            </div>
        </div>
    </div>

    {{-- ── Asset Mix + Top Stores ── --}}
    <p class="db-section-label">Asset & Store Intelligence</p>
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-6">
            <div class="db-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Asset Mix by Type</div>
                        <div class="db-panel__sub">Portfolio split across asset categories</div>
                    </div>
                    <span class="db-panel__badge bg-warning-subtle text-warning">Column</span>
                </div>
                <div id="chart-asset-mix" class="db-chart--300"></div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="db-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Top Stores by Asset Count</div>
                        <div class="db-panel__sub">Stores with highest asset footprint</div>
                    </div>
                    <span class="db-panel__badge bg-success-subtle text-success">Bar</span>
                </div>
                <div id="chart-top-stores" class="db-chart--300"></div>
            </div>
        </div>
    </div>

    {{-- ── Brand Coverage + Operational Health ── --}}
    <p class="db-section-label">Brand & Operational Health</p>
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-7">
            <div class="db-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Brand Coverage</div>
                        <div class="db-panel__sub">Asset-to-brand assignment volume by brand</div>
                    </div>
                    <span class="db-panel__badge bg-primary-subtle text-primary">Column</span>
                </div>
                <div id="chart-brand" class="db-chart--300"></div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="db-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Operational Health</div>
                        <div class="db-panel__sub">Quality and readiness indicators</div>
                    </div>
                </div>
                <div class="db-health-grid mb-3">
                    <div class="db-health-tile db-health-tile--blue">
                        <div class="db-health-tile__label">Assets w/ Planogram</div>
                        <div class="db-health-tile__value" data-counter="{{ (int)($operationalHealth['assets_with_planogram'] ?? 0) }}">0</div>
                    </div>
                    <div class="db-health-tile db-health-tile--teal">
                        <div class="db-health-tile__label">Assets w/ KV Slot</div>
                        <div class="db-health-tile__value" data-counter="{{ (int)($operationalHealth['assets_with_kv_slot'] ?? 0) }}">0</div>
                    </div>
                    <div class="db-health-tile db-health-tile--purple">
                        <div class="db-health-tile__label">Store-Assigned Users</div>
                        <div class="db-health-tile__value" data-counter="{{ (int)($operationalHealth['store_assigned_users'] ?? 0) }}">0</div>
                    </div>
                    <div class="db-health-tile db-health-tile--green">
                        <div class="db-health-tile__label">VM Issues Solved</div>
                        <div class="db-health-tile__value" data-counter="{{ (int)($operationalHealth['vm_solved'] ?? 0) }}">0</div>
                    </div>
                </div>
                <div class="db-divider"></div>
                <div class="db-panel__title mb-1" style="font-size:0.85rem;">Planogram Activity</div>
                <div class="db-panel__sub mb-2">Recent updates over last 14 days</div>
                <div id="chart-planogram" class="db-chart--200"></div>
            </div>
        </div>
    </div>

    {{-- ── Recent Tables ── --}}
    <p class="db-section-label">Recent Activity</p>
    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <div class="db-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Recent Users</div>
                        <div class="db-panel__sub">Latest accounts added to the platform</div>
                    </div>
                    <a href="{{ route('users.index') }}" class="db-panel__badge bg-light text-secondary text-decoration-none">View All &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Employee ID</th>
                                <th>Sector</th>
                                <th>Added</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="db-user-cell__name">{{ $user->name }}</div>
                                    <div class="db-user-cell__email">{{ $user->email }}</div>
                                </td>
                                <td style="color:#64748b; font-variant-numeric: tabular-nums;">
                                    {{ $user->employee_id ?: '—' }}
                                </td>
                                <td>
                                    @php $sector = strtolower($user->usages_sector ?? ''); @endphp
                                    <span class="db-badge db-badge--{{ $sector === 'field' ? 'field' : ($sector ? 'corp' : 'default') }}">
                                        {{ ucfirst($user->usages_sector ?: 'N/A') }}
                                    </span>
                                </td>
                                <td style="color:#94a3b8; font-size:0.78rem; white-space:nowrap;">
                                    {{ optional($user->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color:#94a3b8; padding:2rem 0;">No recent users found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="db-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Recent Planogram Updates</div>
                        <div class="db-panel__sub">Latest planogram changes across stores &amp; assets</div>
                    </div>
                    <a href="{{ route('assets.planogram-histories') }}" class="db-panel__badge bg-light text-secondary text-decoration-none">View All &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Store</th>
                                <th>Asset</th>
                                <th>Brand</th>
                                <th>Changed</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentPlanograms as $history)
                            <tr>
                                <td style="font-weight:600; color:#0f172a;">{{ $history->store?->title ?: '—' }}</td>
                                <td style="color:#334155;">{{ $history->asset?->name ?: '—' }}</td>
                                <td>
                                    <span class="db-badge db-badge--corp">{{ $history->brand?->name ?: '—' }}</span>
                                </td>
                                <td style="color:#94a3b8; font-size:0.78rem; white-space:nowrap;">
                                    {{ optional($history->created_at)->format('d M y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color:#94a3b8; padding:2rem 0;">No planogram activity found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /db-page --}}
@endsection

@push('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
(function () {
    /* ── Clock ── */
    function tickClock() {
        var now  = new Date();
        var pad  = function(n){ return String(n).padStart(2,'0'); };
        var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var el = document.getElementById('db-clock');
        var de = document.getElementById('db-date');
        if (el) el.textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        if (de) de.textContent = days[now.getDay()]+', '+now.getDate()+' '+months[now.getMonth()]+' '+now.getFullYear();
    }
    tickClock();
    setInterval(tickClock, 1000);

    /* ── Counter animation ── */
    function animateCounter(el, target, duration) {
        var start = 0, step = target / (duration / 16);
        var timer = setInterval(function () {
            start += step;
            if (start >= target) { start = target; clearInterval(timer); }
            el.textContent = Math.round(start).toLocaleString();
        }, 16);
    }

    /* ── Progress bar animation ── */
    document.querySelectorAll('.db-kpi__progress-bar-fill').forEach(function(bar){
        var target = bar.getAttribute('data-target-width') || '0%';
        setTimeout(function(){ bar.style.width = target; }, 300);
    });

    /* ── Highcharts global defaults ── */
    Highcharts.setOptions({
        chart: { style: { fontFamily: "'DM Sans', sans-serif" } },
        credits: { enabled: false },
        exporting: { enabled: false },
        title: { text: null },
        colors: ['#1a56db','#0694a2','#7e3af2','#d97706','#e02424','#057a55','#0284c7','#9333ea'],
        xAxis: {
            lineColor: '#e2e8f0',
            tickColor: '#e2e8f0',
            labels: { style: { color: '#94a3b8', fontSize: '0.72rem' } }
        },
        yAxis: {
            gridLineColor: '#f1f5f9',
            title: { text: null },
            labels: { style: { color: '#94a3b8', fontSize: '0.72rem' } }
        },
        tooltip: {
            borderRadius: 10,
            backgroundColor: '#0f172a',
            borderColor: '#0f172a',
            style: { color: '#f8fafc', fontSize: '0.8rem' },
            shadow: false
        },
        legend: {
            itemStyle: { fontWeight: '500', color: '#64748b', fontSize: '0.78rem' },
            itemHoverStyle: { color: '#0f172a' }
        },
        plotOptions: {
            series: { animation: { duration: 900 } },
            column: { borderRadius: 5, borderWidth: 0 },
            bar:    { borderRadius: 5, borderWidth: 0 },
            pie:    { borderWidth: 0 }
        }
    });

    /* ── Data from PHP ── */
    var assetMix         = @json($assetMix->map(fn($i) => ['name' => $i->name, 'y' => (int)$i->total])->values());
    var topStores        = @json($topStores->map(fn($i) => ['name' => $i->title, 'y' => (int)$i->assets_count])->values());
    var userSectorMix    = @json($userSectorMix->map(fn($i) => ['name' => ucfirst($i->usages_sector ?? 'Unknown'), 'y' => (int)$i->total])->values());
    var brandCoverage    = @json($brandCoverage->map(fn($i) => ['name' => $i->name, 'y' => (int)$i->total])->values());
    var monthlyCategories = @json($monthlyAssets->pluck('period')->unique()->values());
    var monthlyAssetsSeries = @json($monthlyAssets->pluck('total')->map(fn($v) => (int)$v)->values());
    var monthlyUsersSeries  = @json($monthlyUsers->pluck('total')->map(fn($v) => (int)$v)->values());
    var monthlyVmSeries     = @json($monthlyVmIssues->pluck('total')->map(fn($v) => (int)$v)->values());
    var planogramCats    = @json($planogramActivity->pluck('activity_date')->values());
    var planogramSeries  = @json($planogramActivity->pluck('total')->map(fn($v) => (int)$v)->values());

    /* ── Monthly Growth ── */
    Highcharts.chart('chart-monthly', {
        chart: { type: 'spline', backgroundColor: 'transparent', height: 300 },
        xAxis: { categories: monthlyCategories },
        series: [
            { name: 'Assets',    data: monthlyAssetsSeries, color: '#1a56db',
              marker: { radius: 4, fillColor: '#fff', lineWidth: 2, lineColor: '#1a56db' } },
            { name: 'Users',     data: monthlyUsersSeries,  color: '#0694a2',
              marker: { radius: 4, fillColor: '#fff', lineWidth: 2, lineColor: '#0694a2' } },
            { name: 'VM Issues', data: monthlyVmSeries,     color: '#e02424', dashStyle: 'ShortDash',
              marker: { radius: 4, fillColor: '#fff', lineWidth: 2, lineColor: '#e02424' } }
        ]
    });

    /* ── User Sector Donut ── */
    Highcharts.chart('chart-sector', {
        chart: { type: 'pie', backgroundColor: 'transparent', height: 300 },
        plotOptions: {
            pie: {
                innerSize: '60%',
                dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.y}', style: { color: '#334155', fontSize: '0.75rem', fontWeight: '500' } }
            }
        },
        series: [{ name: 'Users', data: userSectorMix }],
        legend: { enabled: false }
    });

    /* ── Asset Mix ── */
    Highcharts.chart('chart-asset-mix', {
        chart: { type: 'column', backgroundColor: 'transparent', height: 300 },
        xAxis: { type: 'category' },
        series: [{ name: 'Assets', data: assetMix, colorByPoint: true }]
    });

    /* ── Top Stores (horizontal bar) ── */
    Highcharts.chart('chart-top-stores', {
        chart: { type: 'bar', backgroundColor: 'transparent', height: 300 },
        xAxis: { type: 'category' },
        series: [{ name: 'Assets', data: topStores, color: '#0694a2' }]
    });

    /* ── Brand Coverage ── */
    Highcharts.chart('chart-brand', {
        chart: { type: 'column', backgroundColor: 'transparent', height: 300 },
        xAxis: { type: 'category' },
        series: [{ name: 'Assignments', data: brandCoverage, color: '#7e3af2' }]
    });

    /* ── Planogram Activity ── */
    Highcharts.chart('chart-planogram', {
        chart: { type: 'areaspline', backgroundColor: 'transparent', height: 200 },
        xAxis: { categories: planogramCats, labels: { rotation: -40 } },
        series: [{
            name: 'Updates',
            data: planogramSeries,
            color: '#d97706',
            fillColor: {
                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                stops: [ [0, 'rgba(217,119,6,0.35)'], [1, 'rgba(217,119,6,0.02)'] ]
            },
            marker: { radius: 3 }
        }],
        legend: { enabled: false }
    });

    /* ── Animate counters on page ready ── */
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-counter]').forEach(function (el) {
            animateCounter(el, parseInt(el.getAttribute('data-counter'), 10) || 0, 1200);
        });
    });
    /* Also fire immediately in case DOMContentLoaded already fired */
    document.querySelectorAll('[data-counter]').forEach(function (el) {
        animateCounter(el, parseInt(el.getAttribute('data-counter'), 10) || 0, 1200);
    });
})();
</script>
@endpush
