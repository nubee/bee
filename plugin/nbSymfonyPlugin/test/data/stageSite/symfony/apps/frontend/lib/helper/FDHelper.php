<?php

function box($title) {
  echo <<<TXT
<div class="box">
  <h1>$title</h1>
  <div class="box-inner">
TXT;
}

function end_box() {
  echo <<<TXT
  </div>
</div>
TXT;
}