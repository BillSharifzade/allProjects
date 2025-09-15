<?php echo e(Form::select($name, $months, $value > 0 ? $value : (int)date('m'), ['id' => $name, 'class' => 'form-control'])); ?>

<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/components/forms/select-month.blade.php ENDPATH**/ ?>