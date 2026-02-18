<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Trainings';
$activeNav = 'trainings';
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Trainings</div>
      <div class="sp-card__sub">Schedule practices, manage attendance, and notify parents & coaches.</div>
    </div>

    <div class="sp-actions">
      <div class="sp-viewtabs sp-viewtabs--light" data-viewtabs title="Switch view">
        <button type="button" data-view="list" class="active"><i class="fa-solid fa-list"></i> List</button>
        <button type="button" data-view="calendar"><i class="fa-solid fa-calendar-week"></i> Week</button>
      </div>
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgTrainingCreate"><i class="fa-solid fa-plus"></i>&nbsp; Create Training</button>
      <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-clipboard-check"></i>&nbsp; Attendance</button>
      <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-bell"></i>&nbsp; Notify</button>
    </div>
  </div>

  <div class="sp-card__bd">

    <!-- LIST VIEW -->
    <section data-viewpane="list">
    <div class="sp-kpis" style="margin-bottom:14px;">
      <div class="sp-kpi"><div class="label">Upcoming Trainings</div><div class="value">18</div><div class="meta">next 7 days</div></div>
      <div class="sp-kpi"><div class="label">Attendance Avg.</div><div class="value">78%</div><div class="meta">last 30 days</div></div>
      <div class="sp-kpi"><div class="label">Low Attendance</div><div class="value">4</div><div class="meta">below 60%</div></div>
      <div class="sp-kpi"><div class="label">Conflicts</div><div class="value">2</div><div class="meta">venue overlap</div></div>
    </div>

    <div class="sp-filterbar">
      <div class="sp-filterbar__left">
        <div class="sp-search">
          <i class="fa-solid fa-magnifying-glass icon"></i>
          <input data-table-search="#tblTrainings" type="text" placeholder="Search by team, coach, location…" />
        </div>

        <select class="sp-select" data-table-filter="#tblTrainings" data-col="1">
          <option value="">All teams</option>
          <option>FC Miami Blue</option>
          <option>FC New York</option>
          <option>LA United</option>
        </select>

        <select class="sp-select" data-table-filter="#tblTrainings" data-col="4">
          <option value="">Any status</option>
          <option>Upcoming</option>
          <option>Completed</option>
          <option>Cancelled</option>
        </select>
      </div>

      <div class="sp-filterbar__right">
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
      </div>
    </div>

    <div style="height:12px"></div>

    <div class="sp-table-wrap" style="max-height: 560px; border:1px solid var(--line)">
      <table id="tblTrainings" class="sp-table sp-table--light">
        <thead>
          <tr>
            <th style="width:140px">Date & Time</th>
            <th>Team</th>
            <th>Coach</th>
            <th>Location</th>
            <th style="width:120px">Status</th>
            <th style="width:140px">Attendance</th>
            <th style="width:220px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>2026-03-01</strong><div class="sp-card__sub">17:00</div></td>
            <td>FC Miami Blue</td>
            <td>Coach Williams</td>
            <td>Field A</td>
            <td><span class="sp-pill sp-pill--success">Upcoming</span></td>
            <td>—</td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">Edit</button>
                <button class="sp-btn-tag" type="button">Notify</button>
                <button class="sp-btn-tag danger" type="button">Cancel</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><strong>2026-02-20</strong><div class="sp-card__sub">18:00</div></td>
            <td>FC New York</td>
            <td>Coach Rivera</td>
            <td>Downtown Arena</td>
            <td><span class="sp-pill">Completed</span></td>
            <td>14 / 18</td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag primary" type="button">Attendance</button>
                <button class="sp-btn-tag" type="button">Notes</button>
                <button class="sp-btn-tag danger" type="button">Delete</button>
              </div>
            </td>
          </tr>

          <tr>
            <td><strong>2026-02-18</strong><div class="sp-card__sub">17:30</div></td>
            <td>LA United</td>
            <td>Coach Rivera</td>
            <td>Field C</td>
            <td><span class="sp-pill sp-pill--danger">Cancelled</span></td>
            <td>—</td>
            <td>
              <div class="sp-actions">
                <button class="sp-btn-tag" type="button">Reschedule</button>
                <button class="sp-btn-tag" type="button">Notify</button>
                <button class="sp-btn-tag danger" type="button">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="sp-alert" style="margin-top:12px">
      <strong>Recommended admin control:</strong> training templates per team (weekly schedule) + auto-notifications 24h before.
    </div>
    </section>

    <!-- CALENDAR VIEW -->
    <section data-viewpane="calendar" hidden>
      <div class="sp-filterbar" style="margin-bottom:12px;">
        <div class="sp-filterbar__left">
          <span class="sp-pill"><i class="fa-solid fa-calendar-days"></i>&nbsp; Week of Mar 2–8</span>
          <select class="sp-select" style="height:38px">
            <option>All teams</option>
            <option>FC Miami Blue</option>
            <option>FC New York</option>
            <option>LA United</option>
          </select>
          <select class="sp-select" style="height:38px">
            <option>All coaches</option>
            <option>Coach Williams</option>
            <option>Coach Rivera</option>
          </select>
          <select class="sp-select" style="height:38px">
            <option>All locations</option>
            <option>Field A</option>
            <option>Downtown Arena</option>
            <option>Field C</option>
          </select>
        </div>
        <div class="sp-filterbar__right">
          <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-chevron-right"></i></button>
          <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-wand-magic-sparkles"></i>&nbsp; Auto-generate</button>
        </div>
      </div>

      <div class="sp-calendar" aria-label="Weekly training schedule">
        <div></div>
        <div class="day-hd">MON 2</div>
        <div class="day-hd">TUE 3</div>
        <div class="day-hd">WED 4</div>
        <div class="day-hd">THU 5</div>
        <div class="day-hd">FRI 6</div>
        <div class="day-hd">SAT 7</div>
        <div class="day-hd">SUN 8</div>

        <div class="time-col">
          <div class="time-slot">16:00</div>
          <div class="time-slot">18:00</div>
          <div class="time-slot">20:00</div>
        </div>

        <div class="day">
          <div class="sp-event sp-event--training">
            <div class="t">FC Miami Blue</div>
            <div class="m">17:00 • Field A • Coach Williams</div>
          </div>
        </div>
        <div class="day">
          <div class="sp-event sp-event--training">
            <div class="t">FC New York</div>
            <div class="m">18:00 • Downtown Arena • Coach Rivera</div>
          </div>
        </div>
        <div class="day">
          <div class="sp-event sp-event--training">
            <div class="t">LA United</div>
            <div class="m">17:30 • Field C • Coach Rivera</div>
          </div>
        </div>
        <div class="day"></div>
        <div class="day">
          <div class="sp-event sp-event--training">
            <div class="t">FC Miami Blue</div>
            <div class="m">19:00 • Field A • Coach Williams</div>
          </div>
        </div>
        <div class="day"></div>
        <div class="day"></div>
      </div>

      <div class="sp-alert" style="margin-top:12px">
        <strong>Admin autonomy idea:</strong> set a <em>team template</em> (e.g., Mon/Wed/Fri 17:00) and generate the whole season. Then allow exceptions (weather, venue conflict) + one-click notify.
      </div>
    </section>
  </div>
</section>

<dialog id="dlgTrainingCreate" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Create Training</div>
    <div class="sp-card__sub">UI-only.</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6"><label class="sp-card__sub">Team</label><select class="sp-select" style="width:100%"><option>FC Miami Blue</option><option>FC New York</option><option>LA United</option></select></div>
        <div class="sp-col-6"><label class="sp-card__sub">Coach</label><select class="sp-select" style="width:100%"><option>Coach Williams</option><option>Coach Rivera</option></select></div>
        <div class="sp-col-6"><label class="sp-card__sub">Date</label><input class="sp-input" style="width:100%" placeholder="YYYY-MM-DD" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Time</label><input class="sp-input" style="width:100%" placeholder="HH:MM" /></div>
        <div class="sp-col-12"><label class="sp-card__sub">Location</label><input class="sp-input" style="width:100%" placeholder="Field / Arena" /></div>
        <div class="sp-col-12"><label class="sp-card__sub">Notes</label><input class="sp-input" style="width:100%" placeholder="Bring equipment, etc." /></div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Create (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
