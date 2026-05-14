@extends('backend.master')
@section('title', 'Dashboard')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
<style>
/* ── Root Tokens ─────────────────────────────────────────────────── */
:root {
    --db-bg:        #eef2f8;
    --db-surface:   #ffffff;
    --db-border:    rgba(15,23,60,0.08);
    --db-shadow-sm: 0 1px 3px rgba(15,23,60,0.06), 0 4px 16px rgba(15,23,60,0.04);
    --db-shadow-md: 0 2px 8px rgba(15,23,60,0.08), 0 8px 32px rgba(15,23,60,0.06);
    --db-shadow-lg: 0 4px 16px rgba(15,23,60,0.10), 0 16px 48px rgba(15,23,60,0.08);

    --c-blue:    #1a56db;  --c-blue-bg:   #eff6ff;
    --c-indigo:  #5145cd;  --c-indigo-bg: #f0effe;
    --c-purple:  #7e3af2;  --c-purple-bg: #f5f3ff;
    --c-teal:    #0694a2;  --c-teal-bg:   #ecfeff;
    --c-amber:   #d97706;  --c-amber-bg:  #fffbeb;
    --c-red:     #e02424;  --c-red-bg:    #fef2f2;
    --c-green:   #057a55;  --c-green-bg:  #f0fdf4;
    --c-slate:   #475569;  --c-slate-bg:  #f8fafc;

    --font-head: 'Sora', sans-serif;
    --font-body: 'DM Sans', sans-serif;

    --radius-sm: 10px;
    --radius-md: 14px;
    --radius-lg: 18px;
    --radius-xl: 22px;
}

/* ── Page Shell ──────────────────────────────────────────────────── */
.db-page {
    background: var(--db-bg);
    min-height: 100vh;
    padding: 1.5rem;
    font-family: var(--font-body);
}

/* ── Hero Banner ─────────────────────────────────────────────────── */
.db-hero {
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-xl);
    background: linear-gradient(135deg, #080d24 0%, #101c4a 30%, #1a3480 60%, #0e6dab 100%);
    padding: 2rem 2.25rem;
    margin-bottom: 1.75rem;
    isolation: isolate;
}
.db-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 55% 80% at 92% 50%, rgba(14,109,171,0.45) 0%, transparent 60%),
        radial-gradient(ellipse 40% 60% at 8%  20%, rgba(94,74,255,0.30) 0%, transparent 55%),
        radial-gradient(ellipse 30% 50% at 50% 90%, rgba(6,148,162,0.20) 0%, transparent 55%);
    z-index: -1;
}
.db-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.055) 1px, transparent 0);
    background-size: 26px 26px;
    z-index: -1;
}
.db-hero__body { flex: 1; min-width: 0; }
.db-hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.60);
    margin-bottom: 0.65rem;
}
.db-hero__status-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 0 3px rgba(74,222,128,0.28);
    animation: pulse-green 2.2s ease-in-out infinite;
    flex-shrink: 0;
}
@keyframes pulse-green {
    0%,100% { box-shadow: 0 0 0 3px rgba(74,222,128,0.28); }
    50%      { box-shadow: 0 0 0 7px rgba(74,222,128,0.08); }
}
.db-hero__title {
    font-family: var(--font-head);
    font-size: clamp(1.4rem, 2.8vw, 2.1rem);
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.4rem;
    letter-spacing: -0.03em;
    line-height: 1.15;
}
.db-hero__sub {
    font-size: 0.88rem;
    color: rgba(255,255,255,0.60);
    line-height: 1.65;
    max-width: 520px;
}
.db-hero__meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.85rem;
    flex-shrink: 0;
}
.db-hero__clock {
    font-family: var(--font-head);
    font-size: 2.1rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.05em;
    line-height: 1;
}
.db-hero__date {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.55);
    text-align: right;
}
.db-hero__pill {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255,255,255,0.10);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(255,255,255,0.16);
    border-radius: 50px;
    padding: 0.45rem 1rem 0.45rem 0.45rem;
}
.db-hero__avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-head);
    font-weight: 700;
    font-size: 0.8rem;
    color: #fff;
    flex-shrink: 0;
}
.db-hero__user-name  { font-size: 0.85rem; font-weight: 600; color: #fff; line-height: 1.2; }
.db-hero__user-role  { font-size: 0.7rem;  color: rgba(255,255,255,0.55); }

/* ── Quick Actions strip ─────────────────────────────────────────── */
.db-hero__actions {
    display: flex;
    gap: 0.6rem;
    flex-wrap: wrap;
    margin-top: 1.25rem;
    padding-top: 1.25rem;
    border-top: 1px solid rgba(255,255,255,0.10);
}
.db-quick-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255,255,255,0.80);
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.13);
    border-radius: 50px;
    padding: 0.35rem 0.85rem;
    text-decoration: none;
    transition: background 0.18s, color 0.18s, border-color 0.18s;
}
.db-quick-btn:hover {
    background: rgba(255,255,255,0.18);
    color: #fff;
    border-color: rgba(255,255,255,0.28);
}
.db-quick-btn i { font-size: 0.85rem; }

/* ── Section Labels ──────────────────────────────────────────────── */
.db-section-label {
    font-family: var(--font-head);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.85rem;
    display: flex;
    align-items: center;
    gap: 10px;
}
.db-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--db-border);
}

/* ── KPI Cards ───────────────────────────────────────────────────── */
.db-kpi {
    background: var(--db-surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--db-border);
    box-shadow: var(--db-shadow-sm);
    padding: 1.3rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    position: relative;
    overflow: hidden;
    height: 100%;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
}
.db-kpi:hover { transform: translateY(-3px); box-shadow: var(--db-shadow-md); }
.db-kpi__accent {
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
    border-radius: 0 4px 4px 0;
}
.db-kpi__orb {
    position: absolute;
    top: -20px; right: -20px;
    width: 90px; height: 90px;
    border-radius: 50%;
    opacity: 0.07;
    pointer-events: none;
}
.db-kpi__icon-wrap {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.2rem;
    flex-shrink: 0;
}
.db-kpi__icon-wrap svg { width: 19px; height: 19px; }
.db-kpi__label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #64748b;
}
.db-kpi__value {
    font-family: var(--font-head);
    font-size: 2.15rem;
    font-weight: 700;
    line-height: 1;
    color: #0f172a;
    letter-spacing: -0.04em;
}
.db-kpi__meta { font-size: 0.77rem; color: #94a3b8; line-height: 1.5; }
.db-kpi__progress { margin-top: auto; padding-top: 0.65rem; }
.db-kpi__bar-bg {
    height: 4px;
    border-radius: 99px;
    overflow: hidden;
    background: #f1f5f9;
    margin-bottom: 0.28rem;
}
.db-kpi__bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 1.1s cubic-bezier(0.4,0,0.2,1);
}
.db-kpi__bar-text {
    font-size: 0.68rem;
    color: #94a3b8;
    display: flex;
    justify-content: space-between;
}

/* Color variants */
.db-kpi--blue   .db-kpi__accent, .db-kpi--blue   .db-kpi__orb  { background: var(--c-blue); }
.db-kpi--blue   .db-kpi__icon-wrap { background: var(--c-blue-bg); color: var(--c-blue); }
.db-kpi--blue   .db-kpi__value, .db-kpi--blue .db-kpi__icon-wrap svg { color: var(--c-blue); }
.db-kpi--blue   .db-kpi__bar-fill { background: var(--c-blue); }

.db-kpi--indigo .db-kpi__accent, .db-kpi--indigo .db-kpi__orb  { background: var(--c-indigo); }
.db-kpi--indigo .db-kpi__icon-wrap { background: var(--c-indigo-bg); color: var(--c-indigo); }
.db-kpi--indigo .db-kpi__value, .db-kpi--indigo .db-kpi__icon-wrap svg { color: var(--c-indigo); }
.db-kpi--indigo .db-kpi__bar-fill { background: var(--c-indigo); }

.db-kpi--purple .db-kpi__accent, .db-kpi--purple .db-kpi__orb  { background: var(--c-purple); }
.db-kpi--purple .db-kpi__icon-wrap { background: var(--c-purple-bg); color: var(--c-purple); }
.db-kpi--purple .db-kpi__value, .db-kpi--purple .db-kpi__icon-wrap svg { color: var(--c-purple); }
.db-kpi--purple .db-kpi__bar-fill { background: var(--c-purple); }

.db-kpi--teal   .db-kpi__accent, .db-kpi--teal   .db-kpi__orb  { background: var(--c-teal); }
.db-kpi--teal   .db-kpi__icon-wrap { background: var(--c-teal-bg); color: var(--c-teal); }
.db-kpi--teal   .db-kpi__value, .db-kpi--teal .db-kpi__icon-wrap svg { color: var(--c-teal); }
.db-kpi--teal   .db-kpi__bar-fill { background: var(--c-teal); }

.db-kpi--amber  .db-kpi__accent, .db-kpi--amber  .db-kpi__orb  { background: var(--c-amber); }
.db-kpi--amber  .db-kpi__icon-wrap { background: var(--c-amber-bg); color: var(--c-amber); }
.db-kpi--amber  .db-kpi__value, .db-kpi--amber .db-kpi__icon-wrap svg { color: var(--c-amber); }
.db-kpi--amber  .db-kpi__bar-fill { background: var(--c-amber); }

.db-kpi--red    .db-kpi__accent, .db-kpi--red    .db-kpi__orb  { background: var(--c-red); }
.db-kpi--red    .db-kpi__icon-wrap { background: var(--c-red-bg); color: var(--c-red); }
.db-kpi--red    .db-kpi__value, .db-kpi--red .db-kpi__icon-wrap svg { color: var(--c-red); }
.db-kpi--red    .db-kpi__bar-fill { background: var(--c-red); }

/* ── Billing Stat Tiles ───────────────────────────────────────────── */
.db-billing-tile {
    background: var(--db-surface);
    border-radius: var(--radius-md);
    border: 1px solid var(--db-border);
    box-shadow: var(--db-shadow-sm);
    padding: 1.3rem 1.4rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
}
.db-billing-tile:hover { transform: translateY(-2px); box-shadow: var(--db-shadow-md); }
.db-billing-tile__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; }
.db-billing-tile__icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}
.db-billing-tile__label {
    font-size: 0.71rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.3rem;
}
.db-billing-tile__value {
    font-family: var(--font-head);
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.03em;
    line-height: 1;
}
.db-billing-tile__value--lg { font-size: 1.6rem; }
.db-billing-tile__value--xl { font-size: 1.4rem; }
.db-billing-tile__sub { font-size: 0.74rem; color: #94a3b8; margin-top: 0.3rem; }
.db-billing-tile__badge {
    font-size: 0.65rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 99px;
    white-space: nowrap;
}
.db-billing-tile--green .db-billing-tile__icon { background: #f0fdf4; color: #059669; }
.db-billing-tile--blue  .db-billing-tile__icon { background: #eff6ff; color: #1d4ed8; }
.db-billing-tile--amber .db-billing-tile__icon { background: #fffbeb; color: #d97706; }
.db-billing-tile--slate .db-billing-tile__icon { background: #f8fafc; color: #475569; }

/* ── Chart Panels ────────────────────────────────────────────────── */
.db-panel {
    background: var(--db-surface);
    border-radius: var(--radius-lg);
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
    font-size: 0.92rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.12rem;
}
.db-panel__sub { font-size: 0.76rem; color: #94a3b8; }
.db-panel__badge {
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 0.22rem 0.65rem;
    border-radius: 99px;
    white-space: nowrap;
    flex-shrink: 0;
}
.db-panel__link {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-decoration: none;
    padding: 0.22rem 0.65rem;
    border-radius: 99px;
    background: #f1f5f9;
    transition: background 0.15s, color 0.15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.db-panel__link:hover { background: #e2e8f0; color: #334155; }
.db-chart--300 { height: 300px; flex: none; }
.db-chart--260 { height: 260px; flex: none; }
.db-chart--220 { height: 220px; flex: none; }
.db-chart--200 { height: 200px; flex: none; }

/* ── Operational Health Tiles ────────────────────────────────────── */
.db-health-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.65rem;
    margin-bottom: 0.9rem;
}
.db-health-tile {
    border-radius: var(--radius-sm);
    padding: 0.9rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}
.db-health-tile--blue   { background: #eff6ff; }
.db-health-tile--teal   { background: #ecfeff; }
.db-health-tile--purple { background: #f5f3ff; }
.db-health-tile--green  { background: #f0fdf4; }
.db-health-tile__label {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
}
.db-health-tile__value {
    font-family: var(--font-head);
    font-size: 1.55rem;
    font-weight: 700;
    line-height: 1;
}
.db-health-tile--blue   .db-health-tile__value { color: var(--c-blue); }
.db-health-tile--teal   .db-health-tile__value { color: var(--c-teal); }
.db-health-tile--purple .db-health-tile__value { color: var(--c-purple); }
.db-health-tile--green  .db-health-tile__value { color: var(--c-green); }

/* ── Tables ──────────────────────────────────────────────────────── */
.db-table { width: 100%; font-size: 0.84rem; }
.db-table thead th {
    font-size: 0.67rem;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 0 0.75rem 0.6rem;
    border-bottom: 1px solid var(--db-border);
    white-space: nowrap;
}
.db-table tbody td {
    padding: 0.68rem 0.75rem;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
    color: #334155;
}
.db-table tbody tr:last-child td { border-bottom: none; }
.db-table tbody tr:hover td { background: #fafbfd; }
.db-cell-name  { font-weight: 600; color: #0f172a; line-height: 1.3; }
.db-cell-email { font-size: 0.71rem; color: #94a3b8; }
.db-badge {
    font-size: 0.66rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    padding: 0.25rem 0.65rem;
    border-radius: 99px;
    display: inline-block;
    white-space: nowrap;
}
.db-badge--field   { background: #ecfeff; color: #0e7490; }
.db-badge--corp    { background: #eff6ff; color: #1d4ed8; }
.db-badge--default { background: #f1f5f9; color: #64748b; }
.db-badge--brand   { background: #f5f3ff; color: #6d28d9; }

/* ── Divider ─────────────────────────────────────────────────────── */
.db-divider { height: 1px; background: var(--db-border); margin: 0.9rem 0; }

/* ── Animations ──────────────────────────────────────────────────── */
.db-fadein {
    opacity: 0;
    transform: translateY(14px);
    animation: db-fadein 0.5s ease forwards;
}
@keyframes db-fadein {
    to { opacity: 1; transform: translateY(0); }
}
.db-fadein:nth-child(1) { animation-delay: 0.04s; }
.db-fadein:nth-child(2) { animation-delay: 0.08s; }
.db-fadein:nth-child(3) { animation-delay: 0.12s; }
.db-fadein:nth-child(4) { animation-delay: 0.16s; }
.db-fadein:nth-child(5) { animation-delay: 0.20s; }
.db-fadein:nth-child(6) { animation-delay: 0.24s; }

.db-fadein-panel {
    opacity: 0;
    transform: translateY(10px);
    animation: db-fadein 0.55s ease forwards;
    animation-delay: 0.3s;
}

/* ── Responsive ──────────────────────────────────────────────────── */
@media (max-width: 991.98px) {
    .db-hero { flex-direction: column; align-items: flex-start; }
    .db-hero__meta { align-items: flex-start; flex-direction: row; flex-wrap: wrap; gap: 0.5rem; }
}
@media (max-width: 767.98px) {
    .db-hero__actions { display: none; }
}
@media (max-width: 575.98px) {
    .db-page { padding: 1rem; }
    .db-hero { padding: 1.3rem; }
    .db-hero__clock { font-size: 1.5rem; }
    .db-health-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('body')
@php
    use App\Models\BillPeriod;
    use App\Models\StoreBrandBill;
    use App\Models\BillDispute;
    use App\Models\BrandBillDispute;

    /* ── Billing data (fetched inline) ── */
    $billingTotalAmount    = StoreBrandBill::whereIn('bill_status', ['finalized', 'paid'])->sum('final_amount');
    $activePeriods         = BillPeriod::whereIn('status', ['open', 'generated'])->count();
    $totalPeriods          = BillPeriod::count();
    $pendingBillDisputes   = BillDispute::where('status', 'pending')->count();
    $pendingBrandDisputes  = BrandBillDispute::where('status', 'pending')->count();
    $totalPendingDisputes  = $pendingBillDisputes + $pendingBrandDisputes;
    $totalBills            = StoreBrandBill::count();
    $paidBills             = StoreBrandBill::where('bill_status', 'paid')->count();

    $billStatusCounts = StoreBrandBill::selectRaw('bill_status, COUNT(*) as total')
        ->groupBy('bill_status')
        ->pluck('total', 'bill_status')
        ->toArray();

    /* ── Existing data guards ── */
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

    $userName     = auth()->user()?->name ?? 'Admin';
    $userInitials = collect(explode(' ', $userName))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');

    $kpiIcons = [
        'store'  => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/></svg>',
        'layers' => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polygon stroke-linecap="round" stroke-linejoin="round" points="12 2 2 7 12 12 22 7 12 2"/><polyline stroke-linecap="round" stroke-linejoin="round" points="2 17 12 22 22 17"/><polyline stroke-linecap="round" stroke-linejoin="round" points="2 12 12 17 22 12"/></svg>',
        'tag'    => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>',
        'link'   => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>',
        'clock'  => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline stroke-linecap="round" stroke-linejoin="round" points="12 6 12 12 16 14"/></svg>',
        'alert'  => '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line stroke-linecap="round" x1="12" y1="9" x2="12" y2="13"/><line stroke-linecap="round" x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    ];
@endphp

<div class="db-page">

    {{-- ════════════════════════════════════════════════════════════
         HERO BANNER
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="db-hero d-flex align-items-start justify-content-between gap-3 mb-4">
        <div class="db-hero__body">
            <div class="db-hero__eyebrow">
                <span class="db-hero__status-dot"></span>
                Operations Live &bull; Retail Asset Intelligence
            </div>
            <h1 class="db-hero__title">Remark HB Dashboard</h1>
            <p class="db-hero__sub mb-0">
                Unified visibility across stores, assets, brands, visual merchandising, and billing — all in one place.
            </p>
            <div class="db-hero__actions">
                <a href="{{ route('billing.periods.index') }}" class="db-quick-btn">
                    <i class="las la-file-invoice-dollar"></i> Billing Periods
                </a>
                <a href="{{ route('billing.disputes.index') }}" class="db-quick-btn">
                    <i class="las la-balance-scale"></i> Disputes
                    @if($totalPendingDisputes > 0)
                        <span style="background:rgba(239,68,68,0.9);color:#fff;border-radius:99px;padding:0 5px;font-size:0.62rem;min-width:16px;text-align:center;line-height:16px;height:16px;display:inline-flex;align-items:center;justify-content:center;">{{ $totalPendingDisputes }}</span>
                    @endif
                </a>
                <a href="{{ route('users.index') }}" class="db-quick-btn">
                    <i class="las la-users"></i> Users
                </a>
                <a href="{{ route('assets.planogram-histories') }}" class="db-quick-btn">
                    <i class="las la-history"></i> Planogram History
                </a>
            </div>
        </div>
        <div class="db-hero__meta">
            <div class="db-hero__clock" id="db-clock">--:--:--</div>
            <div class="db-hero__date" id="db-date"></div>
            <div class="db-hero__pill">
                <div class="db-hero__avatar">{{ $userInitials }}</div>
                <div>
                    <div class="db-hero__user-name">{{ $userName }}</div>
                    <div class="db-hero__user-role">Administrator</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         KPI CARDS
    ═══════════════════════════════════════════════════════════════ --}}
    <p class="db-section-label">Key Performance Indicators</p>
    <div class="row g-3 mb-4">
        @foreach($kpis as $kpi)
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
                <div class="db-kpi__icon-wrap">{!! $kpiIcons[$iconKey] ?? $kpiIcons['layers'] !!}</div>
                <div class="db-kpi__label">{{ $kpi['label'] ?? '' }}</div>
                <div class="db-kpi__value" data-counter="{{ $value }}">0</div>
                <div class="db-kpi__meta">{{ $kpi['meta'] ?? '' }}</div>
                @if($total > 0)
                <div class="db-kpi__progress">
                    <div class="db-kpi__bar-bg">
                        <div class="db-kpi__bar-fill" style="width:0%" data-target-width="{{ $pct }}%"></div>
                    </div>
                    <div class="db-kpi__bar-text">
                        <span>{{ number_format($value) }} active</span>
                        <span>{{ $pct }}% of {{ number_format($total) }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ════════════════════════════════════════════════════════════
         BILLING OVERVIEW
    ═══════════════════════════════════════════════════════════════ --}}
    <p class="db-section-label">Billing & Finance</p>
    <div class="row g-3 mb-4">

        {{-- 4 billing stat tiles --}}
        <div class="col-12 col-xl-8">
            <div class="row g-3 h-100">

                {{-- Total Billed Amount --}}
                <div class="col-12 col-sm-6">
                    <div class="db-billing-tile db-billing-tile--green db-fadein-panel">
                        <div>
                            <div class="db-billing-tile__top">
                                <div class="db-billing-tile__icon">
                                    <i class="las la-money-bill-wave" style="font-size:1.1rem"></i>
                                </div>
                                <span class="db-billing-tile__badge" style="background:#f0fdf4;color:#059669;">Collected</span>
                            </div>
                            <div class="db-billing-tile__label mt-2">Total Billed (Finalized + Paid)</div>
                            <div class="db-billing-tile__value db-billing-tile__value--xl" data-counter-float="{{ number_format((float)$billingTotalAmount, 2, '.', '') }}">
                                ৳ 0
                            </div>
                        </div>
                        <div class="db-billing-tile__sub">
                            <i class="las la-check-circle" style="color:#059669"></i>
                            {{ number_format($paidBills) }} bills fully paid
                        </div>
                    </div>
                </div>

                {{-- Active Billing Periods --}}
                <div class="col-12 col-sm-6">
                    <div class="db-billing-tile db-billing-tile--blue db-fadein-panel">
                        <div>
                            <div class="db-billing-tile__top">
                                <div class="db-billing-tile__icon">
                                    <i class="las la-calendar-alt" style="font-size:1.1rem"></i>
                                </div>
                                <span class="db-billing-tile__badge" style="background:#eff6ff;color:#1d4ed8;">Active</span>
                            </div>
                            <div class="db-billing-tile__label mt-2">Active Billing Periods</div>
                            <div class="db-billing-tile__value db-billing-tile__value--lg" data-counter="{{ $activePeriods }}">0</div>
                        </div>
                        <div class="db-billing-tile__sub">
                            <i class="las la-layer-group" style="color:#3b82f6"></i>
                            {{ number_format($totalPeriods) }} total periods
                            &bull; <a href="{{ route('billing.periods.index') }}" style="color:#3b82f6;text-decoration:none;font-weight:600;">View all &rarr;</a>
                        </div>
                    </div>
                </div>

                {{-- Pending Disputes --}}
                <div class="col-12 col-sm-6">
                    <div class="db-billing-tile db-billing-tile--amber db-fadein-panel">
                        <div>
                            <div class="db-billing-tile__top">
                                <div class="db-billing-tile__icon">
                                    <i class="las la-balance-scale" style="font-size:1.1rem"></i>
                                </div>
                                @if($totalPendingDisputes > 0)
                                    <span class="db-billing-tile__badge" style="background:#fef3c7;color:#d97706;">Action Needed</span>
                                @else
                                    <span class="db-billing-tile__badge" style="background:#f0fdf4;color:#059669;">Clear</span>
                                @endif
                            </div>
                            <div class="db-billing-tile__label mt-2">Pending Disputes</div>
                            <div class="db-billing-tile__value db-billing-tile__value--lg" style="{{ $totalPendingDisputes > 0 ? 'color:#d97706' : 'color:#059669' }}" data-counter="{{ $totalPendingDisputes }}">0</div>
                        </div>
                        <div class="db-billing-tile__sub">
                            <i class="las la-file-invoice" style="color:#d97706"></i>
                            {{ $pendingBillDisputes }} bill &bull; {{ $pendingBrandDisputes }} brand
                            &bull; <a href="{{ route('billing.disputes.index') }}" style="color:#d97706;text-decoration:none;font-weight:600;">Review &rarr;</a>
                        </div>
                    </div>
                </div>

                {{-- Total Bills --}}
                <div class="col-12 col-sm-6">
                    <div class="db-billing-tile db-billing-tile--slate db-fadein-panel">
                        <div>
                            <div class="db-billing-tile__top">
                                <div class="db-billing-tile__icon">
                                    <i class="las la-file-invoice-dollar" style="font-size:1.1rem"></i>
                                </div>
                                <span class="db-billing-tile__badge" style="background:#f1f5f9;color:#475569;">All Time</span>
                            </div>
                            <div class="db-billing-tile__label mt-2">Total Bills Generated</div>
                            <div class="db-billing-tile__value db-billing-tile__value--lg" data-counter="{{ $totalBills }}">0</div>
                        </div>
                        <div class="db-billing-tile__sub">
                            <i class="las la-check-double" style="color:#475569"></i>
                            {{ number_format($billStatusCounts['paid'] ?? 0) }} paid &bull;
                            {{ number_format($billStatusCounts['finalized'] ?? 0) }} finalized
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Bill Status Donut --}}
        <div class="col-12 col-xl-4">
            <div class="db-panel db-fadein-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Bill Status Breakdown</div>
                        <div class="db-panel__sub">Distribution across all billing stages</div>
                    </div>
                    <span class="db-panel__badge bg-success-subtle text-success">Donut</span>
                </div>
                <div id="chart-bill-status" class="db-chart--260" style="flex:1"></div>
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════════════
         TREND ANALYSIS
    ═══════════════════════════════════════════════════════════════ --}}
    <p class="db-section-label">Trend Analysis</p>
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <div class="db-panel db-fadein-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Monthly Growth Trends</div>
                        <div class="db-panel__sub">Assets, users and VM issue creation over time</div>
                    </div>
                    <span class="db-panel__badge bg-primary-subtle text-primary">Spline</span>
                </div>
                <div id="chart-monthly" class="db-chart--300"></div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="db-panel db-fadein-panel">
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

    {{-- ════════════════════════════════════════════════════════════
         ASSET & STORE INTELLIGENCE
    ═══════════════════════════════════════════════════════════════ --}}
    <p class="db-section-label">Asset & Store Intelligence</p>
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-6">
            <div class="db-panel db-fadein-panel">
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
            <div class="db-panel db-fadein-panel">
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

    {{-- ════════════════════════════════════════════════════════════
         BRAND & OPERATIONAL HEALTH
    ═══════════════════════════════════════════════════════════════ --}}
    <p class="db-section-label">Brand & Operational Health</p>
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-7">
            <div class="db-panel db-fadein-panel">
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
            <div class="db-panel db-fadein-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Operational Health</div>
                        <div class="db-panel__sub">Quality and readiness indicators</div>
                    </div>
                </div>
                <div class="db-health-grid">
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
                <div class="db-panel__title mb-1" style="font-size:0.84rem;">Planogram Activity</div>
                <div class="db-panel__sub mb-2">Updates over last 14 days</div>
                <div id="chart-planogram" class="db-chart--200"></div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         RECENT ACTIVITY
    ═══════════════════════════════════════════════════════════════ --}}
    <p class="db-section-label">Recent Activity</p>
    <div class="row g-3">

        {{-- Recent Users --}}
        <div class="col-12 col-xl-6">
            <div class="db-panel db-fadein-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Recent Users</div>
                        <div class="db-panel__sub">Latest accounts added to the platform</div>
                    </div>
                    <a href="{{ route('users.index') }}" class="db-panel__link">View All &rarr;</a>
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
                                    <div class="db-cell-name">{{ $user->name }}</div>
                                    <div class="db-cell-email">{{ $user->email }}</div>
                                </td>
                                <td style="color:#64748b;font-variant-numeric:tabular-nums;">
                                    {{ $user->employee_id ?: '—' }}
                                </td>
                                <td>
                                    @php $sector = strtolower($user->usages_sector ?? ''); @endphp
                                    <span class="db-badge db-badge--{{ $sector === 'field' ? 'field' : ($sector ? 'corp' : 'default') }}">
                                        {{ ucfirst($user->usages_sector ?: 'N/A') }}
                                    </span>
                                </td>
                                <td style="color:#94a3b8;font-size:0.76rem;white-space:nowrap;">
                                    {{ optional($user->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;color:#94a3b8;padding:2.5rem 0;">
                                    <i class="las la-user-slash" style="font-size:1.5rem;display:block;margin-bottom:0.4rem;opacity:0.5"></i>
                                    No recent users found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Planogram Updates --}}
        <div class="col-12 col-xl-6">
            <div class="db-panel db-fadein-panel">
                <div class="db-panel__head">
                    <div>
                        <div class="db-panel__title">Recent Planogram Updates</div>
                        <div class="db-panel__sub">Latest planogram changes across stores &amp; assets</div>
                    </div>
                    <a href="{{ route('assets.planogram-histories') }}" class="db-panel__link">View All &rarr;</a>
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
                                <td style="font-weight:600;color:#0f172a;">{{ $history->store?->title ?: '—' }}</td>
                                <td style="color:#334155;">{{ $history->asset?->name ?: '—' }}</td>
                                <td>
                                    <span class="db-badge db-badge--brand">{{ $history->brand?->name ?: '—' }}</span>
                                </td>
                                <td style="color:#94a3b8;font-size:0.76rem;white-space:nowrap;">
                                    {{ optional($history->created_at)->format('d M y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;color:#94a3b8;padding:2.5rem 0;">
                                    <i class="las la-history" style="font-size:1.5rem;display:block;margin-bottom:0.4rem;opacity:0.5"></i>
                                    No planogram activity found.
                                </td>
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

    /* ── Live Clock ───────────────────────────────────────────── */
    function tickClock() {
        var now    = new Date();
        var pad    = function(n){ return String(n).padStart(2,'0'); };
        var days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var el = document.getElementById('db-clock');
        var de = document.getElementById('db-date');
        if (el) el.textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        if (de) de.textContent = days[now.getDay()]+', '+now.getDate()+' '+months[now.getMonth()]+' '+now.getFullYear();
    }
    tickClock();
    setInterval(tickClock, 1000);

    /* ── Counter Animation ────────────────────────────────────── */
    function animateCounter(el, target, duration) {
        var start = 0, step = target / (duration / 16);
        var timer = setInterval(function () {
            start += step;
            if (start >= target) { start = target; clearInterval(timer); }
            el.textContent = Math.round(start).toLocaleString();
        }, 16);
    }

    /* Progress bars */
    document.querySelectorAll('.db-kpi__bar-fill').forEach(function(bar){
        var t = bar.getAttribute('data-target-width') || '0%';
        setTimeout(function(){ bar.style.width = t; }, 400);
    });

    /* Integer counters */
    document.querySelectorAll('[data-counter]').forEach(function(el){
        animateCounter(el, parseInt(el.getAttribute('data-counter'), 10) || 0, 1200);
    });

    /* Float counter for billing amount */
    document.querySelectorAll('[data-counter-float]').forEach(function(el){
        var raw    = parseFloat(el.getAttribute('data-counter-float')) || 0;
        var prefix = '৳ ';
        var start  = 0, duration = 1400, step = raw / (duration / 16);
        var timer  = setInterval(function(){
            start += step;
            if (start >= raw) { start = raw; clearInterval(timer); }
            el.textContent = prefix + start.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }, 16);
    });

    /* ── Highcharts Global Defaults ───────────────────────────── */
    Highcharts.setOptions({
        chart: { style: { fontFamily: "'DM Sans', sans-serif" } },
        credits: { enabled: false },
        exporting: { enabled: false },
        title: { text: null },
        colors: ['#1a56db','#0694a2','#7e3af2','#d97706','#e02424','#057a55','#0284c7','#9333ea'],
        xAxis: {
            lineColor: '#e2e8f0', tickColor: '#e2e8f0',
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
            itemStyle: { fontWeight: '500', color: '#64748b', fontSize: '0.77rem' },
            itemHoverStyle: { color: '#0f172a' }
        },
        plotOptions: {
            series: { animation: { duration: 950 } },
            column: { borderRadius: 4, borderWidth: 0 },
            bar:    { borderRadius: 4, borderWidth: 0 },
            pie:    { borderWidth: 0 }
        }
    });

    /* ── PHP Data → JS ────────────────────────────────────────── */
    var assetMix            = @json($assetMix->map(fn($i) => ['name' => $i->name, 'y' => (int)$i->total])->values());
    var topStores           = @json($topStores->map(fn($i) => ['name' => $i->title, 'y' => (int)$i->assets_count])->values());
    var userSectorMix       = @json($userSectorMix->map(fn($i) => ['name' => ucfirst($i->usages_sector ?? 'Unknown'), 'y' => (int)$i->total])->values());
    var brandCoverage       = @json($brandCoverage->map(fn($i) => ['name' => $i->name, 'y' => (int)$i->total])->values());
    var monthlyCategories   = @json($monthlyAssets->pluck('period')->unique()->values());
    var monthlyAssetsSeries = @json($monthlyAssets->pluck('total')->map(fn($v) => (int)$v)->values());
    var monthlyUsersSeries  = @json($monthlyUsers->pluck('total')->map(fn($v) => (int)$v)->values());
    var monthlyVmSeries     = @json($monthlyVmIssues->pluck('total')->map(fn($v) => (int)$v)->values());
    var planogramCats       = @json($planogramActivity->pluck('activity_date')->values());
    var planogramSeries     = @json($planogramActivity->pluck('total')->map(fn($v) => (int)$v)->values());

    var billStatusData = [
        { name: 'Draft',     y: {{ (int)($billStatusCounts['draft']     ?? 0) }}, color: '#cbd5e1' },
        { name: 'Issued',    y: {{ (int)($billStatusCounts['issued']    ?? 0) }}, color: '#3b82f6' },
        { name: 'Disputed',  y: {{ (int)($billStatusCounts['disputed']  ?? 0) }}, color: '#ef4444' },
        { name: 'Adjusted',  y: {{ (int)($billStatusCounts['adjusted']  ?? 0) }}, color: '#f59e0b' },
        { name: 'Finalized', y: {{ (int)($billStatusCounts['finalized'] ?? 0) }}, color: '#10b981' },
        { name: 'Paid',      y: {{ (int)($billStatusCounts['paid']      ?? 0) }}, color: '#059669' },
    ];

    /* ── Chart: Bill Status Donut ─────────────────────────────── */
    Highcharts.chart('chart-bill-status', {
        chart: { type: 'pie', backgroundColor: 'transparent', height: 260 },
        plotOptions: {
            pie: {
                innerSize: '62%',
                dataLabels: { enabled: false },
                showInLegend: true,
                point: {
                    events: {
                        legendItemClick: function(){ return false; }
                    }
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            itemStyle: { color: '#64748b', fontSize: '0.73rem', fontWeight: '500' },
            symbolRadius: 4,
            itemMarginBottom: 4,
        },
        series: [{
            name: 'Bills',
            data: billStatusData.filter(function(d){ return d.y > 0; }),
        }]
    });

    /* ── Chart: Monthly Growth Spline ─────────────────────────── */
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

    /* ── Chart: User Sector Donut ─────────────────────────────── */
    Highcharts.chart('chart-sector', {
        chart: { type: 'pie', backgroundColor: 'transparent', height: 300 },
        plotOptions: {
            pie: {
                innerSize: '60%',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b><br>{point.y}',
                    style: { color: '#334155', fontSize: '0.73rem', fontWeight: '500', textOutline: 'none' }
                }
            }
        },
        series: [{ name: 'Users', data: userSectorMix }],
        legend: { enabled: false }
    });

    /* ── Chart: Asset Mix Column ──────────────────────────────── */
    Highcharts.chart('chart-asset-mix', {
        chart: { type: 'column', backgroundColor: 'transparent', height: 300 },
        xAxis: { type: 'category' },
        series: [{ name: 'Assets', data: assetMix, colorByPoint: true }]
    });

    /* ── Chart: Top Stores Bar ────────────────────────────────── */
    Highcharts.chart('chart-top-stores', {
        chart: { type: 'bar', backgroundColor: 'transparent', height: 300 },
        xAxis: { type: 'category' },
        series: [{ name: 'Assets', data: topStores, color: '#0694a2' }]
    });

    /* ── Chart: Brand Coverage Column ────────────────────────── */
    Highcharts.chart('chart-brand', {
        chart: { type: 'column', backgroundColor: 'transparent', height: 300 },
        xAxis: { type: 'category' },
        series: [{ name: 'Assignments', data: brandCoverage, color: '#7e3af2' }]
    });

    /* ── Chart: Planogram Sparkline Areaspline ────────────────── */
    Highcharts.chart('chart-planogram', {
        chart: { type: 'areaspline', backgroundColor: 'transparent', height: 200 },
        xAxis: { categories: planogramCats, labels: { rotation: -40, style: { fontSize: '0.65rem' } } },
        series: [{
            name: 'Updates',
            data: planogramSeries,
            color: '#d97706',
            fillColor: {
                linearGradient: { x1:0, y1:0, x2:0, y2:1 },
                stops: [ [0,'rgba(217,119,6,0.35)'], [1,'rgba(217,119,6,0.02)'] ]
            },
            marker: { radius: 3 }
        }],
        legend: { enabled: false }
    });

})();
</script>
@endpush
