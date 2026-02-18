<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Players';
$activeNav = 'players';
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Players</div>
      <div class="sp-card__sub">Player profiles, registrations, team assignments, and eligibility checks.</div>
    </div>

    <div class="sp-actions">
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgPlayerCreate"><i class="fa-solid fa-plus"></i>&nbsp; Add Player</button>
      <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-arrows-rotate"></i>&nbsp; Sync Payments</button>
      <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
    </div>
  </div>

  <div class="sp-card__bd">
    <div class="sp-kpis" style="margin-bottom:14px;">
      <div class="sp-kpi"><div class="label">Total Players</div><div class="value">1,500</div><div class="meta">season roster (demo)</div></div>
      <div class="sp-kpi"><div class="label">Paid Registrations</div><div class="value">1,214</div><div class="meta">81% conversion</div></div>
      <div class="sp-kpi"><div class="label">Unpaid</div><div class="value">210</div><div class="meta">send reminders</div></div>
      <div class="sp-kpi"><div class="label">Forms Missing</div><div class="value">76</div><div class="meta">waivers / medical</div></div>
    </div>

    <div class="sp-filterbar">
      <div class="sp-filterbar__left">
        <div class="sp-search">
          <i class="fa-solid fa-magnifying-glass icon"></i>
          <input data-table-search="#tblPlayersAdmin" type="text" placeholder="Search player, team, parent email…" />
        </div>

        <select class="sp-select" data-table-filter="#tblPlayersAdmin" data-col="3">
          <option value="">All teams</option>
          <option>FC Miami Blue</option>
          <option>FC New York</option>
          <option>LA United</option>
        </select>

        <select class="sp-select" data-table-filter="#tblPlayersAdmin" data-col="4">
          <option value="">All leagues</option>
          <option>U14</option>
          <option>U16</option>
          <option>U18</option>
        </select>

        <select class="sp-select" data-table-filter="#tblPlayersAdmin" data-col="6">
          <option value="">Any payment</option>
          <option>Paid</option>
          <option>Unpaid</option>
          <option>Refunded</option>
        </select>
      </div>

      <div class="sp-filterbar__right">
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-filter"></i>&nbsp; Eligibility</button>
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-list-check"></i>&nbsp; Bulk Actions</button>
      </div>
    </div>

    <div style="height:12px"></div>

    <div class="sp-table-wrap" style="max-height: 540px; border:1px solid var(--line)">
      <table id="tblPlayersAdmin" class="sp-table sp-table--light">
        <thead>
          <tr>
            <th style="width:44px"><input type="checkbox" aria-label="Select all" /></th>
            <th>Player</th>
            <th style="width:92px">Age</th>
            <th>Team</th>
            <th style="width:90px">League</th>
            <th>Parent</th>
            <th style="width:120px">Payment</th>
            <th style="width:140px">Forms</th>
            <th style="width:180px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox" /></td>
            <td><strong>John Smith</strong><div class="sp-card__sub">DOB: 2012-06-14</div></td>
            <td>13</td>
            <td>FC Miami Blue</td>
            <td>U14</td>
            <td>parent1@email.com</td>
            <td><span class="sp-pill sp-pill--success">Paid</span></td>
            <td><span class="sp-pill">Complete</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">View</button>
                <button class="sp-btn-tag" type="button">Transfer</button>
                <button class="sp-btn-tag danger" type="button">Disable</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><input type="checkbox" /></td>
            <td><strong>Marco Polo</strong><div class="sp-card__sub">DOB: 2010-02-11</div></td>
            <td>15</td>
            <td>FC New York</td>
            <td>U16</td>
            <td>parent2@email.com</td>
            <td><span class="sp-pill sp-pill--warning">Unpaid</span></td>
            <td><span class="sp-pill sp-pill--warning">Missing</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">Remind</button>
                <button class="sp-btn-tag" type="button">Edit</button>
                <button class="sp-btn-tag danger" type="button">Cancel</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><input type="checkbox" /></td>
            <td><strong>Ana Ruiz</strong><div class="sp-card__sub">DOB: 2009-09-03</div></td>
            <td>16</td>
            <td>LA United</td>
            <td>U18</td>
            <td>parent3@email.com</td>
            <td><span class="sp-pill sp-pill--success">Paid</span></td>
            <td><span class="sp-pill">Complete</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">View</button>
                <button class="sp-btn-tag" type="button">Edit</button>
                <button class="sp-btn-tag danger" type="button">Disable</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:12px; flex-wrap:wrap;">
      <div class="sp-card__sub">Suggested admin flow: registrations → payment → forms → team assignment.</div>
      <div class="sp-actions">
        <button class="sp-btn sp-btn--ghost" type="button">Prev</button>
        <button class="sp-btn sp-btn--ghost" type="button">Next</button>
      </div>
    </div>
  </div>
</section>

<dialog id="dlgPlayerCreate" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Add Player</div>
    <div class="sp-card__sub">UI-only. In MVP, this is created via parent registration form.</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6">
          <label class="sp-card__sub">First name</label>
          <input class="sp-input" style="width:100%" placeholder="John" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Last name</label>
          <input class="sp-input" style="width:100%" placeholder="Smith" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Date of birth</label>
          <input class="sp-input" style="width:100%" placeholder="YYYY-MM-DD" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">League</label>
          <select class="sp-select" style="width:100%"><option>U14</option><option>U16</option><option>U18</option></select>
        </div>
        <div class="sp-col-12">
          <label class="sp-card__sub">Parent email</label>
          <input class="sp-input" style="width:100%" placeholder="parent@email.com" />
        </div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Add (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
