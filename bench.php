<?php

require("src/SkewHeap.php");

function D($n) {
  return number_format($n, 0, '.', '_');
}

function F($n) {
  return number_format($n, 3, '.', '_');
}

function time_call_ms($fn) {
  $start = hrtime(true);
  $fn();
  $end = hrtime(true);
  return ($end - $start) / 1e+6; // nanoseconds to ms
}

function bench($count) {
  printf("Bench: %s items\n", D($count));

  $nums = range(0, $count - 1);
  shuffle($nums);

  $skew  = new SkewHeap();
  $put   = time_call_ms(function() use($nums, $skew) { $skew->put(...$nums); });
  $take  = time_call_ms(function() use($skew) { $skew->drain(); });
  $total = $put + $take;

  printf("  put/take %s items took %s ms\n", D($count), F($total));
  printf("    -  put(): %s ms (%s ms/call)\n", F($put), F($put / $count));
  printf("    - take(): %s ms (%s ms/call)\n", F($take), F($take / $count));
  print("\n");
}

if (count($argv) > 1) {
  for ($i = 1; $i < count($argv); ++$i) {
    bench($argv[$i]);
  }
}
else {
  bench(10000);
}

?>
