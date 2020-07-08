# NAME

SkewHeap - mergable priority queues

# SYNOPSIS

    use sysread\SkewHeap\SkewHeap;

    $heap = new SkewHeap;

    foreach ($tasks as $task) {
        $heap->put($task);
    }

    while (!$heap->is_empty()) {
        my $next_task = $heap->take();
        do_stuff_to($next_task);
    }

# DESCRIPTION

A skew heaps are light-weight priority queues notable for their ability to be
quickly merged with other heaps.

# INSTALLATION

    composer require sysread/php-skewheap

# USAGE

## CONSTRUCTOR

Accepts an optional argument specifying a function to be used when comparing
entries in the heap that returns -1, 0, or 1 if the first entry has a higher,
equal, or lower priority, respectively. If not specified or null, a simple
numeric comparison is used, giving higher priority to the lower value.

Any number of source heaps from which the skew heap should be initially
populated may be passed as additional arguments to the constructor.

    $heap = new SkewHeap;

    $heap = new SkewHeap(function($a, $b) {
        return $a - $b;
    });

    $heap = new SkewHeap(null, $heap1, $heap2, ...);

## METHODS

### size()

Returns the number of items in the heap.

### is_empty()

Returns true if there is at least one item in the queue.

### put(...$items)

Inserts any number of items into the queue. Returns the new queue size.

### peek()

Returns the top item in the queue without removing it. Returns null when the
queue is empty.

### take()

Removes and returns the next item in the queue. Returns null if the queue is
empty.

### drain()

Removes and returns an array of all items in the queue.

## AUTHOR

Jeff Ober <sysread@fastmail.fm>

## LICENSE

Copyright (c) 2020 Jeff Ober

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
