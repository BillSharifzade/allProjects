<?php $__env->startSection('content'); ?>

    <?php echo e(Form::open(['url' => '/loans/' . $loan->id . '/close', 'method' => 'post'])); ?>

        <?php echo e(csrf_field()); ?>

        <div class="container">
            <div class="row">
                <div class="col">
                    <?php echo app('arrilot.widget')->run('Error'); ?>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Закрытие кредита</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <?php if($loan->audit_document_no > 0): ?>
                        №<?php echo e($loan->document_no); ?>-<?php echo e($loan->audit_document_no); ?>

                    <?php else: ?>
                        №<?php echo e($loan->document_no); ?>

                    <?php endif; ?>

                    <?php echo e($loan->loaner->full_name); ?>

                </div>
            </div>

            <div>

            </div>
            <div class="row">
                <?php if($loan->close_request_at > 0): ?>
                    <div class="col" >
                    <button class="btn btn-primary w-100">
                        <a href="/print/loanslip/<?php echo e($loan->id); ?>" class="text-white">РАСПЕЧАТАТЬ ДОКУМЕНТ</a>
                    </button>
                    </div>
                <?php endif; ?>
                <div class="col" >

                    <?php echo e(Form::submit( $loan->close_request_at == 0 ? 'ОТПРАВИТЬ ЗАПРОС НА ЗАКРЫТИЕ' : 'ЗАКРЫТЬ КРЕДИТ', ['class' => 'btn btn-danger w-100'])); ?>

                </div>
                <div class="col">
                    <a class="btn btn-info w-100" href="/loans">ОТМЕНА</a>
                </div>
            </div>
        </div>
    <?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/loan/close.blade.php ENDPATH**/ ?>