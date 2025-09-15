{{Form::select($name, $years, $value > 0 ? $value : (int)date('Y'), ['id' => $name, 'class' => 'form-control'])}}
