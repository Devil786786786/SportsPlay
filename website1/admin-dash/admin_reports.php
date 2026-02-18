<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Reports';
$activeNav = 'reports';
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Reports & Analytics</div>
      <div class="sp-card__sub">Registration trends, payments, attendance, and operational insights.</div>
    </div>

    <div class="sp-actions">
      <select class="sp-select" style="height:38px">
        <option>Last 30 days</option>
        <option>Last 90 days</option>
        <option>This season</option>
      </select>
      <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
      <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-chart-column"></i>&nbsp; Build Custom</button>
    </div>
  </div>

  <div class="sp-card__bd">
    <div class="sp-kpis">
      <div class="sp-kpi" style="grid-column: span 4">
        <div class="label">Registrations</div>
        <div class="value">142</div>
        <div class="meta">+18% vs previous period</div>
      </div>
      <div class="sp-kpi" style="grid-column: span 4">
        <div class="label">Payment Collected</div>
        <div class="value">$23,480</div>
        <div class="meta">98% successful payments</div>
      </div>
      <div class="sp-kpi" style="grid-column: span 4">
        <div class="label">Avg. Attendance</div>
        <div class="value">81%</div>
        <div class="meta">Trainings + matches</div>
      </div>
    </div>

    <div style="height:16px"></div>

    <div class="sp-split">
      <div class="sp-card sp-surface">
        <div class="sp-card__hd">
          <div>
            <div class="sp-card__title">League Breakdown</div>
            <div class="sp-card__sub">Registrations by league</div>
          </div>
          <span class="sp-pill" style="background:rgba(255,255,255,.14); border-color:rgba(255,255,255,.20); color:#fff;">
            <i class="fa-solid fa-layer-group"></i> This season
          </span>
        </div>
        <div class="sp-card__bd">
          <div class="sp-progress">
            <div class="sp-progress-row"><div>U14</div><div class="sp-track"><span style="width: 72%"></span></div><div style="text-align:right">72</div></div>
            <div class="sp-progress-row"><div>U16</div><div class="sp-track"><span style="width: 51%"></span></div><div style="text-align:right">51</div></div>
            <div class="sp-progress-row"><div>U18</div><div class="sp-track"><span style="width: 19%"></span></div><div style="text-align:right">19</div></div>
          </div>
        </div>
      </div>

      <div class="sp-card">
        <div class="sp-card__hd">
          <div>
            <div class="sp-card__title">Operational Alerts</div>
            <div class="sp-card__sub">What needs admin attention</div>
          </div>
        </div>
        <div class="sp-card__bd">
          <div class="sp-alert sp-alert--error"><strong>8 unpaid</strong> registrations will be auto-cancelled in 48h.</div>
          <div style="height:10px"></div>
          <div class="sp-alert"><strong>3 teams</strong> are above roster cap (manual override enabled).</div>
          <div style="height:10px"></div>
          <div class="sp-alert"><strong>2 matches</strong> missing results (standings not updated).</div>
          <div style="height:10px"></div>
          <div class="sp-alert sp-alert--success"><strong>All coaches</strong> completed training plans this week.</div>
        </div>
      </div>
    </div>

    <div style="height:16px"></div>

    <div class="sp-card">
      <div class="sp-card__hd">
        <div>
          <div class="sp-card__title">Recent Transactions (demo)</div>
          <div class="sp-card__sub">Show payment state across the platform</div>
        </div>
      </div>
      <div class="sp-card__bd">
        <div class="sp-table-wrap" style="max-height: 320px; border:1px solid var(--line)">
          <table class="sp-table sp-table--light">
            <thead>
              <tr>
                <th style="width:160px">Date</th>
                <th>Customer</th>
                <th>Item</th>
                <th style="width:140px">Amount</th>
                <th style="width:120px">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>2026-02-21</td><td>Sarah Johnson</td><td>U14 Registration</td><td>$180</td><td><span class="sp-pill sp-pill--success">Paid</span></td></tr>
              <tr><td>2026-02-21</td><td>Michael Brown</td><td>U16 Registration</td><td>$220</td><td><span class="sp-pill sp-pill--warning">Unpaid</span></td></tr>
              <tr><td>2026-02-20</td><td>Emily Davis</td><td>U18 Registration</td><td>$240</td><td><span class="sp-pill">Refunded</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
