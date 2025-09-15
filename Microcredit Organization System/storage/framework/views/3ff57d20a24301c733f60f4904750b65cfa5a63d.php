<?php $__env->startSection('content'); ?>

    <?php echo $printText; ?>

    <script type="text/javascript">
        $(function(){
            setTimeout(function () {
                window.print();
            }, 500);
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('print', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/print/print.blade.php ENDPATH**/ ?>