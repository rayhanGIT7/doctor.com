<p class="text-muted">Set the days and hours you see patients. Patients can only book inside these hours, one patient per slot.</p>

<?= partial('partials/schedule_manager', ['schedules' => $schedules, 'baseUrl' => 'doctor/schedule']) ?>
