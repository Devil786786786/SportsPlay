<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Leagues';
$activeNav = 'leagues';
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="sp-split">
  <section class="sp-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Leagues & Divisions</div>
        <div class="sp-card__sub">Central configuration for seasons, sports, age groups, and fees.</div>
      </div>

      <div class="sp-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgLeagueCreate"><i class="fa-solid fa-plus"></i>&nbsp; Create League</button>
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-copy"></i>&nbsp; Duplicate Season</button>
      </div>
    </div>

    <div class="sp-card__bd">
      <div class="sp-filterbar">
        <div class="sp-filterbar__left">
          <div class="sp-search">
            <i class="fa-solid fa-magnifying-glass icon"></i>
            <input data-table-search="#tblLeagues" type="text" placeholder="Search leagues…" />
          </div>

          <select class="sp-select" data-table-filter="#tblLeagues" data-col="1">
            <option value="">All seasons</option>
            <option>2026 Spring</option>
            <option>2025 Fall</option>
          </select>

          <select class="sp-select" data-table-filter="#tblLeagues" data-col="2">
            <option value="">All sports</option>
            <option>Soccer</option>
            <option>Basketball</option>
          </select>
        </div>

        <div class="sp-filterbar__right">
          <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
        </div>
      </div>

      <div style="height:12px"></div>

      <div class="sp-table-wrap" style="max-height: 520px; border:1px solid var(--line)">
        <table id="tblLeagues" class="sp-table sp-table--light">
          <thead>
            <tr>
              <th>League</th>
              <th style="width:140px">Season</th>
              <th style="width:120px">Sport</th>
              <th style="width:120px">Fee</th>
              <th style="width:140px">Reg Window</th>
              <th style="width:120px">Status</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>U14</strong><div class="sp-card__sub">Age: 13–14 · Roster cap: 18</div></td>
              <td>2026 Spring</td>
              <td>Soccer</td>
              <td>$180</td>
              <td>Feb 1 – Mar 1</td>
              <td><span class="sp-pill sp-pill--success">Open</span></td>
              <td>
                <div class="sp-actions">
                  <button class="sp-btn-tag primary" type="button">Select</button>
                  <button class="sp-btn-tag" type="button">Edit</button>
                  <button class="sp-btn-tag danger" type="button">Close</button>
                </div>
              </td>
            </tr>

            <tr>
              <td><strong>U16</strong><div class="sp-card__sub">Age: 15–16 · Roster cap: 18</div></td>
              <td>2026 Spring</td>
              <td>Soccer</td>
              <td>$220</td>
              <td>Feb 1 – Mar 1</td>
              <td><span class="sp-pill sp-pill--warning">Draft</span></td>
              <td>
                <div class="sp-actions">
                  <button class="sp-btn-tag primary" type="button">Select</button>
                  <button class="sp-btn-tag" type="button">Edit</button>
                  <button class="sp-btn-tag danger" type="button">Delete</button>
                </div>
              </td>
            </tr>

            <tr>
              <td><strong>U18</strong><div class="sp-card__sub">Age: 17–18 · Roster cap: 20</div></td>
              <td>2025 Fall</td>
              <td>Basketball</td>
              <td>$240</td>
              <td>Aug 15 – Sep 10</td>
              <td><span class="sp-pill">Closed</span></td>
              <td>
                <div class="sp-actions">
                  <button class="sp-btn-tag" type="button">Select</button>
                  <button class="sp-btn-tag" type="button">Edit</button>
                  <button class="sp-btn-tag danger" type="button">Archive</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="sp-alert" style="margin-top:12px">
        <strong>Admin autonomy idea:</strong> League = "template" for everything (fee, forms, roster cap, schedule rules). Then teams inherit defaults.
      </div>
    </div>
  </section>

  <section class="sp-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">League Settings</div>
        <div class="sp-card__sub">Edit once, apply everywhere (selected: U14 Soccer · 2026 Spring)</div>
      </div>
      <span class="sp-pill"><i class="fa-solid fa-wand-magic-sparkles"></i> Smart Defaults</span>
    </div>

    <div class="sp-card__bd">
      <div class="sp-form-grid">
        <div class="sp-col-6">
          <label class="sp-card__sub">Registration fee</label>
          <input class="sp-input" style="width:100%" value="$180" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Roster cap</label>
          <input class="sp-input" style="width:100%" value="18" />
        </div>

        <div class="sp-col-6">
          <label class="sp-card__sub">Registration opens</label>
          <input class="sp-input" style="width:100%" value="2026-02-01" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Registration closes</label>
          <input class="sp-input" style="width:100%" value="2026-03-01" />
        </div>

        <div class="sp-col-12">
          <label class="sp-card__sub">Required forms</label>
          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <span class="sp-pill sp-pill--success"><i class="fa-solid fa-file-signature"></i> Waiver</span>
            <span class="sp-pill sp-pill--success"><i class="fa-solid fa-heart-pulse"></i> Medical</span>
            <span class="sp-pill sp-pill--warning"><i class="fa-solid fa-id-card"></i> ID Proof</span>
          </div>
        </div>

        <div class="sp-col-12">
          <label class="sp-card__sub">Auto-notifications</label>
          <div class="sp-alert" style="background:#f8fafc">
            Send confirmation email, payment receipt, and season kickoff reminder 7 days before first match.
          </div>
        </div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button">Reset</button>
        <button class="sp-btn sp-btn--pill" type="button">Save Changes (UI)</button>
      </div>

      <div class="sp-alert" style="margin-top:12px">
        <strong>Space tip:</strong> keep "edit" on the right and list on the left. Admin can click a league row and settings swap in-place.
      </div>
    </div>
  </section>
</div>

<dialog id="dlgLeagueCreate" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Create League</div>
    <div class="sp-card__sub">UI-only.</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6"><label class="sp-card__sub">Season</label><select class="sp-select" style="width:100%"><option>2026 Spring</option><option>2025 Fall</option></select></div>
        <div class="sp-col-6"><label class="sp-card__sub">Sport</label><select class="sp-select" style="width:100%"><option>Soccer</option><option>Basketball</option></select></div>
        <div class="sp-col-6"><label class="sp-card__sub">League</label><input class="sp-input" style="width:100%" placeholder="U14" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Age range</label><input class="sp-input" style="width:100%" placeholder="13–14" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Fee</label><input class="sp-input" style="width:100%" placeholder="$180" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Roster cap</label><input class="sp-input" style="width:100%" placeholder="18" /></div>
      </div>
      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Create (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
