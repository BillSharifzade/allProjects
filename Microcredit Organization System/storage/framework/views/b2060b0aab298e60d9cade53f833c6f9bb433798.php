<?php echo e(Form::select($name, $days, $value > 0 ? $value : (int)date('d'), ['id' => $name, 'class' => 'form-control'])); ?>

<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/components/forms/select-day.blade.php ENDPATH**/ ?>