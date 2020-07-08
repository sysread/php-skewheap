<?php

require __DIR__ . "/../src/SkewHeap.php";

use PHPUnit\Framework\TestCase;
use sysread\SkewHeap\SkewHeap;

class SkewHeapTest extends TestCase {
  public function testInitialState() {
    $s = new SkewHeap;
    $this->assertTrue($s->is_empty(), "is_empty() initially true");
  }

  public function testOrdering() {
    $nums = range(0, 100);
    shuffle($nums);

    $size = 0;

    $s = new SkewHeap;

    foreach ($nums as $num) {
      $this->assertEquals($s->size(), $size, "expected size before put()");
      $this->assertEquals($s->put($num), ++$size, "incremented size returned from put()");
      $this->assertEquals($s->size(), $size, "expected size after put()");
      $this->assertFalse($s->is_empty(), "is_empty() is false after put");
    }

    sort($nums);
    foreach ($nums as $num) {
      $this->assertEquals($s->peek(), $num, "peek() returns next expected value");
      $this->assertEquals($s->size(), $size, "expected size before take()");
      $this->assertEquals($s->take(), $num, "expected key returned from take()");
      $this->assertEquals($s->size(), --$size, "expected size after take()");
    }

    $this->assertTrue($s->is_empty(), "is_empty() true after draining heap");
  }

  public function testMerging() {
    $set_a = range(0, 9);
    shuffle($set_a);

    $set_b = range(10, 19);
    shuffle($set_b);

    $set_c = array_merge($set_a, $set_b);

    $a = new SkewHeap;
    $b = new SkewHeap;

    foreach ($set_a as $n) {
      $a->put($n);
    }

    foreach ($set_b as $n) {
      $b->put($n);
    }

    $c = new SkewHeap(null, $a, $b);

    $this->assertEquals($a->size(), 10, "input heap #1 retains size after merge()");
    $this->assertEquals($b->size(), 10, "input heap #2 retains size after merge()");
    $this->assertEquals($c->size(), 20, "result heap size is sum of $1 and $2");

    sort($set_a);
    sort($set_b);
    sort($set_c);

    $this->assertEquals($a->drain(), $set_a, "input heap #1 still holds its original contents");
    $this->assertEquals($b->drain(), $set_b, "input heap #2 still holds its original contents");
    $this->assertEquals($c->drain(), $set_c, "heap #3 contains both input heaps' contents");
  }
}

?>
