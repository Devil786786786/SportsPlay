<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Settings';
$activeNav = 'settings';
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="sp-split">
  <section class="sp-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Roles & Permissions</div>
        <div class="sp-card__sub">Admin autonomy requires tight permissions. Keep roles simple (Admin, Coach, Parent).</div>
      </div>
      <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-user-plus"></i>&nbsp; Invite Admin</button>
    </div>

    <div class="sp-card__bd">
      <div class="sp-table-wrap" style="max-height: 380px; border:1px solid var(--line)">
        <table class="sp-table sp-table--light">
          <thead>
            <tr>
              <th>User</th>
              <th style="width:140px">Role</th>
              <th style="width:140px">Status</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Aleksa Ivanovic</strong><div class="sp-card__sub">admin@sportsplay.local</div></td>
              <td>Admin</td>
              <td><span class="sp-pill sp-pill--success">Active</span></td>
              <td>
                <div class="sp-actions">
                  <button class="sp-btn-tag" type="button">Permissions</button>
                  <button class="sp-btn-tag danger" type="button">Remove</button>
                </div>
              </td>
            </tr>
            <tr>
              <td><strong>Coach Rivera</strong><div class="sp-card__sub">coach@email.com</div></td>
              <td>Coach</td>
              <td><span class="sp-pill sp-pill--success">Active</span></td>
              <td>
                <div class="sp-actions">
                  <button class="sp-btn-tag" type="button">Reset</button>
                  <button class="sp-btn-tag danger" type="button">Disable</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="sp-alert" style="margin-top:12px">
        <strong>Suggestion:</strong> add a lightweight permission matrix later (e.g., “Can edit payments”, “Can edit schedules”).
      </div>
    </div>
  </section>

  <section class="sp-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Platform Settings</div>
        <div class="sp-card__sub">Feature toggles and core configuration.</div>
      </div>
      <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-floppy-disk"></i>&nbsp; Save (UI)</button>
    </div>

    <div class="sp-card__bd">
      <div class="sp-grid" style="grid-template-columns: 1fr; gap:12px;">
        <div class="sp-toggle">
          <div>
            <div style="font-weight:800">Public team pages</div>
            <div class="sp-card__sub">Allow public viewing of team roster and schedule.</div>
          </div>
          <label class="sp-switch">
            <input type="checkbox" checked>
            <span class="sp-slider"></span>
          </label>
        </div>

        <div class="sp-toggle">
          <div>
            <div style="font-weight:800">Auto-cancel unpaid</div>
            <div class="sp-card__sub">Cancel registrations after 72h if unpaid.</div>
          </div>
          <label class="sp-switch">
            <input type="checkbox" checked>
            <span class="sp-slider"></span>
          </label>
        </div>

        <div class="sp-toggle">
          <div>
            <div style="font-weight:800">Coach editing</div>
            <div class="sp-card__sub">Allow coaches to edit rosters and training notes.</div>
          </div>
          <label class="sp-switch">
            <input type="checkbox" checked>
            <span class="sp-slider"></span>
          </label>
        </div>

        <div class="sp-toggle">
          <div>
            <div style="font-weight:800">Parent messaging</div>
            <div class="sp-card__sub">Enable inbox messaging to coaches/admin.</div>
          </div>
          <label class="sp-switch">
            <input type="checkbox">
            <span class="sp-slider"></span>
          </label>
        </div>
      </div>

      <div style="height:14px"></div>

      <div class="sp-card" style="padding:14px; border:1px solid var(--line); box-shadow:none;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
          <div>
            <div style="font-weight:800">Payment Provider</div>
            <div class="sp-card__sub">Stripe / PayPal / Offline invoices.</div>
          </div>
          <span class="sp-pill">Stripe</span>
        </div>

        <div style="height:12px"></div>

        <div class="sp-form-grid">
          <div class="sp-col-12"><label class="sp-card__sub">Public key</label><input class="sp-input" style="width:100%" placeholder="pk_..." /></div>
          <div class="sp-col-12"><label class="sp-card__sub">Secret key</label><input class="sp-input" style="width:100%" placeholder="sk_..." /></div>
        </div>

        <div class="sp-form-actions" style="margin-top:10px">
          <button class="sp-btn sp-btn--ghost" type="button">Test Connection</button>
          <button class="sp-btn sp-btn--pill" type="button">Save Provider (UI)</button>
        </div>
      </div>

      <div class="sp-alert" style="margin-top:12px">
        <strong>Space tip:</strong> keep settings grouped in collapsible sections. Most admins only touch payments + seasons.
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
