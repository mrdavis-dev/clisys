<?php
// Shared form fields for create and edit clinic modals.
// $edit_row (array|null) — populated for edit mode, null for create.
$_f = $edit_row ?? [];
?>
<div class="form-group">
    <label for="cf_name">Nombre <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="cf_name" name="name"
           value="<?= h($_f['name'] ?? '') ?>" required maxlength="120">
</div>
<div class="form-group">
    <label for="cf_subdomain">Subdominio <span class="text-danger">*</span></label>
    <div class="input-group">
        <input type="text" class="form-control" id="cf_subdomain" name="subdomain"
               value="<?= h($_f['subdomain'] ?? '') ?>" required maxlength="60"
               pattern="[a-z0-9\-]+" title="Solo minúsculas, números y guiones">
        <div class="input-group-append">
            <span class="input-group-text">.clisys.com</span>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="cf_specialty">Especialidad</label>
    <select class="form-control" id="cf_specialty" name="specialty_id">
        <option value="">— Sin especialidad —</option>
        <?php foreach ($specialties as $sp): ?>
        <option value="<?= (int)$sp['id'] ?>"
            <?= (isset($_f['specialty_id']) && (int)$_f['specialty_id'] === (int)$sp['id']) ? 'selected' : '' ?>>
            <?= h($sp['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label for="cf_plan">Plan</label>
    <select class="form-control" id="cf_plan" name="plan_id">
        <option value="">— Sin plan —</option>
        <?php foreach ($plans as $pl): ?>
        <option value="<?= (int)$pl['id'] ?>"
            <?= (isset($_f['plan_id']) && (int)$_f['plan_id'] === (int)$pl['id']) ? 'selected' : '' ?>>
            <?= h($pl['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label for="cf_expires">Vencimiento del plan</label>
    <input type="date" class="form-control" id="cf_expires" name="plan_expires_at"
           value="<?= h($_f['plan_expires_at'] ?? '') ?>">
</div>
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="cf_active" name="active" value="1"
           <?= (!isset($_f['active']) || $_f['active']) ? 'checked' : '' ?>>
    <label class="form-check-label" for="cf_active">Clínica activa</label>
</div>
