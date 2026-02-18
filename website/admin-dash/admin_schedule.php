<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Schedule';
$activeNav = 'schedule';
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Schedule</div>
      <div class="sp-card__sub">Unified view of matches + trainings. Use filters to reduce noise.</div>
    </div>

    <div class="sp-actions">
      <select class="sp-select" style="height:38px">
        <option>Week view</option>
        <option>Day view</option>
        <option>Month view</option>
      </select>
      <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-filter"></i>&nbsp; Filters</button>
      <button class="sp-btn sp-btn--pill" type="button"><i class="fa-solid fa-plus"></i>&nbsp; Add Event</button>
    </div>
  </div>

  <div class="sp-card__bd">
    <div class="sp-filterbar">
      <div class="sp-filterbar__left">
        <select class="sp-select">
          <option>All leagues</option>
          <option>U14</option>
          <option>U16</option>
          <option>U18</option>
        </select>
        <select class="sp-select">
          <option>All teams</option>
          <option>FC Miami Blue</option>
          <option>FC New York</option>
          <option>LA United</option>
        </select>
        <span class="sp-pill"><i class="fa-solid fa-eye"></i> Show: Matches + Trainings</span>
      </div>

      <div class="sp-filterbar__right">
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-chevron-left"></i></button>
        <span class="sp-pill">Mar 2 – Mar 8</span>
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
    </div>

    <div style="height:14px"></div>

    <div class="sp-calendar" aria-label="Weekly calendar (UI demo)">
      <div></div>
      <div class="day-hd">Mon</div>
      <div class="day-hd">Tue</div>
      <div class="day-hd">Wed</div>
      <div class="day-hd">Thu</div>
      <div class="day-hd">Fri</div>
      <div class="day-hd">Sat</div>
      <div class="day-hd">Sun</div>

      <div class="time-col">
        <div class="time-slot">16:00</div>
        <div class="time-slot">18:00</div>
        <div class="time-slot">20:00</div>
      </div>

      <div class="day">
        <div class="sp-event sp-event--training">
          <div class="t">Training · FC Miami Blue</div>
          <div class="m">16:00 · Field A</div>
        </div>
      </div>

      <div class="day">
        <div class="sp-event sp-event--match">
          <div class="t">Match · FC Miami vs FC NY</div>
          <div class="m">18:00 · Field A</div>
        </div>
        <div class="sp-event sp-event--training">
          <div class="t">Training · FC New York</div>
          <div class="m">20:00 · Arena 2</div>
        </div>
      </div>

      <div class="day"></div>

      <div class="day">
        <div class="sp-event sp-event--training">
          <div class="t">Training · LA United</div>
          <div class="m">18:00 · Field C</div>
        </div>
      </div>

      <div class="day"></div>
      <div class="day"></div>
      <div class="day"></div>
    </div>

    <div class="sp-alert" style="margin-top:12px">
      <strong>Admin autonomy idea:</strong> let admin set "venue blackout dates" + weather alerts so events auto-suggest rescheduling.
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
