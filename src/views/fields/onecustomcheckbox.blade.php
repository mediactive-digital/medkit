<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

<?php if ($showField): ?>
    <?= Form::checkbox($name, $options['value'], $options['checked'], $options['attr']) ?>

    <?php if ($showLabel && $options['label'] !== false && $options['label_show']): ?>
        <?= Form::customLabel($name, $options['label'], $options['label_attr']) ?>
    <?php endif; ?>

    <?php if ($showError && isset($errors) && $errors->has($nameKey)): ?>
    <div <?= $options['errorAttrs'] ?>><?= $errors->first($nameKey) ?></div>
<?php endif; ?>


<?php if ($options['help_block']['text'] && !$options['is_child']): ?>
<<?= $options['help_block']['tag'] ?> <?= $options['help_block']['helpBlockAttrs'] ?>>
    <?= $options['help_block']['text'] ?>
</<?= $options['help_block']['tag'] ?>>
<?php endif; ?>
<?php endif; ?>

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>