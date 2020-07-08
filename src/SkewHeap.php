<?php

/**
 *
 * @author    Jeff Ober <sysread@fastmail.fm>
 * @copyright 2020 Jeff Ober
 * @license   MIT
 *
 */

namespace sysread\SkewHeap;

function _indent($level=0) {
  for ($i = 0; $i < $level; ++$i) {
    echo "  ";
  }
}

class Node {
  public $key;
  public $left;
  public $right;

  function __construct($key, $left=null, $right=null) {
    $this->key   = $key;
    $this->left  = $left;
    $this->right = $right;
  }

  function explain($label='Root', $indent=0) {
    _indent($indent);
    printf("$label: %s\n", $this->key);

    if ($this->left) {
      $this->left->explain('Left', $indent + 1);
    }

    if ($this->right) {
      $this->right->explain('Right', $indent + 1);
    }
  }
}

/**
 * A skew heaps are light-weight priority queues notable for their ability to
 * be quickly merged with other heaps.
 */
class SkewHeap implements \Iterator {
  private $size = 0;
  private $root = null;
  private $cmp;

  /**
   * Creates a new skew heap which sorts in ascending order based on $cmp, a
   * function that understands how to compare two elements being stored in the
   * heap. $cmp returns < 0 if the first element should be placed ahead of the
   * second, > 0 if the second element should be placed ahead of the first, or
   * 0 if they are equal in priority.
   *
   * Any number of additional source skew heaps may be passed to the
   * constructor.  These heaps will be non-destructively merged into the new
   * heap, which will contain all of the elements of the source heaps.
   *
   * @param callable $cmp An optional function that is able to compare items
   * from the heap by priority. By default, a numerical ascending order
   * comparison is used.
   *
   * @param SkewHeap ...$heaps Any number of source heaps from which the new
   * heap will be populated with initial elements.
   */
  public function __construct($cmp=null, ...$heaps) {
    if ($cmp == null) {
      $this->cmp = function($a, $b){ return $a - $b; };
    } else {
      $this->cmp = $cmp;
    }

    foreach ($heaps as $heap) {
      $this->root = $this->merge_safe($this->root, $heap->root);
      $this->size += $heap->size;
    }
  }

  /**
   * Returns the number of items in the heap.
   *
   * @return int
   */
  public function size() {
    return $this->size;
  }

  /**
   * Returns true if there are no items in the heap.
   *
   * @return bool
   */
  public function is_empty() {
    return $this->size == 0;
  }

  /**
   * Inserts any number of items into the heap. Returns the size
   * of the heap after inserting all items.
   *
   * @param ...$items mixed Items of any type, so long as they are sortable by the
   * $cmp function passed to the constructor.
   *
   * @return int
   */
  public function put(...$items) {
    foreach ($items as $item) {
      $this->root = $this->merge($this->root, new Node($item));
      ++$this->size;
    }

    return $this->size;
  }

  /**
   * Returns the next element in the heap without removing it from the heap, or
   * null if the heap is empty.
   *
   * @return mixed|null
   */
  public function peek() {
    if ($this->size == 0) {
      return;
    }

    return $this->root->key;
  }

  /**
   * Removes and returns the next item in the heap, or null if the heap is
   * empty.
   *
   * @return mixed|null
   */
  public function take() {
    if ($this->size == 0) {
      return;
    }

    $item = $this->root->key;
    $this->root = $this->merge($this->root->left, $this->root->right);
    --$this->size;
    return $item;
  }

  /**
   * Removes and returns all items from the heap in an array.
   *
   * @return mixed[]
   */
  public function drain() {
    $items = [];

    while ($this->size > 0) {
      array_push($items, $this->root->key);
      $this->root = $this->merge($this->root->left, $this->root->right);
      --$this->size;
    }

    return $items;
  }

  /**
   * Prints out a visual explanation of the heap structure for debugging.
   *
   * @return void
   */
  public function explain() {
    printf("SkewHeap <size=%d>\n", $this->size);

    if ($this->root) {
      $this->root->explain();
    } else {
      _indent(1);
      printf("Empty");
    }
  }

  private function merge($a, $b) {
    if ($a == null) {
      return $b;
    }

    if ($b == null) {
      return $a;
    }

    if (($this->cmp)($a->key, $b->key) > 0) {
      [$a, $b] = [$b, $a];
    }

    return new Node(
      $a->key,
      $this->merge($b, $a->right),
      $a->left,
    );
  }

  private function merge_safe($a, $b) {
    if ($a == null) {
      return $b == null ? null : clone($b);
    }

    if ($b == null) {
      return $a == null ? null : clone($a);
    }

    if (($this->cmp)($a->key, $b->key) > 0) {
      [$a, $b] = [$b, $a];
    }

    return new Node(
      $a->key,
      $this->merge_safe($b, $a->right),
      $a->left == null ? null : clone($a->left),
    );
  }

  /**
   * Iterable interface:
   *
   *    foreach ($skewheap as $item) {
   *      do_stuff_with($item);
   *    }
   *
   */
  public function rewind()  { }
  public function current() { return $this->peek(); }
  public function key()     { return 0; }
  public function next()    { $this->take(); }
  public function valid()   { return $this->size > 0; }
}

?>
