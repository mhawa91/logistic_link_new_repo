@push('head')
<style>
    .shipment-import-card { border: 0; box-shadow: 0 4px 18px rgba(43,55,72,.07); border-radius: 10px; overflow: hidden; }
    .shipment-import-card .card-inner { padding: 1.5rem; }
    .shipment-import-heading { display: flex; align-items: flex-start; gap: .9rem; margin-bottom: 1.25rem; }
    .shipment-import-icon { width: 46px; height: 46px; flex: 0 0 46px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #20b9a6; background: rgba(32,185,166,.11); font-size: 1.25rem; }
    .shipment-import-actions { display: grid; gap: .65rem; }
    .shipment-import-actions .btn { min-height: 42px; justify-content: center; }
    .shipment-import-card .custom-file-label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding-inline-end: 6.5rem; }
    .shipment-import-card .custom-file-label:after { min-width: 5.75rem; text-align: center; }
    .shipment-import-history thead th, .shipment-import-report thead th { white-space: nowrap; color: #8094ae; font-weight: 600; background: #f7f9fc; }
    .shipment-import-history td, .shipment-import-report td { vertical-align: middle; }
    .shipment-import-file { min-width: 0; overflow-wrap: anywhere; }
    .shipment-import-result { display: inline-flex; align-items: center; gap: .9rem; white-space: nowrap; }
    .shipment-import-result span { display: inline-flex; align-items: center; gap: .25rem; font-weight: 600; }
    .shipment-import-summary { display: grid; grid-template-columns: minmax(220px, 2fr) repeat(4, minmax(115px, 1fr)); gap: 1px; background: #e5e9f2; }
    .shipment-import-summary > div { background: #fff; padding: 1.25rem; min-width: 0; }
    .shipment-import-summary .summary-label { color: #8094ae; display: block; font-size: .8rem; margin-bottom: .35rem; }
    .shipment-import-summary .summary-value { color: #364a63; font-size: 1.05rem; font-weight: 700; overflow-wrap: anywhere; }
    .shipment-import-summary .summary-number { font-size: 1.65rem; line-height: 1; }
    .shipment-import-progress { padding: 1rem 1.25rem 1.25rem; background: #fff; border-top: 1px solid #e5e9f2; }
    .shipment-import-progress .progress { height: 9px; border-radius: 9px; background: #edf1f7; overflow: hidden; }
    .shipment-import-progress .progress-bar { min-width: 0; font-size: 0; background: linear-gradient(90deg, #20b9a6, #6576ff); }
    .shipment-import-error-list { margin: 0; padding-inline-start: 1.1rem; color: #e85347; }
    .shipment-import-error-list li + li { margin-top: .3rem; }
    @media (max-width: 991.98px) {
        .shipment-import-summary { grid-template-columns: repeat(2, 1fr); }
        .shipment-import-summary .summary-main { grid-column: 1 / -1; }
    }
    @media (max-width: 575.98px) {
        .shipment-import-summary { grid-template-columns: 1fr; }
        .shipment-import-summary .summary-main { grid-column: auto; }
        .shipment-import-card .card-inner { padding: 1.1rem; }
    }
</style>
@endpush
