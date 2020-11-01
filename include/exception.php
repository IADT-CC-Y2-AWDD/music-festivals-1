<?php if (isset($request) && $request->has_exception()) { ?>
<div class="alert alert-warning" role="alert"><?= $request->get_exception(  ) ?></div>
<?php } ?>
