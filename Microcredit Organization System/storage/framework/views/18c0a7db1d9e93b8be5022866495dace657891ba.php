<?php $__env->startSection('inc-content'); ?>
    <form method="get" action="/inc/safe/create" class="p-2">
        <div class="form-group">
            <label>Касса</label>
            <select name="cashbox" class="form-control" onchange="this.form.submit()">
                <option value="0">Выберите кассу</option>
                <?php $__currentLoopData = ($cashboxes ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php echo e((int)($selectedCashbox ?? 0) === (int)$c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </form>

    <?php if(isset($loans) && count($loans) > 0): ?>
    <form method="post" action="/inc/safe/create" class="p-2">
        <?php echo e(csrf_field()); ?>

        <input type="hidden" name="cashbox_id" value="<?php echo e($selectedCashbox); ?>" />
        <input type="hidden" name="select_all" id="select_all_input" value="" />
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" /></th>
                    <th>№</th>
                    <th>Клиент</th>
                    <th>Инфо</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><input type="checkbox" name="loan_ids[]" value="<?php echo e($l->id); ?>" /></td>
                        <td><?php echo e($l->document_no); ?></td>
                        <td><?php echo e(optional($l->loaner)->full_name); ?></td>
                        <td>
                            <?php if($l->collateral_type==1): ?>
                                <?php $__currentLoopData = $l->jewelries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge badge-light mr-1"><?php echo e($j->name); ?>, <?php echo e($j->purity); ?> пр., <?php echo e($j->weight); ?> гр.</span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php elseif($l->collateral_type==2 && $l->auto): ?>
                                марка <?php echo e($l->auto->brand); ?>, <?php echo e($l->auto->year); ?>, <?php echo e($l->auto->plate_number); ?>

                            <?php elseif($l->collateral_type==3 && $l->phone): ?>
                                смартфон <?php echo e($l->phone->brand); ?> <?php echo e($l->phone->model); ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex" style="position:sticky;bottom:0;left:0;right:0;z-index:1030;background:#fff;padding:8px 0;">
            <a href="/inc/safe" class="btn btn-light mr-2" style="min-width:120px">Отмена</a>
            <button class="btn btn-success flex-grow-1">Добавить выбранные</button>
            <button class="btn btn-outline-primary ml-2" type="submit" onclick="document.getElementById('select_all_input').value='1'">Добавить все (все страницы)</button>
        </div>
    </form>
    <div class="p-2" style="margin-bottom: 120px;"><?php echo e($loans->appends(['cashbox' => $selectedCashbox])->links()); ?></div>
    <script>
        (function(){
            var sel = document.getElementById('selectAll');
            if(!sel) return;
            sel.addEventListener('change', function(){
                var boxes = document.querySelectorAll('input[name="loan_ids[]"]');
                for (var i=0;i<boxes.length;i++){ boxes[i].checked = sel.checked; }
            });
        })();
    </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('incassator.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/incassator/safe/create.blade.php ENDPATH**/ ?>