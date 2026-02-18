<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Parents';
$activeNav = 'parents';
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Parents</div>
      <div class="sp-card__sub">Manage parent accounts, contact info, payments, and messaging.</div>
    </div>

    <div class="sp-actions">
      <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-paper-plane"></i>&nbsp; Send Announcement</button>
      <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-plus"></i>&nbsp; Add Parent</button>
    </div>
  </div>

  <div class="sp-card__bd">
    <div class="sp-filterbar">
      <div class="sp-filterbar__left">
        <div class="sp-search">
          <i class="fa-solid fa-magnifying-glass icon"></i>
          <input data-table-search="#tblParents" type="text" placeholder="Search parents by name, email, phone…" />
        </div>

        <select class="sp-select" data-table-filter="#tblParents" data-col="4">
          <option value="">Any balance</option>
          <option>Overdue</option>
          <option>Paid</option>
        </select>

        <select class="sp-select" data-table-filter="#tblParents" data-col="5">
          <option value="">Any status</option>
          <option>Active</option>
          <option>Disabled</option>
        </select>
      </div>

      <div class="sp-filterbar__right">
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
      </div>
    </div>

    <div style="height:12px"></div>

    <div class="sp-table-wrap" style="max-height: 540px; border:1px solid var(--line)">
      <table id="tblParents" class="sp-table sp-table--light">
        <thead>
          <tr>
            <th>Parent</th>
            <th>Email</th>
            <th style="width:160px">Phone</th>
            <th style="width:90px">Players</th>
            <th style="width:120px">Balance</th>
            <th style="width:120px">Status</th>
            <th style="width:220px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Sarah Johnson</strong><div class="sp-card__sub">Preferred: Email</div></td>
            <td>sarah.j@email.com</td>
            <td>(555) 120-222</td>
            <td>2</td>
            <td><span class="sp-pill sp-pill--success">Paid</span></td>
            <td><span class="sp-pill sp-pill--success">Active</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">Message</button>
                <button class="sp-btn-tag" type="button">Invoices</button>
                <button class="sp-btn-tag danger" type="button">Disable</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><strong>Michael Brown</strong><div class="sp-card__sub">Preferred: SMS</div></td>
            <td>mike.b@email.com</td>
            <td>(555) 330-111</td>
            <td>1</td>
            <td><span class="sp-pill sp-pill--warning">Overdue</span></td>
            <td><span class="sp-pill sp-pill--success">Active</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">Remind</button>
                <button class="sp-btn-tag" type="button">Invoices</button>
                <button class="sp-btn-tag danger" type="button">Disable</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><strong>Emily Davis</strong><div class="sp-card__sub">Preferred: Email</div></td>
            <td>emily.d@email.com</td>
            <td>(555) 445-999</td>
            <td>3</td>
            <td><span class="sp-pill sp-pill--success">Paid</span></td>
            <td><span class="sp-pill">Disabled</span></td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag" type="button">View</button>
                <button class="sp-btn-tag" type="button">Enable</button>
                <button class="sp-btn-tag danger" type="button">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="sp-alert" style="margin-top:12px">
      <strong>Good UX default:</strong> show parent balances and payment state everywhere (players, teams, league dashboards) so admins can act quickly.
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
