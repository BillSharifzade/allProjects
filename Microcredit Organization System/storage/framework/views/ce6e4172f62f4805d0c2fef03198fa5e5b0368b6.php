<?php $__env->startSection('content'); ?>
    <div class="locked-hide">
    <?php echo e(Form::open(['url' => '/payments/'.$loan->id.'/create', 'method' => 'post', 'id' => 'paymentForm', 'name' => 'paymentForm'])); ?>

        <?php echo e(csrf_field()); ?>

        <input type="hidden" name="idempotency_key" value="<?php echo e($idempotencyToken ?? ''); ?>" />
        <div class="container">
            <div class="row">
                <div class="col">
                    <?php echo app('arrilot.widget')->run('Error'); ?>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Новый платеж</h5>
                </div>
            </div>

            <?php if((int)($loan->last_interest_payment_date ?? 0) <= 0): ?>
            <div class="row my-2">
                <div class="col">
                    <div class="alert alert-info mb-2" role="alert">
                        Первый процентный платеж: минимальная сумма по процентам — 10 сомони.
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="interest_sum">Сумма процента</label>
                        <?php echo e(Form::text('interest_sum', '', ['id' => 'interest_sum', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="principal_sum">Сумма основного кредита</label>
                        <?php echo e(Form::text('principal_sum', '', ['id' => 'principal_sum', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <?php echo e(Form::button('СОХРАНИТЬ', ['id' => 'submitBtn', 'class' => 'btn btn-primary'])); ?>

                </div>
            </div>
        </div>

    <?php echo e(Form::close()); ?>

    </div>
    <script type="text/javascript">
        $(function(){
            $('#submitBtn').click(function (event){
                event.preventDefault()

                $(this).hide();
                $('#paymentForm').trigger('submit');

                return false;
            });
            // disable double-submit on back/refresh
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/payment/create.blade.php ENDPATH**/ ?>