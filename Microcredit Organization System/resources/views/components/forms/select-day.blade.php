{{Form::select($name, $days, $value > 0 ? $value : (int)date('d'), ['id' => $name, 'class' => 'form-control'])}}
