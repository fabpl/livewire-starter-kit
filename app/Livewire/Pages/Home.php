<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Livewire\Component;

/*
 * @note The empty body is the shape, not an omission waiting to be filled. No `render`, because
 * `livewire.view_path` resolves the view and says there why; no properties, because this Page
 * holds no state and ADR-0003 forbids inventing any to justify the class. The consequence to
 * know before it surprises anyone at review: this contributes nothing to coverage and nothing to
 * the mutation perimeter, because there is nothing in it to execute.
 */
final class Home extends Component {}
