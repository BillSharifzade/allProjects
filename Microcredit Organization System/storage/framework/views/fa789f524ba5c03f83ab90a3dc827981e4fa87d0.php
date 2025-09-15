<?php echo e(Form::select($name, $years, $value > 0 ? $value : (int)date('Y'), ['id' => $name, 'class' => 'form-control'])); ?>

<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/components/forms/select-year.blade.php ENDPATH**/ ?>