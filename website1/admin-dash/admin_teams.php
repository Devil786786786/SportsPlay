<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Teams';
$activeNav = 'teams';
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Teams</div>
      <div class="sp-card__sub">Create, edit, assign coaches, manage rosters, and message teams.</div>
    </div>

    <div class="sp-actions">
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgTeamCreate"><i class="fa-solid fa-plus"></i>&nbsp; Create Team</button>
      <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-paper-plane"></i>&nbsp; Message Selected</button>
    </div>
  </div>

  <div class="sp-card__bd">
    <div class="sp-filterbar">
      <div class="sp-filterbar__left">
        <div class="sp-search">
          <i class="fa-solid fa-magnifying-glass icon"></i>
          <input data-table-search="#tblTeams" type="text" placeholder="Search teams, coaches, cities…" />
        </div>

        <select class="sp-select" data-table-filter="#tblTeams" data-col="2">
          <option value="">All sports</option>
          <option>Soccer</option>
          <option>Basketball</option>
          <option>Baseball</option>
        </select>

        <select class="sp-select" data-table-filter="#tblTeams" data-col="3">
          <option value="">All leagues</option>
          <option>U14</option>
          <option>U16</option>
          <option>U18</option>
        </select>

        <select class="sp-select" data-table-filter="#tblTeams" data-col="6">
          <option value="">Any status</option>
          <option>Active</option>
          <option>Pending</option>
          <option>Archived</option>
        </select>
      </div>

      <div class="sp-filterbar__right">
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-sliders"></i>&nbsp; Advanced Filters</button>
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
      </div>
    </div>

    <div style="height:12px"></div>

    <div class="sp-table-wrap" style="max-height: 520px; border:1px solid var(--line)">
      <table id="tblTeams" class="sp-table sp-table--light">
        <thead>
          <tr>
            <th style="width:44px"><input type="checkbox" aria-label="Select all" /></th>
            <th>Team</th>
            <th>Sport</th>
            <th>League</th>
            <th>Coach</th>
            <th style="width:110px">Roster</th>
            <th style="width:120px">Status</th>
            <th style="width:180px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox" /></td>
            <td><strong>FC Miami Blue</strong><div class="sp-card__sub">Miami, FL</div></td>
            <td>Soccer</td>
            <td>U14</td>
            <td>Coach Williams</td>
            <td>16 / 18</td>
            <td><span class="sp-pill sp-pill--success">Active</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">View</button>
                <button class="sp-btn-tag" type="button">Edit</button>
                <button class="sp-btn-tag danger" type="button">Archive</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><input type="checkbox" /></td>
            <td><strong>FC New York</strong><div class="sp-card__sub">New York, NY</div></td>
            <td>Soccer</td>
            <td>U16</td>
            <td>Unassigned</td>
            <td>12 / 18</td>
            <td><span class="sp-pill sp-pill--warning">Pending</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">Assign</button>
                <button class="sp-btn-tag" type="button">Edit</button>
                <button class="sp-btn-tag danger" type="button">Archive</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><input type="checkbox" /></td>
            <td><strong>LA United</strong><div class="sp-card__sub">Los Angeles, CA</div></td>
            <td>Basketball</td>
            <td>U18</td>
            <td>Coach Rivera</td>
            <td>18 / 18</td>
            <td><span class="sp-pill">Archived</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag" type="button">View</button>
                <button class="sp-btn-tag" type="button">Restore</button>
                <button class="sp-btn-tag danger" type="button">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:12px; flex-wrap:wrap;">
      <div class="sp-card__sub">Showing 3 teams · Filters update instantly (client-side demo).</div>
      <div class="sp-actions">
        <button class="sp-btn sp-btn--ghost" type="button">Prev</button>
        <button class="sp-btn sp-btn--ghost" type="button">Next</button>
      </div>
    </div>
  </div>
</section>

<dialog id="dlgTeamCreate" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Create Team</div>
    <div class="sp-card__sub">UI-only for now. Hook to DB later.</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6">
          <label class="sp-card__sub">Team name</label>
          <input class="sp-input" style="width:100%" placeholder="FC Miami Blue" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Club / City</label>
          <input class="sp-input" style="width:100%" placeholder="Miami, FL" />
        </div>

        <div class="sp-col-6">
          <label class="sp-card__sub">Sport</label>
          <select class="sp-select" style="width:100%"><option>Soccer</option><option>Basketball</option><option>Baseball</option></select>
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">League</label>
          <select class="sp-select" style="width:100%"><option>U14</option><option>U16</option><option>U18</option></select>
        </div>

        <div class="sp-col-6">
          <label class="sp-card__sub">Coach</label>
          <select class="sp-select" style="width:100%"><option>Unassigned</option><option>Coach Williams</option><option>Coach Rivera</option></select>
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Roster cap</label>
          <input class="sp-input" style="width:100%" placeholder="18" />
        </div>

        <div class="sp-col-12">
          <label class="sp-card__sub">Public team page</label>
          <input class="sp-input" style="width:100%" placeholder="Auto-generate URL + allow coach updates" />
        </div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Create (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
