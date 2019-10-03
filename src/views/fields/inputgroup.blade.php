<?php 
        if(isset($options['icon']['wrapper']) && $options['icon']['wrapper'] != '') {
            $wrapperClass = $options['icon']['wrapper'];
        }
        else {
            $wrapperClass = '';
        }

        if(isset($options['icon']['class']) && $options['icon']['class'] != '') {
            $iconClass = $options['icon']['class'];
        }
        else {
            $iconClass = '';
        }


?>



<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

<?php if ($showLabel && $options['label'] !== false && $options['label_show']): ?>
    <?= Form::customLabel($name, $options['label'], $options['label_attr']) ?>
<?php endif; ?>

<?php if (isset($options['icon']['prepend']) && $options['icon']['prepend']): ?>
    
    <div class="input-group-prepend">
        <span class="input-group-text <?php echo $wrapperClass; ?>">
            <i class=" <?php echo $iconClass; ?>"><?php  echo  $options['icon']['name'] ; ?></i>
        </span>
    </div>
<?php endif; ?>



<?php if ($showField): ?>
    <?php if (isset($options['attr']['type']) && $options['attr']['type'] != ''): ?>
        <?= Form::input($options['attr']['type'], $name, $options['value'], $options['attr']) ?>
    <?php else: ?>
        <?= Form::input($type, $name, $options['value'], $options['attr']) ?>
    <?php endif; ?>
<?php endif; ?>
<?php if (isset($options['icon']['append']) && $options['icon']['append']): ?>
    <div class="input-group-append">
        <span class="input-group-text">
            <i class="material-icons"><?php  echo  $options['icon']['name'] ; ?></i>
        </span>
    </div>
    
<?php endif; ?>

<?php if ($options['help_block']['text'] && !$options['is_child']): ?>
<<?= $options['help_block']['tag'] ?> <?= $options['help_block']['helpBlockAttrs'] ?>>
    <?= $options['help_block']['text'] ?>
</<?= $options['help_block']['tag'] ?>>
<?php endif; ?>

<?php if ($showError && isset($errors) && $errors->has($nameKey)): ?>
    <div <?= $options['errorAttrs'] ?>><?= $errors->first($nameKey) ?></div>
<?php endif; ?>

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>

