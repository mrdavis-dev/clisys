<?php
/**
 * Breadcrumb partial
 * Usage: set $breadcrumb array before including this file.
 * Example:
 *   $breadcrumb = [
 *       ['label' => 'Inicio', 'url' => 'inicio.php'],
 *       ['label' => 'Pagos',  'url' => 'getinfo.php'],
 *       ['label' => 'Registrar pago'],   // last item: no url
 *   ];
 *   include 'partials/breadcrumb.php';
 */
if (empty($breadcrumb)) return;
?>
<nav aria-label="Ruta de navegación" class="mb-3">
    <ol class="breadcrumb bg-transparent px-0 mb-0 small">
        <?php foreach ($breadcrumb as $i => $crumb):
            $isLast = ($i === count($breadcrumb) - 1);
        ?>
            <?php if (!$isLast && !empty($crumb['url'])): ?>
                <li class="breadcrumb-item">
                    <a href="<?= h($crumb['url']) ?>"><?= h($crumb['label']) ?></a>
                </li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= h($crumb['label']) ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
