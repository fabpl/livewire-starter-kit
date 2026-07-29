# 05 — The guarantees

**What to build:** the last block of the composition — three cards stating what the gate
guarantees — and the second Primitive they are built from. This is the block that makes the
page true: it is where the repository stops describing a framework and starts describing
itself.

The claims are facts drawn from this repository's own configuration, not marketing. The
analysis level and the two rule sets that sit beyond it; the coverage minimum and the mutation
score; formatting and refactoring applied to the whole tree with no suppression mechanism
anywhere. Each of the three is checkable by opening a file in this repository, and a claim that
stops being true when a configuration changes is a claim that should fail review. If a number
is quoted, quote the one the configuration actually holds at the time of writing.

The card Primitive exposes no variants. It is a near-white surface on the parchment stage with
a warm border, a modest radius and airy padding — the layering the design document calls the
theme's signature stage. Borders do the structural work and the shadow is a whisper; the
temptation to reach for a heavier elevation is the one thing to resist here.

The three cards sit on the page as one composition, at the medium-low density the design
document prescribes, with a readable measure for their prose. Their body is reading text, so it
is the second and last place on this page that opts into the reading face.

This ticket closes the effort, so it carries the two closing obligations. The spec's status
moves to resolved. And the mutation score is re-measured, because tickets 01 to 04 changed the
measured perimeter — though in an unusual direction: this effort adds almost no mutable PHP at
all, since the Page has no body and the variant tables live outside the perimeter by design. The
score may therefore have moved without anyone deciding to move it. The pinned minimum is updated
only if the score moved **up**. If it moved down, that is a finding: report it with the escaping
mutants named, and leave the pin where it is. The bar is never lowered to make it pass.

**Parent:** [spec.md](../spec.md)

**Blocked by:** 03

**Status:** resolved

- [x] The card Primitive exists with no variants, and passes the purity test
- [x] Three cards state what the gate guarantees, each claim checkable against this repository's own configuration
- [x] Any number quoted is the number the configuration holds at the time of writing
- [x] The cards are near-white surfaces on the page stage, with border-led structure rather than heavy elevation — the two Tokens hold the same value here, see the first comment
- [x] Their body text opts into the reading face
- [x] The HTTP seam asserts the three claims are present
- [x] The purity test, the contrast test and the accessibility audit still pass
- [x] The mutation score is re-measured and the figure recorded
- [x] The pinned minimum is raised only if the score moved up; if it moved down, the fall is reported with the escaping mutants named and the pin is left alone
- [x] The spec's status is moved to resolved
- [x] `composer test` passes
- [x] `composer browser:test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

**The near-white surface and the parchment stage are the same colour, and the border is what
separates them.** `--card` is `#faf9f5` and `--background` is `#faf9f5`; in dark both are
`#262624`. The ticket's description of a card layered on the stage is the design document's, and
the theme as published does not implement it that way. Nothing was moved to fix that: ADR-0004
licenses moving a Token when a *pairing fails a threshold*, not when a surface is less
distinguishable than the prose describing it, and inventing a separation here would put a value
in the stylesheet that no mechanism holds. So the Primitive names the semantic Tokens — a theme
swapped into this contract may well separate them — and the border does the structural work the
ticket asks for, with `shadow-sm` behind it. What does differ is the text: `--card-foreground`
is darker than `--foreground` in light and lighter in dark, so a card's prose already reads as
its own surface even where the fill does not.

**The mutation score moved up and not for this effort's reasons, so the pin moves to 75.** 75.00
per cent over 28 mutants against the pinned 72.00 over 25. The home redesign contributed no
mutants at all — `Home` has no body, exactly as ticket 01 predicted — and the three that
appeared came from the defaults effort's `Model::shouldBeStrict` and `URL::forceHttps`, all three
killed by that effort's own tests. The seven escaping mutants are the same seven ticket 10
listed: two `RemoveMethodCall` in `AppServiceProvider` on `Date::use` and
`DB::prohibitDestructiveCommands`, two on the `initials()` ternary, and three on `User::casts()`.
Discriminating power was checked rather than assumed — `--min=76` exits one, `--min=75` exits
zero — and parallel and serial runs agree on both the score and the count. The full write-up is
in the spec.

**The figures on the page are not asserted by the suite, and that is deliberate.** The HTTP seam
asserts the three headings, because a heading is the claim in its shortest form and a card that
goes takes one with it. It does not assert "level 10", "a hundred per cent" or "75 per cent". The
mutation score already lives in `composer.json`, the coverage minimum beside it and the level in
`phpstan.neon`; pinning any of them in a test would make the suite a third holder of a number it
does not own, which is the class of test the quality-gate spec refused. That the quoted figure
matches the configuration is a review obligation, and it is checkable in one file each — which is
the bar the ticket sets rather than one it asks a mechanism to hold.

**The card takes no structural slots, and refusing them is the same rule as the button's variant
matrix.** shadcn's card ships a header, a title, a description, a content region and a footer.
Each would be a component this composition does not render, verifiable only through a
demonstration view whose only consumer is a test — the fourth seam the spec considered and
refused. The Primitive owns the surface; what sits inside it belongs to the composer.

**The measure inside a card is under forty characters, which is narrower than running prose
wants.** Three columns in a `max-w-5xl` shell leave about 260px of text at `text-sm`. That is
below the range long-form body text is set at, and the response was to write two-sentence bodies
rather than to widen the shell or drop to two columns — a card is read at a glance. The middle
breakpoint exists for the same reason: three columns at the tablet width would take the measure
under thirty, so it holds at two there and the third card sits alone on the second row.

**`text-balance` on the card headings came out of looking at the page.** Without it "Coverage at
a hundred per cent" broke as five words and one, which reads as a defect rather than as a line
break. Recorded because it is the second time on this page that a typographic fault was invisible
in the markup and obvious in the browser.
