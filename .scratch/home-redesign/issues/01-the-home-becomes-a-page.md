# 01 — The home becomes a Page

**What to build:** the root route stops serving the upstream kit's welcome view and starts
serving a Page — a full-page Livewire component — rendered into a layout that carries the
document and nothing else. Nothing about the page's appearance changes on purpose here; what
changes is what a page *is* in this repository. This is the tracer bullet, and it is
deliberately plain.

The placement follows [ADR-0003](../../../docs/adr/0003-livewire-pages-are-classes-ui-primitives-are-templates.md).
Livewire's configuration is published so that a component's view is found by mirroring its
class path from the root of the views directory, which is what lets the Page carry no `render`
method. The `pages` entry is removed from the component namespaces in the same edit: that
entry binds the same directory to the view-based resolver this repository has just refused,
and leaving both in place would mean one path with two resolvers, failing on the wrong one.
The layouts entry stays, because the configured page layout resolves through it.

Because the view path mirrors the class path from the root of the views directory, a Livewire
component sitting at the root of its namespace would write its view among the ordinary views.
That is the one hazard the configuration change introduces, so it is closed by a test in the
same ticket rather than by a note.

The route is registered through Livewire's own routing macro, and it is passed the component
**class** rather than its dot name. The macro accepts either; a class reference is checked by
the analyser, so renaming or moving the Page breaks the analysis instead of breaking the page.
The macro expands to a controller action, so the existing prohibition on route closures is
unaffected. The route keeps the name it already has.

The Page class has no body. It has no `render` because resolution is implicit, and no
properties because it holds no state — per ADR-0003 it is forbidden from inventing any to
justify its own existence. A consequence worth knowing before it surprises anyone at review:
this class contributes nothing to coverage and nothing to the mutation perimeter, because
there is nothing in it to execute.

The upstream welcome view goes, and its inline stylesheet fallback goes with it. That fallback
exists so the page looks presentable without a build; kept, it would need regenerating by hand
on every theme change, which makes it a second source of truth for styling condemned to
disagree with the first. The browser suite builds before it runs, so the page may assume a
build exists.

The existing example feature test is absorbed rather than kept. It already asserts that the
root route responds, without naming what it is verifying; the Page's test says the same thing
and says what it is about. The existing browser test is renamed and its assertion updated to
match the new copy.

**Parent:** [spec.md](../spec.md)

**Blocked by:** None — can start immediately.

**Status:** resolved

- [x] Livewire's configuration is published, with the view root set to the views directory itself
- [x] The `pages` entry is removed from the component namespaces, and the layouts entry is left in place
- [x] A layout exists that carries the document only — shell, head, font directive, bundle, slot — and no header, navigation or footer
- [x] A Page class exists in a `Pages` namespace under the application's Livewire root, with an empty body: no `render` method and no properties
- [x] The Page's view is found without a `render` method, by mirroring the class path
- [x] The root route is registered through Livewire's routing macro, passing the component class rather than a string name, and keeps its existing route name
- [x] The upstream welcome view is deleted, together with its inline stylesheet fallback
- [x] The example feature test is absorbed into a named test for the Page rather than left alongside it
- [x] A test fails if any Livewire component sits at the root of its namespace
- [x] The existing browser test is renamed to match the Page and its assertion updated; console hygiene and the absence of JavaScript errors are still asserted
- [x] The routing test still passes, with no closure-backed route introduced
- [x] `composer test` passes
- [x] `composer browser:test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention
